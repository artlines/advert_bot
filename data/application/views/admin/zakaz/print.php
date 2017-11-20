<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
  * {
    FONT-FAMILY: Arial;
    FONT-SIZE: 12px;
    COLOR: #000000;
  }
  h1 {
    FONT-SIZE: 14px;
    text-align: center;
  }
  table {
    border: 1px solid #000000;
    border-collapse: collapse;
    width: 100%;
  }
  </style>
</head>
<body>
  <b>Интернет-магазин «XXXXXXXX»</b><br />
  0000000000000000000000.<br />
  ОГРН 00000000000000000000000<br />
  ИНН 00000000000000000000000<br />
  <br />
  <br />
  <h1>Товарный чек</h1>
  
  <table cellpadding="1" border="1">
    <tr>
      <td align="center">№</td>
      <td align="center">Артикул</td>
      <td align="center">Наименование</td>
      <td align="center">Кол-во</td>
      <td align="center">Цена</td>
      <td align="center">Сумма</td>
    </tr>
    <? $i = 1;?>
    <? if (!empty($zakaz_detail)):?>
    <? foreach($zakaz_detail as $key => $item):?>
    <tr>
      <td><?=$i++;?></td>
      <td><?=$item->code;?></td>
      <td><?=$item->name;?></td>
      <td><?=$item->cnt;?></td>
      <td><?=round($item->price, 2);?></td>
      <td><?=round($item->price * $item->cnt, 2);?></td>
    </tr>
    <? endforeach;?>
    <? endif;?>
      <td colspan="4"></td>
      <td>Итого:</td>
      <td><?=round($zakaz->summa_tovar, 2);?></td>
    </tr>
  </table>
  <br /><br />
  На общую сумму:<?=round($zakaz->summa_tovar, 2);?>
  <br /><br />
  Выдал ________________

</body>
</html>