<div id="small_left">
  <h4>SEO</h4>
  Введите страницу<br />
  <?=in_text('seo_page', '', array('width' => '100%'));?>
</div>

<div id="big_right"></div>

<script>
var seo_page_text_string = '';

$(function () {

  $("#seo_page").keyup(function() {
    if (seo_page_text_string != $("#seo_page").val()) {
      seo_page_text_string = $("#seo_page").val();
      showPage();
    }
  });

});

/**
 *
 */
function showPage() {
    if (typeof CKEDITOR !== 'undefined'){
      CKEDITOR.instances['textHTML'].destroy(true);
      CKEDITOR.instances['footerTextHTML'].destroy(true);
    }
    $("#big_right").html(Loader);
  $("#big_right").load("/admin/seo/showURL", {url: $("#seo_page").val()});
}
</script>