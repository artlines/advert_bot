<? $count = count($review);?>
<div class="services">
  <div class="content_block_left">
    <? foreach (array_slice($review, 0, ceil($count / 2)) as $key => $item):?>
    <div class="review">
      <div class="img_float_left"><img src="<?=$item->image;?>" class="service_img" /></div>
      <div class="title"><a href="/review/item/<?=$item->id;?>" class="standart">
        <?=$item->firstname;?><br /><?=$item->lastname;?>
        </a>
      </div>
      <div class="title2"><?=$item->company;?></div>
      <div class="text">
        <?=$item->pretext;?>
      </div>
    </div>
    <? endforeach;?>
  </div>
  <div class="content_block_right">
    <? foreach (array_slice($review, ceil($count / 2), floor($count / 2)) as $key => $item):?>
    <div class="review">
      <div class="img_float_left"><img src="<?=$item->image;?>" class="service_img" /></div>
      <div class="title"><a href="/review" class="standart"><?=$item->firstname;?><br /><?=$item->lastname;?></a></div>
      <div class="title2"><?=$item->company;?></div>
      <div class="text">
        <?=$item->pretext;?>
      </div>
    </div>
    <? endforeach;?>
  </div>
</div>
<hr class="clear" />

<? if ($pages > 1):?>
<div class="pagination">
  <? for ($i = 1; $i <= $pages; $i++):?>
    <? if (
        $pages > 10 && 
        ($pages - $i >= 3) && 
        ($i > 3) && 
        ( ($i > $page + 5 ) || ($i < $page - 5 ) )
      ):?>
      <? continue;?>
    <? endif;?>
    <? if ($i - $last_printed > 1):?><div>...</div><? endif;?>
    <? if ($i==$page):?>
      <div class="current"><?=$page;?></div>
    <? else:?>
      <div class="item"><a href="/review/<?=$i;?>"><?=$i;?></a></div>
    <? endif;?>
    <? $last_printed = $i;?>
    <? if ($last_printed < $pages):?><div class="dot">.</div><? endif;?>
  <? endfor;?>
</div>
<? endif;?>
<div class="spacer"></div>
