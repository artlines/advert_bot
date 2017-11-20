<h1 style="font-size: 14px;">Информация о пользователе #<?=$user['id'];?></h1>
<table width="90%" id="table_list" cellspacing="1px" style="margin: 10px;">
  <tr>
    <td align="right" style="padding:5px;">Логин</td>
    <td align="left" style="padding:5px;"><?=$user['username'];?></td>
  </tr>
<? foreach($user['values'] as $v => $k):?>
  <tr>
    <td align="right" style="padding:5px;"><?=$k['name'];?></td>
    <td align="left" style="padding:5px;"><?=$k['value'];?></td>
  </tr>
<? endforeach;?>
</table>