<div class="row">
  <div class="col-md-1"></div>
  <div id="cart_contactInfo" align="left" class="col-md-8">

    <div class="type_move">
      Способ получения товара<br />
      <?=in_select('type_move_id', $typeMove, array('width'=>'400px'));?>
    </div>

    <div class="type_pay">
      Способ оплаты<br />
      <?=in_select('type_pay_id', $typePay, array('width'=>'400px'));?>
    </div>

    <div class="email">
      E-mail<br />
      <?=in_text('email', $userInfo['username'], '400px');?>
    </div>

    <div class="password" style="display:none;">
      Пароль<br />
      <?=in_password('password', '', '400px');?>
    </div>

    <div class="fio">
      ФИО<br />
      <?=in_text('name', $userInfo['values'][USER_V_FIO]['value'], '400px');?>
    </div>

    <div class="address">
      Адрес доставки<br />
      <?=in_text('address', trim($address), '400px');?>
    </div>

    <div class="phone">
      Телефон<br />
      <?=in_text('phone', $userInfo['values'][USER_V_PHONE]['value'], '400px', 'phone_number');?>
    </div>

    <div class="comment">
      Примечание к заказу<br />
      <?=in_text('comment', trim($comment), '400px');?>
    </div>

    <div class="row-20"></div>
    <?=in_bs_button('zakazSend', 'Отправить заказ', array('align' => 'left'));?>
  </div>
</div>

<script>
$(function() {

  $.mask.definitions['~']='[+-]';
  $(".phone_number").mask("+7 (999) 999-99-99");
  
  $("#type_move_id").change(function() {
    var type_move_id = $("#type_move_id").val();
    if (type_move_id == '<?=ZAKAZ_TYPE_MOVE_SELF;?>') {
      $("#cart_contactInfo").find(".address").hide();
      $("#cart_contactInfo").find(".type_pay").hide();
      $(".zakaz_cost_dop_info").hide();
    }
    else {
      $("#cart_contactInfo").find(".address").show();
      $("#cart_contactInfo").find(".type_pay").show();
      $(".zakaz_cost_dop_info").show();
    }
  });


  $("#zakazSend").click(function() {
    $("#zakazFullInfo").post_ajax_form("/catalog/cart/zakazSend", function(data) {
      location.href = '/catalog/cart/zakazSuccess';
    });
  });
  
  /*$("#email").keyup(function() {
    searchUser();
  });
  
  $("#email").blur(function() {
    searchUser();
  });*/
  
});

function searchUser() {
  $.post("/user/checkMail", {email:$("#email").val()}, function(data) {
    if (data.user_id > 0) {
      $("#cart_contactInfo").find(".password").show();
    }
    else {
      $("#cart_contactInfo").find(".password").hide();
    }
  }, "json");
}
</script>