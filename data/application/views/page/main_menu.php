          <div id="main_menu_items">
            <div class="first_level">
              <ul>
              <? foreach ($categories as $key => $item):?>
                <li category="<?=$item->id;?>" class="main_menu_item <? if ($vars->category[0] == $item->id):?>selected<? endif;?>">
                  <div class="menu_item left"></div>
                  <div class="menu_item center">
                    <a href="/catalog/category/<?=$item->id;?>" title="<?=$item->name;?>"><?=$item->name;?></a>
                  </div>
                  <div class="menu_item right"></div>
                  <div class="second_level">
                    <div class="items3">
                      <div class="items2">
                        <div class="items">
                          <ul id="subcategory_<?=$item->id;?>" class="subcategory">
                            <? foreach ($item->child as $subcat):?>
                            <li class="<? if ($vars->category[1] == $subcat->id):?>selected<? endif;?>">
                              <a href="/catalog/category/<?=$item->id;?>_<?=$subcat->id;?>"><?=$subcat->name;?></a>
                            </li>
                            <? endforeach;?>
                          </ul>
                          <div style="clear:both;"></div>
                        </div>
                      </div>
                    </div>
                    <div class="items_bottom"></div>
                  </div>
                </li>
                <? endforeach;?>
              </ul>
            </div>
          </div>