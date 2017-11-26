<div class="row">
  <div class="col-md-3" id="list-panel">
    <h1>Пользователи</h1>
    <div class="form-group">
      <input type="text" class="form-control" name="user-search" placeholder="Поиск пользователя. Всего <?=$count;?>" />
    </div>
    <ul class="list-group object-group">
      <? foreach ($items as $item):?>
        <li class="list-group-item">
          <?=$item->username;?>
          <div class="pull-right">
            <? if (!$item->active):?>
              <i class="fa fa-minus-square" aria-hidden="true" title="Не активен"></i>
            <? endif;?>
            <a href="#edit" data-id="<?=$item->id;?>" data-type="user" class="edit-object" title="Редактировать">
              <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
            </a>
            <a href="#delete" data-id="<?=$item->id;?>" data-type="user" class="delete-object" title="Удалить">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
          </div>
        </li>
      <? endforeach;?>
    </ul>
  </div>
  <div class="col-md-8" id="action-panel"></div>
</div>