<ul style="padding-left:15px;">
  <? foreach ($razdel as $key => $item): ?>
    <? $checked = ($banner_razdel[$item->id] ? 1 : 0);?>
    <li><?=in_check("razdel_id[{$item->id}]", $checked, $item->id);?>&nbsp;<?=$item->name;?></li>
    <? if ( count($this->razdel_model->Get(array('parent_id' => $item->id))) > 0 ):?>
      <?=$CI->_banners_razdel_makeTree($item->id);?>
    <? endif;?>
  <? endforeach;?>
</ul>