<b style="color:#FF0000;"><?=$error_message;?></b>

<form action="/admin/cmodules/<?=$this->module;?>/photo/<?=$this->id;?>/add" method="post" class="nyroModal" enctype="multipart/form-data">

  Файл<br />
  <?=in_file('filename');?><br /><br />
  
  <?=in_hidden('id', $id);?>
  
  <?=in_submit('ok', 'Закачать');?>
  
</form>
