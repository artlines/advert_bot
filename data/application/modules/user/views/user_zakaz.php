<? foreach($zakazes as $key => $zakaz):?>
<table width="100%" cellspacing="0" cellpadding="3" border="0" class="user_zakaz_table">
  <tbody>
  <tr>
    <td width="50%" align="left" style="font-size:14px; padding-top:10px;">
      <b>Заказ №<?=$zakaz->id;?> от <?=format_date($zakaz->date);?>. </b>
    </td>
    <td width="50%" align="right" style="font-size:14px; padding-top:10px;">
      Сумма: <?=round($zakaz->summa, 2);?> руб.
    </td>
  </tr>
  <tr>
    <td align="left" style="border-top: 1px solid #000000; padding-top: 10px;">
      <b>Статус:&nbsp;&nbsp;&nbsp;&nbsp;</b><?=$CI->tovar_model->getStatus($zakaz->status);?></td>
    <td align="right" rowspan="3" id="ch_zakaz<?=$zakaz->id;?>">
      <? if (in_array($zakaz->status, array(ZAKAZ_STATUS_WAIT_PAY, ZAKAZ_STATUS_WAIT_PROCESS))):?>
      <a href="javascript:view_zakaz(<?=$zakaz->id;?>);">Посмотреть заказ</a><br />
      <a href="javascript:cancel_zakaz(<?=$zakaz->id;?>);">Аннулировать заказ</a>
      <? else:?>
      <a href="javascript:view_zakaz(<?=$zakaz->id;?>);">Посмотреть заказ</a><br />
      <? endif;?>
    </td>
  </tr>
  <tr>
    <td align="left">
      <b>Доставка:&nbsp;&nbsp;&nbsp;&nbsp;</b><?=$CI->tovar_model->typeMoveGet($zakaz->type_move_id);?>
    </td>
  </tr>
  <tr>
    <!--td align="left">
      <b>Оплата:&nbsp;&nbsp;&nbsp;&nbsp;</b><?=$CI->tovar_model->typePayGet($zakaz->type_pay_id);?>
    </td-->
  </tr>
  </tbody>
</table>
<? endforeach;?>
<script>
function cancel_zakaz(id) {
  if (id<=0)
    return false;
  if (confirm("Вы действительно хотите отменить заказ?")) {
    $("#ch_zakaz"+id).html(loader_small);
    $.post("/user/zakaz/cancel/"+id, {}, function() {
      reloadTabs();
    });
  }
}

function change_zakaz(id) {
  modal('/user/zakaz/change/'+id, 400, function() {
    reloadTabs();
  });
}

function view_zakaz(id) {
  modal('/user/zakaz/view/'+id, 400, function() {
    reloadTabs();
  });
}
</script>
