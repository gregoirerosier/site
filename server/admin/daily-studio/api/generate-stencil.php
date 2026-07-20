<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__, 4) . '/config/bootstrap.php';
require_once dirname(__DIR__, 4) . '/includes/beyond-ai.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

function stencilJson(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') stencilJson(['ok'=>false,'error'=>'POST required.'], 405);
    if (!Auth::check()) stencilJson(['ok'=>false,'error'=>'Administrator access required.'], 403);
    if (!Auth::verifyCsrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) stencilJson(['ok'=>false,'error'=>'Invalid security token.'], 403);
    $input = json_decode((string)file_get_contents('php://input'), true, 32, JSON_THROW_ON_ERROR);
    $idea = mb_substr(trim((string)($input['idea'] ?? '')), 0, 700);
    $style = mb_substr(trim((string)($input['style'] ?? 'Fine-line blackwork')), 0, 80);
    $placement = mb_substr(trim((string)($input['placement'] ?? 'Outer forearm')), 0, 80);
    if (mb_strlen($idea) < 8) stencilJson(['ok'=>false,'error'=>'Describe the stencil concept in a little more detail.'], 422);
    if (!function_exists('curl_init')) stencilJson(['ok'=>false,'error'=>'The server cURL extension is required.'], 503);
    $prompt = "Create a premium, professional tattoo stencil design sheet. Concept: {$idea}. Style: {$style}. Intended placement: {$placement}. Render only isolated artwork on pure white: crisp black transfer-ready linework, intentional line-weight hierarchy, generous negative space, anatomy-aware flow, symmetrical where appropriate, no skin, person, mockup, color, gray wash, shadows, text, logo, watermark, or border. Centered vertical composition with exceptionally clean edges, practical for an artist to print and refine.";
    $openAiKey = trim((string)beyond_ai_config('api_key', ''));
    $googleKey = trim((string)beyond_ai_config('google_image_key', ''));
    $errors = [];
    if ($openAiKey !== '' && !str_contains($openAiKey, 'YOUR_')) {
        $body = json_encode(['model'=>'gpt-image-2','prompt'=>$prompt,'size'=>'1024x1536','quality'=>'high','output_format'=>'png','background'=>'opaque'], JSON_THROW_ON_ERROR);
        $curl = curl_init('https://api.openai.com/v1/images/generations');
        curl_setopt_array($curl, [CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>180,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$openAiKey,'Content-Type: application/json'],CURLOPT_POSTFIELDS=>$body]);
        $raw = curl_exec($curl); $curlError = curl_error($curl); $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE); curl_close($curl);
        $response = is_string($raw) ? json_decode($raw, true) : null;
        $image = is_array($response) ? (string)($response['data'][0]['b64_json'] ?? '') : '';
        if ($status >= 200 && $status < 300 && $image !== '' && base64_decode($image, true) !== false) stencilJson(['ok'=>true,'image'=>'data:image/png;base64,'.$image,'model'=>'gpt-image-2','provider'=>'openai']);
        $errors[] = is_array($response) ? (string)($response['error']['message'] ?? 'OpenAI image generation failed.') : ($curlError ?: 'OpenAI image generation failed.');
    }
    if ($googleKey !== '' && !str_contains($googleKey, 'YOUR_')) {
        $model = trim((string)beyond_ai_config('google_image_model', 'gemini-3.1-flash-image')) ?: 'gemini-3.1-flash-image';
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) throw new RuntimeException('The configured Google image model is invalid.');
        $body = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['responseModalities'=>['TEXT','IMAGE'],'imageConfig'=>['aspectRatio'=>'2:3']]], JSON_THROW_ON_ERROR);
        $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.rawurlencode($model).':generateContent');
        curl_setopt_array($curl, [CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>180,CURLOPT_HTTPHEADER=>['x-goog-api-key: '.$googleKey,'Content-Type: application/json'],CURLOPT_POSTFIELDS=>$body]);
        $raw = curl_exec($curl); $curlError = curl_error($curl); $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE); curl_close($curl);
        $response = is_string($raw) ? json_decode($raw, true) : null; $image=''; $mime='image/png';
        foreach ((array)($response['candidates'][0]['content']['parts'] ?? []) as $part) { if (!empty($part['inlineData']['data'])) { $image=(string)$part['inlineData']['data']; $mime=(string)($part['inlineData']['mimeType']??'image/png'); break; } }
        if ($status >= 200 && $status < 300 && $image !== '' && ($bytes=base64_decode($image, true)) !== false) {
            // Publishing expects a PNG. Normalize Google's JPEG/WebP output
            // when GD is available so preview and publishing use one format.
            if ($mime !== 'image/png' && function_exists('imagecreatefromstring')) {
                $canvas=@imagecreatefromstring($bytes);
                if ($canvas !== false) { ob_start(); imagepng($canvas, null, 9); $png=ob_get_clean(); imagedestroy($canvas); if (is_string($png) && $png!=='') { $image=base64_encode($png); $mime='image/png'; } }
            }
            if ($mime !== 'image/png') throw new RuntimeException('Google returned an image, but PHP GD is required to convert it to PNG for publishing.');
            stencilJson(['ok'=>true,'image'=>'data:image/png;base64,'.$image,'model'=>$model,'provider'=>'google']);
        }
        $errors[] = is_array($response) ? (string)($response['error']['message'] ?? 'Google Imagen generation failed.') : ($curlError ?: 'Google Imagen generation failed.');
    }
    if (!$errors) throw new RuntimeException('Add an OpenAI or Google Imagen API key in protected Site Settings.');
    throw new RuntimeException('Image providers failed: '.implode(' | ', $errors));
} catch (Throwable $error) {
    error_log('Stencil generation failed: '.$error->getMessage());
    stencilJson(['ok'=>false,'error'=>$error->getMessage()], 400);
}
