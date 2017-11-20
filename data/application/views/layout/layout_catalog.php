<? require('_layout_head.php');?>

         <body class="boxed-layout container">
            <? require_once('_modal_call.php');?>
            <div class="wrapper">
              <? require_once('_layout_header.php');?>
              <div class="breadcrumbs">
                  <div class="container">
                    <?=getCrumbs();?>
                  </div>
              </div>
              <!--=== Content Part ===-->
              <div class="container content">
                  <div class="row" id="main-content">
                    <?=$text;?>
                  </div>
                  <div class="seo-text">
                    <?=$seo_text;?>
                  </div>
                <div id="prev-viewed"></div>
                <? require_once('_layout_brand_carousel.php');?>
              </div>
              <!--/container-->
              <!--=== End Content Part ===-->
<? require_once('_layout_footer.php');?>