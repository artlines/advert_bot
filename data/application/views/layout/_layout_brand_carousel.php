<!-- ============================================== BRANDS CAROUSEL ============================================== -->
<div id="brands-carousel" class="logo-slider margin-bottom-30">
  <div class="headline">
    <h2>Наши бренды</h2>
  </div>
  <div class="logo-slider-inner">
      <div id="brand-slider" class="owl-carousel brand-slider custom-carousel owl-theme">
        <? foreach ($brands as $brand):?>
          <div class="item">
            <img src="<?=$brand->pic;?>" alt="<?=$brand->manufacturer_name;?>" title="<?=$brand->description;?>" />
          </div>
        <? endforeach;?>
      </div>
      <!-- /.owl-carousel #logo-slider -->
  </div>
  <!-- /.logo-slider-inner -->
</div>
<!-- /.logo-slider -->