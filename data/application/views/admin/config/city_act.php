<div id="form">
  <form class="nyroModal" method="post" action="/admin/config/city/<?=$action;?>/<?=$id;?>">
    Название<br />
    <?=in_text('name', $values->name);?><br /><br />
    Приоритет<br />
    <?=in_text('priority', $values->priority);?><br /><br />
    Широта<br />
    <?=in_text('lat', $values->lat);?><br /><br />
    Долгота<br />
    <?=in_text('lon', $values->lon);?><br /><br />
    <input type="submit" name="submit" value="Сохранить" class="button">
  </form>
</div>