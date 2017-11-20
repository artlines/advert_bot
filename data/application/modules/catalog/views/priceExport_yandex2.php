<?='<?xml version="1.0" encoding="'.$charset.'"?>'."\n";?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?=date("Y-m-d H:i");?>">
  <shop>
    <name><?=config('short_name');?></name>
    <company><?=config('full_ur_name');?></company>
    <url>http://<?=SERVER;?></url>
    <platform>angCMS</platform>
    <version>2.1</version>
    <agency>Газетдинов А.Н.</agency>
    <email>alexey@gazetdinov.ru</email>
    
    <currencies>
      <currency id="RUR" rate="1"/>
    </currencies>
    
    <categories>
    <? foreach ($category as $key => $item):?>
      <category id="<?=$item->id;?>" <? if ($item->parent_id):?>parentId="<?=$item->parent_id;?>"<? endif;?>><?=$item->name;?></category>
    <? endforeach;?>
    </categories>
    
    <local_delivery_cost>100</local_delivery_cost>
    
    <offers>
    <? foreach ($catalog['search'] as $key => $item):?>
      <? $full_info = $CI->tovar_model->Get($item->id);?>
      <offer id="<?=$item->id;?>" available="true">
        <url>http://<?=SERVER;?>/catalog/tovarFull/<?=$item->id;?></url>
        <price><?=round($item->price);?></price>
        <currencyId>RUR</currencyId>
        <categoryId><?=(int)$full_info->category[0]->category_id;?></categoryId>
        <picture>http://<?=SERVER;?>/<?=$item->photo_main_big;?></picture>
        <store>false</store>
        <pickup>true</pickup>
        <delivery>true</delivery>
        <local_delivery_cost>0</local_delivery_cost>
        <name><?=htmlspecialchars($item->name, ENT_QUOTES);?></name>
        <vendor><?=htmlspecialchars($item->manufacturer, ENT_QUOTES);?></vendor>
        <vendorCode><?=$item->code;?></vendorCode>
        <description><![CDATA[<?=htmlspecialchars($item->description, ENT_QUOTES);?>]]></description>
        <sales_notes>доставка БЕСПЛАТНО при стоимости заказа от 800р.</sales_notes>
      </offer>
    <? endforeach;?>
    </offers>
  </shop>
</yml_catalog>
