<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
  <table border="1">
    <tr>
      <th>Наименование</th>
      <th>Артикул</th>
      <th>Цена</th>
      <th>Ссылка</th>
    </tr>
    <? foreach ($catalog['search'] as $key => $item):?>
    <tr>
      <td><?=htmlspecialchars($item->name, ENT_QUOTES);?></td>
      <td><?=$item->code;?></td>
      <td><?=round($item->price, 2);?>руб.</td>
      <td><a href="http://<?=SERVER;?>/catalog/tovarFull/<?=$item->id;?>">Смотреть</a></td>
    </tr>
    <? endforeach;?>
  </table>
</body>
</html>