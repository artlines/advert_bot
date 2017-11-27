<div class="row">
  <div class="col-md-6">
    <h2>Редактировать пользователя</h2>
    <form class="obj-edit-form" action="/admin/users/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="username" value="<?=$username;?>" />
      </div>
      <div class="form-group">
        <label class="form-check-label">
          <input type="checkbox" class="form-check-input" name="active"<? if ($active):?>checked<? endif;?> /> Активность
        </label>
      </div>
      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
      </div>
    </form>
  </div>
</div>