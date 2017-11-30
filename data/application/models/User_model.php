<?php
/**
 * @property CI_DB_query_builder $db
 */
class User_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }

  /**
   * Поиск
   * @author Alexey
   */
  function find($params) {
    $where = [
      'admin' => 0
    ];
    if ($params['limit']) {
      $this->db->limit($params['limit']);
    }
    if ($params['search']) {
      $where['username LIKE'] = "%{$params['search']}%";
    }
    return $this->db
      ->where($where)
      ->order_by('tm_register DESC')
      ->get('user')
      ->result();
  }

  /**
   * Количество
   * @author Alexey
   */
  function count() {
    return $this->db->where(['admin' => 0])->count_all_results('user');
  }

  /**
   * Получить юзера
   * @author Alexey
   */
  function get($id) {
    $id = (int)$id;
    return $this->db->where(['id' => $id])->get('user')->row();
  }

  /**
   * Изменить юзера
   * @author Alexey
   */
  function set($id, $params) {
    $id = (int)$id;
    $this->db->update('user', [
      'username'  => $params['username'],
      'email'     => $params['email'],
      'phone'     => $params['phone'],
      'city_id'   => (int)$params['city_id'],
      'priority'  => (int)$params['priority'],
      'active'    => ($params['active'] == 'on' ? 1 : 0)
    ], ['id' => $id]);
  }

  // ---- OLD -----
  /**
   * Выход
   */
  function Logout() {
    @session_unset();
    @session_destroy();
  }
}
?>