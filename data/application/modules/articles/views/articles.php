<div class="news_block">
  <? if (!empty($articles)):?>
  <? foreach ($articles as $v => $item):?>
  <div class="item">
    <span class="date"><?=format_date($item['date']);?></span>
    <a href="/articles/<?=$item['id'];?>" class="head"><?=$item['title'];?></a>
    <div class="item-text">
    <?=$item['small'];?>
    </div>
  </div>
  <? endforeach;?>
  <? else:?>
    Пока статей нет. Приходите попозже!
  <? endif;?>
</div>