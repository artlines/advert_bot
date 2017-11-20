<font color="RED"><?=$error_message;?></font>
<form method="post" action="/admin/banners/razdel/<?=$id;?>/set" id="banners_razdel_edit_form" class="nyroModal" id="form" style="margin:10px;">
  <b>Отображать в разделах:</b><br /><br />
  <a href="javascript:setAll();">Все</a> | <a href="javascript:unsetAll();">Ни одного</a><br />
  <?=$tree;?>
  <br />
  <?=in_bs_button('submit', 'Сохранить', array('type' => 'submit'));?>
</form>

<script>
function setAll() {
  $("#banners_razdel_edit_form").find("input:checkbox").prop('checked', true);
}

function unsetAll() {
  $("#banners_razdel_edit_form").find("input:checkbox").prop('checked', false);
}
</script>