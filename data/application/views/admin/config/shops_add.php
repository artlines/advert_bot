<b style="color:#FF0000;"><?=$error_message;?></b>

<form action="/admin/config/shops/<?=$action;?>/<?=$id;?>" method="post" class="nyroModal" id="save_shop_form">
  <div>
    Город<br />
    <?=in_table('city_id', array('table' => 'city', 'width' => '100%'));?>
  </div>
  <div class="row-20"></div>
  <div>
    Адрес (без города)<br />
    <?=in_text('addr', '', ['width' => '100%']);?>
  </div>

  <br /><br />
  <?=in_bs_button('save_shop', 'Сохранить');?>
  
</form>
<script>
$(function() {
  $("#save_shop").click(function() {
    $("#save_shop_form").submit();
  });
});
</script>