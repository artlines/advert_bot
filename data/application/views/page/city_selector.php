            <div id="city_selector_list">
              <ul>
                <? foreach ($cities as $key => $item):?>
                <li><a href="#" class="color_683f8c size12px" cityid="<?=$key;?>"><?=$item;?></a></li>
                <? endforeach;?>
              </ul>
            </div>
<script>
city_selector_show = 0;
$(function() {

  $("#city_selector").click(function() {
    if (city_selector_show) {
      $("#city_selector_list").hide();
      city_selector_show = 0;
    }
    else {
      $("#city_selector_list").show();
      city_selector_show = 1;
    }
  });
  
  $("#city_selector_list").find("a").click(function() {
    city_id = $(this).attr('cityid');
    $("#city_selector_list").html(loader_small);
    $.post('/', {city_select:city_id}, function() {
      location.reload();
    });
  });

});
</script>