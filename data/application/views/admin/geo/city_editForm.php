<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 13.12.2017
 * Time: 22:39
 */
?>
<div class="row">
  <div class="col-md-6">
    <h2>Редактировать город</h2>
    <form class="obj-edit-form" action="/admin/city/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Регион</div>
        <?=in_table('region_id', [
          'table' => 'region',
          'value' => $region_id,
          'width' => '100%'
        ]);?>
      </div>
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="name" value="<?=$name;?>" />
      </div>
      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
      </div>
    </form>
  </div>
</div>