<h2>Вы смотрели:</h2>
<ul class="products">
  <? foreach ($recent as $item):?>
  <li>
    <a href="/catalog/tovarFull/<?=$item->id;?>">
      <img src="<?=$item->photo_main->thumb_file;?>" title="<?=$item->name;?>" />
    </a>
  </li>
  <? endforeach;?>
</ul>