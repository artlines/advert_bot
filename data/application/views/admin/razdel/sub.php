<? foreach($razdel as $key => $item):?>
  <tr id="sub_<?=$item->id;?>">
    <td width="10%" valign="center">&nbsp;</td>
    <td width="70%"><?=$item->name;?></td>
    <td width="10%"><a href="javascript:edit_razdel(<?=$item->id;?>);">
      <span class="glyphicon glyphicon-edit"></span>
      </a>
    </td>
    <td width="10%">
      <a href="javascript:del_razdel(<?=$item->id;?>);">
        <span class="glyphicon glyphicon-remove"></span>
      </a>
    </td>
  </tr>
<? endforeach;?>