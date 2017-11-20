<div id="brand_carousel_container">
  <div id="brand_carousel">
    <ul>
      <? foreach ($brands as $item):?>
      <li>
        <a href="/catalog/brand/<?=$item->id;?>" title="<?=$item->manufacturer_name;?>">
          <img src="<?=$item->pic;?>" />
        </a>
      </li>
      <? endforeach;?>
      <hr class="clear">
    </ul>
  </div>
  <a href="#" class="jcarousel-control-prev2" >&lsaquo;</a>
  <a href="#" class="jcarousel-control-next2">&rsaquo;</a>
</div>
<script>
(function($) {
  $(function() {
    $('#brand_carousel').jcarousel();

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