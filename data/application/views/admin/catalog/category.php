<font color="RED"><?=$error_message;?></font>
<form method="post" action="/admin/catalog/category/<?=$id;?>/set" name="category" class="nyroModal" id="form" style="margin:10px;">
  <b>Изменить состав категорий</b><br />
  <?=$tree;?>
  <br />
  <?=in_bs_button('saveNewTovar', 'Сохранить', array('type' => 'submit', 'align' => 'left'));?>
</form>