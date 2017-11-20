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
Добрый день!<br /><br />
Спасибо, что сделали заказ на сайте <?=NAME;?>. <br /><br />
<? if ($_SESSION['new_user']):?>
  Ваши учетные данные для входа на сайт:<br />
  Логин: <?=$_SESSION['username'];?><br />
  Пароль: <?=$_SESSION['password'];?><br />
  <br /><br />
<? endif;?>
Оплата: <?=$zakaz->type_pay;?><br />
Доставка: <?=$zakaz->type_move;?><br /><br />

<table width="700" border="1" cellpadding="5">
  <tr>
    <th>Nпп</th>
    <th>Артикул</th>
    <th>Название</th>
    <th>Цвет</th>
    <th>Размер</th>
    <th>К-во</th>
    <th>Цена</th>
    <th>Сумма</th>
  </tr>
  <? $i = 1;?>
  <? $itog_count = 0;?>
  <? $itog_summa = 0;?>
  <? foreach($zakaz->detail as $key => $item):?>
  <tr>
    <td><?=$i++;?></td>
    <td><?=$item->code;?>&nbsp;</td>
    <td><?=$item->name;?></td>
    <td><?=$item->color;?></td>
    <td><?=$item->size;?></td>
    <td align="center"><?=$item->cnt;?></td>
    <td align="right"><?=round($item->price, 2);?></td>
    <td align="right"><?=round($item->price * $item->cnt, 2);?></td>
  </tr>
  <? $itog_count += $item->cnt;?>
  <? $itog_summa += round($item->price * $item->cnt, 2);?>
  <? endforeach;?>
  <tr>
    <th colspan="5" align="left">Сумма заказа</th>
    <th><?=$itog_count;?></th>
    <th>&nbsp;</th>
    <th align="right"><?=$itog_summa;?></th>
  </tr>
  <tr>
    <th colspan="7" align="left">Стоимость доставки</th>
    <th align="right"><?=$zakaz->summa_move;?></th>
  </tr>
  <tr>
    <th colspan="7" align="left">Итого с доставкой</th>
    <th align="right"><?=($itog_summa + $zakaz->summa_move);?></th>
  </tr>
</table>
</div>
