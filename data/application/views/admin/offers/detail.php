<h4>Редактирование Спецпредложения #<?=$offers->id;?></h4>
<hr />
<table width="100%" border="0" id="paramsForm" cellpadding="5" cellspacing="0">
  <tr>
    <td>
      Название<br />
      <?=in_text('name', $offers->name);?>
    </td>
    <td>
      Ссылка<br />
      <?=in_text('link', $offers->link);?>
    </td>
    <td>
      Приоритет<br />
      <?=in_text('priority', $offers->priority);?>
    </td>
    <td>
      На главной<br />
      <?=in_check('main_page', $offers->main_page);?>
    </td>
    <td>
      Активен<br />
      <?=in_check('active', $offers->active);?>
    </td>
  </tr>
  <tr>
    <td colspan="5">
      Текст акции<br />
      <?=in_textarea('text', $offers->text, array('width' => '100%', 'height' => '100px'));?>
    </td>
  </tr>
  <tr>
    <td colspan="3"><?=in_ui_button('save', 'Сохранить', array('align' => 'left', 'icon' => 'note'));?></td>
  </tr>
</table>

<div>
  <div class="block_50">
    <b>Изображение</b><hr />
      <div style="margin:5px;">
        <img src="<?=$offers->image;?>" style="width:300px;" />
      </div>
      <?=in_ui_button('photo_set_button', 'Заменить', array('align' => 'left', 'icon' => 'plus'));?>
      <?=in_ui_button('photo_del_button', 'Удалить', array('align' => 'left', 'icon' => 'minus'));?>
  </div>

</div>

<script>
var offer_id = "<?=$offers->id;?>";

$(function() {
        
  $("#save").click(function() {
    $("#paramsForm").post_ajax_form("/admin/offers/detail/"+offer_id, function() {
      alert("Успешно сохранено!");
      edit(offer_id);
    });
  });
  
  $("#photo_set_button").click(function () {
    modal('/admin/offers/photo/<?=$offers->id;?>/set/', 400, function() {
      edit(offer_id);
    });
  });
  
  $("#photo_del_button").click(function () {
    if ( !confirm("Вы действительно хотите удалить?") ) {
      return;
    }
    $("#all_photos").post_ajax_form('/admin/offers/photo/<?=$offers->id;?>/del/', function() {
      edit(offer_id);
    });
  });
  
});
</script>