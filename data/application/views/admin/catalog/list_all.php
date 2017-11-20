<div class="panel panel-default">
  <div class="panel-heading">Всего товаров в каталоге: <?=$list['count'];?></div>
  <table class="table">
    <? if (!empty($list['search'])):?>
      <? foreach($list['search'] as $key => $item):?>
      <tr>
        <td width="80%">
          <?=$item->name;?> / Артикул: <?=$item->code;?><br>
          <b>Код: </b><?=$item->id;?>&nbsp;&nbsp;&nbsp;<b>Цена: </b><?=$item->def_price;?>
          <br>
          <b>Производитель: </b><?=$item->manufacturer;?>
        </td>
        <td width="20%" align="right">
          <a href="javascript:loadTovar('<?=$item->id;?>');" border="0" title="Редактировать"> 
            <span class="glyphicon glyphicon-edit"></span>
          </a>
          &nbsp;
          <a href="javascript:del_tovar('<?=$item->id;?>');"  border="0" title="Удалить">       
            <span class="glyphicon glyphicon-remove"></span>
          </a>
        </td>
      </tr>
      <? endforeach;?>
    <? endif;?>
  </table>
</div>
      
<ul class="pagination pagination-sm">
  <? if ($list['pages']>1):?>
    <? for ($i=1 ; $i<=$list['pages'] ; $i++):?>
      <?
        $print = 0;
        if ($i==1 || $i==$list['pages']) $print = 1; 
        elseif ($i<7 && $list['page']<8) $print = 1; 
        elseif (($i+7)>$list['pages'] && ($list['pages']-$list['page'])<8) $print = 1; 
        elseif (($list['page']+3)>$i && ($list['page']-3)<$i) $print = 1;
        if ($print==1 && ($i-$last_print)>1) echo '<li><a href="">...</a></li>';
      ?>
      <? if ($print==1):?>
        <li <? if ($i==$list['page']):?>class="active"<? endif;?>>
          <a href="javascript:get_page(<?=$i;?>);" id="pager<?=$i;?>"><?=$i;?></a>
        </li>
        <? $last_print = $i;?>
      <? endif;?>
    <? endfor;?>
  <? endif;?>
    
  <? if(empty($list['search'])):?>
    Товаров по заданным критериям поиска не найдено.
  <? endif;?>
  
</ul>
  
<br /><br /><br />

<script>
function del_tovar(id) {
  if (confirm('Действительно хотите удалить товар?')) {
    $.post_ajax_json("/admin/catalog/del/"+id, {}, function () {
      $('#res').load("/admin/catalog/list_all", {value:$("#find").val()});
    });
  }
}

function get_page(id) {
  $("#res").html(Loader);
  $("#res").load("/admin/catalog/list_all/"+id, {value:$("#find").val()});
}
</script>
