<?php

class Cmodules_model extends CI_Model {
  
  const PREFIX = 'mod_';
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Поиск элементов
   */
  function Find($filter = array(), $limit = null, $order = 'priority') {
    $this->db->where($filter)->order_by($order);
    if (is_array($limit)) {
      list($limit, $offset) = $limit;
      $this->db->limit($limit, $offset);
    }
    else {
      $this->db->limit($limit);
    }
    $res = $this->db->get(self::PREFIX . $this->module)->result();
    foreach ($res as $key => $item) {
      $res[$key]->pretext = htmlspecialchars_decode($item->pretext);
      $res[$key]->text    = htmlspecialchars_decode($item->text);
    }
    $this->count = $this->db->
      select("count(id) as cnt")->
      from(self::PREFIX . $this->module)->
      where($filter)->get()->row()->cnt;
    return $res;
  }
  
  /**
   * Добавление 
   */
  function Add($params) {
    $insert = array(
      'name' => trim($params['name'])
    );
    return $this->db->insert(self::PREFIX . $this->module, $insert);
  }
  
  
  /**
   * Удаление
   */
  function Del($id) {
    $res = $this->db->delete(self::PREFIX . $this->module, array(
      'id' => (int)$id
    ));
    if (!$res) {
      return "Ошибка БД";
    }
    return;  
  }
  
  /**
   * Получение инфы
   */
  function Get($id) {
    $info = $this->db->where('id', (int)$id)->get(self::PREFIX . $this->module)->row();
    $info->pretext  = htmlspecialchars_decode($info->pretext);
    $info->text     = htmlspecialchars_decode($info->text);
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
    $config = $this->config->item('cmodules');
    $checkArray = array_merge($config['default'], (array)$config[$this->module]);
    $update = array_intersect_key($params, $checkArray);
    $ret = $this->db->update(self::PREFIX . $this->module, $update, array('id' => $id));
    if (!$ret) {
      return "Ошибка БД!";
    }
    return;
  }
  
  /**
   * Заливка картинки
   */
  function addFile($id, $file) {
    $id = (int)$id;
    
    $info = $this->Get($id);
    if ($info->image <> '') {
      unlink(ROOT . $info->image);
    }
    
    if (!$file['size']) {
      throw new Exception("Нулевой размер файла!");
    }
    $ext = getExt($file['name']);
    $filebasis = $id . "_" . md5_file($file['tmp_name']);
    $fileweb   = CMODULES_DIR . $filebasis . "." . $ext;
    $filename  = ROOT.$fileweb;
    if (is_file($filename)) {
      unlink($filename);
    }
    $res = rename($file['tmp_name'], $filename);
    
    if (!$res) {
      throw new Exception("Не получилось переместить временный файл!");
    }
    
    chmod($filename,  0755);
    
    $update = array(
      'image' => $fileweb
    );
    $res = $this->db->update(self::PREFIX . $this->module, $update, array('id' => $id));
    if (!$res) {
      throw new Exception("Ошибка БД.");
    }
    return;
  }
}
?>