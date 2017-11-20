<div id="small_left">

  <div class="panel panel-default">
    <div class="panel-heading"><?=$content_type->name;?></div>
    <table class="table">
    <? foreach((array)$razdel as $key => $item):?>
      <tr id="razdel<?=$item->id;?>">
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="70%" class="root_razdel" razdel-id="<?=$item->id;?>">
          <?=$item->name;?>
        </td>
        <td width="10%"><a href="javascript:void(0);" onClick="edit_razdel(<?=$item->id;?>)">
          <span class="glyphicon glyphicon-edit"></span>
        </td>
        <td width="10%"><a href="javascript:void(0);" onClick="del_razdel(<?=$item->id;?>)">
          <span class="glyphicon glyphicon-remove"></span>
        </td>
      </tr>
    <? endforeach;?>
    </table>
    <?=in_bs_button('add_razdel', 'Добавить', array(
      'icon'    => 'plus', 
      'align'   => 'right', 
      'action'  => "add_razdel({$content_type->id})"
    ));?>
  </div>
</div>
<div id="big_right"></div>

<script>
function add_razdel(type) {
  modal('/admin/razdel/add/' + type);
}

function del_razdel(id) {
  if( confirm("Вы действительно хотите удалить данный раздел?") ) {
    $("#big_right").html(Loader);
    $.post_ajax_json("/admin/razdel/del/"+id, {}, function(data) {
      location.reload();
    });
  }
}

function edit_razdel(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/razdel/detail/"+id);
}

$(".root_razdel").each(function(index, value) {
  var razdel_id = $(this).attr('razdel-id');
  $.post("/admin/razdel/sub/" + razdel_id, {}, function(data) {
    $("#razdel" + razdel_id).after(data);
  });
});
</script>