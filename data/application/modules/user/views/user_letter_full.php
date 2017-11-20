<h3>Ваш вопрос:</h3>
<?=nl2br($mess['question']);?>

<br />

<h3>Ответ администратора:</h3>
<? if ($mess['answer']<>''):?>
  <?=nl2br($mess['answer']);?>
<? else:?>
  Ответ появится в ближайшее время.
<? endif;?>