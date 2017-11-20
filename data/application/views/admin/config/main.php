<? $CI = &get_instance();?>
<div id="small_left">
  
  <div class="panel panel-default">
    <div class="panel-heading">Настройки</div>
    <table class="table">

      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="90%">
          <a href="javascript:config_detail(1);" title="Редактировать">Общие настройки</a></td>
      </tr>
      <? if (USE_BRANDS):?>
      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="90%">
          <a href="javascript:config_manufacturer();" title="Редактировать">Производители</a></td>
      </tr>
      <? endif;?>
      
      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="90%">
          <a href="javascript:config_category();" title="Редактировать">Категории</a></td>
      </tr>
      
      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="90%">
          <a href="javascript:config('shops');" title="Редактировать">Адреса магазинов</a></td>
      </tr>
        
      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="90%">
          <a href="javascript:config('filters');" title="Редактировать">Фильтры</a></td>
      </tr>
      
    </table>
  </div>
</div>
<div id="big_right"></div>
<script>
function config_detail(id) {
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/config/detail/'+id);
}

function config_manufacturer() {
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/config/manufacturer/');
}

function config_category() {
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/config/category/');
}

function config(type) {
  $("#big_right").html(Loader);
  $("#big_right").load('/admin/config/' + type + '/');
}
</script>