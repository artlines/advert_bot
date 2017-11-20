<?=in_textarea('text_new_q', '', array('width' => '100%', 'height' => '200px'));?>

<br /><br />

<div id="letter_button_block">
  <?=in_blue_button('ok_new_q', 'Отправить');?>
  &nbsp;&nbsp;&nbsp;
  <?=in_blue_button('cancel_new_q', 'Скрыть');?>
</div>

<script>
$(function() {
  $("#cancel_new_q").click(function() {
    $("#new_q").hide(1000);
    $("#add_question").show(1000);
  });
  
  $("#ok_new_q").click(function() {
    var newtext = $("#text_new_q").val();
    if (newtext!='') {
      $("#new_q").html(Loader);
      $.post("/user/letter/save_new", {newtext:newtext}, function() {
        location.href = '/user/letter';
      });
    }
    else
      alert("Пожалуйста, введите сообщение ненулевой длины!");
  });
});
</script>