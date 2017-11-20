<hr class="clear" />
<div class="row-20"></div>
<h4>Описание для "<?=$values->name;?>"</h4>
<div class="row">
  <div class="col-md-12">
    <?=$editor;?>
  </div>
</div>
<div class="row-20"></div>
<div class="row">
  <div class="col-md-12">
    <?=in_bs_button('save-category', 'Сохранить', array('align' => 'right'));?>
  </div>
</div>
<div class="row-20"></div>
<script>
var id = '<?=$id;?>';
$(function() {
  $("#save-category").click(function() {
    var text = CKEDITOR.instances['text'].getData();
    $.post("/admin/config/category/edit-text/" + id, {text: text}, function(data) {
      $("#category-text").html();
      if (data == '') {
        alert("Успешно сохранено");
      }
      else {
        alert(data);
      }
    });
  });
});
</script>