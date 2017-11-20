<div class="left_menu">
  <div class="items">
    <ul>
      <? foreach ($services as $item):?>
        <li><a href="javascript:loadPortfolio(<?=$item->id;?>)" class="dark_blue standart"><?=$item->name;?></a></li>
      <? endforeach;?>
    </ul>
  </div>
  <div class="spacer"></div>
  <div id="portfolio_list">
  </div>
  <hr class="clear" />
</div>
<script>
$(function() {
  
  var serv_id = <?=(int)$serv_id;?>

  loadPortfolio(serv_id);
});

/**
 * loadPortfolio
 */
function loadPortfolio(service_id) {
  $("#portfolio_list").html(Loader);
  $("#main_title").load("/portfolio/get_title/" + service_id);
  $("#portfolio_list").load("/portfolio/list/" + service_id);
  return;
}
</script>

