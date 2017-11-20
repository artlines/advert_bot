<h2>Личный кабинет</h2>
<ul>
  <li style="margin-top:5px;"><a href="/user/config/">Изменить личные данные</a></li>
  <li style="margin-top:5px;"><a href="/user/settings/">Настройки</a></li>
  <li style="margin-top:5px;"><a href="/user/calendar/">План на день</a></li>
  <li style="margin-top:5px;"><a href="/user/report/">Отчет</a></li>
  <li style="margin-top:5px;"><a href="/user/chpass/">Сменить пароль</a></li>
  <li style="margin-top:5px;"><a href="/user/letter/">Вопрос/ответ</a></li>
  <li style="margin-top:5px;"><a href="/user/logout/">Выход</a></li>
</ul>

<hr />
<div id="shop_info_text">
  <b>Персональная страница</b><br />
  <?=in_text_readonly('personal_page', 'http://'.SERVER.'/services/company/'.$user_id, '220px');?>
  <br /><br />

  <b>Код кнопки</b><br />
  <?=in_textarea(
    'personal_button', 
    '<a href="http://'.SERVER.'/services/company/'.$user_id.'/back"><img src="http://'.SERVER.'/images/main/signup_big.png" border="0" /></a>', 
    array('width' => '220px', 'height' => '100px', 'readonly' => 'readonly')
  );?><br /><br />
</div>