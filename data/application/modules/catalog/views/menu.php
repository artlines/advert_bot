<div id="catalog_tabs">
  <ul>
    <li><a href="/catalog/menu/category" id="menu_category">Категории</a></li>
    <li><a href="/catalog/menu/manufacturer" id="manufacturer">Производители</a></li>
  </ul>
</div>

<script>
$(function() {

  $("#catalog_tabs").tabs({
    ajaxOptions: {
      error: function( xhr, status, index, anchor ) {
        $( anchor.hash ).html("Ошибка загрузки!");
      }
    },
    select: function(event, ui) { $(".menu_category_item").html(Loader); }
  });
  
  $(".menu_category_item").find('li').find('a').live("click", function() {
    $(".menu_category_item").find('li').css('background-color', '#FFFFFF');
    $(this).closest('li').css('background-color', '#FFF0E0');
  });
  
});

function loadTovar(params) {
  $("#tovar_list").html(Loader);
  $("#tovar_list").load("/catalog/tovarPaginator/", params);
}
</script>
