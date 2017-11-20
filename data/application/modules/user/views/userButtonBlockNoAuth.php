<a class="none color_683f8c dotted_underline" href="#" id="login_link">Вход с паролем</a>
<div id="auth_window">
  <div class="top"></div>
  <div class="center">
    <div class="content">
      <div>Пожалуйста укажите ваш логин и пароль.</div>
      <div class="title">Имя пользователя:</div>
      <div class="input">
        <div class="input_left"></div>
        <div class="input_center"><input type="text" name="username" class="username" /></div>
        <div class="input_right"></div>
      </div>
      <div class="title">Пароль:</div>
      <div class="input">
        <div class="input_left"></div>
        <div class="input_center"><input type="password" name="password" class="password" /></div>
        <div class="input_right"></div>
      </div>
      <a href="javascript:login();"><img src="/images/main/login_button.png" /></a>
      <div class="recovery">
        <a href="/user/recovery" class="color_683f8c underline">Забыли пароль?</a><br />
        <a href="/user/register" class="color_683f8c underline">Регистрация</a>
      </div>
      <div id="login_loader"></div>
      <hr class="clear" />
    </div>
  </div>
  <div class="bottom"></div>
</div>
<script>
$(function() {
  var showed = 0;
  $("#login_link").click(function() {
    if (showed == 0) {
      showLoginWindow();
      showed = 1;
    }
    else {
      $("#auth_window").animate({opacity: "hide"});
      showed = 0;
    }
  });
});

function showLoginWindow() {
  $("#auth_window").animate({opacity: "show"});
  $("#auth_window").find(".username").focus();
  $('#dialog_window').dialog('close');
  scrollTopPage();
}

function login() {
  uobj = $("#auth_window").find(".username");
  pobj = $("#auth_window").find(".password");
  username = trim(uobj.val());
  password = trim(pobj.val());
  if (username == '') {
    alert("Введите логин");
    uobj.focus();
    return;
  }
  if (password == '') {
    alert("Введите пароль");
    pobj.focus();
    return;
  }
  $.post_ajax_json("/user/ajaxLogin", {username:username, password:password}, function() {
    $("#auth_block").load("/user/userButtonBlock");
  });
}
</script>