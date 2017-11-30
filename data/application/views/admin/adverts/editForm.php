<div class="row">
  <div class="col-md-6">
    <h2>Редактировать объявление</h2>
    <form class="obj-edit-form" action="/admin/adverts/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="username" value="<?=$username;?>" />
      </div>
      <div class="form-group">
        <div>E-mail</div>
        <input type="text" class="form-control" name="email" value="<?=$email;?>" />
      </div>
      <div class="form-group">
        <div>Телефон</div>
        <input type="text" class="form-control" name="phone" value="<?=$phone;?>" />
      </div>
      <div class="form-group">
        <div>Город</div>
        <?=in_table('city_id', [
          'table' => 'city',
          'value' => $city_id,
          'width' => '100%'
        ]);?>
      </div>
      <div class="form-group">
        <div>Приоритет</div>
        <input type="text" class="form-control" name="priority" value="<?=$priority;?>" />
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