<?php

/**
 * Created by PhpStorm.
 * User: alexe
 * Date: 29.11.2017
 * Time: 22:14
 */
class Adverts_model extends CI_Model
{


  function __construct() {
    parent::__construct();
  }

  /**
   * Поиск
   * @author Alexey
   */
  function find($params) {
    $where = [
      'deleted' => 0
    ];
    if ($params['limit']) {
      $this->db->limit($params['limit']);
    }
    if ($params['search']) {
      $where['title LIKE'] = "%{$params['search']}%";
    }
    return $this->db
      ->where($where)
      ->order_by('tm DESC')
      ->get('advert')
      ->result();
  }

  /**
   * Количество
   * @author Alexey
   */
  function count() {
    return $this->db->where(['deleted' => 0])->count_all_results('advert');
  }

  /**
   * Получить юзера
   * @author Alexey
   */
  function get($id) {
    $id = (int)$id;
    return $this->db->where(['id' => $id])->get('advert')->row();
  }
}