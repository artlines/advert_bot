<b style="color:#FF0000;"><?=$error_message;?></b>
<div id="form" style="width: 800px;">
  <form action="/admin/config/category/<?=$action;?>/<?=$id;?>" method="post" class="nyroModal" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-6">
        Название<br />
        <?=in_text('name', $values->name, ['width' => '100%']);?>
        <div class="row-20"></div>

        title<br />
        <?=in_text('title', $values->title, ['width' => '100%']);?>
        <div class="row-20"></div>

        Приоритет<br />
        <?=in_text('priority', $values->priority, ['width' => '100%']);?>
        <div class="row-20"></div>
        <div>
        <?=in_check('products', $values->products, 1);?> Товарная категория
        </div>
      </div>
    
      <div class="col-md-6">
        <div>Изображение</div>
        <div>
          <?=in_file('pic');?>
        </div>
        <div>
          <? if ($values->pic):?>
          <img src="<?=$values->pic;?>" style="width: 90%;" />
          <? endif;?>
        </div>
        <div class="row-20"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?=$editor;?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?=in_bs_button('save-category', 'Сохранить', array('type' => 'submit', 'align' => 'right'));?>
      </div>
    </div>
    <div class="row-20"></div>
  </form>
</div>