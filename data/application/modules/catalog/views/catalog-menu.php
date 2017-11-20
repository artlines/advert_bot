<div class="col-md-3 akrist-filter">
  <!-- Begin Sidebar Menu -->
  <ul class="list-group sidebar-nav-v1" id="sidebar-nav">
    <? foreach ($categories as $catg):?>
      <? if ($catg->cnt > 0 && false):?>
        <li class="list-group-item list-toggle">
            <a class="accordion-toggle" href="#collapse-cid<?=$catg->id;?>" data-toggle="collapse">
            <?=$catg->name;?>
          </a>
          <ul id="collapse-cid<?=$catg->id;?>" class="collapse">
            <? foreach ($catg->children as $item):?>
            <li>
              <!--span class="badge badge-u">Новинки</span-->
              <a href="/<?=CATALOG_URL;?><?=$item->url;?>"><i class="fa fa-arrow-circle-o-right"></i>
                <?=$item->name;?>
              </a>
            </li>
            <? endforeach;?>
          </ul>
        </li>
      <? else:?>
        <li class="list-group-item">
          <a class="accordion-toggle" href="/<?=CATALOG_URL;?><?=$catg->url;?>">
            <?=$catg->name;?>
          </a>   
        </li>
      <? endif;?>
    <? endforeach;?>
  </ul>
  
  <!-- Filter -->
  <form action="/<?=$page_url;?>" method="get" id="catalog-filters">
    <?=in_hidden('sort-type', $get['sort-type']);?>
    <?=in_hidden('on-page',  $get['on-page']);?>
  <? foreach ((array)$filters as $type => $filter):?>
  <? if ($type == PRODUCT_FILTER_SIZE) { continue;}?>
  <div class="panel-group" id="accordion">
     <div class="panel panel-default">
        <div class="panel-heading">
           <h2 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                <?=$filter->name;?>
              <i class="fa fa-angle-down"></i>
              </a>
           </h2>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in">
           <div class="panel-body">
              <ul class="list-unstyled checkbox-list">
                <? foreach ((array)$filter->values as $value_id => $value):?>
                <li>
                  <label class="checkbox">
                    <?php $checked = ($get['filter'][$type][$value_id] ? 'checked' : '');?>
                    <input type="checkbox" name="filter[<?=$type;?>][<?=$value_id;?>]" value="<?=$filter->name;?>-<?=$value;?>" <?=$checked;?> />
                    <i></i>
                    <?=$value;?>
                    <!--small><a href="#">(23)</a></small-->
                  </label>
                </li>
                <? endforeach;?>
              </ul>
           </div>
        </div>
     </div>
  </div>
  <? endforeach;?>
  </form>
  <!--/end panel group-->
  <!-- End Brand -->
  
  <? if ($category->id && !$category->products):?>
  <div class="row margin-bottom-5">
    <div class="col-md-12 category-buttons">
      <div>
        <button class="btn-u" href="#" data-target="#call-modal" data-toggle="modal" data-product="<?=$category->name;?>">
          <i class="fa fa-angle-right margin-right-5"></i>Заказать
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal" 
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Наши цены" data-type="prices">
          <i class="fa fa-rub margin-right-5"></i>Наши цены
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal" 
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Выбор цвета" data-type="colors">
          <i class="fa fa-photo margin-right-5"></i>Выбор цвета
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal" 
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Инструкция по уходу" data-type="instructions">
          <i class="fa fa-book margin-right-5"></i>Инструкция по уходу
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal"
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Замеры" data-type="measurements">
          <i class="fa fa-cube margin-right-5"></i>Замеры
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal" 
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Монтаж" data-type="montage">
          <i class="fa fa-building-o margin-right-5"></i>Монтаж
        </button>
      </div>
      <div>
        <button class="btn-u btn-brd btn-u-dark" href="#" data-target="#description-modal" data-toggle="modal" 
                data-cat-id="<?=$category->id;?>" data-title="<?=$category->name;?>. Ремонт" data-type="repair">
          <i class="fa fa-wrench margin-right-5"></i>Ремонт
        </button>
      </div>
    </div>
  </div>
  <? endif;?>
  
  <!-- End Sidebar Menu -->
</div>