<div style="width:1000px;">

  <form method="post" action="/admin/catalog/newTovarList" class="nyroModal">

    <? if (MY_LEVEL == SITE_LEVEL_CATALOG):?>
      Наименование&lt;tab&gt;Артикул
    <? elseif (MY_LEVEL == SITE_LEVEL_SHOP && CODE_1C):?>
      Код 1С&lt;tab&gt;Наименование&lt;tab&gt;Артикул&lt;tab&gt;Остаток&lt;tab&gt;Цена
    <? elseif (MY_LEVEL == SITE_LEVEL_SHOP && !CODE_1C):?>
      Наименование&lt;tab&gt;Артикул&lt;tab&gt;Остаток&lt;tab&gt;Цена
    <? endif;?>
    <?=in_textarea('tovar_list', $tovar_list, array('width' => '900px', 'height' => '200px'));?>
    <br />

    Производитель<br />
    <?=in_table('manufacturer_id', array('table' => 'shop_tovar_manufacturer', 'name' => 'manufacturer_name', 'is_empty' => 1));?>
    <br />

    Категория<br />
    <?=in_table('category_id', array('table' => 'shop_category', 'is_empty' => 1));?>
    <br />

    <?=in_bs_button('saveNewTovar', 'Добавить', array('type' => 'submit', 'align' => 'left'));?>
    <hr class="clear" />
  
  </form>
</div>