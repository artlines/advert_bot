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
    <h2>Добавить категорию</h2>
    <form class="obj-add-form" action="/admin/category/add" method="post">
      <div class="form-group">
        <div>Родительская категория</div>
        <?=in_table('parent_id', [
          'table' => 'category',
          'order' => 'left_key',
          'ident' => 'level',
          'width' => '100%'
        ]);?>
      </div>
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

