<h3>Общие настройки</h3><hr />
<div id="form" style="width:500px;">

  <div>
    <div class="col-sm-5">
      <input type="password" id="admin_password1" class="form-control" placeholder="Новый пароль администратора">
    </div>                   
    <div class="col-sm-5">   
      <input type="password" id="admin_password2" class="form-control" placeholder="Повтор пароля">
    </div>
    <div class="col-sm-2">
      <button type="button" class="btn btn-default" onClick="save_password()">
        OK
      </button>
    </div>
  </div>
  <hr class="clear" />
  <hr />
  <br />

<? foreach($list as $v => $k):?>
  <?=$k['name'];?><br>
  <div class="input-group">
    <input type="text" name="<?=$k['key'];?>" id="<?=$k['key'];?>" value='<?=$k['value'];?>' class="form-control">
    <span class="input-group-btn">
      <button type="button" class="btn btn-default" onClick="save_config('<?=$k['key'];?>')">
        OK
      </button>
    </span>
  </div>
  <br />
<? endforeach;?>
</div>
<script>
function save_config(key) {
  var div = "#"+key; 
  var value = $(div).val();
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/config/detail/", {action:'set', key:key, value:value});
}

function save_password() {
  var saveData = {
    action: 'set_password', 
    admin_password1: $("#admin_password1").val(), 
    admin_password2: $("#admin_password2").val()
  };
  if (saveData.admin_password1.length < 8) {
    alert("Введите пароль не менее 8 символов.");
    return;
  }
  if (saveData.admin_password1 != saveData.admin_password2) {
    alert("Пароль не совпадает с повтором.");
    return;
  }
  $("#big_right").html(Loader);
  $.post_ajax_json("/admin/config/set_password/", saveData, function(result) {
    $("#big_right").load("/admin/config/detail/");
  });
}
</script>
