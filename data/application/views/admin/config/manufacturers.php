<div class="panel panel-default">
  <div class="panel-heading"><h3>Производители</h3></div>
  <table class="table">
    <tr>
      <th>ID</th>
      <th>Наименование</th>
      <th>Описание</th>
      <th>Картинка</th>
      <th>Лента</th>
      <th>&nbsp;</th>
    </tr>
    <? foreach ($manufacturers as $key => $item):?>
    <tr>
      <td align="center"><?=$item->id;?></td>
      <td align="left"><?=$item->manufacturer_name;?></td>
      <td align="left"><?=mb_substr($item->description, 0, 300);?><? if (mb_strlen($item->description) > 300):?>...<? endif;?></td>
      <td align="center"><? if ($item->pic):?><img src="<?=$item->pic;?>" width="100px" /><? else:?>&nbsp;<? endif;?></td>
      <td align="center"><? if ($item->main_page):?><img src="/images/base/on.gif" /><? else:?>&nbsp;<? endif;?></td>
      <td align="center">
        <a href="javascript:manufacturer_full(<?=$item->id;?>);">  <span class="glyphicon glyphicon-edit"></span></a>&nbsp;
        <a href="javascript:manufacturer_delete(<?=$item->id;?>);"><span class="glyphicon glyphicon-remove"></span></a>
      </td>
    </tr>
    <? endforeach;?>
  </table>
</div>
<?=in_bs_button('manufacturer_add', 'Добавить производителя');?>
<br />
<br />
<script>
function manufacturer_delete(id) {
  if(confirm("Вы действительно хотите удалить производителя?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/config/manufacturer/del", {id:id}, function(data) {
      if (data) {
        alert(data);
      }
      config_manufacturer();
    });
  }
}

function manufacturer_full(id) {
  modal("/admin/config/manufacturer/edit/"+id, 100, function() {
    config_manufacturer();
  });
}

$(function(){
  
  $("#manufacturer_add").click(function() {
    modal("/admin/config/manufacturer/add", 100, function() {
      config_manufacturer();
    });
  });
  
});

</script>