<div id="small_left">

  <h4>Пользователи</h4>
  
  <?=in_ui_button('subscribe', 'Рассылка');?>
  <br />
  <br />  
  <table width="100%" cellpadding=0 cellspacing=3 border=0>
  <? foreach($users as $v => $k):?>
    <tr>
      <td width="10%" valign="center"><img src="/images/link_arrow2.gif"></td>
      <td width="60%">
        <a href="javascript:user_full_info(<?=$k['id'];?>);" title="<?=$k['username'];?>"><?=$k['username'];?></a></td>
      <td width="10%" onclick="user_status(<?=$k['id'];?>);" style="cursor:pointer;" id="user_status<?=$k['id'];?>">
        <? if($k['active']==1):?>
          <img src="/images/on.gif" border=0 title="Включен" />
        <? else:?>
          <img src="/images/off.gif" border=0 title="Выключен" />
        <? endif;?>
      </td>
      <td width="10%"><a href="javascript:edit_user(<?=$k['id'];?>);">
        <img src="/images/edit2.jpg" border=0 title="Редактировать" /></a></td>
      <td width="10%"><a href="javascript:del_user(<?=$k['id'];?>);">
        <img src="/images/delete.jpg" border=0 title="Удалить" /></a></td>
    </tr>
    <tr><td colspan="5" style="height:7px;"> </td></tr>
  <? endforeach;?>
  </table>
  
  <br />
    <?=in_ui_button('add_btn', 'Добавить');?>
  <br />
  <br />
  
  <!--h4>Новые вопросы</h4>
  <? if (!empty($messages)):?>
    <ul>
    <? foreach($messages as $v => $k):?>
      <li><?=$k['tm'];?><br />
      <a href="/admin/users/messages/<?=$k['user_id'];?>/<?=$k['id'];?>" class="nyroModal">
        <?=mb_substr($k['question'], 0, 50);?> ...
      </a></li>
    <? endforeach;?>
    </ul>
  <? else:?>
    Новых вопросов нет
  <? endif;?>
  -->
</div>
  
<div id="big_right"></div>
  
<script>
function user_full_info(id) {
  modal('/admin/user_full_info/'+id);
}
      
function edit_user(id) {
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/users/detail/'+id);
}

function user_status(id) {
  if ( confirm("Сменить статус?") ) {
    $("#user_status"+id).html(loader_mini);
    $("#user_status"+id).load("/admin/users/ch_status/"+id);
  }
}

function del_user(id) {
  if (confirm("Вы действительно хотите удалить пользователя?")) {
    $("#small_left").html(Loader);
    $.post("/admin/users/del/"+id, {}, function(data) {
      if (data) {
        alert(data);
      }
      else {
        alert("Успешно удален!");
      }
      location.href = '/admin/users/';
    });
  }
}

  
$(function(){
  var user_id = '<?=$user_id;?>';
  if (user_id>0) {
    edit_user(user_id);
  }
    
  $("#add_btn").click(function() {
    modal('/admin/users/add/'+user_id);
  });
  
  $("#subscribe").click(function() {
    modal('/admin/users/subscribe/');
  });
  
  $("#export_btn").click(function() {
    $("#big_right").html(Loader);
    location.href = '/admin/users/export/';
    $("#big_right").html('');
  });
  
});
</script>
