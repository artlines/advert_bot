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
    <h2>Редактировать область</h2>
    <form class="obj-edit-form" action="/admin/region/edit/<?=$id;?>" method="post">
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="name" value="<?=$name;?>" />
      </div>
      <div class="form-group">
        <div>Сокращения (через запятую)</div>
        <input type="text" class="form-control" name="alias" value="<?=$alias;?>" />
      </div>
      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Сохранить</button>
      </div>
    </form>
  </div>
</div>