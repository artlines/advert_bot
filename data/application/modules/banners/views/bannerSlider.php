<!-- ========================================== SECTION – HERO ========================================= -->
<div id="hero" class="homepage-slider2">
  <div id="owl-main" class="owl-carousel owl-inner-nav owl-ui-sm m-t-20">
      <? foreach ((array)$banner->sliderFile as $item):?>
      <div class="full-width-slider">
        <a href="<?=$item->link;?>">
         <div class="item" style="background-image: url(<?=$item->filename;?>);">
            <div class="container-fluid">
              <div class="caption vertical-center text-center">
                 <!-- <div class="big-text fadeInDown-1">
                    Sale!
                    </div>

                    <div class="excerpt fadeInDown-2">
                    Save up to 25% off
                    </div>

                    <div class="button-holder hidden-sm fadeInDown-3">
                    <a href="index.php?page=single-product" class="big btn btn-primary">check now</a>
                    </div> -->
              </div>
              <!-- /.caption -->
            </div>
            <!-- /.container-fluid -->
          </div>
          <!-- /.item -->
        </a>
      </div>
      <? endforeach;?>
      <!-- /.full-width-slider -->
  </div>
  <!-- /.owl-carousel -->
</div>