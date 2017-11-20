<div class="row-20"></div>
<form class="row button-group" id="product-form">
  <div class="col-md-8 col-sm-8 col-xs-8">
    <?=in_hidden('id', $product->id);?>
    <?=in_hidden('prod-count', 1);?>
    <? if ($product->type == 'weight'):?>
      <input type="text" placeholder="Количество, кг" name="prod-count" id="prod-count" class="form-control product-weight">
    <? else:?>
      <?=in_hidden('prod-count', 1);?>
      <ul class="list-inline product-weight">
        <? foreach ($product->variants as $key => $variant):?>
        <? if (!$variant->name) continue;?>
        <li>
          <input type="radio" id="weight-<?=$key;?>" class="weightcontrol" name="variant" value="<?=$variant->name;?>" <? if (!$key):?>checked<? endif;?> />
          <label for="weight-<?=$key;?>"><?=$variant->name;?></label>
        </li>
        <? endforeach;?>
      </ul>
    <? endif;?>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-4 text-right">
    <!-- <h3 class="shop-product-title">Цена</h3> -->
    <ul class="list-inline shop-product-prices">
      <li class="shop-red">
        <? if ($product->dop[PRODUCT_FIELD_SALE_PRICE]->value):?>
        <div class="line-through"><?=$product->dop[PRODUCT_FIELD_SALE_PRICE]->value;?>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></div>
        <? endif;?>
        <span class="price variant-price">
          <? if ($product->price > 0):?>
            <?=number_format($product->price, 0, '.', ' ');?>
            <i class="fa fa-rub" aria-hidden="true"></i>
          <? else:?>
            <span class="no-price">Цена по запросу</span>
          <? endif;?>
        </span>
      </li>
    </ul>
    <!--/end shop product prices-->
    <!-- <h3 class="shop-product-title">&nbsp;</h3> id="to-cart-button" -->
    <button type="button" class="btn-u btn-u-sea-shop btn-u-lg" data-target="#call-modal" data-toggle="modal" data-product="<?=$product->name;?>">
      <i class="fa fa-shopping-cart"></i>
      Заказать
    </button>
    <!--/end product button-->
  </div>
</form>
