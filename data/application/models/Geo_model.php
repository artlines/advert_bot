<?php

/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 13.12.2017
 * Time: 22:02
 */
class Geo_model extends CI_Model
{
  function __construct() {
    parent::__construct();
  }

  /**
   * Поиск
   * @author Alexey
   */
  function find() {
    $result = $this->db->order_by('name')->get('region')->result();
    foreach ($result as $key => $item) {
      $result[$key]->cities = $this->findCity(['region_id' => $item->id]);
    }
    return $result;
  }

  /**
   * Поиск городов
   * @author Alexey
   */
  function findCity($params) {
    return $this->db->where($params)->order_by('name')->get('city')->result();
  }

  /**
   * добавить
   * @author Alexey
   */
  function add($params) {
    return $this->db->insert('region', [
      'name' => $params['name']
    ]);
  }

  /**
   * получить 1
   * @author Alexey
   */
  function get($id) {
    return $this->db->where(['id' => $id])->get('region')->row();
  }

  /**
   * получить город
   * @author Alexey
   */
  function getCity($id) {
    return $this->db->where(['id' => $id])->get('city')->row();
  }

  /**
   * добавить город
   * @author Alexey
   */
  function addCity($params) {
    return $this->db->insert('city', [
      'name'      => $params['name'],
      'region_id' => (int)$params['region_id']
    ]);
  }

  /**
   * Сохранить город
   * @author Alexey
   */
  function setCity($id, $data) {
    return $this->db->update('city', [
      'name'      => $data['name'],
      'region_id' => $data['region_id']
    ], ['id' => $id]);
  }

  /**
   * Сохранить регион
   * @author Alexey
   */
  function set($id, $data) {
    return $this->db->update('region', [
        'name'  => $data['name'],
        'alias' => $data['alias']
      ], ['id' => $id]
    );
  }

  /**
   * удалить
   * @author Alexey
   */
  function del($id) {
    $id = (int)$id;
    $this->db->delete('region', ['id' => $id]);
  }

  /**
   * удалить город
   * @author Alexey
   */
  function delCity($id) {
    $id = (int)$id;
    $this->db->delete('city', ['id' => $id]);
  }

}