<? if ($page_config['filters']):?>
<div id="catalog_filter">
  <div class="top">
    <div class="item left"></div>
    <div class="item center"></div>
    <div class="item right"></div>
  </div>
  <div class="middle">
      <div id="catalog_filter_items">
      
        <? if ( !empty($categories) ):?>
          <span class="name">Тип:</span>
          <ul class="list">
          <? foreach ($categories as $item):?>
            <li><a href="<?=$pre_link;?><?=$item->link;?>" <? if ($item->id==$search['category_id']):?>class="selected"<? endif;?>><?=$item->name;?></a></li>
          <? endforeach;?>
          </ul>
          <hr class="clear" />
        <? endif;?>
        
        <? if ( !empty($manufacturers) ):?>
          <span class="name">Бренд:</span>
          <ul class="list">
          <? foreach ($manufacturers as $key => $item):?>
            <li><a href="<?=$pre_link;?><?=$item->link;?>" <? if ($key==$search['manufacturer_id']):?>class="selected"<? endif;?>><?=$item->name;?></a></li>
          <? endforeach;?>
          </ul>
          <br />
        <? endif;?>

      </div>
      <hr class="clear" />
  </div>
  <div class="bottom">
    <div class="item left"></div>
    <div class="item center"></div>
    <div class="item right"></div>
  </div>
</div>
<? endif;?>

<? if ($page_config['sorters']):?>
<div id="catalog_sorter">
Сортировка по:
<? foreach ($sorters as $key => $item):?>
  <? $sdir = (($key == $tovars['sort'] && $tovars['sdir'] == 'asc') ? 'desc' : 'asc');?>
  <? $class = ($key == $tovars['sort'] ? 'none selected '.$sdir : 'underline');?>
  <? $element[] = "<a href='{$pre_link}{$link}/1/{$key}/{$sdir}' class='{$class}'>{$item}</a>";?>
<? endforeach;?>
<?=implode("&nbsp;|&nbsp;", $element);?>
</div>
<? endif;?>

