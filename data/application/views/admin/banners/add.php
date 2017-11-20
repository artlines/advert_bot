<div id="form">

  <form class="nyroModal" method="post" action="/admin/banners/add">

    Название баннера<br />
    <?=in_text('name', $post['name'], array('width' => '250px'));?><br /><br />

    <?=in_submit('submit', 'Добавить');?>

  </form>
  
</div>
