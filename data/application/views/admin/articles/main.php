<div id="small_left">
<h4>Статьи</h4>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<? foreach($articles as $v => $k):?>
  <tr>
    <td width="10%" valign="center"><img src="/images/link_arrow2.gif"></td>
    <td width="70%"><?=$k['title'];?> / <?=$k['date'];?></td>
    <td width="10%"><a href="javascript:void(0);" onClick="edit_articles(<?=$k['id'];?>)">
      <img src="/images/edit2.jpg" border=0 title="Редактировать" /></a></td>
    <td width="10%"><a href="javascript:void(0);" onClick="del_articles(<?=$k['id'];?>)">
      <img src="/images/delete2.jpg" border=0 title="Удалить" /></a></td>
  </tr>
  <tr><td colspan="4" style="height:7px;"> </td></tr>
<? endforeach;?>
  <tr><td colspan=4 align="right">&nbsp;</td></tr>
  <tr><td colspan=4 align="right">
    <a href="javascript:void(0);" onClick="add_articles();" style="text-decoration:none;">
    <img src="/images/plus2.jpg" border=0>Добавить</a></td></tr>
</table>
</div>
<div id="big_right"></div>

<script>
function add_articles() {
  modal('/admin/articles_add');
}

function del_articles(id) {
  if( confirm("Вы действительно хотите удалить данную новость?") ) {
    $("#big_right").html(Loader);
    $.post("/admin/articles_del/"+id, {}, function() {
      location.reload();
    });
  }
}

function edit_articles(id) {
  $("#big_right").html(Loader);
  $("#big_right").load("/admin/articles_detail/"+id);
}

</script>