<table width="100%" cellspacing="5" id="userInfoTable" class="standart_form">
  <tr>
    <? $i = 1;?>
    <? foreach((array)$values as $key => $val):?>
      <td>
        <?=$val['name'];?><br />
        <? $class = '';?>
        <? $val['id']==USER_V_PHONE   ? $class = 'phone_number' : 0;  ?>
        <? $val['id']==USER_V_BIRSDAY ? $class = 'birsday'      : 0;  ?>
        <?=in_text('register['.$val['id'].']', $val['value'], '300px', $class);?>
      </td>
      <? if( ($i)%2==0 ):?>
        </tr><tr>
      <? endif;?>
      <? $i++;?>
    <? endforeach;?>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <?=in_ui_button('saveInfo', 'Сохранить');?>
    </td>
  </tr>
</table>

<script>
$(function() {
  
  $("#saveInfo").click(function() {
    $("#userInfoTable").post_ajax_form("/user/config/submit", function() {
      reloadTabs();
    });
  });

  $.mask.definitions['~']='[+-]';
  $(".phone_number").mask("+7 (999) 999-99-99");

});
</script>
