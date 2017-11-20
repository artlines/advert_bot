<div id="password_recovery" class="standart_form">

  Введите новый пароль<br />
  <?=in_password('password', '', '400px');?><br /><br />
  
  Повтор пароля<br />
  <?=in_password('password2', '', '400px');?><br /><br />
  
  <?=in_ui_button('saveNewPass', 'Сохранить');?>
</div>

<script>
$(function() {
  
  $("#saveNewPass").click(function() {
    $("#password_recovery").post_ajax_form("/user/chpass/submit", function() {
      reloadTabs();
      alert("Пароль успешно изменен!");
    });
  });

});
</script>
