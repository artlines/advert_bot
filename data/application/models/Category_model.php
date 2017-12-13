<?php

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
    $result = $this->db
      ->where(['id >' => 0])
      ->order_by('left_key')->get('category')->result();
    return $result;
  }

  /**
   * Добавить
   * @author Alexey
   */
  function add($params) {
    $parent_id = (int)$params['parent_id'];
    $this->db->trans_start();
    $parent = $this->db->where(['id' => $parent_id])->get('category')->row();
    if (!$parent->name) {
      throw new Exception('Родительская категория не найдена');
    }
    $left_key   = $parent->right_key;
    $right_key  = $left_key + 1;
    $this->db->query("
      UPDATE category 
      SET 
        right_key = right_key + 2,
        left_key = IF(left_key > {$parent->right_key}, left_key + 2, left_key)
      WHERE right_key >= {$parent->right_key}
    ");
    $this->db->insert('category', [
      'parent_id' => $parent_id,
      'name'      => $params['name'],
      'left_key'  => $left_key,
      'right_key' => $right_key,
      'level'     => $parent->level + 1
    ]);
    $this->db->trans_complete();
  }

  /**
   *
   * @author Alexey
   */
  function del($id) {
    $id = (int)$id;
    $this->db->trans_start();
    $item = $this->db->where(['id' => $id])->get('category')->row();
    $this->db->query("
      DELETE FROM category 
      WHERE left_key >= {$item->left_key} 
        AND right_key <= {$item->right_key}
    ");
    $this->db->query("
      UPDATE category 
      SET 
        left_key = IF(left_key > {$item->left_key}, left_key - ({$item->right_key} - {$item->left_key} + 1), left_key), 
        right_key = right_key - ({$item->right_key} - {$item->left_key} + 1)
      WHERE right_key > {$item->right_key} 
    ");
    $this->db->trans_complete();
  }

  /**
   * Получить
   * @author Alexey
   */
  function get($id) {
    $id = (int)$id;
    return $this->db->where(['id' => $id])->get('category')->row();
  }

  /**
   * Изменить
   * @author Alexey
   */
  function set($id, $params) {
    $id = (int)$id;
    $this->db->update('category', [
      'name'      => $params['name'],
      'is_active' => ($params['is_active'] == 'on' ? 1 : 0)
    ], ['id' => $id]);
  }
}
