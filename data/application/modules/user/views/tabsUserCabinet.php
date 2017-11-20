<div id="catalog_tabs">
  <ul>
    <li><a href="/user/zakaz">Мои заказы</a></li>
    <li><a href="/user/saved">Запомненные</a></li>
    <li><a href="/user/config">Личные данные</a></li>
    <li><a href="/user/chpass">Смена пароля</a></li>
  </ul>
</div>

<script>
$(function() {

  $("#catalog_tabs").tabs();
  
});

function reloadTabs() {
  var selected = $("#catalog_tabs").tabs('option', 'selected');
  $("#catalog_tabs").tabs('load', selected);
}
</script>
