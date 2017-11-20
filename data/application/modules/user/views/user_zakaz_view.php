<h2>Информация о заказе #<?=$id;?></h2>
<table cellspacing="1" cellpadding="3" border="1" width="100%" id="cart_table">
  <tr>
    <th>Nпп</th>
    <th>Артикул</th>
    <th>Название</th>
    <th>К-во</th>
    <th>Цена</th>
    <th>Сумма</th>
    <th>Прим.</th>
  </tr>
<? $i = 1;?>
<? foreach($detail as $key => $item):?>
  <tr>
    <td align="center" width="5%"><?=$i++;?></td>
    <td align="center" width="15%"><?=$item->code;?></td>
    <td align="left"  width="35%"><?=$item->name;?>; <?=$item->manufacturer;?></td>
    <td align="right" width="5%"><?=$item->cnt;?></td>
    <td align="right" width="5%"><?=round($item->price, 2);?></td>
    <td align="right"><?=round($item->price*$item->cnt, 2);?></td>
    <td align="left" width="25%"><?=$item->comment;?></td>
  </tr>
<? endforeach;?>
  <tr>
    <th align="right" colspan="5">
      Сумма заказа:
    </th>
    <th align="right" id="zakaz_summa_itog"><?=round($info->summa_tovar, 2);?></th>
    <th>&nbsp;</th>
  </tr>
  <? if ($info->summa_move > 0):?>
  <tr>
    <th align="right" colspan="5">
      Стоимость доставки:
    </th>
    <th align="right" id="zakaz_summa_itog"><?=round($info->summa_move, 2);?></th>
    <th>&nbsp;</th>
  </tr>
  <tr>
    <th align="right" colspan="5">
      Итого:
    </th>
    <th align="right" id="zakaz_summa_itog"><?=round($info->summa, 2);?></th>
    <th>&nbsp;</th>
  </tr>
  <? endif;?>
</table>