               <!--=== Shop Suvbscribe ===-->
               <div class="shop-subscribe">
                  <div class="container bordered">
                     <div class="row">
                        <div class="col-md-6 md-margin-bottom-20">
                           <h2><strong>УЗНАЙ ПЕРВЫМ</strong></h2>
                           <p>Новости, акции, скидки и спецредложения в нашей официальной группе,
                              или подпишись на еженедельные обновления.
                           </p>
                           <ul class="subscribe-socials list-inline">
                              <li>
                                 <a href="https://vk.com/akristru" target="_blank"
                                    class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Vkontakte">
                                  <i class="fa fa-vk"></i>
                                 </a>
                              </li>
                              <li>
                                 <a href="https://www.youtube.com/channel/UCCRP7e8DCw4_dezLuNPKfeQ" target="_blank"
                                    class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Youtube">
                                 <i class="fa fa-youtube"></i>
                                 </a>
                              </li>
                              <!--<li>
                                 <a href="#" class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Google Plus">
                                 <i class="fa fa-google-plus"></i>
                                 </a>
                              </li>
                              <li>
                                 <a href="#" class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Linkedin">
                                 <i class="fa fa-linkedin"></i>
                                 </a>
                              </li>
                              <li>
                                 <a href="#" class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pinterest">
                                 <i class="fa fa-pinterest"></i>
                                 </a>
                              </li>
                              <li>
                                 <a href="#" class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Twitter">
                                 <i class="fa fa-twitter"></i>
                                 </a>
                              </li>
                              <li>
                                 <a href="#" class="tooltips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Dribbble">
                                 <i class="fa fa-dribbble"></i>
                                 </a>
                              </li>-->
                           </ul>
                        </div>
                        <div class="col-md-2 md-margin-bottom-20">
                        </div>
                        <div class="col-md-4" id="subscribe-form">
                           <div class="input-group">
                              <input type="text" class="form-control mail" placeholder="Введите ваш Email">
                              <span class="input-group-btn">
                                <button class="btn" type="button"><i class="fa fa-envelope-o"></i></button>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--/end container-->
               </div>
               <!--=== End Shop Suvbscribe ===-->
               <!--=== Footer Version 1 ===-->
               <div class="footer-v1">
                  <div class="footer">
                     <div class="container">
                        <div class="row">
                           <!-- Address -->
                           <div class="col-md-6 map-img md-margin-bottom-40">
                              <div class="headline">
                                 <h2>Контакты</h2>
                              </div>
                              <img src="/assets/img/logo_small.jpg" alt="">
                              <address class="md-margin-bottom-40">
                                <strong>ООО «Акрист» : www.akrist.ru</strong><br />
                                Адрес: г. <?=$current_city->name;?>, <?=$current_city_shop->name;?>, <?=$current_city_shop->addr;?><br />
                                Телефон: <?=$current_city_shop->shop_tel;?>  <br />
                                Электронная почта: <a href="">sales@akrist.ru</a><br />
                                Пн.-Вс.: с 10.00 до 20.00
                              </address>
                           </div>
                           <!--/col-md-3-->
                           <!-- End Address -->
                           <!-- Latest -->
                           <div class="col-md-6 md-margin-bottom-40">
                              <div class="posts">
                                 <div class="headline">
                                    <h2>Навигация</h2>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-4">
                                      <ul class="list-unstyled link-list">
                                        <?php foreach (array_slice($footer_menu, 0, 5) as $item):?>
                                        <li><a href="/<?=$item->url;?>"><?=$item->menu_text;?></a><i class="fa fa-angle-right"></i></li>
                                        <?php endforeach;?>
                                      </ul>
                                    </div>
                                    <div class="col-md-4">
                                       <ul class="list-unstyled link-list">
                                        <?php foreach (array_slice($footer_menu, 5, 5) as $item):?>
                                        <li><a href="/<?=$item->url;?>"><?=$item->menu_text;?></a><i class="fa fa-angle-right"></i></li>
                                        <?php endforeach;?>
                                       </ul>
                                    </div>
                                    <div class="col-md-4">
                                      <ul class="list-unstyled link-list">
                                        <? foreach (array_slice($categories, 0, 5) as $category):?>
                                        <li>
                                          <a href="/<?=$catalog?>/<?=$category->translite_name;?>">
                                            <?=$category->name;?>
                                          </a>
                                          <i class="fa fa-angle-right"></i>
                                        </li>
                                        <? endforeach;?>
                                      </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!--/col-md-3-->
                           <!-- End Latest -->
                        </div>
                     </div>
                  </div>
                  <!--/footer-->
                  <div class="copyright">
                     <div class="container">
                        <div class="row">
                           <div class="col-md-6">
                              <p>
                              </p>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!--/copyright-->
               </div>
               <!--=== End Footer Version 1 ===-->
            </div>
            <!--/wrapper-->
            <!-- JS Global Compulsory -->
            <script type="text/javascript" src="/assets/js/jquery.easing-1.3.min.js"></script>
            <!-- JS Implementing Plugins -->
            <script type="text/javascript" src="/assets/plugins/back-to-top.js"></script>
            <script type="text/javascript" src="/assets/plugins/smoothScroll.js"></script>
            <script type="text/javascript" src="/assets/plugins/parallax-slider/js/modernizr.js"></script>
            <script type="text/javascript" src="/assets/plugins/parallax-slider/js/jquery.cslider.js"></script>
            <script type="text/javascript" src="/assets/plugins/owl-carousel/owl-carousel/owl.carousel.js"></script>
            <!-- JS Customization -->
            <script type="text/javascript" src="/assets/js/custom.js"></script>
            <!-- JS Page Level -->
            <script type="text/javascript" src="/assets/js/app.js"></script>
            <script type="text/javascript" src="/assets/js/plugins/owl-carousel.js"></script>
            <script type="text/javascript" src="/assets/js/plugins/parallax-slider.js"></script>
            <!-- Master Slider -->
            <script type="text/javascript" src="/assets/plugins/masterslider/js/masterslider.min.js"></script>
            <script type="text/javascript" src="/assets/plugins/masterslider/js/jquery.easing.min.js"></script>
            <script type="text/javascript" src="/assets/plugins/masterslider/js/master-slider.js"></script>
            <script src="http://firmsonmap.api.2gis.ru/js/DGWidgetLoader.js" charset="utf-8"></script>
            <!--[if lt IE 9]>
            <script src="/assets/plugins/respond.js"></script>
            <script src="/assets/plugins/html5shiv.js"></script>
            <script src="/assets/plugins/placeholder-IE-fixes.js"></script>
            <![endif]-->
    <div class="modal fade" tabindex="-1" role="dialog" id="show-message">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close custom-close" type="button"><span>×</span></button>
          </div>
          <div class="modal-body">
            <h4>Сообщение</h4>
            <p>...</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-u btn-u-default rounded-2x" data-dismiss="modal">OK</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  </body>
</html>