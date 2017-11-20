<script>
var varParams     = <?=json_encode($product->varParams);?>;
var varCount      = <?=count($product->varParams);?>;
var price         = <?=$product->price;?>;
var productName   = '<?=$product->name;?>';
var varPhotos     = <?=json_encode($varPhoto);?>;
var firstVariant  = <?=json_encode(current($product->varParams));?>;

function selectVariant(value) {
  variant = varParams[value];
  if (variant.price == '0') {
    value = '<span class="no-price">Цена по запросу</span>';
  }
  else {
    value = variant.price_format + ' <i class="fa fa-rub " aria-hidden="true"></i>'
  }
  $('.variant-price').html(value);
  $('#shop-product-full').find('.vendor-code').html(variant.vendor_code);
  productSlide(varPhotos[variant.name]);
}

function initVariant() {
  if (varCount == 1) {
    return;
  }
  selectVariant(firstVariant.name);
}

$(function() {
  
  setTimeout(function() {initVariant();}, 300);
  
  $('.weightcontrol').on("click", function () {
    selectVariant($(this).val());
  });
  
  $('.shop-product').find('.product-weight').keyup(function() {
    value = parseFloat($(this).val());
    if (value <= 0) {
      return;
    }
    $(".variant-price").html(value * price);
  });
  
  $("#to-cart-button").click(function() {
    $("#to-cart-button").hide();
    count = $('#prod-count').val();
    if (!count) {
      $("#to-cart-button").show();
      return showMessage('Не выбрано количество');
    }
    $.post("/catalog/zakaz/addToZakaz", $("#product-form").serialize(), function () {
      $("#to-cart-button").show();
      showMessage("Товар добавлен в корзину" + 
        '<hr class="clear" /><div class="row-20"></div>'+
        '<div class="col-md-6"><button type="button" class="btn-u btn-brd btn-brd-hover rounded-3x btn-u-yellow btn-u-xs go-cart">'+
        '<i class="fa fa-caret-right"></i> Оформить заказ</button></div>' +
        '<div class="col-md-6"><button type="button" class="btn-u btn-brd btn-brd-hover rounded-3x btn-u-light-green btn-u-xs" ' +
        'data-dismiss="modal">Продолжить покупки</button></div>'+
        '<hr class="clear" /><div class="row-20"></div>');
      loadCart();
    });
  });
  
});
</script>