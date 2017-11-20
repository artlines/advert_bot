<form action="/admin/config/filters/edit/<?=$filter->id;?>" method="post" class="align-left over-hide-x" id="filter-edit">
  <div>
    Наименование<br />
    <?=in_text('name', $filter->name);?>
  </div>
  
  <hr />
  Значения
  <div class="values">
  <? foreach ($filter->values as $value):?>
    <div class="row row-value">
      <div class="col-md-6"><?=in_text('values[]', $value->value, ['width' => '100%']);?></div>
      <div class="col-md-1">
        <div class="row">
          <a href="javascript:void(0);" class="add-filter-value"><span class="glyphicon glyphicon-plus"></span></a>
          <a href="javascript:void(0);" class="del-filter-value"><span class="glyphicon glyphicon-minus"></span></a>
        </div>
      </div>
    </div>
  <? endforeach;?>
  </div>
  
  <hr />
  
  <?=in_bs_button('save-button', 'Сохранить', ['type' => 'submit']);?>
</form>

<script>
$(function() {
  $("#filter-edit").submit(function() {
    $.post($(this).attr("action"), $(this).serialize(), function (data) {
      if (data.err == '0') {
        alert('Успешно');
      }
      else if (data.err > 0) {
        alert(data.err_str);
      }
      else {
        alert(data);  
      }
    }, 'json');
    return false;
  });
    
  $(".add-filter-value").on('click', function() {
    $(this).closest('div.row-value').clone(true).appendTo('#filter-edit > div.values');
  });
  
  $(".del-filter-value").on('click', function() {
    $(this).closest('div.row-value').remove();
  });
});
</script>