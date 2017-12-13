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
        <h1>Области</h1>
      </div>
      <div class="col-md-6 text-right">
        <button type="button" class="btn btn-primary add-object" data-type="region" title="Добавить область">
          <i class="fa fa-plus" aria-hidden="true"></i> область
        </button>
        <button type="button" class="btn btn-info add-object" data-type="city" title="Добавить город">
          <i class="fa fa-plus" aria-hidden="true"></i> город
        </button>
      </div>
    </div>
    <ul class="list-group object-group">
      <? foreach ($items as $item):?>
        <li class="list-group-item">
          <?=$item->name;?>
          <div class="pull-right">
            <a href="#edit" data-id="<?=$item->id;?>" data-type="region" class="edit-object" title="Редактировать">
              <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </a>
            <a href="#delete" data-id="<?=$item->id;?>" data-type="region" class="delete-object" title="Удалить">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
          </div>
        </li>
        <? foreach ($item->cities as $city):?>
          <li class="list-group-item">
            <span class="ident-20"></span>
            <?=$city->name;?>
            <div class="pull-right">
              <a href="#edit" data-id="<?=$city->id;?>" data-type="city" class="edit-object" title="Редактировать">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
              </a>
              <a href="#delete" data-id="<?=$city->id;?>" data-type="city" class="delete-object" title="Удалить">
                <i class="fa fa-trash" aria-hidden="true"></i>
              </a>
            </div>
          </li>
        <? endforeach;?>
      <? endforeach;?>
    </ul>
  </div>
  <div class="col-md-8" id="action-panel"></div>
</div>
