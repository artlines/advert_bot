<div class="col-md-9">
  <!-- Header filter -->
  <div class="row margin-bottom-5">
    <div class="col-sm-12 result-category">
      <h1><?=$category->title;?></h1>
    </div>
  </div>

  <div class="row">
    <? foreach ((array)$categories[$category->id]->children as $item):?>
    <div class="col-md-3">
      <div class="thumbnails thumbnail-style">
        <a href="/<?=CATALOG_URL;?><?=$item->url;?>"><img alt="" src="<?=$item->pic;?>" class="img-responsive" /></a>
        <div class="caption">
          <h3><a href="/<?=CATALOG_URL;?><?=$item->url;?>" class="hover-effect"><?=$item->name;?></a></h3>
          <p><?=mb_substr($item->text, 0, 100);?></p>
          <p>
            <a class="btn-u btn-u-xs" href="#" data-target="#call-modal" data-toggle="modal" data-product="<?=$category->name;?>. <?=$item->name;?>">
              Заказать <i class="fa fa-angle-right margin-left-5"></i>
            </a>
          </p>
        </div>
       </div>
    </div>
    <? endforeach;?>
  </div>
  
  <div class="col-md-12"><?=$category->text;?></div>
  
</div>
