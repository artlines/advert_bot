<hr class="clear" />
<h4>Дополнительные настройки "<?=$category->name;?>"</h4>
<?=$fields;?>
<div id="category-options-data"></div>
<div class="row-20"></div>

<script>
  var selected_field = '';
  $(function () {
    $("#field_id").change(function() {
      selected_field = $(this).val();
      $("#category-options-data").load("/admin/config/category/edit_option/<?=$category->id;?>/", {field: $(this).val()});
    });
  });
  
  function saveCategoryValue() {
    if (selected_field == '') {
      return;
    }
    save = {
      field:  selected_field,
      text:   CKEDITOR.instances['option-text'].getData()
    };
    $.post_ajax_json("/admin/config/category/save_option/<?=$category->id;?>/", save, function () {
      alert("Успешно сохранено!");
    });
    
  }
</script>