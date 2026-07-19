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
    $apiKey = (string)beyond_ai_config('api_key', '');
    if ($apiKey === '' || str_contains($apiKey, 'YOUR_')) stencilJson(['ok'=>false,'error'=>'Add OPENAI_API_KEY to the protected hosting environment to enable stencil generation.'], 503);
    if (!function_exists('curl_init')) stencilJson(['ok'=>false,'error'=>'The server cURL extension is required.'], 503);
    $prompt = "Create a premium, professional tattoo stencil design sheet. Concept: {$idea}. Style: {$style}. Intended placement: {$placement}. Render only isolated artwork on pure white: crisp black transfer-ready linework, intentional line-weight hierarchy, generous negative space, anatomy-aware flow, symmetrical where appropriate, no skin, person, mockup, color, gray wash, shadows, text, logo, watermark, or border. Centered vertical composition with exceptionally clean edges, practical for an artist to print and refine.";
    $body = json_encode(['model'=>'gpt-image-2','prompt'=>$prompt,'size'=>'1024x1536','quality'=>'high','output_format'=>'png','background'=>'opaque'], JSON_THROW_ON_ERROR);
    $curl = curl_init('https://api.openai.com/v1/images/generations');
    curl_setopt_array($curl, [CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>180,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$apiKey,'Content-Type: application/json'],CURLOPT_POSTFIELDS=>$body]);
    $raw = curl_exec($curl); $curlError = curl_error($curl); $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE); curl_close($curl);
    if (!is_string($raw) || $raw === '') throw new RuntimeException($curlError ?: 'OpenAI returned an empty response.');
    $response = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    if ($status < 200 || $status >= 300) throw new RuntimeException((string)($response['error']['message'] ?? 'OpenAI image generation failed.'));
    $image = (string)($response['data'][0]['b64_json'] ?? '');
    if ($image === '' || base64_decode($image, true) === false) throw new RuntimeException('OpenAI did not return a usable stencil image.');
    stencilJson(['ok'=>true,'image'=>'data:image/png;base64,'.$image,'model'=>'gpt-image-2']);
} catch (Throwable $error) {
    error_log('Stencil generation failed: '.$error->getMessage());
    stencilJson(['ok'=>false,'error'=>$error->getMessage()], 400);
}
