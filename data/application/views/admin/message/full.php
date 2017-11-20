<b>Вопрос пользователя:</b><br /><br />
<?=nl2br($question);?>
<br /><br />
<div id="new_q">
<b>Ответ администратора:</b><br /><br />
<?=in_textarea('text_new_q', $answer, '100%', '300px');?>
<br /><br />
</div>
<?=in_button('ok_new_q', 'Отправить');?>
&nbsp;&nbsp;&nbsp;
<?=in_button('cancel_new_q', 'Скрыть');?>
<script>
$(function() {
  $("#cancel_new_q").click(function() {
    $.nyroModalRemove();
  });
  
  $("#ok_new_q").click(function() {
    var newtext = $("#text_new_q").val();
    if (newtext!='') {
      $("#new_q").html(Loader);
      $.post("/admin/users/messages/<?=$user_id;?>/<?=$id;?>", {newtext:newtext}, function() {
        location.href = '/admin/users';
      });
    }
    else
      alert("Пожалуйста, введите сообщение ненулевой длины!");
  });
});
</script>