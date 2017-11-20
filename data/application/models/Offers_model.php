<?php

class Offers_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Поиск
   */
  function Find($params = array()) {
    return $this->db->where($params)->order_by('priority')->get('shop_offers')->result();
  }
  
  /**
   * Добавление
   */
  function Add($params) {
    $insert = array(
      'name' => trim($params['name'])
    );
    return $this->db->insert('shop_offers', $insert);
  }
  
  /**
   * Получение инфы
   */
  function Get($id) {
    $id = (int)$id;
    $info = $this->db->where('id', $id)->get('shop_offers')->row();
    return $info;
  }
  
  /**
   * Изменение баннера
   */
  function Set($id, $params) {
    $id = (int)$id;
    if (!$id || empty($params)) {
      return "Необходимые параметры не найдены!";
    }
    
    foreach ($params as $key => &$item) {
      $item=='true' ? $item = '1' : 0;
    }
    
    $update = array(
      'name'      => $params['name'],
      'link'      => $params['link'],
      'text'      => $params['text'],
      'active'    => $params['active'],
      'main_page' => $params['main_page'],
      'priority'  => $params['priority']
    );
    
    $ret = $this->db->update('shop_offers', $update, array('id' => $id));
    if (!$ret) {
      return "Ошибка БД!";
    }
    return;
  }
  
  /**
   * Добавление фото
   */
  function addFile($banner_id, $file) {
    $banner_id = (int)$banner_id;
    $this->delFile($banner_id);
    
    if (!$file['size']) {
      throw new Exception("Нулевой размер файла!");
    }
    $ext = getExt($file['name']);
    $filebasis = $banner_id . "_" . md5_file($file['tmp_name']);
    $fileweb   = OFFERS_DIR . $filebasis . "." . $ext;
    $filename  = ROOT.$fileweb;
    $res = rename($file['tmp_name'], $filename);
    
    if (!$res) {
      throw new Exception("Не получилось переместить временный файл!");
    }
    
    chmod($filename,  0755);
    
    $update = array(
      'image' => $fileweb
    );
    $res = $this->db->update('shop_offers', $update, array('id' => $banner_id));
    if (!$res) {
      throw new Exception("Ошибка БД.");
    }
    return;
  }
  
  /**
   * Удаление файла баннера
   */
  function delFile($offer_id) {
    $offer_id = (int)$offer_id;
    $offer    = $this->Get($offer_id);
    
    if (!$offer->image) {
      return;
    }
    
    if ( is_file (ROOT . $offer->image) ) {
      $res = unlink(ROOT . $offer->image);
      if (!$res) {
        throw new Exception("Не получилось удалить файл " . ROOT . $offer->image);
      }
    }
    
    $res = $this->db->update('shop_offers', array('image' => ''), array('id' => $offer_id)); 
    if (!$res) {
      throw new Exception("Ошибка БД!");
    }
    return;
  }
  
  /**
   * Удаление
   */
  function Del($id) {
    $delete = array(
      'id' => (int)$id
    );
    $res = $this->db->delete('shop_offers', $delete);
    if (!$res) {
      return "Ошибка БД";
    }
    return;  
  }
  
  
}