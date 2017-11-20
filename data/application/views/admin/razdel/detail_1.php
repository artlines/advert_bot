<h4>Редактирование раздела "<?=$razdel->name;?>"</h4>
<hr />
<table width="100%" border="0" class="params" cellpadding="5" cellspacing="0">
  <tr>
    <td>Название<br />
    <?=in_text('name', $razdel->name);?></td>
    <td>Название в меню<br />
    <?=in_text('menu_text', $razdel->menu_text);?></td>
    <td>Заголовок раздела<br />
    <?=in_text('title', $razdel->title);?></td>
  </tr>
  <tr>
    <td>
      Url<br />
      <?=in_text('url', $razdel->url);?>
    </td>
    <td>Модуль<br />
      <?=in_select('module_id', $modules, $razdel->module_id);?>
    </td>
    <td>Шаблон<br />
      <?=in_select('template_id', $template, $razdel->template_id);?>
    </td>
  </tr>
  <tr>
    <td>Приоритет<br />
      <?=in_text('priority', $razdel->priority);?>
    </td>
    <td>Ключевые слова (SEO)<br />
      <?=in_text('keywords', $razdel->keywords);?>
    </td>
    <td>Описание (SEO)<br />
      <?=in_text('description', $razdel->description);?>
    </td>
  </tr>
  <tr>
    <td>
      <?=in_check('in_menu', $razdel->in_menu);?>&nbsp;Показывать в меню
      
    </td>
    <td>
      <?=in_check('active', $razdel->active);?>&nbsp;Активен
    </td>
    <td>
      <?=in_check('in_footer_menu', $razdel->in_footer_menu);?>&nbsp;Footer-меню
    </td>
  </tr>
  <tr>
    <td colspan="3"><?=in_bs_button("save", "Сохранить", array('align' => 'left'));?></td>
  </tr>
</table>
<div id="razdel_text_div">
<h4>Текст</h4>
<?=$editor?>
</div>

<script>
var id = "<?=$razdel->id;?>";

$(function() {
  $("#save").click(function() {
    LoaderOn();
    $.post("/admin/razdel/detail/"+id, {
        action:       'save_common', 
        name:           $("#name").val(), 
        module_id:      $("#module_id").val(), 
        template_id:    $("#template_id").val(), 
        priority:       $("#priority").val(), 
        in_menu:        $("#in_menu").prop('checked'), 
        in_footer_menu: $("#in_footer_menu").prop('checked'), 
        active:         $("#active").prop('checked'), 
        text:           CKEDITOR.instances['text'].getData(),
        menu_text:      $("#menu_text").val(), 
        title:          $("#title").val(),
        url:            $("#url").val(),
        date:           $("#date").val(),
        keywords:       $("#keywords").val(),
        description:    $("#description").val()
      }, 
      function(data) {
        LoaderOff();
        if (data.err==0) {
          alert("Успешно сохранено!");
        } 
        else {
          alert("Ошибка сохранения! " + data.err_str);
        }
      },
      "json"
    );
  });
  
  $("#date").datepicker();
  $('#date').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $('#date').datepicker( "option", "firstDay", 1 );
  $('#date').datepicker( "option", "changeYear", true );
  $('#date').datepicker( "option", "duration", 'fast' );
  $("#date").val('<?=$razdel->date;?>');
  
  showTextDiv();
  
  $("#module_id").change(function() {
    showTextDiv();
  });
});

function showTextDiv() {
  module_id = $("#module_id").val();
  if (module_id==1) {
    $("#razdel_text_div").show();
  }
  else {
    $("#razdel_text_div").hide();
  }
}
</script>