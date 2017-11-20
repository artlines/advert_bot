<b style="color:#FF0000;"><?=$error_message;?></b>
<form action="/admin/catalog/photo/<?=$id;?>/add" method="post" class="nyroModal" enctype="multipart/form-data">

  <div>Вариант</div>
  <div>
    <?=in_table('variant', [
      'table' => 'shop_product_variants',
      'id'    => 'name',
      'name'  => 'name',
      'where' => [
        'product_id' => $id
      ]
    ]);?>
  </div>
  <div class="row-20"></div>
  
  Файл<br />
  <?=in_file('photo');?>    <br />
  
  Основное фото<br />
  <?=in_check('is_main', 0, 1);?> <br />
  
  Комментарий<br />
  <?=in_text('comment');?>  <br />
  
  <?=in_hidden('id', $id);?>
  <?=in_bs_button('saveNewTovar', 'Добавить', array('type' => 'submit', 'align' => 'left'));?>
</form>
