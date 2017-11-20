"Наименование";"Марка";"Модель";"Наличие";"Доставка";"Сайт";"Цена";"Описание";"Адрес картинки"
<? foreach($catalog['search'] as $key => $item):?>
"<?=trim($item->name);?>";"<?=htmlspecialchars_decode($item->manufacturer, ENT_QUOTES);?>";"<?=trim($item->code);?>";"1";"доставка БЕСПЛАТНО - если стоимость Вашего заказа 1000р. и более, доставка 100 р. - если сумма заказа меньше 1000 р.";"http://www.umeika.com/catalog/tovarFull/<?=$item->id;?>";"<?=round($item->price);?>";"<?=$item->description;?>";"http://www.umeika.com<?=$item->photo_main_big;?>"
<? endforeach;?>
