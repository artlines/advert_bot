<div id="small_left">
  
  <table width="100%" cellpadding="0" cellspacing="7" border="0">
    
    <hr />
    
    <? foreach((array)$list as $key => $item):?>
  
    <tr>
      <td width="10%" valign="center"><img src="/images/link_arrow2.gif"></td>
      <td width="70%">
        <a href="javascript:edit(<?=$item->id;?>);" title="<?=$item->name;?>">
          <?=$item->name;?>
        </a>
      </td>
      <td width="10%">
        <a href="javascript:edit(<?=$item->id;?>);">
          <img src="/images/edit2.jpg" border=0 title="Редактировать" />
        </a>
      </td>
      <td width="10%">
        <a href="javascript:del(<?=$item->id;?>);">
          <img src="/images/delete2.jpg" border=0 title="Удалить" />
        </a>
      </td>
    </tr>
    
    <? endforeach;?>
  
  </table>
  
  <hr />
  
  <?=in_ui_button('add_item', 'Добавить', array('icon' => 'plus', 'align' => 'right', 'action' => 'add()'));?>
  
</div>

<div id="big_right"></div>

<script>
function add() {
  modal('/admin/cmodules/<?=$this->module;?>/add');
}

function del(id) {
  if( confirm("Вы действительно хотите удалить?") ) {
    $("#big_right").html(Loader);
    $.post_ajax_json("/admin/cmodules/<?=$this->module;?>/del/"+id, {}, function(data) {
      location.reload();
    });
  }
}

function edit(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/cmodules/<?=$this->module;?>/detail/"+id);
}

</script>