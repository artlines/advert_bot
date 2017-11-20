<ul>
<? foreach ($items as $item):?>
  <li><a href="/portfolio/item/<?=$item->id?>"><img src="<?=$item->image?>" title="<?=$item->name;?>" /></a></li>
<? endforeach;?>
</ul>