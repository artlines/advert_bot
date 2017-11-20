<div id="catalog_container">
  <ul class="products">
    <? foreach ($tovars as $tovar):?>
    <!--start product-->
    <li class="product">
      <div class="product">
        <div class="thumb">
          <a href="/catalog/tovarFull/<?=$tovar->id;?>">
            <? if($tovar->photo_main):?>
              <img src="<?=$tovar->photo_main;?>" alt="Фото" border="0" title="<?=$tovar->name;?>" />
            <? else:?>
              <img src="/images/catalog/nophoto.jpg" alt="Фото отсутствует" border="0" title="<?=$tovar->name;?>" />
            <? endif;?>
          </a>
        </div>
        <div class="title" title="<?=$tovar->name;?>">
          <a href="/catalog/tovarFull/<?=$tovar->id;?>" class="underline"><?=$tovar->name;?></a>
        <br />
        <?=$tovar->manufacturer;?>
        </div>
        <div class="bottom">
          <? if ($tovar->old_price > 0):?>
          <div class="old_price_pic">
            <img src="/images/main/sale.png" title="Распродажа" />
          </div>
          <div class="old_price" title="<?=round($tovar->old_price, 2);?> р.">
            <?=round($tovar->old_price, 2);?> р.
          </div>
          <? endif;?>
          <div class="price" title="<?=round($tovar->price, 2);?> р.">
            <?=round($tovar->price, 2);?> р.
          </div>
        </div>
      </div>
    </li>
    <!--end product-->
    <? endforeach;?>
  </ul>
</div>