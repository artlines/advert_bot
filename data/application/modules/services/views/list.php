<? $count = count($services);?>
<div class="services">
  <div class="content_block_left">
    <? $colors = array('dark_blue', 'default', 'green');?>
    <? foreach (array_slice($services, 0, ceil($count / 2)) as $key => $item):?>
    <h2><a href="/services/item/<?=$item->id;?>" class="<?=$colors[$key%3];?> standart"><?=$item->name;?></a></h2>
    <div class="item">
      <div class="img_float_left"><img src="<?=$item->image;?>" class="service_img" /></div>
      <div class="text">
        <?=$item->pretext;?>
      </div>
    </div>
    <? endforeach;?>
  </div>
  <div class="content_block_right">
    <? $colors = array('dark_red', 'gorchichnik', 'blue');?>
    <? foreach (array_slice($services, ceil($count / 2), floor($count / 2)) as $key => $item):?>
    <h2><a href="/services/item/<?=$item->id;?>" class="<?=$colors[$key%3];?> standart"><?=$item->name;?></a></h2>
    <div class="item">
      <div class="img_float_left"><img src="<?=$item->image;?>" class="service_img" /></div>
      <div class="text">
        <?=$item->pretext;?>
      </div>
    </div>
    <? endforeach;?>
  </div>
</div>
<hr class="clear" />
