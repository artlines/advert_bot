<font color="RED"><?=$error_message;?></font>
<h3>Основные параметры</h3>
<form method="post" id="form-new-product" action="/admin/catalog/newTovar" class="nyroModal" style="margin:10px; width: 800px;">

  <div class="row">
    <div class="col-md-3">
      <div class="row">Название</div>
      <div class="row"><?=in_text('name', $name, ['width' => '100%']);?></div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-3">
      <div class="row">Производитель</div>
      <div class="row"><?=in_table('manufacturer_id', array(
          'table' => 'shop_tovar_manufacturer', 
          'name'  => 'manufacturer_name',
          'value' => $manufacturer_id
        ));?>
      </div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-3">
      <div class="row">Артикул</div>
      <div class="row"><?=in_text('code', $code);?></div>
    </div>
    <div class="col-md-1"></div>
  </div>
  
  <div class="row-20"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">Описание</div>
      <div class="row"><?=in_textarea('description', $description, '100%');?></div>
    </div>
  </div>
  
  <?=in_bs_button('saveNewTovar', 'Добавить', array('type' => 'submit', 'align' => 'right'));?>
</form>
