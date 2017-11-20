<font color="RED"><?=$error_message;?></font>
<form method="post" action="/admin/banners/places/<?=$id;?>/set" id="banners_places_edit_form" class="nyroModal" id="form" style="margin:10px;">
  <b>Места показов:</b><br /><br />
  
  <a href="javascript:setAll();">Все</a> | <a href="javascript:unsetAll();">Ни одного</a><br />
  
  <ul style="padding-left:15px;">
  <? foreach ($places as $key => $item): ?>
    <? $checked = ($banner_places[$item->id] ? 1 : 0);?>
    <li><?=in_check("place_id[{$item->id}]", $checked, $item->id);?>&nbsp;<?=$item->name;?></li>
  <? endforeach;?>
  </ul>
  <br />
  <?=in_bs_button('submit', 'Сохранить', array('type' => 'submit'));?>
</form>

<script>
function setAll() {
  $("#banners_places_edit_form").find("input:checkbox").prop('checked', true);
}

function unsetAll() {
  $("#banners_places_edit_form").find("input:checkbox").prop('checked', false);
}
</script>