<div id="form">
<form class="nyroModal" method="post" action="/admin/users/add">
Имя пользователя<br />
<?=in_text('username');?><br /><br />
Пароль<br />
<?=in_text('password', genPass(8));?><br /><br />
Тип клиента<br />
<?=in_table('t_kl', array('table' => 'user_t_kl'));?><br /><br />
<input type="submit" name="submit" value="Сохранить" class="button">
<input type="button" id="cancel" value="Отмена" class="button">
</form>
</div>
<script>
$(function(){
  $("#cancel").click(function(){
    modalClose();
  });
  
  var close = '<?=$close?>';
  if (close>0) {
    location.href = '/admin/users';
  }
});
</script>
