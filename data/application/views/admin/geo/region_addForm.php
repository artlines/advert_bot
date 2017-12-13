<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 22.11.2017
 * Time: 22:58
 */
?>
<div class="row">
  <div class="col-md-6">
    <h2>Добавить область</h2>
    <form class="obj-add-form" action="/admin/region/add" method="post">
      <div class="form-group">
        <div>Наименование</div>
        <input type="text" class="form-control" name="name" />
      </div>
      <div class="form-group">
        <button class="btn btn-info" name="submit" type="submit">Создать</button>
      </div>
    </form>
  </div>
</div>

