<?php

class Razdel_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Получить раздел
   */
  function Get($id, $params = array()) {
    $where = array();
    if ( is_array($id) ) {
      $params = $id;
    }
    elseif ( is_numeric($id) ) {
      $where['id'] = (int)$id;
    }
    if ($params['url']) {
      $params['url'] = htmlspecialchars($params['url'], ENT_QUOTES);
      $where['url']  = trim($params['url']);
    }
    if ($params['url_like']) {
      $where["position(url in '{$params['url_like']}') = "] = 1;
      $where["url !="] = '';
      unset($params['url_like']);
    }
    $fields = ($params['fields'] ? $params['fields'] : '*');
    unset($params['fields']);
    $where = array_merge($where, (array)$params);
    $query = $this->db->select("{$fields}, date(date) as date, content_type")
                      ->from("razdel")
                      ->where($where)
                      ->order_by("priority")
                      ->get();
    if ( is_numeric($id) > 0 ) {
      $res = $query->row();
      isset($res->text) ? $res->text = htmlspecialchars_decode($res->text) : 0;
    }
    else {
      $res = $query->result();
    }
    return $res;
  }
  
  /**
   * Получить контент блок по метке
   */
  function getBlockByName($name) {
    $result = $this->db->where(array(
      'url'           => $name,
      'active'        => 1,
      'content_type'  => CONTENT_TYPE_CBLOCK
    ))->get('razdel')->row();
    if (isset($result->text)) {
      $result->text = htmlspecialchars_decode($result->text);
    }
    return $result;
  }
  
  /**
   * Добавить раздел
   */
  function Add($params) {
    $insert = array(
      'parent_id'     => (int)$params['parent_id'], 
      'content_type'  => (int)$params['content_type'], 
      'name'          => trim($params['name']),
      'url'           => trim($params['url']),
      'priority'      => (int)$this->db->query("SELECT MAX(priority) as prio FROM razdel")->row()->prio + 1
    );
    return $this->db->insert('razdel', $insert);
  }
  
  /**
   * Удалить раздел
   */
  function Del($id) {
    $id = (int)$id;
    $childs = $this->Get(array('parent_id' => $id));
    if ( count($childs) > 0 ) {
      return "У раздела есть подразделы. Удаление невозможно!";
    }
    $this->db->trans_start();
    $this->db->delete('banners_razdel', array('razdel_id' => $id));
    $where = array(
      'id' => $id
    );
    $res = $this->db->delete('razdel', $where);
    $this->db->trans_complete();
    if (!$res) {
      return "Ошибка БД";
    }
    return;
  }
  
  /**
   * Изменить раздел
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
      'text'            => $params['text'],      
      'name'            => $params['name'],       
      'title'           => $params['title'],      
      'menu_text'       => $params['menu_text'],  
      'module_id'       => (int)$params['module_id'],  
      'template_id'     => (int)$params['template_id'],
      'priority'        => (int)$params['priority'],   
      'in_menu'         => $params['in_menu'],    
      'in_footer_menu'  => $params['in_footer_menu'],    
      'url'             => $params['url'],        
      'active'          => $params['active'],     
      'date'            => $params['date'],     
      'keywords'        => $params['keywords'],     
      'description'     => $params['description']         
    );
    
    $ret = $this->db->update('razdel', $update, array('id' => $id));
    if (!$ret) {
      return "Ошибка БД!";
    }
    return;
  }
  
  /**
   * Получить доступные модули
   */
  function moduleGet($id = 0) {
    $id = (int)$id;
    if ($id>0) {
      return $this->db->query("select * from razdel_modules where id={$id}")->row();
    }
    else {
      return $this->db->query("select * from razdel_modules order by id asc")->result();
    }
  }
  
  /**
   * Получить доступные шаблоны
   */
  function templateGet($id = 0) {
    $id = (int)$id;
    if ($id > 0) {
      return $this->db->query("select * from razdel_templates where id={$id}")->row();
    }
    else {
      return $this->db->query("select * from razdel_templates order by id asc")->result();
    }
  }
  
  
  
  function configGet() {
    $res = $this->db->query("SELECT * FROM config ORDER BY id ASC")->result_array();
    return $res;
  }
  
  function configSet($key, $value) {
    $value = trim($value);
    $key = trim($key);
    if ($value=='' || $key=='')
      return false;
    $ret = $this->db->query("UPDATE config t2 SET value='{$value}' WHERE t2.key='{$key}'");
    return $ret;
  }
  
  function search($search) {
    $search = trim($search);
    if ($search=='')
      return false;
    $search = mb_strtolower($search);
    $query = "select t1.id, t1.name, lower(t1.text) as text, case when t1.parent_id=0 then t1.url else concat(t2.url,'/page/',t1.id) end as url 
              from razdel t1 left join razdel t2 on t1.parent_id=t2.id
              where lower(t1.text) like lower('%$search%')";
    $res = $this->db->query($query)->result_array();
    foreach ($res as $v => &$k) {
      $k['text'] = htmlspecialchars_decode($k['text']);
      $k['text'] = strip_tags($k['text']);
      $pos = mb_strpos($k['text'], $search);
      if ($pos==0) {
        unset($res[$v]);
        continue;
      }
      $pos>100
        ? $shift = 100
        : $shift = $pos;
      $k['text'] = mb_substr($k['text'], $pos-$shift, 200);
      $k['text'] = str_replace($search, "<b style='color:#00DD00; font-size:14px;'><i>{$search}</i></b>", $k['text']);
      $k['text'] = "...".$k['text']."...";
    }
    return $res;
  }
  
  /**
   * Поиск типов контента
   */
  function findContentType() {
    return $this->db->order_by('id')->get('razdel_content_type')->result();
  }
  
  /**
   * Получение типа контента
   */
  function getContentType($id) {
    return $this->db->where('id', (int)$id)->get('razdel_content_type')->row();
  }   
  
  /**
   * Получить информацию SEO для страницы
   */
  function getSeoPage($path) {
    $res = $this->db->where('path', (string)$path)->get('razdel_seo')->row();
    @$res->text         = htmlspecialchars_decode($res->text);
    @$res->footer_text  = htmlspecialchars_decode($res->footer_text);
    return $res;
  }
  
  /**
   * Изменить информацию SEO для страницы
   */
  function setSeoPage($params) {
    $path = (string)$params['path'];
    $valid = array(
      'title'         => 'Заголовок',
      'text'          => 'Текст',
      'footer_text'   => 'Текст footer',
      'keywords'      => 'Keywords',
      'description'   => 'Description',
      'h1'            => 'H1'
    );
    $params = array_intersect_key($params, $valid);
    $isset = $this->getSeoPage($path);
    if ($isset->id) {
      $res = $this->db->update('razdel_seo', $params, array('path' => $path));
    }
    else {
      $res = $this->db->insert('razdel_seo', array_merge($params, array('path' => $path)));
    }
    return $res;
  }
}