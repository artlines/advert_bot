<? require('_layout_head.php');?>

         <body class="boxed-layout container">
            <? require_once('_modal_call.php');?>
            <div class="wrapper">
              <? require_once('_layout_header.php');?>
              <div class="breadcrumbs">
                  <div class="container">
                    <h1 class="pull-left"><?=$h1;?></h1>
                    <?=getCrumbs();?>
                  </div>
               </div>
              <!--=== Content Part ===-->
              <div class="container content">
                  <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10" id="main-content"><?=$text;?></div>
                    <div class="col-md-1"></div>
                  </div>
                  <div class="seo-text">
                    <?=$seo_text;?>
                  </div>
                <div id="prev-viewed"></div>
              </div>
              <!--/container-->
              <!--=== End Content Part ===-->
<? require_once('_layout_footer.php');?>