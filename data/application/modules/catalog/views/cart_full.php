<div id="zakazFullInfo">
  <table class="table" id="cart_table">
    <tbody>
      <tr>
        <th width="5%">Nпп</th>
        <!--th>Артикул</th-->
        <th width="50%">Название</th>
        <th>К-во</th>
        <th class="align-right">Цена</th>
        <th class="align-right">Сумма</th>
        <!--th>Примечание</th-->
        <th>&nbsp;</th>
      </tr>
      <? $i = 1;?>
      <? foreach ($pay_table as $key => $item):?>
      <tr>
        <td align="center">  <?=$i++;?></td>
        <!--td align="center" width="15%"> <?=$item->code;?></td-->
        <td align="left">   
          <a href="/catalog/tovarFull/<?=$item->id;?>-<?=$item->translite;?>">
            <?=$item->name . ($item->variant ? ', ' : '') . $item->variant;?>
          </a>
        </td>
        <td class="align-right">
          <div class="tovar_count_block input-group">
            <span class="glyphicon glyphicon-minus input-group-addon" attr-id="<?=$key;?>"></span>
            <?=in_text($item->id, $item->count, '50px', 'tovarCount form-control', $key);?>
            <span class="glyphicon glyphicon-plus input-group-addon" attr-id="<?=$key;?>"></span>
          </div>
        </td>
        <td class="align-right">   
          <?=round($item->price,2);?>
        </td>
        <td class="align-right"  id="sum<?=$key;?>">
          <?=round($item->price * $item->count, 2);?>
        </td>
        <!--td align="left">             
          <?=in_text('comment['.$key.']', '', '95%', 'zakaz_comment');?>
        </td-->
        <td style="text-align: center;" id="del<?=$key;?>">
          <a href="javascript: tovar_delete('<?=$key;?>');">
            <img border="0" src="/images/base/delete.gif">
          </a>
        </td>
      </tr>
      <? $summa += $item->price * $item->count; ?>
      <? endforeach;?>
      <tr>
        <th class="align-right" colspan="4">
          Сумма заказа:
        </th>
        <th class="align-right" id="zakaz_summa_itog"><?=round($summa, 2);?></th>
        <th>&nbsp;</th>
      </tr>
      <tr class="zakaz_cost_dop_info">
        <th class="align-right"  colspan="4">
          Стоимость доставки:
          <? $delivery_flag = ($summa < config('courier_free_limit'));?>
        </th>
        <th class="align-right"  id="zakaz_summa_move"><?=config('courier_cost') * $delivery_flag;?></th>
        <th>&nbsp;</th>
      </tr>
      <tr class="zakaz_cost_dop_info">
        <th class="align-right"  colspan="4">
          Итого:
        </th>
        <th class="align-right"  id="zakaz_summa"><?=(round($summa, 2) + config('courier_cost') * $delivery_flag);?></th>
        <th>&nbsp;</th>
      </tr>
    </tbody>
  </table>
  <div class="align-right"  id="zakaz_button"><?=in_bs_button('send_zakaz', 'Оформить заказ');?></div>
  <br />
  <div class="align-right"  id="zakaz_contacts"></div>
</div>

<script>
var tovarCount = {
<?
$tovarCount = array();
foreach ($pay_table as $key => $item) {
  $tovarCount[] = $key . ':' . $item->count;
}
echo implode(",\n", $tovarCount);
?>
};
var config_courier_cost = <?=(float)config('courier_cost');?>;
var config_courier_free_limit = <?=(float)config('courier_free_limit');?>;
$(function() {
  /////////////////////////////////////////
  $("#send_zakaz").click(function() {
    $("#zakaz_button").hide();
    $("#zakaz_contacts").html(loader_small);
    $("#zakaz_contacts").load('/catalog/cart/contactInfo', {}, function() {
      $(".zakaz_cost_dop_info").show();
    });
  });
  
  /////////////////////////////////////////
  $(".tovarCount").on("keyup", function(event) {
    countRecalculate($(this));
  });
  
  $(".tovar_count_block").find(".glyphicon-plus").click(function() {
    tovar_id = $(this).attr('attr-id');
    var object_id = $("#" + tovar_id);
    var count = parseInt(object_id.val()) + 1;
    object_id.val(count);
    countRecalculate(object_id);
  });
  
  $(".tovar_count_block").find(".glyphicon-minus").click(function() {
    tovar_id = $(this).attr('attr-id');
    console.log(tovar_id);
    var object_id = $("#" + tovar_id);
    var count = parseInt(object_id.val()) - 1;
    if (count < 1) {
      count = 1;
    }
    object_id.val(count);
    countRecalculate(object_id);
  });
  
});

/**
 * Пересчет количества товара
 */
function countRecalculate(object_id) {
  var id = $(object_id).attr('id');
  var text_cnt = $(object_id).val();
  if (text_cnt == '') {
    return;
  }
  var cnt = parseInt(text_cnt) * 1;

  if (cnt <= 0 || isNaN(cnt)) {
    $(object_id).val(1);
    cnt = 1;
  }
  if (text_cnt != cnt) {
    $(object_id).val(cnt);
  }
  tovarCount[id] = cnt;
  $("#sum"+id).html(loader_small);
  $("#zakaz_summa_itog").html(loader_small);
  $.post('/catalog/cart/itemChange/', {id:id, cnt:cnt}, function (data) {
    $("#sum"+id).html(numberFormatDefault(data.summa));
    var current_courier_cost = config_courier_cost * (data.summa_itog < config_courier_free_limit);
    $("#zakaz_summa_itog").html(numberFormatDefault(data.summa_itog));
    $("#zakaz_summa_move").html(numberFormatDefault(current_courier_cost));
    $("#zakaz_summa").html(numberFormatDefault(data.summa_itog + current_courier_cost));
    loadCart();
  }, "json");
}

/////////////////////////////////////////
function tovar_delete(id) {
  if ( confirm("Вы действительно хотите удалить данную позицию?") ) {
    $("#del" + id).html(loader_small);
    $.post("/catalog/cart/itemChange/", {id:id, cnt:0}, function() {
      location.href = '/catalog/cart/full';
    });
  }
}
</script>
