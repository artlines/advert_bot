<div id="form">

  <form class="nyroModal" method="post" action="/admin/offers/add">

    Название<br />
    <?=in_text('name', $post['name'], array('width' => '350px'));?><br /><br />

    <?=in_submit('submit', 'Добавить');?>

  </form>
  
</div>
