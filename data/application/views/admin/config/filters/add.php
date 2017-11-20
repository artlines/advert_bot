<b style="color:#FF0000;"><?=$error_message;?></b>

<form action="/admin/config/filters/<?=$action;?>/<?=$id;?>" method="post" class="nyroModal">
  <div>
    Наименование<br />
    <?=in_text('name');?>
  </div>
  <div class="row-20"></div>

  <?=in_bs_button('save-button', 'Сохранить', ['type' => 'submit']);?>
  
</form>