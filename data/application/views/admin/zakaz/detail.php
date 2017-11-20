<div class="panel panel-default">
  <div class="panel-heading"><h3>Заказы</h3></div>
  <table class="table">
    <tr>
      <th>№ заказа</th>
      <th>userID</th>
      <th>Контакты</th>
      <th>Дата/время</th>
      <th>Статус</th>
      <th>Способ доставки</th>
      <!--th>Способ оплаты</th-->
      <th>Кол.</th>
      <th>Сумма товары</th>
      <th>Сумма доставка</th>
      <th>Сумма</th>
      <th>Действия</th>
    </tr>
  <? if (!empty($zakaz)):?>
  <? foreach($zakaz as $key => $item):?>
    <tr>
      <td align="center"><?=$item->id;?></td>
      <td align="center"><a href="javascript:user_full_info(<?=$item->user_id;?>);"><?=$item->user_id;?></td>
      <td><?=$item->name;?><br /><?=$item->address;?><br /><?=$item->phone;?></td>
      <td align="center"><?=$item->tm;?></td>
      <td align="center"><?=$CI->tovar_model->getStatus($item->status);?>          </td>
      <td align="center"><?=$CI->tovar_model->typeMoveGet($item->type_move_id);?>  </td>
      <!--td align="center"><?=$CI->tovar_model->typePayGet($item->type_pay_id);?>    </td-->
      <td align="center"><?=$item->count;?></td>
      <td align="center"><?=$item->summa_tovar;?></td>
      <td align="center"><?=$item->summa_move;?></td>
      <td align="center"><?=$item->summa;?></td>
      <td align="center">
        <a href="/admin/zakaz/print/<?=$item->id;?>" target="_blank"><img src="/images/base/print.png"  border="0" /></a>
        <a href="javascript:zakaz_full(<?=$item->id;?>);">           <img src="/images/base/text.gif"   border="0" /></a>
        <a href="javascript:zakaz_delete(<?=$item->id;?>);">         <img src="/images/base/delete.gif" border="0" /></a>
      </td>
    </tr>
  <? endforeach;?>
  <? endif;?>
  </table>
</div>

<script>
function zakaz_delete(id) {
  if(confirm("Вы действительно хотите удалить заказ?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/zakaz/delete/"+id, {}, function() {
      zakaz_detail();
    });
  }
}

function user_full_info(id) {
  modal('/admin/user_full_info/'+id, 100, function() {
    zakaz_detail();
  });
}

function zakaz_full(id) {
  modal('/admin/zakaz/full/'+id, 100, function() {
    zakaz_detail();
  });
}
</script>
