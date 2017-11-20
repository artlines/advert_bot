<br />
<form action="/user/register/submit/<?=$t_kl;?>" method="post" id="register_user_form">

  <b>E-mail *</b><br />
  <?=in_text('username', $_SESSION['register_vars']['username'], '400px');?><br /><br />
  
  <b>Пароль</b><br />
  <?=in_password('password', '', '400px');?><br /><br />
  
  <b>Повтор пароля</b><br />
  <?=in_password('password2', '', '400px');?><br /><br />
  
  <hr />
  
  <table width="100%">
    <tr>
      <? foreach($vars as $v => $k):?>
        <td>
        <?=$k['name'];?><br />
        <? $k['id']==USER_V_PHONE ? $class = 'phone_number' : $class = '';  ?>
        <?=in_text('register['.$k['id'].']', $_SESSION['register_vars'][$k['id']], '300px', $class);?><br /><br />
        </td>
        <? if(($v+1)%2==0):?>
        </tr>
        <tr>
        <? endif;?>
      <? endforeach;?>
    </tr>
  </table>
  
  <hr />
  
  Защита от авторегистрации!
  Ответьте на простой вопрос:<br />
  <i><?=$question;?></i><br />
  <?=in_text('answer', '', '300px');?><br />
  
  <?=in_ui_button('submit', 'Зарегистрировать', array('align' => 'center'));?><br />
</form>

<script>
$(function() {
  $.mask.definitions['~']='[+-]';
  $(".phone_number").mask("+7 (999) 999-99-99");
  
  $("#submit").click(function() {
    $("#register_user_form").submit();
  });
});
</script>