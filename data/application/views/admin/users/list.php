<? foreach ($items as $item):?>
  <li class="list-group-item">
    <?=$item->username;?>
    <div class="pull-right">
      <? if (!$item->active):?>
        <i class="fa fa-minus-square" aria-hidden="true" title="Не активен"></i>
      <? endif;?>
      <a href="#edit" data-id="<?=$item->id;?>" data-type="users" class="edit-object" title="Редактировать">
        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
      </a>
      <a href="#delete" data-id="<?=$item->id;?>" data-type="users" class="delete-object" title="Удалить">
        <i class="fa fa-trash" aria-hidden="true"></i>
      </a>
    </div>
  </li>
<? endforeach;?>