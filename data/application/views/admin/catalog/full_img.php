<h4>Изображение</h4>

<img src="/img/detali/<?=$detali['pic'];?>" border="0" width="60px" /><br />
<form method="post" action="/admin/catalog/img/<?=$detali['id'];?>/add" class="nyroModal" id="form" enctype="multipart/form-data">
  <input type="file" name="img">
  <input type="submit" name="submit" value="Добавить" class="button" />
  <a href="/admin/catalog/img/<?=$detali['id'];?>/del" class="nyroModal">
  <input type="button" name="button" value="Удалить" class="button" id="del" />
  </a>
</form>
<br /><br />