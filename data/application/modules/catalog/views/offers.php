<div id="offers_container">
<? if (!empty($offers)):?>
  <? foreach ($offers as $key => $item):?>
    <div class="offers_item">
      <div class="picture"><img src="<?=$item->image;?>" /></div>
      <div class="description">
        <div class="title"><a href="<?=$item->link;?>" class="underline"><?=$item->name;?></a></div>
        <div class="text"><?=$item->text;?></div>
        <div class="detail"><a href="<?=$item->link;?>" class="none"><img src="/images/main/detail.png" /></a></div>
      </div>
      <hr class="clear" />
    </div>
    <? if (isset($offers[$key+1])):?><div class="offers_spacer"></div><? endif;?>
  <? endforeach;?>
<? else:?>
  На текущий момент нет доступных спецпредложений.
<? endif;?>
</div>