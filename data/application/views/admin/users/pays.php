<table width="90%" id="table_list" cellspacing="1px" style="margin: 10px;">
  <tr>
    <th>Сумма</th>
    <th>Дата</th>
    <th>Описание</th>
    <th>Способ платежа</th>
    <th>Удалить</th>
  </tr>
<? if (!empty($pays)):?>
<? foreach($pays as $v => $k):?>
  <tr>
    <td align="right"><?=number_format($k['summa'], 2, '.', '\'');?></td>
    <td align="center"><?=$k['data'];?></td>
    <td><?=$k['description'];?></td>
    <td align="center"><?=$k['way_name'];?></td>
    <td align="center"><?=button_del("javascript:del_pay({$k['id']})")?></td>
  </tr>
<? endforeach;?>
<? endif;?>
</table>