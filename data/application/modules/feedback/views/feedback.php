<div align="left">

  <div align="left" style="width:400px;" id="feedback_form">
    Тема обращения<br />
    <?=in_table('subject', array(
      'table' => 'callback_subjects',
      'width' => '400px'
    ));?><br /><br />
  
    Ваше имя<br />
    <?=in_text('name', '', array('width' => '400px'));?><br /><br />

    Телефон<br />
    <?=in_phone('phone', '', array('width' => '400px'));?><br /><br />

    E-mail<br />
    <?=in_text('email', '', array('width' => '400px'));?><br /><br />

    Сообщение<br />
    <?=in_textarea('message', '', array('width' => '400px', 'height' => '150px'));?><br /><br />
    
    Защита от спама!
    Ответьте на простой вопрос:<?=$question;?><br />
    <?=in_text('answer', '', array('width' => '400px'));?><br /><br />
    
    <?=in_ui_button('submit_feedback', 'Отправить');?>
  </div>

</div>
<script>
$(function() {
  $("#submit_feedback").click(function() {
    $("#feedback_form").post_ajax_form("/<?=$url;?>/feedbackSend", function() {
      $("#feedback_form").html("Спасибо! <br />Ваше сообщение успешно отправлено! <br /> Постараемся ответить как можно скорее!");
      location.href = '#';
    });
  });
  
  $.mask.definitions['~']='[+-]';
  $(".phone_number").mask("+7 (999) 999-99-99");
  
});
</script>