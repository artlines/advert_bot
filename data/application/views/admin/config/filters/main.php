<div id="filters-shops"></div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3>Фильтры товаров каталога</h3>
  </div>

  <table class="table" id="table_list">
    <tr>
      <th>ID</th>
      <th>Наименование</th>
      <th>Действия</th>
    </tr>
    <? foreach ((array)$items as $key => $item):?>
    <tr>
      <td align="center"><?=$item->id;?></td>
      <td align="left"><?=$item->name;?></td>
      <td align="center">
        <a href="javascript:filter_edit(<?=$item->id;?>);">  <span class="glyphicon glyphicon-edit"></span>   </a>
        <a href="javascript:filter_delete(<?=$item->id;?>);"><span class="glyphicon glyphicon-remove"></span> </a>
      </td>
    </tr>
    <? endforeach;?>
  </table>
</div>

<?=in_bs_button('add_button', 'Добавить', array('icon' => 'plus'));?>

<br /><br />

<script>
var selected_id = 0;

function filter_delete(id) {
  if (confirm("Вы действительно хотите удалить?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/config/filters/del", {id: id}, function(data) {
      if (data) {
        alert(data);
      }
      config('filters');
    });
  }
}

/**
 * редактирование описания
 */
function filter_edit(id) {
  if (!id) {
    return;
  }
  modalUI("/admin/config/filters/edit/" + id);
  selected_id = id;
}

/**
 * нажатие кнопки "Сохранить"
 */
function filters_edit_save() {
  if (!selected_id) {
    return;
  }
  var save = {
    id:   selected_id,
    name: $("#edit_name").val(),
  };
  $.post_ajax_json("/admin/config/filters/edit/", save, function () {
    config('filters');
  });
  return;
}

$(function(){
  
  $("#add_button").click(function() {
    modal("/admin/config/filters/add", 100, function() {
      config('filters');
    });
  });
  
});
</script>