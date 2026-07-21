<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
require_login();
$userId=bt_current_user_id(); $tattoos=bt_list_tattoos($userId); $selectedId=filter_input(INPUT_GET,'tattoo_id',FILTER_VALIDATE_INT)?:null;
$message=flash('success'); $error=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=(string)($_POST['action']??'upload');
  if($action==='delete'){
    $entryId=filter_input(INPUT_POST,'entry_id',FILTER_VALIDATE_INT)?:0;
    $relative=bt_delete_healing_entry($userId,$entryId);
    if($relative){$base=realpath(UPLOAD_DIR);$target=realpath(UPLOAD_DIR.'/'.$relative);if(is_string($base)&&is_string($target)&&str_starts_with(str_replace('\\','/',$target),str_replace('\\','/',$base).'/'))@unlink($target);flash('success','Healing entry deleted.');}
    redirect('healing.php');
  }
  $tattooId=filter_input(INPUT_POST,'tattoo_id',FILTER_VALIDATE_INT)?:null;
  $photo=$_FILES['photo']??null;
  if($tattooId===null||!bt_owned_tattoo($userId,$tattooId))$error='Choose one of your tattoos.';
  elseif(!is_array($photo)||($photo['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)$error='Choose an image to upload.';
  elseif((int)($photo['size']??0)<1||(int)$photo['size']>10*1024*1024)$error='The image must be smaller than 10 MB.';
  elseif(!is_uploaded_file((string)$photo['tmp_name']))$error='The upload could not be verified.';
  else{
    $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=(string)$finfo->file((string)$photo['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];$dimensions=@getimagesize((string)$photo['tmp_name']);
    if(!isset($allowed[$mime])||$dimensions===false||($dimensions['mime']??'')!==$mime)$error='Only valid JPEG, PNG, or WebP images are accepted.';
    elseif((int)$dimensions[0]>12000||(int)$dimensions[1]>12000)$error='The image dimensions are too large.';
    else{
      $ownerKey=hash('sha256','user:'.$userId);$ownerDir=UPLOAD_DIR.'/'.$ownerKey;
      if(!is_dir($ownerDir)&&!mkdir($ownerDir,0750,true)&&!is_dir($ownerDir))$error='Private upload storage is unavailable.';
      else{$name=gmdate('Ymd-His').'-'.bin2hex(random_bytes(12)).'.'.$allowed[$mime];$target=$ownerDir.'/'.$name;
        if(!move_uploaded_file((string)$photo['tmp_name'],$target))$error='The image could not be stored.';
        else{try{bt_add_healing_entry($userId,$tattooId,['file_path'=>$ownerKey.'/'.$name,'mime'=>$mime,'bytes'=>(int)$photo['size'],'width'=>(int)$dimensions[0],'height'=>(int)$dimensions[1],'notes'=>$_POST['notes']??'']);flash('success','Healing entry saved privately.');redirect('healing.php?tattoo_id='.$tattooId);}catch(Throwable $e){@unlink($target);$error='The healing entry could not be saved.';}}
      }
    }
  }
}
$entries=bt_list_healing_entries($userId);$pageTitle='Healing Journal — Beyond Tattoo';require __DIR__.'/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="my-tattoos.php"><span class="brand-badge">B</span><span>Healing Journal</span></a><a class="btn btn-secondary" href="api/healing-export.php">Export CSV</a></div></header>
<main class="container dashboard"><div class="dashboard-grid"><section class="panel"><h2>Upload today’s photo</h2><p class="meta">JPEG, PNG, or WebP up to 10 MB. Photos remain in private account storage.</p><?php if($message):?><div class="notice"><?=e($message)?></div><?php endif;?><?php if($error):?><div class="notice error-notice"><?=e($error)?></div><?php endif;?><form class="form-grid" method="post" enctype="multipart/form-data"><input type="hidden" name="_csrf" value="<?=e(bt_csrf_token())?>"><input type="hidden" name="action" value="upload"><select class="input" name="tattoo_id" required><option value="">Choose tattoo</option><?php foreach($tattoos as $tattoo):?><option value="<?=(int)$tattoo['id']?>" <?=((int)$selectedId===(int)$tattoo['id'])?'selected':''?>><?=e($tattoo['name'])?> — day <?=(int)$tattoo['healing_day']?></option><?php endforeach;?></select><input class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required><textarea class="input" name="notes" maxlength="2000" placeholder="How does it feel today?"></textarea><button class="btn btn-primary" type="submit">Save healing entry</button></form><?php if(!$tattoos):?><p><a href="add-tattoo.php">Add a tattoo before starting a journal.</a></p><?php endif;?></section>
<aside class="panel"><h2>Journal history</h2><div class="plan"><?php foreach($entries as $entry):?><article class="task"><a href="api/healing-photo.php?id=<?=(int)$entry['id']?>" target="_blank"><strong><?=e($entry['tattoo_name']?:'Tattoo')?></strong><br><small class="meta"><?=e(date('M j, Y',strtotime($entry['created_at'])))?><?= $entry['notes']?' • '.e($entry['notes']):''?></small></a><form method="post" onsubmit="return confirm('Delete this private healing entry?')"><input type="hidden" name="_csrf" value="<?=e(bt_csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="entry_id" value="<?=(int)$entry['id']?>"><button class="btn btn-secondary" type="submit">Delete</button></form></article><?php endforeach;?><?php if(!$entries):?><p class="meta">Your private photo timeline will appear here.</p><?php endif;?></div></aside></div></main></div>
<?php require __DIR__.'/includes/footer.php'; ?>

