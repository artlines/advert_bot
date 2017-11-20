<b style="color:#FF0000;"><?=$error_message;?></b>

<form action="/admin/banners/photoSlider/<?=$id;?>/add" method="post" class="nyroModal" enctype="multipart/form-data">

  Файл<br />
  <?=in_file('filename');?><br /><br />

  Ссылка<br />
  <?=in_text('link');?><br /><br />
  
  <?=in_hidden('id', $id);?>
  
  <?=in_submit('ok', 'Закачать');?>
  
</form>
