<ul>
  <? foreach ($razdel as $item):?>
    <? if (trim($item->title) == '' || $item->url == 'feedback') continue; ?>
    <li>
      <a href="/<?=$item->url;?>"><?=$item->title;?></a>
      <? if ($item->module_id == MODULE_ID_CATALOG):?>
        <ul>
          <? foreach ($categories as $category):?>
            <li><a href="/<?=$item->url;?><?=$category->url;?>"><?=$category->name;?></a>
              <? if (!empty($category->children)):?>
                <ul>
                  <? foreach ($category->children as $scategory):?>
                    <li><a href="/<?=$item->url;?><?=$scategory->url;?>"><?=$scategory->name;?></a></li>
                  <? endforeach;?>
                </ul>
              <? endif;?>
            </li>
          <? endforeach;?>
        </ul>
      <? endif;?>
    </li>
  <? endforeach;?>
</ul>