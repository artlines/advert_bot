<div id="brands_images">
  <? foreach ($manufacturers as $key => $item):?>
  <div class="item<? if ($key == 0):?> first<? endif;?>">
    <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
      <img src="<?=$item->pic;?>" alt="brand <?=$item->manufacturer_name;?>" />
    </a>
  </div>
  <? endforeach;?>
</div>