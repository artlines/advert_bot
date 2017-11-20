<div id="getPriceForm" align="center">
  <div class="form-group">
  <?=in_text('company', '', array(
    'placeholder' => 'Название компании',
    'width'       => '300px'
  ));?>
  </div>
  <div class="form-group">
  <?=in_text('fio', '', array(
    'placeholder' => 'ФИО',
    'width'       => '300px'
  ));?>
  </div>
  <div class="form-group">
  <?=in_phone('phone', '', array(
    'placeholder' => 'Телефон',
    'width'       => '300px'
  ));?>
  </div>
  <div class="form-group">
  <?=in_textarea('comment', '', array(
    'placeholder' => 'Примечание',
    'width'       => '300px'
  ));?>
  </div>
  <?=in_ui_button('sendPriceForm', 'Отправить запрос');?>
</div>
<script>
$(function() {
  $("#sendPriceForm").click(function () {
    $("#getPriceForm").post_ajax_form("/catalog/getPriceSendForm/" + <?=$tovar_id;?>, function() {
      modalUIClose();
      modalMessage("Запрос успешно отправлен!");
    });
  });
  $.mask.definitions['~']='[+-]';
  $(".phone_number").mask("+7 (999) 999-99-99");
})
</script>