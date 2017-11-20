<? $CI = &get_instance();?>
<form id="form">
  <h4>Информация о пользователе #<?=$user['id'];?></h4 >
  <hr />
  <!--b>Вопросы пользователя</b>
  <? if (!empty($messages)):?>
    <ul>
      <? foreach($messages as $v => $k):?>
        <li><?=$k['tm'];?><br />
          <a href="javascript: message_view(<?=$k['user_id'];?>, <?=$k['id'];?>);">
            <?=mb_substr($k['question'], 0, 50);?> ...
          </a>
        </li>
      <? endforeach;?>
    </ul>
  <? else:?>
    Вопросов нет
  <? endif;?>
  <hr />
  <br /-->

  Логин<br />
  <?=in_text('username', $user['username'], '400px');?><br /><br />

  Пароль<br />
  <?=in_text('password', $user['password'], '400px');?><br /><br />

  <? foreach($user['values'] as $v => $k):?>
    <?=$k['name'];?><br />
    <?=in_text('user_values['.$k['id'].']', $k['value'], '400px');?><br /><br />
  <? endforeach;?>

  <br />

  <?=in_button('save_user', 'Сохранить');?>

  <br /><br /><br /><br />
</form>
<script>

function message_view(user_id, id) {
  modal('/admin/users/messages/'+user_id+'/'+id);
}

$(function(){
  
  $("#save_user").click(function(){
    $("#form").post_ajax_form("/admin/users/submit/<?=$user['id'];?>", function() {
      edit_user(<?=$user['id'];?>);
    });
  });
  
});
</script>
