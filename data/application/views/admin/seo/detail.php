<div id="seoDetail">
  <h4>Страница "<?=$path;?>"</h4>

  Заголовок (title)<br />
  <?=in_text('title', $title, array('width' => '95%'));?><br />

  H1<br />
  <?=in_text('h1', $h1, array('width' => '95%'));?><br />

  Keywords<br />
  <?=in_text('keywords', $keywords, array('width' => '95%'));?><br />

  Description<br />
  <?=in_text('description', $description, array('width' => '95%'));?><br />
  <?=in_bs_button('savePage', 'Сохранить', array('align' => 'left'));?><br />
  
  <hr class="clear" />
  <br />
  Текст<br />
  <?=$editor;?><br /><br />

  Текст footer<br />
  <?=$editor_footer;?><br /><br />
  <?=in_hidden('path', $path);?>
  <?=in_hidden('text', '');?>
  <?=in_hidden('footer_text', '');?>
  <?=in_hidden('befor_text', '');?>
</div>

<script>
$(function() {
  $("#savePage").click(function() {
    var text        = CKEDITOR.instances['textHTML'].getData();
    var footer_text = CKEDITOR.instances['footerTextHTML'].getData();
    $("#befor_text").val(befor_text);
    $("#text").val(text);
    $("#footer_text").val(footer_text);
    $("#seoDetail").post_ajax_form("/admin/seo/saveURL", function() {
      showPage();
    });
  });
});
</script>