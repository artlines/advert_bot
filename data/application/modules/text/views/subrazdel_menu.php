<? if (!empty($subrazdel)):?>
  <div id="subrazdel_menu">
    <ul>
      
      <? foreach ($subrazdel as $key => $item):?>
      <li>
        <a href="/<?=$razdel['url'];?>/page/<?=$item['id'];?>" <? if ($self_info['id']==$item['id']):?>class="selected"<? endif;?>>
          <?=$item['menu_text'];?>
        </a>
      </li>
      <? endforeach;?>

      <? if ($self_info['parent_id']>0):?>
      <li>
        <a href="/<?=$razdel['url'];?>"><?=$razdel['menu_text'];?></a>
      </li>
      <? endif;?>
      
    </ul>
  </div>
<? endif;?>