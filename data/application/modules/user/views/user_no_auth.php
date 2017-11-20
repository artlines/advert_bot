<div style="width:300px;">
<form action="/user/login" method="post" id="user_login">

    E-mail<br />
    <?=in_text('username', '', '300px');?>
    <br /><br />
    
    Пароль<br />
    <?=in_password('password', '', '300px');?>
    
    <div class="password_recovery_link">
      <a href="/user/recovery">Забыли пароль</a>
    </div>
    
    <div align="center">
    <?=in_ui_button('user_submit', 'Войти', array('align' => 'center'));?>
    </div>

</form>
</div>
  
<script>
$(function() {
  $("#user_submit").click(function() {
    auth_me();
  });
  
  $("#username").keypress(function(e) {
    if(e.which==13) {
      auth_me();
    }
  });
  
  $("#password").keypress(function(e) {
    if(e.which==13) {
      auth_me();
    }
  });
});

function auth_me() {
  var username = $("#username").val();
  var password = $("#password").val();
  if (username!='' && password!='') {
    $("#user_login").submit();
  }
  else {
    alert("Пожалуйста, введите непустые логин и пароль!");
  }
}
</script>