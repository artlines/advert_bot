<h2 style="font-size:14px; font-weight:bold;">Производители</h2>
<table width="100%" id="table_list" cellspacing="1px">
  <tr>
    <th>ID</th>
    <th>Наименование</th>
    <th>Приоритет</th>
    <th>Широта</th>
    <th>Долгота</th>
    <th>Действия</th>
  </tr>
  <? foreach ($cities as $key => $item):?>
  <tr>
    <td align="center"><?=$item->id;?></td>
    <td align="left"><?=$item->name;?></td>
    <td align="center"><?=$item->priority;?></td>
    <td align="center"><?=$item->lat;?></td>
    <td align="center"><?=$item->lon;?></td>
    <td align="center">
      <a href="javascript:city_full(<?=$item->id;?>);">  <img src="/images/text.gif"   border="0" /></a>
      <a href="javascript:city_delete(<?=$item->id;?>);"><img src="/images/delete.gif" border="0" /></a>
    </td>
  </tr>
  <? endforeach;?>
</table>
<div align="right" style="margin:15px;"><?=in_blue_button('city_add', 'Добавить город');?></div>

<script>
function city_delete(id) {
  if(confirm("Вы действительно хотите удалить город?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/config/city/del", {id:id}, function(data) {
      if (data) {
        alert(data);
      }
      config_city();
    });
  }
}

function city_full(id) {
  modal("/admin/config/city/edit/"+id);
}

$(function(){

  $.fn.nyroModal.settings.endRemove=function(elts, settings){
    config_city();
  }
  
  $("#city_add").click(function() {
    modal("/admin/config/city/add");
  });
});

</script>