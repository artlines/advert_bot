<?php
/**
 * @property CI_DB_mysqli_driver $db
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
   * Поиск категорий
   * @author Alexey
   */
  function find($params = []) {
    return $this->db->order_by('left_key')->get('category')->result();
  }

  /**
   * Добавить
   * @author Alexey
   */
  function add() {

  }

  /**
   *
   * @author Alexey
   */
  function del() {

  }

  /**
   *
   * @author Alexey
   */
  function get() {

  }

  /**
   *
   * @author Alexey
   */
  function set() {

  }
}
