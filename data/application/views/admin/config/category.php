<div class="panel panel-default">
  <div class="panel-heading">
    <h3>Категории</h3>
  </div>

  <table class="table">
    <tr>
      <th>ID</th>
      <th>Наименование</th>
      <th>Действия</th>
    </tr>
    <? foreach ((array)$categories as $key => $item):?>
    <tr>
      <td align="center"><?=$item->id;?></td>
      <td align="left">
        <?php for ($i = 0; $i <= 4 * ($item->level - 1); $i++):?>&nbsp;<?php endfor;?>
        <?=$item->name;?>
      </td>
      <td align="center">
        <a href='javascript:addCatg(<?=$item->id;?>);'> <span class='glyphicon glyphicon-plus' title="Добавить"></span></a>
        <a href='javascript:editCatg(<?=$item->id;?>);'><span class='glyphicon glyphicon-edit' title="Общие параметры"></span></a>
        <a href='javascript:editCatgText(<?=$item->id;?>);'><span class='glyphicon glyphicon-text-width' title="Текст раздела"></span></a>
        <a href='javascript:editCatgOptions(<?=$item->id;?>);'><span class='glyphicon glyphicon-cog' title="Дополнительные параметры категории"></span></a>
        <a href='javascript:delCatg(<?=$item->id;?>);'> <span class='glyphicon glyphicon-remove' title="Удалить"></span></a>
      </td>
    </tr>
    <? endforeach;?>
  </table>
</div>

<?=in_bs_button('category_add', 'Добавить', array('icon' => 'plus'));?>

<div id="category-text"></div>


<script>
$(function() {
  
  
  $("#category_add").click(function() {
    addCatg(0);
  });
  
});

function addCatg(parent_id) {
  modal("/admin/config/category/add/"+parent_id, 100, function() {
    config_category();
  });
}

function editCatg(id) {
  modal("/admin/config/category/edit/"+id, 100, function() {
    config_category();
  });
}

function delCatg(id) {
  if(confirm("Вы действительно хотите удалить категорию?")) {
    $("#big_right").html(Loader);
    $("#big_right").load("/admin/config/category/del", {id:id}, function(data) {
      if (data) {
        alert(data);
      }
      config_category();
    });
  }
}

function editCatgText(id) {
  $("#category-text").html(Loader);
  $("#category-text").load("/admin/config/category/edit-text/" + id);
}

function editCatgOptions(id) {
  $("#category-text").html(Loader);
  $("#category-text").load("/admin/config/category/edit_options/" + id);
}
</script>