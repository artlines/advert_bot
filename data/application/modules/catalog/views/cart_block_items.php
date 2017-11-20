<span class="badge rounded-2x badge-orange">
   <?=$tovar_count;?> товар(ов)
</span>
<i class="basket fa fa-shopping-cart basket-btn"></i>
<!-- Open Cart -->
<div class="cart-open">
  В вашей корзине <?=$tovar_count;?> товар(ов).<br />
  На сумму <?=$tovar_summa;?> <i class="fa fa-rub" aria-hidden="true"></i>
  <hr>
  <button class="btn-u btn-brd btn-brd-hover rounded-3x btn-u-yellow btn-u-xs go-cart" type="button">
    <i class="fa fa-caret-right"></i> Оформить заказ
  </button>
  <div class="margin-bottom-20"></div>
  <? foreach ((array)$pay_table as $key => $item):?>
  <div class="row">
    <div class="col-sm-7 align-left" title="<?=$item->name;?>">
      <a href="/catalog/tovarFull/<?=$item->id;?>"><?=$item->name;?></a>
    </div>
    <div class="col-sm-1"><?=$item->count;?></div>
    <div class="col-sm-3"><?=$item->price;?> р.</div>
  </div>
  <? endforeach;?>
</div>
<!-- ./ Open Cart -->