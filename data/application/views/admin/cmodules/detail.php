<h4>Редактирование "<?=$item->name;?>"</h4>
<hr />

<div id="paramsForm">
  <div class="item">
    <? if ($item->image <> ''):?>
      <a href="javascript:chPic();">
        <img src="<?=$item->image;?>" style="height:40px; max-width:200px;" title="Заменить картинку" />
      </a>
    <? else:?>
      <?=in_ui_button('ch_pic', 'Закачать картинку', array('align' => 'left', 'icon' => 'plus'));?>
    <? endif;?>
  </div>
  <div class="item">
    Название<br />
    <?=in_text('name', $item->name);?>
  </div>
  <? foreach ((array)$dopFields[$this->module] as $key => $field):?>
  <div class="item">
    <?=$field['name'];?><br />
    <? if (is_array($field['fdata'])):?>
      <?=$field['format']($key, array_merge($field['fdata'], array('value' => $item->{$key})));?>
    <? else:?>
      <?=$field['format']($key, $item->{$key});?>
    <? endif;?>
  </div>
  <? endforeach;?>
  <div class="item">
    Приоритет<br />
    <?=in_text('priority', $item->priority);?>
  </div>
  <div class="item">
    На главной<br />
    <?=in_check('main_page', $item->main_page);?>
  </div>
  <div class="item">
    Активен<br />
    <?=in_check('active', $item->active);?>
  </div>
  <hr class="clear" />
  <?=in_ui_button('save', 'Сохранить', array('align' => 'left', 'icon' => 'note'));?>
  <?=in_hidden('pretext', '');?>
  <?=in_hidden('text', '');?>
</div>

<hr class="clear" />
<br /><br />

Кратко<br />
<?=$editor1;?> <br /><br />

Полный текст<br />
<?=$editor2;?> <br /><br />

<script>
var item_id = "<?=$item->id;?>";

$(function() {
        
  $("#save").click(function() {
    $("#pretext").val(CKEDITOR.instances['e_pretext'].getData());
    $("#text").val(CKEDITOR.instances['e_text'].getData());
    $("#paramsForm").post_ajax_form("/admin/cmodules/<?=$this->module;?>/detail/" + item_id, function() {
      alert("Успешно сохранено!");
      edit(item_id);
    });
  });
  
  $("#ch_pic").click(function() {
    chPic();
  });
 
});

/**
 * замена картинки
 */
function chPic() {
  modal("/admin/cmodules/<?=$this->module;?>/photo/" + item_id, 100, function() {
    edit('<?=$item->id;?>');
  });
}
</script>