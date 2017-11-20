<h1>Новый запрос цены</h1>
<div>Название компании: <?=$post['company']?></div>
<div>ФИО: <?=$post['fio']?></div>
<div>Телефон: <?=$post['phone']?></div>
<div>Комментарий: <?=$post['comment']?></div>

<h2>Товар</h2>
<div><?=$tovarInfo->manufacturer_name;?> <?=$tovarInfo->name;?> (#<?=$tovarInfo->id;?>) <?=$tovarInfo->code;?></div>