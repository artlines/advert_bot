<div id="small_left">
  <h4>Заказы</h4>
  
  По статусу:<br />
  <?=in_select('status', $status, array('value' => $params['status'], 'width' => '230px'));?><br />
  
  По типу оплаты:<br />
  <?=in_select('type_pay_id', $type_pay, array('value' => $params['type_pay_id'], 'width' => '230px'));?> <br />
  
  По типу доставки:<br />
  <?=in_select('type_move_id', $type_move, array('value' => $params['type_move_id'], 'width' => '230px'));?><br />
  
  По дате заказа (с ... по ...):&nbsp;&nbsp;<a href="javascript:dateClear();">Сбросить</a>
  <div class="form-inline">
    <?=in_text('date1', '', '100px');?>
    <?=in_text('date2', '', '100px');?>
  </div>
  
  <?=in_bs_button('detail', 'Показать', array('align' => 'left'));?>
</div>

<div id="big_right"></div>

<script>
$(function(){

  $("#detail").click(function() {
    zakaz_detail();
  });
  
  $("#date1").datepicker();
  $('#date1').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $("#date2").datepicker();
  $('#date2').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $("#date1").val('<?=$params['date1'];?>');
  $("#date2").val('<?=$params['date2'];?>');
});

function zakaz_detail() {
  params = $("#small_left").getFormData();
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/zakaz/detail', params);
}

function dateClear() {
  $("#date1").val('');
  $("#date2").val('');
}
</script>
