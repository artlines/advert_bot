<form id="form" method="post" action="/admin/zakaz/full/<?=$zakaz->id;?>/save" class="nyroModal">
<div class="panel panel-default">
  <div class="panel-heading"><h4>Информация о заказе #<?=$zakaz->id;?> на сумму <?=$zakaz->summa;?> руб.</h4></div>
  <table class="table">
    <tr>
      <th>Nпп</th>
      <th>Код</th>
      <th>Артикул</th>
      <th>Название</th>
      <th>К-во</th>
      <th>Цена</th>
      <th>Сумма</th>
      <th>Прим.</th>
      <th>&nbsp;</th>
    </tr>
  <? $i = 1;?>
  <? foreach((array)$zakaz_detail as $key => $item):?>
    <? $ostatok = unserialize($item->ostatok);?>
    <tr>
      <td align="center" width="5%"><?=$i++;?></td>
      <td><?=$item->code_1c;?></td>
      <td><?=$item->code;?></td>
      <td>
        <?=$item->name;?> (<?=$item->manufacturer;?>)
      </td>
      <td align="right"><?=in_text('tovar['.$item->id.']', $item->cnt, '40px');?></td>
      <td align="right"><?=round($item->price, 2);?></td>
      <td align="right"><?=round($item->price * $item->cnt, 2);?></td>
      <td align="left" width="25%"><?=in_text('comment['.$item->id.']', unsafe($item->comment), '150px', 'zakaz_comment');?></td>
      <td align="center"><?=button_del("/admin/zakaz/full/{$zakaz->id}/del_item/{$item->id}", 'nyroModal');?></td>
    </tr>
  <? endforeach;?>
    <tr>
      <th colspan="4">&nbsp;</th>
      <th align="right"><?=$zakaz->count;?></th>
      <th colspan="1">&nbsp;</th>
      <th align="right"><?=$zakaz->summa_tovar;?></th>
      <th colspan="2">&nbsp;</th>
    </tr>
  </table>
  <?=in_bs_button('save_pos', 'Сохранить изменения', array('type' => 'submit'));?>
</div>
</form>

<div>
  <?=$zakaz->comment;?>
</div>

<hr class="clear" />
<hr />

<div class="block_50">

  <form id="form" method="post" action="/admin/zakaz/full/<?=$zakaz->id;?>/add_to_zakaz" class="nyroModal">
    <h2 style="font-size: 12px; color: #000000;">Добавить товар в заказ</h2>

    Поиск по коду<br />
    <?=in_hidden('new_tovar_id', '');?>
    <?=in_text('new_tovar', '', '300px');?><br />

    Количество<br />
    <?=in_text('new_tovar_cnt', '', '300px');?><br />

    <?=in_bs_button('add_to_zakaz', 'Добавить товар', array('type' => 'submit', 'align' => 'left'));?>
  </form>

</div>

<div class="block_50">

  <form id="form" method="post" action="/admin/zakaz/full/<?=$zakaz->id;?>/ch_common" class="nyroModal">
    <h2 style="font-size: 12px; color: #000000;">Изменить параметры</h2>
    
    Стоимость доставки<br />
    <?=in_text('summa_move', $zakaz->summa_move, array('width' => '300px'));?>
    <br />
    
    Статус<br />
    <?=in_table('status', array('table' => 'zakaz_status', 'value' => $zakaz->status, 'width' => '300px'));?>
    <br />

    Тип оплаты<br />
    <?=in_table('type_pay_id', array('table' => 'shop_type_pay', 'value' => $zakaz->type_pay_id, 'width' => '300px'));?>
    <br />

    Тип доставки<br />
    <?=in_table('type_move_id', array('table' => 'shop_type_move', 'value' => $zakaz->type_move_id, 'width' => '300px'));?>
    <br />

    <?=in_bs_button('ch_status', 'Сменить статус', array('type' => 'submit', 'align' => 'left'));?>
  </form>

</div>

<script>
$(function() {
  $("#new_tovar").autocomplete('/admin/zakaz/auto', {
    autoFill: false,
    cacheLength: 1
  }).result(function(event,item) {
    $("#new_tovar_id").val(item[1]);
  });
});

</script>
