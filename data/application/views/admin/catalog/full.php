<h3>Подробная информация</h3>
<hr />
<div id="form">
  
  <h4>Основные параметры</h2>
  
  <div class="block_50">
    Код<br />
    <?=in_text_readonly('id', $tovar->id, '250px');?>
  </div>
  
  <div class="block_50">
    Активен<br />
    <?=in_check('active', $tovar->active);?>
  </div>
  
  <div class="block_50">
    Название<br />
    <?=in_text('name', $tovar->name, array('width' => '250px'));?>
  </div>
  
  <div class="block_50">
    Артикул<br />
    <?=in_text('code', $tovar->code, array('width' => '250px'));?>
  </div>
  
  <div class="block_50">
    Производитель<br />
    <?=in_table('manufacturer_id', array(
      'table' => 'shop_tovar_manufacturer', 
      'value' => $tovar->manufacturer_id, 
      'name'  => 'manufacturer_name',
      'width' => '250px'
    ));?>
  </div>
  
  <? if (MY_LEVEL == SITE_LEVEL_SHOP && CODE_1C):?>
  <div class="block_50">
    Код 1с<br />
    <?=in_text('code_1c', $tovar->code_1c, array('width' => '250px'));?>
  </div>
  <? endif;?>
  
  <? if (MY_LEVEL == SITE_LEVEL_SHOP):?>
  <div class="row-20"></div>
  <div>
    <div class="col-md-12">
      <div class="row">Тип продукта</div>
      <div class="row">
        <div class="btn-group" data-toggle="buttons">
          <? foreach ($productTypes as $key => $item):?>
          <label class="btn btn-default <? if ($key == $tovar->type):?>active<? endif;?>">
            <input type="radio" name="type" value="<?=$key;?>" autocomplete="off" <? if ($key == $tovar->type):?>checked<? endif;?> /> 
            <?=$item;?>
          </label>
          <? endforeach;?>
        </div>
      </div>
      <div class="row-20"></div>
      <div class="row product-var product-var-simple hide">
        <div class="col-md-12">
          <div class="row">Цена</div>
          <div class="row"><?=in_text('price', $tovar->price, ['width' => '100%']);?></div>
        </div>
      </div>
      <div class="row product-var product-var-variant hide">
        <? foreach ($tovar->variants as $item):?>
        <div class="row-variant">
          <div class="col-md-3">
            <div class="row">Вариант</div>
            <div class="row"><?=in_text('variants[]', $item->name, ['width' => '100%']);?></div>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-3">
            <div class="row">Цена</div>
            <div class="row"><?=in_text('prices[]', $item->price, ['width' => '100%']);?></div>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-3">
            <div class="row">Артикул</div>
            <div class="row"><?=in_text('vendor_code[]', $item->vendor_code, ['width' => '100%']);?></div>
          </div>
          <div class="col-md-1">
            <div class="row">&nbsp;</div>
            <div class="row">
              <a href="javascript:void(0);" class="add-product-var"><span class="glyphicon glyphicon-plus"></span></a>
              <a href="javascript:void(0);" class="del-product-var"><span class="glyphicon glyphicon-minus"></span></a>
            </div>
          </div>
        </div>
        <? endforeach;?>
      </div>
      <div class="row product-var product-var-weight hide">
        <div class="col-md-12">
          <div class="row">Цена за кг</div>
          <div class="row"><?=in_text('price-w', $tovar->price, ['width' => '100%']);?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <? if (CODE_1C):?>
        <div class="row">Код 1C</div>
        <div class="row"><?=in_text('code_1c');?></div>
      <? endif;?>
    </div>
    <div class="col-md-1"></div>
  </div>
  <? endif;?>
  
  <hr class="clear" />
  <div class="row-20"></div>
  <div>
    Описание<br />
    <?=in_hidden('description', '');?>
    <?=$editor;?>
  </div>
  
  <? if (!empty($tovar->dop)):?>
    <h4>Дополнительные параметры</h2>
    <? foreach ((array)$tovar->dop as $key => $item):?>
    <div class="block_50">
      <?=$item->name;?><br />
      <? $method = (function_exists("in_{$item->type}") ? "in_{$item->type}" : "in_text");?>
      <?=$method("dop[{$item->id}]", $item->value);?>
    </div>
    <? endforeach;?>
  <? endif;?>
    <hr class="clear" />
    <tr><td colspan="2"><hr />&nbsp;</td></tr>
    <tr>
      <td valign="bottom">
        <?=in_bs_button('submit', 'Сохранить');?>
      </td>
    </tr>
  </table>
  <br /><br />
  
  <table width="100%">
    <tr>
      <td width="50%" valign="top">
        <div class="row">
          <div class="col-md-12">
            <b>Категории</b><br /><br />
            <a href="javascript:void(0);" id="category" style="text-decoration:none;">
            <? if(!empty($tovar->category)):?>
              <? foreach($tovar->category as $key => $item):?>
                <li><?=$item->name;?></li>
              <? endforeach;?>
            <? else:?>
              Нет категорий
            <? endif;?></a>
          </div>
        </div>
        <div class="row-20"></div>
        <div class="row">
          <div class="col-md-12">
            <div><b>Фильтры</b></div>
            <a href="javascript:void(0);" id="product-filters" style="text-decoration:none;">
              <? if(!empty($tovar->filters)):?>
                <? foreach($tovar->filters as $key => $item):?>
                  <li><?=$item->name;?> - <?=$item->value;?></li>
                <? endforeach;?>
              <? else:?>
                Нет фильтров
              <? endif;?>
            </a>
          </div>
        </div>
      </td>
        
      <td width="50%" valign="top">
        <b>Изображения</b><br /><br />
        <br />
        <div id="all_photos" class="row">
          <? foreach ($tovar->photo as $v => $k):?>
            <div class="col-md-4 item">
              <?=in_check("photo_id[{$k->id}]", 0, $k->id);?>
              <? if ($k->is_main):?>
                <span class="glyphicon glyphicon-certificate"></span>
              <? endif;?>
              <a href="<?=$k->big_file;?>" class="highslide" onclick="return hs.expand(this)">
                <img src="<?=$k->thumb_file;?>" alt="Highslide JS" title="Нажмите для просмотра" class="preview" />
              </a>
            </div>
          <? endforeach;?>
        </div>
        <?=in_bs_button('photo_del_button', 'Удалить', array('icon' => 'remove'));?>
        <?=in_bs_button('photo_add_button', 'Добавить', array('icon' => 'plus'));?>
      </td>
    </tr>
  </table>
</div>
              
<br /><br />

<script>
var tovar_id = '<?=$tovar->id;?>';
        
$(function() {

  $("#submit").click(function(){
    var text = CKEDITOR.instances['descriptionHtml'].getData();
    $("#description").val(text);
    $("#form").post_ajax_form("/admin/catalog/save/"+tovar_id, function() {
      loadTovar(tovar_id);
    });
  });

  $('#category').click(function() {
    modal('/admin/catalog/category/'+tovar_id, 400, function() {
      loadTovar(tovar_id);
    });
  });
  
  $("#product-filters").click(function() {
    modal('/admin/catalog/filters/' + tovar_id, 400, function() {
      loadTovar(tovar_id);
    });
  });
  
  $("#photo_add_button").click(function () {
    modal('/admin/catalog/photo/<?=$tovar->id;?>/add/', 400, function() {
      loadTovar(tovar_id);
    });
  });
  
  $("#photo_del_button").click(function () {
    $("#all_photos").post_ajax_form('/admin/catalog/photo/<?=$tovar->id;?>/del/', function() {
      loadTovar(tovar_id);
    });
  });
  
    $("[name=type]").change(function() {
    value = $(this).val();
    selectProductType(value);
  });
  
  selectProductType('<?=$tovar->type;?>');
  
  $(".add-product-var").on('click', function() {
    $(this).closest('div.row-variant').clone(true).appendTo('div.product-var-variant');
  });
  
  $(".del-product-var").on('click', function() {
    $(this).closest('div.row-variant').remove();
  });
  
  $("#form-new-product").submit(function() {
    console.log($(this).serialize());return false;
  });

});

function selectProductType(value) {
  $(".product-var").addClass('hide');
  $(".product-var-" + value).removeClass('hide');
}
</script>