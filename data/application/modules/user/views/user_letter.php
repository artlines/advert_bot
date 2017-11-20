<? foreach ($mess as $v => $k):?>
  <hr style="color: rgb(255, 255, 255);">
  <span class="news_date"><?=$k['tm'];?></span><br />
  <a href="/user/letter/full/<?=$k['id'];?>"><?=mb_substr($k['question'], 0, 200);?>...</a>
<? endforeach;?>

<br />
<br />

<a name="new_question"></a>
<div id="new_q"></div>

<div align="left"><?=in_blue_button('add_question', 'Задать вопрос');?></div>

<script>
$(function() {

  $("#add_question").click(function() {
    $("#new_q").show();
    $("#new_q").html(Loader);
    $.post("/user/letter/new", {}, function(data) {
      $("#new_q").hide();
      $("#new_q").html(data);
      $("#new_q").show(1000);
      $("#add_question").hide(1000);
      location.href = '#new_question';
    });
  });
  
});
</script>