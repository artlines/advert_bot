<style>
table {
  border-collapse: collapse;
}
td, th {
  font-family: Verdana; 
  font-size:12px; 
  color:#000000;
  border:1px solid #777777;
}
th {
  background-color:#CCCCCC;
  border:1px solid #777777;
}
</style>
<div style="font-family: Verdana; font-size:12px; color:#000000;">
ФИО:      <?=$zakaz->name;?><br />
Телефон:  <?=$zakaz->phone;?><br />
Адрес:    <?=$zakaz->address;?><br />
E-mail:   <?=$username;?><br />
Оплата: <?=$zakaz->type_pay;?><br />
Доставка: <?=$zakaz->type_move;?><br />
Примечание: <?=$zakaz->comment;?><br /><br />

<table width="700" border="1" cellpadding="5">
  <tr>
    <th>Nпп</th>
    <th>Артикул</th>
    <th>Код</th>
    <th>Название</th>
    <th>К-во</th>
    <th>Цена</th>
    <th>Сумма</th>
    <th>Примечание</th>
  </tr>
  <? $i = 1;?>
  <? $itog_count = 0;?>
  <? $itog_summa = 0;?>
  <? foreach($zakaz->detail as $key => $item):?>
  <tr>
    <td><?=$i++;?></td>
    <td><?=$item->code;?>&nbsp;</td>
    <td><?=$item->code_1c;?></td>
    <td><?=$item->name;?></td>
    <td><?=$item->cnt;?></td>
    <td><?=round($item->price, 2);?></td>
    <td><?=round($item->price * $item->cnt, 2);?></td>
    <td><?=$item->comment;?>&nbsp;</td>
  </tr>
  <? $itog_count += $item->cnt;?>
  <? $itog_summa += round($item->price * $item->cnt, 2);?>
  <? endforeach;?>
  <tr>
    <th colspan="4" align="left">Сумма заказа</th>
    <th><?=$itog_count;?></th>
    <th>&nbsp;</th>
    <th align="right"><?=$itog_summa;?></th>
    <th>&nbsp;</th>
  </tr>
  <tr>
    <th colspan="6" align="left">Стоимость доставки</th>
    <th align="right"><?=$zakaz->summa_move;?></th>
    <th>&nbsp;</th>
  </tr>
  <tr>
    <th colspan="6" align="left">Итого с доставкой</th>
    <th align="right"><?=($itog_summa + $zakaz->summa_move);?></th>
    <th>&nbsp;</th>
  </tr>
</table>
</div>
