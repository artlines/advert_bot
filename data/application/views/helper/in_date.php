<? $uniq = uniqid();?>
<input type="text" name="<?=$name;?>" id="<?=$id;?>" value="<?=$value;?>" 
       style="width:<?=$width;?>" 
       class="<?=$class;?> form-control date-picker-<?=$uniq;?>" 
       placeholder="<?=$placeholder;?>" />
<script>
  $(function() {
    var date_object = $(".date-picker-<?=$uniq;?>");
    date_object.datepicker();
    date_object.datepicker('option', {dateFormat: 'yy-mm-dd'});
    date_object.datepicker( "option", "firstDay", 1 );
    date_object.datepicker( "option", "changeYear", true );
    date_object.datepicker( "option", "duration", 'fast' );
    date_object.val('<?=$value;?>');
  });
</script>