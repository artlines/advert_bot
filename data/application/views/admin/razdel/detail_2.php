<h4>Редактирование раздела "<?=$razdel->name;?>"</h4>
<hr />
<table width="100%" border="0" class="params" cellpadding="5" cellspacing="0">
  <tr>
    <td>Название<br />
    <?=in_text('name', $razdel->name);?></td>
    <td>Заголо  вок раздела<br />
    <?=in_text('title', $razdel->title);?></td>
  </tr>
  <tr>
    <td>
      Метка<br />
      <?=in_text('url', $razdel->url);?>
    </td>
    <td>
      Активен<br />
      <?=in_check('active', $razdel->active);?>
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
        name:         $("#name").val(), 
        module_id:    $("#module_id").val(), 
        template_id:  $("#template_id").val(), 
        priority:     $("#priority").val(), 
        in_menu:      $("#in_menu").prop('checked'), 
        active:       $("#active").prop('checked'), 
        text:         CKEDITOR.instances['text'].getData(),
        menu_text:    $("#menu_text").val(), 
        title:        $("#title").val(),
        url:          $("#url").val(),
        date:         $("#date").val()
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
  
  $("#razdel_text_div").show();
});
</script>