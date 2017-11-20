<? if($info_message):?>
  <div class="info_message"><?=$info_message;?></div>
<? endif;?>
<form action="/user/recovery/submit/" method="post" id="recovery_form" class="standart_form">

  <b>Введите Ваш E-mail</b><br />
  <?=in_text('username_recovery', $username, '400px');?>
  <br />
  
  <?=in_ui_button('recovery_button', 'Восстановить', array('align' => 'left'));?>
</form>

<script>
$(function() {

  $("#recovery_button").click(function() {
    username = $("#username_recovery").val();
    if (username=='') {
      alert('Введите непустой e-mail.');
      return false;
    }
    $('#recovery_form').submit();
  });
  
});
</script>