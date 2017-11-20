<h4>Редактирование статьи</h4>
<hr />
<br />
<table width="70%" border="0">
  <tr>
    <td>
      Дата<br />
      <?=in_text('date');?>
    </td>
    <td>
      Заголовок<br />
      <?=in_text('title', $title);?>
    </td>
    <td>
      Активен<br />
      <?=in_check('active', $active);?>
    </td>
  </tr>
  <tr>
    <td align="left" colspan="3">
      &nbsp;
    </td>
  </tr>
  <tr>
    <td align="left" colspan="3">
      <?=in_blue_button("save", "Сохранить");?>
    </td>
  </tr>
</table>
<br />
<br />
Кратко<br />
<?=$editor1;?> <br /><br />

Полный текст<br />
<?=$editor2;?> <br /><br />

<script>
var id = "<?=$id;?>"
var FCKE = new Array();
$(function() {
  $("#save").click(function() {
    LoaderOn();
    $.post("/admin/articles_detail/"+id, {
        action: 'save', 
        date:   $("#date").val(), 
        active: $("#active").prop('checked'), 
        small:  CKEDITOR.instances['small'].getData(), 
        title:  $("#title").val(), 
        text:   CKEDITOR.instances['text'].getData() 
      }, 
      function(data) {
        LoaderOff();
        if (data==1) {
          alert("Успешно сохранено!");
        } 
        else {
          alert("Ошибка сохрания! " + data);
        }
      }
    );
  });
  $("#date").datepicker();
  $('#date').datepicker('option', {dateFormat: 'yy-mm-dd'});
  $('#date').datepicker( "option", "firstDay", 1 );
  $('#date').datepicker( "option", "changeYear", true );
  $('#date').datepicker( "option", "duration", 'fast' );
  $("#date").val('<?=$date;?>');
});
</script>