<div id="form">
<form class="nyroModal" method="post" action="/admin/payment/add/<?=$id;?>">
Сумма<br />
<?=in_text('summa');?><br /><br />
Описание<br />
<?=in_text('description');?><br /><br />
<input type="submit" name="submit" value="Сохранить" class="button">
<input type="button" id="cancel" value="Отмена" class="button">
</form>
</div>
<script>
$(function() {
  <? if ($close>0):?>
    edit_user(<?=$id;?>);
    $.nyroModalRemove();
  <? endif;?>
  $("#cancel").click(function(){
    $.nyroModalRemove();
  });
});
</script>
