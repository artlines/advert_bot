<?php

class News_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Получить новости
   */
  function Get($params) {
    $id     = (int)$params['id'];
    $limit  = (int)$params['limit'];
    $active = (int)$params['active'];
    $active ? $where = " and active=1" : 0;
    if ($id>0) {
      $res = $this->db->query("select * from news where id={$id} {$where}")->row_array();
      $res['small'] = htmlspecialchars_decode($res['small']);
      $res['text']  = htmlspecialchars_decode($res['text']);
    }
    else {
      $limit>0 ? $lim = " limit {$limit}" : $lim = "";
      $res = $this->db->query("select * from news where 1=1 {$where} order by date desc {$lim}")->result_array();
      foreach ($res as $v => &$k) {
        $k['small'] = htmlspecialchars_decode($k['small']);
        $k['text']  = htmlspecialchars_decode($k['text']);
      }
    }
    return $res;
  }
  
  /**
   * Добавление новости
   */
  function Add($params) {
    $insert = array(
      'title'  => $params['title'],
      'date'   => ($params['date'] ? $params['date'] : date("Y-m-d")),
      'active' => 0
    );
    $ret = $this->db->insert("news", $insert);
    return $ret;
  }
  
  /**
   * Удаление новости
   */
  function Del($id) {
    $id = (int)$id;
    if (!$id)
      return false;
    $this->db->where('id', $id);
    $ret = $this->db->delete('news'); 
    return $ret;
  }
  
  /**
   * Изменение новости
   */
  function Set($params) {
    $id = (int)$params['id'];
    if (!$id)
      return false;
    $update = array(
      'title'  => $params['title'],
      'htitle' => $params['htitle'],
      'small'  => $params['small'],
      'text'   => $params['text'],
      'date'   => $params['date'],
      'active' => ($params['active']=='true' ? 1 : 0)
    );
    $this->db->where('id', $id);
    $ret = $this->db->update('news', $update);
    return $ret;
  }
  
}

?>
