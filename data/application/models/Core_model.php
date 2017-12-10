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
    return;
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
