<table width="100%" cellspacing="1" cellpadding="3" border="1" id="cart_table">
  <tr>
    <th align="center">Дата</th>
    <th align="center">Фото</th>
    <th align="center">Наименование</th>
    <th align="center">Цена</th>
  </tr>
<? foreach ($tovars as $item):?>
  <? $tovar = $this->tovar_model->Get($item->tovar_id);?>
  <tr>
    <td align="center"><?=format_date($item->tm);?></td>
    <td align="center"><img src="<?=$tovar->photo_main->thumb_file;?>" width="60px" /></td>
    <td><a href="/catalog/tovarFull/<?=$tovar->id;?>"><?=$tovar->name;?></a></td>
    <td align="right"><?=$tovar->price;?> руб.</td>
  </tr>
<? endforeach;?>
</table>