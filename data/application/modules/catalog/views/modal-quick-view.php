<div class="modal-content">
   <div class="modal-header">
      <button aria-hidden="true" data-dismiss="modal" class="close custom-close" type="button">
        <span>x</span>
      </button>
   </div>
   <div class="modal-body">
      <!-- Content modal -->
      <div class="shop-product">
         <div class="row">
            <!-- Begin Sidebar Menu -->
            <div class="col-md-6 margin-bottom-60">
               <div class="ms-showcase2-template">
                  <!-- Master Slider -->
                  <div class="master-slider ms-skin-default" id="masterslider">
                     <div class="ms-slide">
                        <img class="ms-brd" src="/assets/img/blank.gif" data-src="<?=$product->photo_main->big_file;?>" alt="<?=$product->name;?>">
                     </div>
                  </div>
                  <!-- End Master Slider -->
               </div>
            </div>
            <!-- End Sidebar Menu -->
            <!-- Begin Content -->
            <div class="col-md-6">
               <div class="shop-product-heading">
                  <h2><?=$product->name?></h2>
               </div>
               <!--div class="icons">
                  <ul class="list-unstyled list-inline margin-bottom-40">
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-165" data-toggle="tooltip" data-original-title="Время приготовления: 30 мин"></span>
                        <p> 30 мин.</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-003" data-toggle="tooltip" data-original-title="Блюдо рассчитано на: 3-4 персоны"></span>
                        <p> 3-4 персоны</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-007" data-toggle="tooltip" data-original-title="Состав мяса: Овощи"></span>
                        <p> Без мяса</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-025 color-orange" data-toggle="tooltip" data-original-title="Состав мяса: Овощи"></span>
                        <p> 100 % овощи</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-123" data-toggle="tooltip" data-original-title="Состав мяса: Овощи"></span>
                        <p> Вес 1,4 кг</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-017" data-toggle="tooltip" data-original-title="Яблоко, груша"></span>
                        <p> Яблоко, груша</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-023	" data-toggle="tooltip" data-original-title="Сладкий десерт"></span>
                        <p> Сладкий десерт</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-030 color-red" data-toggle="tooltip" data-original-title="Острое блюдо"></span>
                        <p> Острое блюдо</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-173 color-sea" data-toggle="tooltip" data-original-title="Морской деликатес"></span>
                        <p> Морской деликатес</p>
                     </li>
                     <li>
                        <span aria-hidden="true" class="tooltips icon icon-food-004" data-toggle="tooltip" data-original-title="Напиток"></span>
                        <p>Домашний лимонад</p>
                     </li>
                  </ul>
                </div-->
                <?=$product->description;?><br />
                <?php require_once('product-full-selector.php');?>
            </div>
            <!-- End Content -->
         </div>
      </div>
      <!-- End content modal -->
   </div>
</div>
<?php require_once('product-full-scripts.php');?>