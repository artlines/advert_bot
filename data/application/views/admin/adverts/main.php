<div class="row">
  <div class="col-md-3" id="list-panel">
    <h1>Объявления</h1>
    <div class="form-group">
      <input type="text" class="form-control adverts-search" name="adverts-search" placeholder="Поиск объявления. Всего <?=$count;?>." />
    </div>
    <ul class="list-group object-group adverts-list">
    </ul>

    <div class="v-ident-10"></div>

    <nav aria-label="Page navigation ">
      <ul class="pagination pagination-sm justify-content-center advert-nav">
        <? for ($i = 1; $i <= ceil($count / Admin::ADVERT_PAGINATION_SIZE); $i++ ): ?>
          <li class="page-item"><a class="page-link" href="#" attr-id="<?=$i;?>"><?=$i;?></a></li>
        <? endfor;?>
      </ul>
    </nav>

  </div>

  <div class="col-md-8" id="action-panel"></div>

</div>
