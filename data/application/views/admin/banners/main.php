<div id="small_left">

  <div class="panel panel-default">
    <div class="panel-heading">Баннеры</div>
    <table class="table">
  
      <? foreach($banners as $key => $item):?>
    
      <tr>
        <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
        <td width="70%">
          <a href="javascript:edit(<?=$item->id;?>);" title="<?=$item->name;?>">
            <?=$item->name;?>
          </a>
        </td>
        <td width="10%">
          <a href="javascript:edit(<?=$item->id;?>);">
            <span class="glyphicon glyphicon-edit"></span>
          </a>
        </td>
        <td width="10%">
          <a href="javascript:del(<?=$item->id;?>);">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
        </td>
      </tr>
      
      <? endforeach;?>
    
    </table>
  </div>
  
  <?=in_bs_button('add_banner', 'Добавить баннер', array('icon' => 'plus', 'align' => 'right', 'action' => 'add()'));?>
  
</div>

<div id="big_right"></div>

<script>
function add() {
  modal('/admin/banners/add');
}

function del(id) {
  if( confirm("Вы действительно хотите удалить баннер?") ) {
    $("#big_right").html(Loader);
    $.post_ajax_json("/admin/banners/del/"+id, {}, function(data) {
      location.reload();
    });
  }
}

function edit(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/banners/detail/"+id);
}

</script>