<? foreach ((array)$products['search'] as $item):?>
<li class="col-md-4">
  <div class="product-img full-height product-img-brd">
    <? $image = ($item->photo_main ? $item->photo_main : '/images/catalog/no-photo.jpg');?>
    <a href="<?=CATALOG_URL . $item->url;?>">
      <img class="full-width img-responsive" src="<?=$image;?>" alt="<?=$item->name;?>">
    </a>
    <a class="add-to-cart" href="#" data-target="#call-modal" data-toggle="modal" data-product="<?=$item->name;?>">
      <i class="fa fa-shopping-cart"></i>Заказать
    </a>
    <? if ($item->latest == 1):?>
    <div class="shop-rgba-dark rgba-banner">Новинка</div>
    <? endif;?>
    <? if ($item->old_price > 0):?>
    <div class="shop-rgba-red rgba-banner">Распродажа</div>
    <? endif;?>
  </div>
  <div class="product-description product-description-brd margin-bottom-30">
     <div class="overflow-h margin-bottom-5">
        <div class="pull-left">
           <h4 class="title-price">
             <a href="<?=CATALOG_URL . $item->url;?>"><?=$item->name;?></a></h4>
           <span class="gender text-uppercase"><?=$title;?></span>
        </div>
     </div>
     <!--ul class="list-inline product-ratings">
        <li><i class="rating-selected fa fa-star"></i></li>
        <li><i class="rating-selected fa fa-star"></i></li>
        <li><i class="rating-selected fa fa-star"></i></li>
        <li><i class="rating fa fa-star"></i></li>
        <li><i class="rating fa fa-star"></i></li>
     </ul-->
    <div class="row">
      <div class="col-md-6">
        <? if ($item->size):?>
        Размер: <?=$item->size;?>
        <? endif;?>
      </div>
      <div class="col-md-6 product-price shop-red">
        &nbsp;
        <span class="line-through">
          <? if ($item->old_price):?>
          <?=$item->old_price;?>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i>
          <? endif;?>
        </span>
        <div class="price-block">
          <? if ($item->price):?>
          <span class="price"><?=number_format($item->price, 0, '.', ' ');?></span>&nbsp;
          <i class="fa fa-rub" aria-hidden="true"></i>
          <? else:?>
            Цена по запросу
          <? endif;?>
        </div>
      </div>
    </div>
  </div>
</li>
<? endforeach;?>