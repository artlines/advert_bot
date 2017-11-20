<b style="color:#FF0000;"><?=$error_message;?></b>

<form action="/admin/config/manufacturer/<?=$action;?>/<?=$id;?>" method="post" class="nyroModal" enctype="multipart/form-data">
  <table width="100%" border="0">
    <tr>
      <td valign="top">
        <b>Название</b><br />
        <?=in_text('manufacturer_name', $values->manufacturer_name);?><br /><br />
      </td>
      <td rowspan="4" valign="top">
        <b>Картинка</b><br />
        <? if ($values->pic):?><img src="<?=$values->pic;?>" width="200px" /><br /><? else:?>&nbsp;<? endif;?>
        <br />
      </td>
    </tr>
    <tr>
      <td valign="top">
        <b>title</b><br />
        <?=in_text('title', $values->title);?><br /><br />
      </td>
    </tr>
    <tr>
      <td valign="top">
        <b>Карусель (только при загруженной картинке)</b><br />
        <?=in_check('main_page', $values->main_page, 1);?><br /><br />
      </td>
    </tr>
    <tr>
      <td valign="top">
        <b>Заменить картинку</b><br />
        <?=in_file('pic');?><br />
      </td>
    </tr>
  </table>
  
  <b>Описание</b><br />
  <?=in_textarea('description', $values->description, array('width' => '400px', 'height' => '150px'));?><br /><br />

  <?=in_submit('ok', 'Сохранить');?>
  
</form>
