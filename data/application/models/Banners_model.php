<?php
class Banners_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Поиск баннеров
   */
  function Find($params = array()) {
    $sort   = ($params['active'] ? 'rand() * t1.percent'                 : 't1.name asc');
    $select = ($params['active'] ? 't1.*, t3.alias, t3.width, t3.height' : 't1.*');
    $res = $this->db
      ->select($select)->distinct()
      ->from('banners t1')
      ->join('banners_places_items t2', 't1.id = t2.banner_id', 'left')
      ->join('banners_places t3',       't3.id = t2.place_id',  'left')
      ->join('banners_razdel t4',       't1.id = t4.banner_id', 'left')
      ->where($params)
      ->order_by($sort)
      ->get()->result();
    foreach ($res as $key => $item) {
      if ($item->slider) {
        $res[$key]->sliderFile = $this->db->where('banner_id', $item->id)->get('banners_slider_files')->result();
      }
    }
    return $res;
  }
  
  /**
   * Добавление баннера
   */
  function Add($params) {
    $insert = array(
      'name' => trim($params['name'])
    );
    return $this->db->insert('banners', $insert);
  }
  
  /**
   * Удаление баннера
   */
  function Del($id) {
    $delete = array(
      'id' => (int)$id
    );
    $this->db->trans_start();
    $this->db->delete('banners_places_items', array('banner_id' => $delete['id']));
    $this->db->delete('banners_razdel', array('banner_id' => $delete['id']));
    $this->db->delete('banners', $delete);
    $res = $this->db->trans_complete();
    if (!$res) {
      return "Ошибка БД";
    }
    return;  
  }
  
  /**
   * Получение инфы о баннере
   */
  function Get($id) {
    $id = (int)$id;
    $info = $this->db->where('id', $id)->get('banners')->row();
    $info->places = $this->db->query(
     "select t1.place_id, t2.name
      from banners_places_items t1
        inner join banners_places t2 on t1.place_id=t2.id
      where t1.banner_id={$id}"
    )->result();
    $info->razdel = $this->db->query(
     "select t1.razdel_id, t2.name
      from banners_razdel t1
        inner join razdel t2 on t1.razdel_id=t2.id
      where t1.banner_id={$id}"
    )->result();
    if ($info->slider) {
      $res = $this->db->where('banner_id', $id)->get('banners_slider_files')->result();
      foreach ($res as $item) {
        $info->sliderFile[$item->id] = $item;
      }
    }
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
      'name'     => $params['name'],
      'tm_start' => $params['tm_start'],
      'tm_stop'  => $params['tm_stop'],
      'active'   => $params['active'],
      'link'     => $params['link'],
      'percent'  => $params['percent'],
      'slider'   => $params['slider']
    );
    
    $ret = $this->db->update('banners', $update, array('id' => $id));
    if (!$ret) {
      return "Ошибка БД!";
    }
    return;
  }
  
  /**
   * Изменение разделов для отображения баннера
   */
  function setRazdel($banner_id, $razdel) {
    $banner_id = (int)$banner_id;
    $razdel = array_map("intval", (array)$razdel);
    $banner = $this->Get($banner_id);
    foreach ($banner->razdel as $key => $item) {
      $banner_razdel[$item->razdel_id] = $item->razdel_id;
    }
    $for_insert = array_diff((array)$razdel, (array)$banner_razdel);
    $for_delete = array_diff((array)$banner_razdel, (array)$razdel);
    
    $this->db->trans_start();
    if (!empty($for_insert)) {
      foreach ($for_insert as $razdel_id) {
        $this->db->insert("banners_razdel", array('banner_id' => $banner_id, 'razdel_id' => $razdel_id));
      }
    }
    if (!empty($for_delete)) {
      $delete = implode(",", $for_delete);
      $this->db->query("delete from banners_razdel where banner_id={$banner_id} and razdel_id in ({$delete})");
    }
    $ret = $this->db->trans_complete();
    
    if (!$ret) {
      return "Ошибка БД.";
    }
    return;
  }
  
  /**
   * Получить список мест размещения
   */
  function getPlaces() {
    return $this->db->order_by('name')->get('banners_places')->result();
  }
  
  /**
   * Изменение разделов для отображения баннера
   */
  function setPlace($banner_id, $places) {
    $banner_id = (int)$banner_id;
    $places = array_map("intval", (array)$places);
    $banner = $this->Get($banner_id);
    foreach ($banner->places as $key => $item) {
      $banner_places[$item->place_id] = $item->place_id;
    }
    $for_insert = array_diff((array)$places, (array)$banner_places);
    $for_delete = array_diff((array)$banner_places, (array)$places);
    
    $this->db->trans_start();
    if (!empty($for_insert)) {
      foreach ($for_insert as $place_id) {
        $this->db->insert("banners_places_items", array('banner_id' => $banner_id, 'place_id' => $place_id));
      }
    }
    if (!empty($for_delete)) {
      $delete = implode(",", $for_delete);
      $this->db->query("delete from banners_places_items where banner_id={$banner_id} and place_id in ({$delete})");
    }
    $ret = $this->db->trans_complete();
    
    if (!$ret) {
      return "Ошибка БД.";
    }
    return;
  }
  
  /**
   * Добавление фото
   */
  function addSliderFile($banner_id, $file, $link) {
    $banner_id = (int)$banner_id;
    
    if (!$file['size']) {
      throw new Exception("Нулевой размер файла!");
    }
    $ext = getExt($file['name']);
    $filebasis = $banner_id . "_" . md5_file($file['tmp_name']);
    $fileweb   = BANNERS_DIR . $filebasis . "." . $ext;
    $filename  = ROOT.$fileweb;
    $res = rename($file['tmp_name'], $filename);
    
    if (!$res) {
      throw new Exception("Не получилось переместить временный файл!");
    }
    
    chmod($filename,  0755);
    
    $insert = array(
      'banner_id' => $banner_id,
      'filename'  => $fileweb,
      'filetype'  => $ext,
      'link'      => $link
    );
    $res = $this->db->insert('banners_slider_files', $insert);
    if (!$res) {
      throw new Exception("Ошибка БД.");
    }
    return;
  }
  
  /**
   * Удаление файла баннера
   */
  function delSliderFile($banner_id, $file_id) {
    $banner_id = (int)$banner_id;
    $banner = $this->Get($banner_id);
    
    if (!$banner->sliderFile[$file_id]) {
      return;
    }
    
    if ( is_file (ROOT . $banner->sliderFile[$file_id]->filename) ) {
      $res = unlink(ROOT . $banner->sliderFile[$file_id]->filename);
      if (!$res) {
        throw new Exception("Не получилось удалить файл " . ROOT . $banner->filename);
      }
    }
    
    $res = $this->db->delete('banners_slider_files', array(
      'banner_id' => $banner_id,
      'id'        => $file_id
    )); 
    if (!$res) {
      throw new Exception("Ошибка БД!");
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
    $fileweb   = BANNERS_DIR . $filebasis . "." . $ext;
    $filename  = ROOT.$fileweb;
    $res = rename($file['tmp_name'], $filename);
    
    if (!$res) {
      throw new Exception("Не получилось переместить временный файл!");
    }
    
    chmod($filename,  0755);
    
    $update = array(
      'filename' => $fileweb,
      'filetype' => $ext
    );
    $res = $this->db->update('banners', $update, array('id' => $banner_id));
    if (!$res) {
      throw new Exception("Ошибка БД.");
    }
    return;
  }
  
  /**
   * Удаление файла баннера
   */
  function delFile($banner_id) {
    $banner_id = (int)$banner_id;
    $banner = $this->Get($banner_id);
    
    if (!$banner->filename) {
      return;
    }
    
    if ( is_file (ROOT . $banner->filename) ) {
      $res = unlink(ROOT . $banner->filename);
      if (!$res) {
        throw new Exception("Не получилось удалить файл " . ROOT . $banner->filename);
      }
    }
    
    $res = $this->db->update('banners', array('filename' => '', 'filetype' => ''), array('id' => $banner_id)); 
    if (!$res) {
      throw new Exception("Ошибка БД!");
    }
    return;
  }
  
}
  
?>