<h4>Редактирование баннера "<?=$banners->name;?>"</h4>
<hr />
<table width="100%" border="0" id="paramsForm" class="params">
  <tr>
    <td>
      Название<br />
      <?=in_text('name', $banners->name);?>
    </td>
    <td>
      Дата начала<br />
      <?=in_text('tm_start', $banners->tm_start, array('class' => 'dates'));?>
    </td>
    <td>
      Дата окончания<br />
      <?=in_text('tm_stop', $banners->tm_stop, array('class' => 'dates'));?>
    </td>
  </tr>
  <tr>
    <td>
      Ссылка<br />
      <?=in_text('link', $banners->link, ($banners->slider ? array('disabled' => true) : array()));?>
    </td>
    <td>
      Процент показов<br />
      <?=in_text('percent', $banners->percent, ($banners->slider ? array('disabled' => true) : array()));?>
    </td>
    <td>
      Активен<br />
      <?=in_check('active', $banners->active);?>
    </td>
  </tr>
  <tr>
    <td colspan="3">Слайдер для главной страницы<br /><?=in_check('slider', $banners->slider);?></td>
  </tr>
  <tr>
    <td colspan="3"><?=in_bs_button('save', 'Сохранить', array('align' => 'left', 'icon' => 'note'));?></td>
  </tr>
</table>

<div>
  <div class="block_50">
    <b>Разделы для отображения</b><hr />
    <a href="javascript:void(0);" id="razdel" style="text-decoration:none;">
    <? if (!empty($banners->razdel)):?>
      <? foreach($banners->razdel as $key => $item):?>
        <?=$item->name;?><br />
      <? endforeach;?>
    <? else:?>
      Не выбрано ни одного раздела
    <? endif;?>
    </a>  
  </div>
    
  <div class="block_50">
    <b>Места показов</b><hr />
    <a href="javascript:void(0);" id="places" style="text-decoration:none;">
    <? if (!empty($banners->places)):?>
      <? foreach($banners->places as $key => $item):?>
        <?=$item->name;?><br />
      <? endforeach;?>
    <? else:?>
      Не выбрано ни одного места
    <? endif;?>
    </a>  
  </div>

  <? if ($banners->slider):?>
    
    <div id="sliderFiles">
      <b>Изображения</b><hr />
      <? foreach ((array)$banners->sliderFile as $item):?>
      <div style="margin:5px; float:left;">
        <?=in_check("sliderFile[{$item->id}]", 0);?>
        <a href="<?=$item->link;?>" target="_blank"><img src="<?=$item->filename;?>" width="200px" /></a>
      </div>
      <? endforeach;?>
      <hr class="clear" />
      <?=in_bs_button('photo_slider_add_button', 'Добавить', array('align' => 'left', 'icon' => 'plus'));?>
      <?=in_bs_button('photo_slider_del_button', 'Удалить',  array('align' => 'left', 'icon' => 'minus'));?>
      <hr class="clear" />
    </div>
  
  <? else:?>
    
    <div class="block_50">
      <b>Изображение</b><hr />
      <div style="margin:5px;">
        <? if ($banners->filetype=='swf'):?>
          <a id="banner_container" class="flash-replaced" target="_blank" href="<?=$banners->link;?>">
            <script>
            $("#banner_container").flash({src: '<?=$banners->filename;?>', width: <?=$banners->width;?>, height: <?=$banners->height;?>});
            </script>
          </a>
        <? else:?>
          <img src="<?=$banners->filename;?>" width="<?=$banners->width;?>" />
        <? endif;?>
      </div>
      <?=in_bs_button('photo_set_button', 'Заменить', array('align' => 'left', 'icon' => 'plus'));?>
      <?=in_bs_button('photo_del_button', 'Удалить', array('align' => 'left', 'icon' => 'minus'));?>
    </div>
      
  <? endif;?>
  <br /><br />
</div>

<script>
var banner_id = "<?=$banners->id;?>";

$(function() {
        
  $("#save").click(function() {
    $("#paramsForm").post_ajax_form("/admin/banners/detail/"+banner_id, function() {
      alert("Успешно сохранено!");
      edit(banner_id);
    });
  });
  
  $(".dates").datepicker();
  $('.dates').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $('.dates').datepicker( "option", "firstDay", 1 );
  $('.dates').datepicker( "option", "changeYear", true );
  $('.dates').datepicker( "option", "duration", 'fast' );
  $("#tm_start").val('<?=$banners->tm_start;?>');
  $("#tm_stop").val('<?=$banners->tm_stop;?>');

  $('#razdel').click(function() {
    modal('/admin/banners/razdel/'+banner_id, 400, function() {
      edit(banner_id);
    });
  });

  $('#places').click(function() {
    modal('/admin/banners/places/'+banner_id, 400, function() {
      edit(banner_id);
    });
  });
  
  $("#photo_set_button").click(function () {
    modal('/admin/banners/photo/<?=$banners->id;?>/set/', 400, function() {
      edit(banner_id);
    });
  });
  
  $("#photo_del_button").click(function () {
    if ( !confirm("Вы действительно хотите удалить?") ) {
      return;
    }
    $("#all_photos").post_ajax_form('/admin/banners/photo/<?=$banners->id;?>/del/', function() {
      edit(banner_id);
    });
  });
  
  $("#photo_slider_add_button").click(function() {
    modal('/admin/banners/photoSlider/<?=$banners->id;?>/set/', 400, function() {
      edit(banner_id);
    });
  });
  
   $("#photo_slider_del_button").click(function () {
    if ( !confirm("Вы действительно хотите удалить?") ) {
      return;
    }
    $("#sliderFiles").post_ajax_form('/admin/banners/photoSlider/<?=$banners->id;?>/del/', function() {
      edit(banner_id);
    });
  });
 
  
});
</script>