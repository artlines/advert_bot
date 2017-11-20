<h4>Каталог</h4>

<div class="input-group" style="margin-bottom:20px;">
  <span class="input-group-btn">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
      Опции<span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="#" id="newTovarLink">Добавить товар</a></li>
      <? if (USE_MASS_TOVAR_ADD):?>
      <li><a href="javascript:modal('/admin/catalog/newTovarList');">Добавить список товаров</a></li>
      <li><a href="javascript:modal('/admin/catalog/newTovarImages');">Изображения(<?=(int)$cnt;?>)</a></li>
      <li><a href="javascript:clearCatalog();">Очистить каталог</a></li>
      <? endif;?>
    </ul>
  </span>
  <input type="text" name="find" id="find" class="form-control" placeholder="Поиск" />
</div>


<div id="res"></div>

<script>
$(function() {
  
  $("#find").keyup(function() {
    findTovar();
  });

  $('#small_left').css('width','40%');
  $('#big_right').css('width','45%');
  $('#big_right').css('left','48%');
  findTovar();
  
  $("#newTovarLink").click(function() {
    modal('/admin/catalog/newTovar', 400, function() {
      if (typeof(tovar_id) != 'undefined') {
        loadTovar(tovar_id);
        findTovar();
      }
    });
  });
  
});

function findTovar() {
  $('#res').html(Loader);
  $('#res').load("/admin/catalog/list_all", {value:$("#find").val()});
}

function loadTovar(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/catalog/full/"+id);
  location.href = '#';
}

/**
 * Очистка каталога
 */
function clearCatalog() {
  if (!confirm("Вы действительно хотите очистить каталог? Действие нельзя обратить.")) {
    return;
  }
  $.post("/admin/catalog/clearCatalog", {}, function() {
    findTovar();
  });
  return;
}
</script>