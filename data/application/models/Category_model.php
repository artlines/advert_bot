<?php
/**
 * @Property db $db
 */
class Category_model extends CI_Model {
  
  /**
   * __construct
   * @author Alexey
   */
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Получить параметр категории
   * @author Alexey
   */
  function getValue($params) {
    $res = $this->db->where($params)->get('shop_category_values')->row();
    return $res;
  }
  
  /**
   * Сохранить параметр категории
   * @author Alexey
   */
  function setValue($params) {
    $this->db->trans_start();
    $this->db->delete('shop_category_values', [
      'field'       => $params['field'],
      'category_id' => $params['category_id']
    ]);
    $this->db->insert('shop_category_values', $params);
    $res = $this->db->trans_complete();
    return $res;
  }
}
