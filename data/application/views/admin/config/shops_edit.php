<div>
  <div class="block_50">
    Город<br />
    <?=in_table('edit_city_id', array('table' => 'city', 'value' => $shop->city_id));?>
  </div>
  <div class="block_50">
    Адрес (без города)<br />
    <?=in_text('edit_addr', $shop->addr);?>
  </div>
  <hr class="clear" />
  <div class="block_50">
    Телефон<br />
    <?=in_text('edit_shop_tel', $shop->shop_tel);?>
  </div>
  <div class="block_50">
    Наименование<br />
    <?=in_text('edit_name', htmlspecialchars($shop->name));?>
  </div>
  <hr class="clear" />
</div>
<div>
  <?=in_bs_button('save_button', 'Сохранить', array('align' => 'left'));?>
</div>
<hr class="clear" />
<div class="row-20"></div>
<div align="left">
  Описание<br />
  <?=$text;?>

</div>
<br /><br />
<script>
$(function(){
  
  $("#save_button").click(function() {
    shop_edit_save();
  });
  
});

</script>