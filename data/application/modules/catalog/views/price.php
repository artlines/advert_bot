<table id="cart_table"  cellspacing="1" cellpadding="3" border="1"  width="100%">
  <tr>
    <th>Nпп</th>
    <th>Фото</th>
    <th>Артикул</th>
    <th>Наименование</th>
    <th>Производитель</th>
    <th>Цена</th>
  </tr>
<? foreach ($catalog['search'] as $key => $item):?>
  <tr>
    <td><?=$key+1;?></td>
    <td>
      <? if($item->photo_main):?>
        <img src="<?=$item->photo_main;?>" alt="Фото" border="0" style="max-height:40px; width:40px;" title="<?=$item->name;?>" />
      <? else:?>
        &nbsp;
      <? endif;?>
    </td>
    <td><a href="/catalog/tovarFull/<?=$item->id;?>"><?=$item->code;?></a></td>
    <td><a href="/catalog/tovarFull/<?=$item->id;?>"><?=$item->name;?></a></td>
    <td><?=$item->manufacturer;?></td>
    <td align="right" width="50px"><?=round($item->price);?>р.</td>
  </tr>
<? endforeach;?>
</table>