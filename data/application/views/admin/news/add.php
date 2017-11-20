<div id="form">
<form class="nyroModal" method="post" action="/admin/news_add/add">
  Дата<br />
  <?=in_text('date', date("Y-m-d"));?><br /><br />
  Заголовок<br />
  <?=in_text('title');?><br /><br />
  <input type="submit" name="submit" value="Сохранить" class="button">
  <input type="button" id="cancel" value="Отмена" class="button">
</form>
</div>
<script>
$(function(){
  $("#date").datepicker();
  $('#date').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $("#cancel").click(function(){
    modalClose();
  });
});
</script>
