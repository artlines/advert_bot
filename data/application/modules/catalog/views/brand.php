<? if ($brandInfo->pic <> ''): ?>
<div class="brand_pic">
  <img src="<?=$brandInfo->pic;?>" width="150px" />
</div>
<? endif;?>
<?=htmlspecialchars_decode(nl2br($brandInfo->description));?>

<hr class="clear" />

<div id="catalog_loader"></div>

<hr class="clear" />

<div class="all_product" align="right">
  <a href="/catalog/category/param/brand/<?=$brandInfo->id;?>" class="color_683f8c underline">Все товары брэнда</a>
</div>
<br />

<script type="text/javascript">
$(function() {

  $("#catalog_loader").html(Loader);
  $("#catalog_loader").load("/catalog/brandPage/<?=$brandInfo->id;?>");
    
});
</script>
