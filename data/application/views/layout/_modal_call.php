<!-- remove boxed-layout & container for full width -->
<form class="form-horizontal" role="form" id="call-form" method="post">
  <div class="modal fade" id="call-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
            <span>×</span>
          </button>
          <h4 class="modal-title">Оставьте ваше имя и телефон</h4>
        </div>
        <div class="modal-body">
           
          <p>Наш менеджер свяжется с вами для учточнения заказа.</p>
          <div class="form-group">
             <label for="call-phone" class="col-lg-3 control-label">Ваш телефон</label>
             <div class="col-lg-9">
                <input type="text" class="form-control" id="call-phone" name="feedback_phone" placeholder="Телефон">
             </div>
          </div>
          <div class="form-group">
             <label for="call-name" class="col-lg-3 control-label">Ваше имя</label>
             <div class="col-lg-9">
                <input type="text" class="form-control" id="call-name" name="feedback_name" placeholder="Имя">
             </div>
          </div>
          <div class="form-group">
             <label for="call-name" class="col-lg-3 control-label">Комментарий</label>
             <div class="col-lg-9">
                <input type="text" class="form-control product" id="call-product" name="feedback_product" placeholder="Комментарий">
             </div>
          </div>
          <div class="form-group">
            <label for="call-name" class="col-lg-3 control-label"></label>
            <div class="col-lg-9">
              <button class="btn-u" type="submit">Отправить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- Simple modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-quick-view">
  <div class="modal-dialog modal-lg"></div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-quick-view-small">
  <div class="modal-dialog modal-md"></div>
</div>
<!-- ./ Simple modal -->
<!-- description modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="description-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close custom-close" type="button"><span>×</span></button>
        <h4 class="modal-title">...</h4>
      </div>
      <div class="modal-body">
        <p>...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-u btn-u-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<!-- description modal -->