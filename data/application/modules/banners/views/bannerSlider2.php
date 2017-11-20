<div class="jcarousel-wrapper">
  <div class="jcarousel">
    <ul>
      <? foreach ((array)$banner->sliderFile as $item):?>
      <li>
        <a href="<?=$item->link;?>">
          <img 
            src="<?=$item->filename;?>" 
            style="width:<?=($banner->width - 20);?>px; <? if ($banner->height):?>height:<?=$banner->height;?>px;<? endif;?>" 
            border="0" 
            alt="banner" />
        </a>
      </li>
      <? endforeach;?>
    </ul>
  </div>

  <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
  <a href="#" class="jcarousel-control-next">&rsaquo;</a>
    
  <p class="jcarousel-pagination"></p>
</div>

<script>
(function($) {
  $(function() {
    $('.jcarousel').jcarousel().jcarouselAutoscroll({
      interval: 3000,
      target: '+=1',
      autostart: true
    });
    setInterval(function() {
      $('.jcarousel').jcarousel('scroll', '-=4');
    }, <?=count($banner->sliderFile) * 3;?>000);

    $('.jcarousel-control-prev')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '-=1'
        });

    $('.jcarousel-control-next')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '+=1'
        });

    $('.jcarousel-pagination')
        .on('jcarouselpagination:active', 'a', function() {
            $(this).addClass('active');
        })
        .on('jcarouselpagination:inactive', 'a', function() {
            $(this).removeClass('active');
        })
        .jcarouselPagination();
});
})(jQuery);
</script>