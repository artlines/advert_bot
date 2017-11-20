<div id="ajax-loader" style="display: none;"></div>
<!--=== Header ===-->
<div class="header header-sticky">
   <div class="topbar-v1">
      <div class="container">
         <div class="row">
            <div class="col-md-6 pull-right">
               <ul class="list-inline top-v1-data">
                  <li><a href="/"><i class="fa fa-home"></i></a></li>
                  <li class="phone top-elem">
                    <?php list($phone) = explode(',', $current_city_shop->shop_tel);?>
                    <a href="tel:<?=$phone;?>"><i class="fa fa-mobile" aria-hidden="true"></i>
                      <?=$phone;?>
                    </a>
                  </li>
                  <li class="location dropdown top-elem">
                    <a href="javascript:void(0);" id="select-city-value" aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-map-marker" ></i> 
                      <?=$current_city->name;?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="select-city-value" id="select-city">
                      <? foreach ($city as $id => $name):?>
                      <li attr-id="<?=$id;?>"><a href="#"><?=$name;?></a></li>
                      <? endforeach;?>
                    </ul>
                  </li>
                  <li class="call-me top-elem">
                    <a href="#" data-target="#call-modal" data-toggle="modal" data-product="Позвоните мне!">
                      <i class="fa fa-phone" aria-hidden="true"></i> 
                      Позвоните мне!
                    </a>
                  </li>
                  <li class="top-elem"><a href="/sitemap"><i class="fa fa-sitemap" aria-hidden="true"></i></a></li>
               </ul>
            </div>
         </div>
      </div>
   </div>
   <div class="container">
      <div class="row">
         <div class="col-md-5">
            <!-- Logo -->
            <a class="logo" href="/">
            <img class="img-responsive" src="/assets/img/logo1-default.png" alt="Logo">
            </a>
            <!-- End Logo -->
         </div>
         <div class="col-md-7 pull-left">
            <div class="address">
              <address>
                <span class="">
                  г. <?=$current_city->name;?>, <?=$current_city_shop->name;?>, <?=$current_city_shop->addr;?>
                </span>
              </address>
            </div>
         </div>
         <div class="col-md-2">
            <!-- Toggle get grouped for better mobile display -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-bars"></span>
            </button>
            <!-- End Toggle -->
         </div>
      </div>
   </div>
   <!--/end container-->
   <!-- Collect the nav links, forms, and other content for toggling -->
   <div class="collapse navbar-collapse mega-menu navbar-responsive-collapse">
      <div class="container">
        <ul class="nav navbar-nav">
          <? foreach ($main_menu as $item):?>
            <? if ($item->module_id == MODULE_ID_CATALOG):?>
             <li class="dropdown">
               <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                 <?=$item->menu_text;?>
               </a>
               <ul class="dropdown-menu">
                 <? foreach ($categories as $category):?>
                 <li>
                   <a href="/<?=$item->url;?>/<?=$category->translite_name;?>">
                     <span aria-hidden="true" class="icon-food-<?=$category->icon_code;?>"></span>
                     <?=$category->name;?>
                   </a>
                 </li>
                 <? $catalog = $item->url;?>
                 <? endforeach;?>
               </ul>
            </li>
            <? else:?>
            <li>
               <a href="/<?=$item->url;?>"><?=$item->menu_text;?></a>
            </li>
            <? endif;?>
          <? endforeach;?>
            <!-- Search Block -->
            <li>
               <i class="search fa fa-search search-btn"></i>
               <div class="search-open">
                  <div id="search-form" class="input-group animated fadeInDown">
                     <input type="text" class="form-control search-text" placeholder="Введите фразу для поиска...">
                     <span class="input-group-btn">
                     <button class="btn-u" type="button">Найти</button>
                     </span>
                  </div>
               </div>
            </li>
            <!-- End Search Block -->
         </ul>
      </div>
      <!--/end container-->
   </div>
   <!--/navbar-collapse-->
</div>
<!--=== End Header ===-->
