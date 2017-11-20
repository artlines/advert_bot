<ul>
  <? foreach ($brands as $item):?>
  <li>
    <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
      <img src="<?=$item->pic;?>" />
    </a>
  </li>
  <? endforeach;?>
  <? foreach ($brands as $item):?>
  <li>
    <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
      <img src="<?=$item->pic;?>" />
    </a>
  </li>
  <? endforeach;?>
  <? foreach ($brands as $item):?>
  <li>
    <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
      <img src="<?=$item->pic;?>" />
    </a>
  </li>
  <? endforeach;?>
  <? foreach ($brands as $item):?>
  <li>
    <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
      <img src="<?=$item->pic;?>" />
    </a>
  </li>
  <? endforeach;?>
  <hr class="clear">
</ul>
<a href="#" class="jcarousel-control-prev2" >&lsaquo;</a>
<a href="#" class="jcarousel-control-next2">&rsaquo;</a>
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

    $('.jcarousel-control-prev2')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '-=1'
        });

    $('.jcarousel-control-next2')
        .on('jcarouselcontrol:active', function() {
            $(this).removeClass('inactive');
        })
        .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('inactive');
        })
        .jcarouselControl({
            target: '+=1'
        });
});
})(jQuery);
</script>