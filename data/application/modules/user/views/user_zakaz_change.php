<form id="form" method="post" action="/user/zakaz/change/<?=$id;?>/save">
  <table width="100%" id="table_list" cellspacing="1px" style="margin: 10px;">
    <tr>
      <th>Nпп</th>
      <th>Артикул</th>
      <th>Название</th>
      <th>К-во</th>
      <th>Цена</th>
      <th>Сумма</th>
      <th>Прим.</th>
      <th>&nbsp;</th>
    </tr>
  <? $i = 1;?>
  <? foreach($detail as $v => $k):?>
    <tr>
      <td align="center" width="5%"><?=$i++;?></td>
      <td align="center" width="15%"><?=$k['articul'];?></td>
      <td align="left"  width="35%"><?=$k['name'];?>; <?=$k['proizv'];?></td>
      <td align="right" width="5%"><?=in_text('detali['.$k['detali_id'].']', $k['cnt'], '40px', 'book_count', $k['detali_id']);?></td>
      <td align="right" width="5%"><?=round($k['price'], 2);?></td>
      <td align="right" id="sum<?=$k['detali_id'];?>"><?=round($k['price']*$k['cnt'], 2);?></td>
      <td align="left" width="25%"><?=in_text('comment['.$k['detali_id'].']', $k['comment'], '150px', 'zakaz_comment');?></td>
      <td style="text-align: center;" width="5%"><?=button_del("/user/zakaz/change/{$id}/del_item/{$k['detali_id']}");?></td>
    </tr>
  <? $summa += $k['price']*$k['cnt']; ?>
  <? endforeach;?>
    <tr>
      <th align="right" colspan="5">
        <b>Сумма заказа:</b></th>
      <th align="right" id="zakaz_summa_itog"><b><?=round($summa, 2);?></b></th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
  </table>
  <input type="submit" name="recalc" id="recalc" class="button" value="Сохранить изменения" style="margin-left:10px;" />
</form>
<br />
<h2 style="font-size: 12px;">Добавить товар</h2>
<div style="margin-left:10px;">
Название или код<br />
<?=in_text('new_tovar', '', '550px');?><br /><br />
</div>
<div id="new_tovar_list" style="height: 150px;"></div>
<div align="right"><a href="/user/zakaz"><img src="/img/arrow_up.gif" style="border:1px;" />&nbsp;Все заказы</a></div>

<script>
$(function() {
  $("#new_tovar").keyup(function() {
    var new_tovar_list_html = $("#new_tovar_list").html();
    if (new_tovar_list_html=='')
      $("#new_tovar_list").html(Loader);
    $("#new_tovar_list").load("/user/zakaz/change/<?=$id;?>/find_for_add", {search:$("#new_tovar").val()});
  });
  
  $(".book_count").keyup(function() {
    var id = $(this).attr('id');
    var cnt = $(this).val() * 1;
    if (cnt<=0 || !cnt) {
      $(this).val(1);
      cnt = 1;
    }
    $("#sum"+id).html(loader_small);
    $("#zakaz_summa_itog").html(loader_small);
    $.post('/user/zakaz/zakaz_item_ch/<?=$id;?>/'+id+'/'+cnt, {}, function (data) {
      data = JSON.parse(data);
      $("#sum"+id).html(data.summa);
      $("#zakaz_summa_itog").html(data.summa_itog);
    });
  });
  
});
</script>