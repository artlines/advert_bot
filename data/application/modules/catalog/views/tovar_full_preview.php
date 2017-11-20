<div id="tovar_full">
  <table width="100%" border="0">
    <tr>
      <td id="tovar_photos">
        <? if ($tovar->photo_main->avg_file):?>
        <img class="image_border" src="<?=$tovar->photo_main->big_file;?>" width="344" />
        <? else:?>
          <img class="image_border" src="/images/catalog/nophoto.jpg" width="344" />
        <? endif;?>
        <ul class="small_thumb">
          <? foreach ($tovar->photo as $item):?>
            <li>
              <a href="<?=$item->big_file;?>" class="highslide" onclick="return hs.expand(this)">
                <img src="<?=$item->thumb_file;?>" width="77" title="<?=$item->comment;?>. Нажмите для просмотра" />
              </a>
            </li>
          <? endforeach;?>
        </ul>
      </td>
      <td id="tovar_full_info">
        <table border="0" cellspacing="5">
          <tr>
            <td colspan="2">
              <div class="price">
                <?=$tovar->price;?>р.
              </div>
              <div class="qty">
                &nbsp;X
                <label>
                <input id="qty" type="text" size="10" name="qty" value="1">
                </label>
              </div>
              <div class="button">
                <img width="112" height="26" alt="Купить" src="/images/main/button_add_to_cart.gif" onclick="buyTovar();">
              </div>
              <? if ($tovar->price<>$tovar->def_price):?>
              <div class="price">
                <span class="skidka"><?=$tovar->def_price;?>р.</span>
              </div>
              <? endif;?>
            </td>
          </tr>
          <tr>
            <td colspan="2"><hr /></td>
          </tr>
          <tr>
            <th>Артикул</th>
            <td><?=$tovar->code;?></td>
          </tr>
          <tr>
            <th>Производитель</th>
            <td><?=$tovar->manufacturer_name;?></td>
          </tr>
          <tr>
            <td colspan="2"><hr /></td>
          </tr>
          <tr>
            <td colspan="2"><?=$tovar->description;?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>

<div id="vk_comments" style="margin-top:20px;"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 10, width: "688", attach: "*"});
</script>

<script>
function buyTovar() {
  var id = '<?=$tovar->id;?>';
  var count = $("#qty").val();
  $("#tovar_full_info").find('.button').css('display', 'none');
  $.post("/catalog/zakaz/addToZakaz", {id:id, count:count}, function () {
    $("#tovar_full_info").find('.button').css('display', 'inline');
    loadCart();
  });
}
</script>