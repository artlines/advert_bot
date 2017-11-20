<div id="form">
<form class="nyroModal" method="post" action="/admin/razdel/add/<?=$type;?>">

  Родительская рубрика<br />
  <?=in_select('parent_id', $razdel, array('width' => '250px', 'is_empty' => true));?>
  <br />

  Название раздела<br />
  <?=in_text('name', $post['name'], array('width' => '250px'));?><br />

  URL<br />
  <?=in_text('url', $post['url'], array('width' => '250px'));?><br />

  <?=in_hidden('content_type', $type);?>

  <?=in_bs_button('cancel', 'Отмена', array('icon' => 'remove'));?>
  <?=in_bs_button('submit', 'Добавить', array('type' => 'submit', 'icon' => 'plus'));?>

</form>
</div>
<script>
$(function(){
  $("#cancel").click(function() {
    modalClose();
  });
});
</script>
