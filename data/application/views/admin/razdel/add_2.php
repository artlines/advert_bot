<div id="form">
<form class="nyroModal" method="post" action="/admin/razdel/add/<?=$type;?>">

  Название<br />
  <?=in_text('name', $post['name'], array('width' => '250px'));?><br />

  Метка<br />
  <?=in_text('url', $post['url'], array('width' => '250px'));?><br />

  <?=in_hidden('content_type', $type);?>

  <?=in_bs_button('submit', 'Добавить', array('type' => 'submit', 'align' => 'left', 'icon' => 'plus'));?>
  <?=in_bs_button('cancel', 'Отмена', array('align' => 'left', 'icon' => 'remove'));?>

</form>
</div>
<script>
$(function(){
  $("#cancel").click(function() {
    modalClose();
  });
});
</script>
