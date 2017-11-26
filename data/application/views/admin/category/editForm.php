<div class="row">
  <div class="col-md-6">
    <h2>Редактировать категорию</h2>
    <form class="obj-edit-form" action="/admin/category/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="name" value="<?=$name;?>" />
      </div>
      <div class="form-group">
        <label class="form-check-label">
          <input type="checkbox" class="form-check-input" name="is_active"<? if ($is_active):?>checked<? endif;?> /> Активность
        </label>
      </div>
      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
      </div>
    </form>
  </div>
</div>