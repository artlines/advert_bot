<? foreach ($categories as $key => $item):?>
  <div class="item">
    <a href="/catalog/search/<?=$item->link;?>" class="standart" title="<?=$item->name;?>">
      <?=$item->name;?>
    </a>
  </div>
<? endforeach;?>