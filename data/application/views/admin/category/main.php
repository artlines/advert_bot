<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 19.11.2017
 * Time: 23:12
 */
?>
<div class="row">
  <div class="col-md-3" id="list-panel">
    <div class="row">
      <div class="col-md-6">
        <h1>Категории</h1>
      </div>
      <div class="col-md-6 text-right">
        <button type="button" class="btn btn-primary add-object" data-type="category" title="Добавить категорию">
          <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
      </div>
    </div>
    <ul class="list-group object-group">
      <? foreach ($items as $item):?>
        <li class="list-group-item">
          <span class="ident-<?=($item->level - 1) * 2;?>0"></span>
          <?=$item->name;?>
          <div class="pull-right">
            <? if (!$item->is_active):?>
              <i class="fa fa-minus-square" aria-hidden="true" title="Не активна"></i>
            <? endif;?>
            <a href="#edit" data-id="<?=$item->id;?>" data-type="category" class="edit-object" title="Редактировать">
              <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </a>
            <a href="#delete" data-id="<?=$item->id;?>" data-type="category" class="delete-object" title="Удалить">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
          </div>
        </li>
      <? endforeach;?>
    </ul>
  </div>
  <div class="col-md-8" id="action-panel"></div>
</div>
