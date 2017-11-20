<div class="col-md-9">
  <!-- Header filter -->
  <div class="row margin-bottom-5">
     <div class="col-sm-12 result-category">
        <h1><?=$title;?></h1>
        <small class="shop-bg-red badge-results"><?=$products['count'];?> Товаров</small>
     </div>
  </div>
  <? if ($valid_limit):?>
  <div class="row margin-bottom-5" id="catalog-manage">
     <div class="col-sm-6">
        <ul class="list-inline pull-left clear-both">
          <li class="sort-list-btn">
            <h3>Сортировать :</h3>
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <?=$sort_name;?> 
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu sort-type-sel" role="menu">
                <? foreach ($valid_sort as $type => $item):?>
                  <li><a href="#" attr-type="<?=$type;?>"><?=$item;?></a></li>
                <? endforeach;?>
              </ul>
            </div>
          </li>
        </ul>
     </div>
     <div class="col-sm-6">
        <ul class="list-inline pull-right">
           <li class="sort-list-btn">
              <h3>Показывать :</h3>
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  <?=$get['on-page'];?><span class="caret"></span>
                </button>
                <ul class="dropdown-menu on-page" role="menu">
                   <li><a href="#" count="all">Все</a></li>
                  <? foreach ($valid_limit as $item):?>
                  <li><a href="#" count="<?=$item;?>"><?=$item;?></a></li>
                  <? endforeach;?>
                 </ul>
              </div>
           </li>
        </ul>
     </div>
  </div>
  <? endif;?>
  <!-- End header filter -->
  <!-- Products item -->
  <div class="filter-results">
    <ul class="row illustration-v2 margin-bottom-30 inline">
      <? require_once('product-list-items.php');?>
    </ul>
  </div>
  <!-- End products item -->
  <? if ($products['pages'] > 1):?>
  <? 
  $getp = $get;
  unset($getp['page']);
  $query = http_build_query($getp);
  ?>
  <div class="text-center">
    <ul class="pagination pagination-v2">
      <li>
        <? if ($products['page'] - 1 > 0):?>
        <a href="/<?=$page_url;?>?page=<?=($products['page'] - 1);?>&<?=$query?>"><i class="fa fa-angle-left"></i></a>
        <? else:?>
        <span><i class="fa fa-angle-left"></i></span>
        <? endif;?>
      </li>         
      <? for ($i = 1; $i <= $products['pages']; $i++):?>
      <li <? if ($i == $products['page']):?>class="active"<? endif;?>>
        <a href="/<?=$page_url;?>?page=<?=$i;?>&<?=$query?>"><?=$i;?></a>
      </li>
      <? endfor;?>
      <li>
        <? if ($products['page'] + 1 <= $products['pages']):?>
        <a href="/<?=$page_url;?>?page=<?=($products['page'] + 1);?>&<?=$query?>"><i class="fa fa-angle-right"></i></a>
        <? else:?>
        <span><i class="fa fa-angle-right"></i></span>
        <? endif;?>
      </li>
    </ul>
  </div>
  <? endif;?>
  <!--/end pagination-->
  <div class="row">
    <div class="col-md-12"><?=$category->text;?></div>
  </div>
</div>