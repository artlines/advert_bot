<div style="margin-left: 20px;" id="user_register_div">
<script>
$(function(){
  load_user_vars(<?=$t_kl;?>);
  $("[name=t_kl]").click(function() {
    load_user_vars($(this).val());
  });
});
function load_user_vars(value) {
  if (value>0) {
    $("#user_register_var").html(Loader);
    $("#user_register_var").load("/user/register/loadVars/"+value);
  }
}
</script>
Вы -<br>
<input name="t_kl" value="<?=USER_TYPE_FIZ;?>" type="radio" id="fiz_cl" <? if($t_kl==USER_TYPE_FIZ):?>checked<? endif;?> />
  <label for="fiz_cl">&nbsp;Физическое лицо</label><br />
<input name="t_kl" value="<?=USER_TYPE_UR;?>" type="radio" id="ur_cl"   <? if($t_kl==USER_TYPE_UR):?> checked<? endif;?> />
  <label for="ur_cl">&nbsp;Юридическое лицо</label>
<br><br>
<? if($error_message<>''):?>
  <font style="color:#FF0000;"><?=$error_message;?></font>
<? endif;?>
<div id="user_register_var"></div>
</div>