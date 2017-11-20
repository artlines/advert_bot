<div id="edit_shops"></div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3>Адреса магазинов</h3>
  </div>

  <table class="table" id="table_list">
    <tr>
      <th>ID</th>
      <th>Город</th>
      <th>Наименование</th>
      <th>Действия</th>
    </tr>
    <? foreach ($shops as $key => $item):?>
    <tr>
      <td align="center"><?=$item->id;?></td>
      <td align="left"><?=$item->city_name;?></td>
      <td align="left"><?=$item->addr;?></td>
      <td align="center">
        <a href="javascript:shop_edit(<?=$item->id;?>);">  <span class="glyphicon glyphicon-edit"></span>   </a>
        <a href="javascript:shop_delete(<?=$item->id;?>);"><span class="glyphicon glyphicon-remove"></span> </a>
      </td>
    </tr>
    <? endforeach;?>
  </table>
</div>

<?=in_bs_button('add_button', 'Добавить магазин', array('icon' => 'plus'));?>

<br /><br />

<script>
var selected_id = 0;

function shop_delete(id) {
  if (confirm("Вы действительно хотите удалить?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/config/shops/del", {id: id}, function(data) {
      if (data) {
        alert(data);
      }
      config('shops');
    });
  }
}

/**
 * редактирование описания
 */
function shop_edit(id) {
  if (!id) {
    return;
  }
  $("#edit_shops").html(Loader);
  location.href = '#';
  $("#edit_shops").load("/admin/config/shops/edit/", {id: id});
  selected_id = id;
  console.log(selected_id);
}

/**
 * нажатие кнопки "Сохранить"
 */
function shop_edit_save() {
  if (!selected_id) {
    return;
  }
  var save = {
    id:           selected_id,
    description:  CKEDITOR.instances['text'].getData(),
    addr:         $("#edit_addr").val(),
    name:         $("#edit_name").val(),
    city_id:      $("#edit_city_id").val(),
    shop_tel:     $("#edit_shop_tel").val(),
    is_shop:      $("#edit_is_shop").val()
  }
  $.post_ajax_json("/admin/config/shops/edit/", save, function () {
    config('shops');
  });
  return;
}

$(function(){
  
  $("#add_button").click(function() {
    modal("/admin/config/shops/add", 100, function() {
      config('shops');
    });
  });
  
});
</script>