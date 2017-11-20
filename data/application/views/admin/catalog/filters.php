<form method="post" action="/admin/catalog/filters/<?=$id;?>" class="nyroModal">  
  <? foreach ($filters as $item):?>
    <b><?=$item->name;?></b>
    <ul class="row inline">
      <? foreach ($item->values as $value):?>
      <li class="col-md-6">
        <?=in_check('values[' . $item->id . '][' . $value->id . ']', ($product[$value->id] ? 1 : 0), $value->id)?>&nbsp;
        <?=$value->value;?>
      </li>
      <? endforeach;?>
    </ul>
    <hr class="clear" />
  <? endforeach;?>
  
  <?=in_hidden('action', 'save')?>

  <?=in_bs_button('saveNewTovar', 'Сохранить', array('type' => 'submit', 'align' => 'left'));?>
</form>