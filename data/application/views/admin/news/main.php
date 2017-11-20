<div id="small_left">
  <div class="panel panel-default">
    <div class="panel-heading">Новости</div>
    <table class="table">

      <? foreach($news as $v => $k):?>
        <tr>
          <td width="10%" valign="center"><span class="glyphicon glyphicon-chevron-right"></span></td>
          <td width="70%"><?=$k['title'];?> / <?=$k['date'];?></td>
          <td width="10%"><a href="javascript:void(0);" onClick="edit_news(<?=$k['id'];?>)">
            <span class="glyphicon glyphicon-edit"></span>
          </td>
          <td width="10%"><a href="javascript:void(0);" onClick="del_news(<?=$k['id'];?>)">
            <span class="glyphicon glyphicon-remove"></span>
          </td>
        </tr>
      <? endforeach;?>
    </table>
    
    <?=in_bs_button('add_news', 'Добавить новость', array('icon' => 'plus', 'align' => 'right', 'action' => 'add_news()'));?>

  </div>
</div>
<div id="big_right"></div>

<script>
function add_news() {
  modal('/admin/news_add');
}

function del_news(id) {
  if ( confirm("Вы действительно хотите удалить данную новость?") ) {
    $("#big_right").html(Loader);
    $.post("/admin/news_del/"+id, {}, function() {
      location.reload();
    });
  }
}

function edit_news(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/news_detail/"+id);
}

</script>