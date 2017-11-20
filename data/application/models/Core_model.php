<?php
/**
 * Description of Core_model
 *
 * @author Alexey
 */
class Core_model extends S_Model {
  
  /**
   * Изменить город
   * @author Alexey
   */
  function coreActionChangeCity($params) {
    $_SESSION['city_id'] = (int)$params['get']['city-id'];
    exit;
  }
  
  /**
   * Действие по умолчанию 
   * @author Alexey
   */
  function coreAction() {
    if (!$_SESSION['city_id']) {
      $_SESSION['city_id'] = CITY_ID_DEFAULT;
    }
    $this->params->current_city = $this->tovar_model->getCity($_SESSION['city_id']);
    $this->params->current_city_shops = $this->tovar_model->findShop(['city_id' => $_SESSION['city_id']]);
    $this->params->current_city_shop  = current((array)$this->params->current_city_shops);
    
    $this->deleteOldProductActions();
    $this->loadBrands();
    return $this->params;
  }
  
  /**
   * Очистка старых акций
   * @author Alexey
   */
  function deleteOldProductActions() {
    $products = $this->db->query("
      SELECT group_concat(tovar_id SEPARATOR ',') as products
      FROM shop_tovar_values
      WHERE var_id = " . PRODUCT_FIELD_SALE_TTL . "
        AND date(value) < NOW()
    ")->row()->products;
    if (!$products) {
      return;
    }
    $this->db->query("
      DELETE FROM shop_tovar_values
      WHERE var_id = " . PRODUCT_FIELD_SALE_PRICE . "
        AND tovar_id IN ({$products})
    ");
  }
  
  /**
   * Загрузить бренды
   * @author Alexey
   */
  function loadBrands() {
    $this->params->brands = $this->tovar_model->manufacturerFind(['main_page' => 1]);
  }
}
