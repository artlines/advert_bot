<div class="row">
  <div class="col-md-6">
    <h2>Редактировать объявление #<?=$id;?></h2>
    <form class="obj-edit-form" action="/admin/adverts/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Пользователь "<?=outTable([
            'table' => 'user',
            'ident' => 'tg_id',
            'value' => $user_id,
            'name'  => 'username'
          ]);?>"</div>
      </div>
      <div class="form-group">
        <div>Категория</div>
        <?=in_table('category_id', [
          'table' => 'category',
          'value' => $category_id,
          'order' => 'left_key',
          'ident' => 'level',
          'width' => '100%'
        ]);?>
      </div>
      <div class="form-group">
        <div>Регион</div>
        <?=in_table('region_id', [
          'table' => 'region',
          'value' => $region_id,
          'width' => '100%'
        ]);?>
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
        <div>Заголовок</div>
        <input type="text" class="form-control" name="title" value="<?=$title;?>" />
      </div>

      <div class="form-group">
        <div>Текст</div>
        <textarea class="form-control" name="content"><?=$content;?></textarea>
      </div>

      <div class="form-group">
        <label class="form-check-label">
          <input type="checkbox" class="form-check-input" name="active"<? if ($active):?>checked<? endif;?> /> Опубликовать
        </label>
      </div>

      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
      </div>
    </form>
  </div>
</div>