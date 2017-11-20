<h1><?=$title;?></h1>
<p id="content_text">
  <? if (!empty($result)):?>
  <? foreach ($result as $v => $k):?>
    <p>
      <a href="/<?=$k['url'];?>"><?=$k['name'];?></a><br />
      <?=$k['text'];?>
    </p>
  <? endforeach;?>
  <? else:?>
    <p>Ничего не найдено</p>
  <? endif;?>
</p>
