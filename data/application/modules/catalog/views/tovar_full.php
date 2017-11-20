<div id="tovar_full">

  <div class="left">
  
    <div id="tovar_photos">
      <div class="main_photo">
        <? if ($tovar->photo_main->big_file):?>
          <a href="<?=$tovar->photo_main->big_file;?>" onclick="return hs.expand(this)" class="none">
            <img class="main" src="<?=$tovar->photo_main->big_file;?>" />
          </a>
        <? else:?>
          <img class="image_border nophoto" src="/images/base/nophoto.jpg" />
        <? endif;?>
      </div>
      
      <div class="small_thumb">
        <ul>
          <? foreach ($tovar->photo as $item):?>
            <li>
              <a href="<?=$item->big_file;?>" class="highslide" onclick="return hs.expand(this)">
                <img src="<?=$item->thumb_file;?>" title="<?=$item->comment;?>. Нажмите для просмотра" class="view" />
              </a>
              <? if (ADMIN_ID):?>
                <a href="/catalog/tovarFull/<?=$tovar->id;?>/photoDel/<?=$item->id;?>">
                  <img src="/images/base/delete2.jpg" />
                </a>
              <? endif;?>
            </li>
          <? endforeach;?>
        </ul>
        <hr class="clear" />
      </div>
        
      <? if (ADMIN_ID):?>
        <form action="/catalog/tovarFull/<?=$tovar->id;?>/photoAdd" method="post" enctype="multipart/form-data" id="photoAdd">

          Файл<br />
          <?=in_file('photo');?>    <br />
          
          Основное фото<br />
          <?=in_check('is_main', 0, 1);?> <br />
          
          Комментарий<br />
          <?=in_text('comment');?>  <br />
          
          <?=in_hidden('id', $tovar->id);?>
          <?=in_ui_button('saveNewPhoto', 'Добавить фото', array('action' => '$(\'#photoAdd\').submit()'));?>
        </form>
      <? endif;?>
        
      <script type="text/javascript" src="http://yandex.st/share/share.js" charset="utf-8"></script>
      <div 
        class="yashare-auto-init" 
        data-yasharequickservices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,moikrug" 
        data-yasharetype="button" data-yasharel10n="ru">
      </div>
        
    </div>
  </div>
  
  <div class="right">
    <div id="tovar_full_info">
      
      <? if (MY_LEVEL == SITE_LEVEL_SHOP && TOVAR_INNER_NAV):?>
      <div id="tovarNavigation">
        <? if ($prevTovar):?>
        <div class="navLeft"><a href="/catalog/tovarFull/<?=$prevTovar;?>">&nbsp;</a></div>
        <? endif;?>
        <div class="navCount"><?=$curNavPos;?> из <?=$cntNavPos;?></div>
        <? if ($nextTovar):?>
        <div class="navRight"><a href="/catalog/tovarFull/<?=$nextTovar;?>">&nbsp;</a></div>
        <? endif;?>
      </div>
      <? endif;?>
        
      <div class="get_price_button">
        <? if (ADMIN_ID):?>
          <?=in_text('price', $tovar->price, array('width' => '70px'));?>руб
        <? else:?>
          <div class="price size16px"><?=$tovar->price;?> руб.</div>
        <? endif;?>
        <?=in_ui_button("to_cart_button", "Купить");?>
        <span id="to_cart_button_action_notify"></span>
      </div>
      
      <? if ($tovar->manufacturer_name):?>
        <div class="manufacturer">
          <a class="black" href="/catalog/brand/<?=$tovar->manufacturer_id;?>">
            <?=$tovar->manufacturer_name;?>
          </a>
        </div>
      <? endif;?>
      
      <div class="short_info">
        <? if ($tovar->code):?>
          <div>Артикул: <?=$tovar->code;?></div>
        <? endif;?>
        <? if ($tovar->code_1c && CODE_1C):?>
          <div>Код товара: <?=$tovar->code_1c;?></div>
        <? endif;?>
        <? if (ADMIN_ID && $tovar->parent_code <> ''):?>
          <div><a href="<?=$tovar->parent_code;?>" target="_blank">LINK</a></div>
        <? endif;?>
        <hr class="clear" />
        <hr />
      </div>
      
      <? if (ADMIN_ID):?>
        <div class="description">
          <?=in_textarea('description', $tovar->description, array('width' => '100%', 'height' => '200px'));?>
        </div>
        <?=in_ui_button('saveTovar', 'Сохранить');?>
      <? else:?>
        <div class="description"><?=nl2br($tovar->description);?></div>
      <? endif;?>
    </div>
  </div>
  <hr class="clear" />
</div>

<? if (MY_LEVEL == SITE_LEVEL_SHOP && TOVAR_SIMILAR_SHOW):?>
<h2>Похожие товары:</h2>
<div id="similarTovars"></div>
<? endif;?>

<div id="dialog_window" align="center"></div>

<script>
var id = '<?=$tovar->id;?>';

$(function() {

  $('#dialog_window').dialog({
    width:    800,
    height:   'auto',
    autoOpen: false,
    position: "center top+10%",
    show:     { effect: "toggle", duration: 500 }
  });

  // сохранить
  $("#saveTovar").click(function() {
    $.post_ajax_json("/catalog/tovarSave/" + id, {
      action:       'text',
      price:        $("#price").val(),
      description:  $("#description").val()
    }, function(){
      location.reload();
    });
  });
  
  // в корзину
  $("#to_cart_button").click(function() {
    var count = 1; //$("#tovar_count").val();
    $("#to_cart_button").hide();
    $.post("/catalog/zakaz/addToZakaz", {id:id, count:count}, function () {
      $("#to_cart_button").show();
      $("#to_cart_button_action_notify")
        .text("Товар добавлен в корзину")
        .addClass("ui-tooltip ui-corner-all ui-state-default")
        .css('display','inline').css('margin', '-7px 0 0 -200px')
        .show({
          effect: "fade"
        })
        .delay(1000)
        .hide({
          effect: "fade",
          duration: "slow"
        });
      loadCart();
    });
  });
  
  // запомнить
  $("#save_button").click(function() {
    $.post("/user/checkAuth", {}, function (data) {
      if (data == 1) {
        $("#save_button").hide();
        $.post("/catalog/saveTovarToUser", {id:id}, function () {
          $("#save_button").show();
          $("#to_cart_button_action_notify")
            .text("Товар запомнен")
            .addClass("ui-tooltip ui-corner-all ui-state-default")
            .css('display','inline').css('margin', '30px 0 0 -130px')
            .show({
              effect: "fade"
            })
            .delay(1000)
            .hide({
              effect: "fade",
              duration: "slow"
            });
        });
      }
      else {
        $('#dialog_window').dialog('open');
        $('#dialog_window').html(
          "Чтобы запомнить товар, пожалуйста, " +
          "<a href='/user/register'>зарегистрируйтесь</a>, либо <a href='javascript:showLoginWindow();'>авторизуйтесь</a>");
      }
    });
  });
  
  $("#tovar_buttons").find(".val_suffix").click(function() {
    var count = parseInt($("#tovar_count").val()) + 1;
    $("#tovar_count").val(count);
  });
  
  $("#tovar_buttons").find(".val_prefix").click(function() {
    var count = parseInt($("#tovar_count").val()) - 1;
    if (count < 1) {
      count = 1;
    }
    $("#tovar_count").val(count);
  });
  
  // похожие товары
  $("#similarTovars").html(Loader);
  $("#similarTovars").load("/catalog/findSimilarProducts/<?=$tovar->id;?>");
 
});

function openModal(href_link, file_type) {
  $('#dialog_window').dialog('open');
  $('#dialog_window').html(Loader);
  if (file_type == 'img') {
    $('#dialog_window').html("<img src='" + href_link + "' width='700px' />");
    return;
  }
  $('#dialog_window').load(href_link, {ajax: 1});
  return;
}
</script>