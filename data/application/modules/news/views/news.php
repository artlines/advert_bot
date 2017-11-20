<div class="news">
  <? if (!empty($news)):?>
  <? foreach ($news as $v => $item):?>
  <div class="item margin-bottom-35">
    <div class="float-right">
      <div><?=format_date($item['date']);?></div>
      <div><a href="/news/<?=$item['id'];?>" class="none">Подробнее</a></div>
    </div>
    <div class="headline title">
      <a href="/news/<?=$item['id'];?>" class="none"><h2><?=$item['title'];?></h2></a>
    </div>
    <div class="text">
    <?=$item['small'];?>
    </div>
  </div>
  <? endforeach;?>
  <? endif;?>
</div>