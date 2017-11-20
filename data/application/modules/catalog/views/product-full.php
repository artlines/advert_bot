<div class="shop-product" id="shop-product-full">
  <!-- End Sidebar Menu -->
  <!-- Begin Sidebar Menu -->
  <div class="col-md-6 margin-bottom-60">
    <div class="owl-product-images">
      <? $varPhoto = [];?>
      <? foreach ($product->photo as $key => $photo):?>
        <div class="item">
          <img src="<?=$photo->big_file;?>" alt="<?=$product->name;?>">   
        </div>
        <? $varPhoto[$photo->variant] = $key;?>
      <? endforeach;?> 
    </div>
  </div>
  <!-- End Sidebar Menu -->
  <!-- Begin Content -->
  <div class="col-md-6">
    <div class="shop-product-heading">
       <h2><?=$product->name;?></h2>
    </div>
    <div class="row">
      <div class="col-md-2 bold">Артикул:</div>
      <div class="col-md-10 vendor-code"><?=$product->code;?></div>
    </div>
    <? if ($product->size):?>
      <div class="row">
        <div class="col-md-2 bold">Размер:</div>
        <div class="col-md-10"><?=$product->size;?></div>
      </div>
    <? endif;?>
    <?php require_once('product-full-selector.php');?>
    <?=$product->description;?><br />
  </div>
  <!-- End Content -->
</div>
<?php require_once('product-full-scripts.php');?>