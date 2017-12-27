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
      unset($params['url_like']);
    }
    $fields = ($params['fields'] ? $params['fields'] : '*');
    unset($params['fields']);
    $where = array_merge($where, (array)$params);
    $query = $this->db->select("{$fields}, tm_upd as date")
      ->from("resource")
      ->where($where)
      ->order_by("url DESC")
      ->get();
    if ( is_numeric($id) > 0 ) {
      $res = $query->row();
      //Проверка на ресурс замены
      if ($res->replace_id){
      }
      isset($res->content) ? $res->text = htmlspecialchars_decode($res->content) : 0;
    }
    else {
      $res = $query->result();
    }
    return $res;
  }

  /**
   * Добавить раздел
   */
  function Add($params) {
    $insert = array(
      'parent_id' => (int)$params['parent_id'],
      'name'      => trim($params['name']),
      'url'       => trim($params['url'])
    );
    return $this->db->insert('resource', $insert);
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
    $this->db->delete('banners_resource', array('resource_id' => $id));
    $where = array(
      'id' => $id
    );
    $res = $this->db->delete('resource', $where);
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
      'text'        => $params['text'],
      'name'        => $params['name'],
      'title'       => $params['title'],
      'menu_text'   => $params['menu_text'],
      'module_id'   => (int)$params['module_id'],
      'template_id' => (int)$params['template_id'],
      'priority'    => (int)$params['priority'],
      'in_menu'     => $params['in_menu'],
      'url'         => $params['url'],
      'active'      => $params['active'],
      'date'        => $params['date']
    );

    $ret = $this->db->update('resource', $update, array('id' => $id));
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
      return $this->db->query("select * from resource_modules where id={$id}")->row();
    }
    else {
      return $this->db->query("select * from resource_modules order by id asc")->result();
    }
  }

  /**
   * Получить доступные шаблоны
   */
  function templateGet($id = 0) {
    $id = (int)$id;
    if ($id > 0) {
      return $this->db->query("select * from resource_templates where id={$id}")->row();
    }
    else {
      return $this->db->query("select * from resource_templates order by id asc")->result();
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
              from resource t1 left join resource t2 on t1.parent_id=t2.id
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
   * Получить информацию SEO для страницы
   */
  function getSeoPage($path) {
    $path = str_replace('/?', '?', $path);
    $res = $res = $this->getResourceViaUrl($path);

    if(!$res){
      list($url, $params) = explode('?', $path);
      $res = $this->getResourceViaUrl($url);
    }

    @$res->text         = htmlspecialchars_decode($res->content);
    @$res->footer_text  = htmlspecialchars_decode($res->content_bottom);
    @$res->befor_text   = htmlspecialchars_decode($res->content_top);

    return $res;
  }

  /**
   * получить ресурс по url
   * @return object;
   */
  function getResourceViaUrl($url)
  {
    return $this->db->query("
      SELECT max(id) as id, parent_id, object_id, title, h1, url, name,
      description, keywords, content, content_top, content_bottom, replace_id
      FROM resource
      WHERE url='{$url}'
      GROUP BY parent_id, object_id, title, h1, url, name,
      description, keywords, content, content_top, content_bottom
      ORDER BY id DESC  
      ")->row();
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
      'befor_text'    => 'Текст перед списком',
      'keywords'      => 'Keywords',
      'description'   => 'Description',
      'h1'            => 'H1'
    );
    $params = array_intersect_key($params, $valid);
    $isset = $this->getSeoPage($path);
    // удалить пустой абзац в конце
    $params['befor_text'] = preg_replace('/&lt;p&gt;\s*(?:&amp;nbsp;)?\s*&lt;\/p&gt;$/','', $params['befor_text']);
    if ($isset->id) {
      $res = $this->db->update('resource_seo', $params, array('path' => $path));
    }
    else {
      $res = $this->db->insert('resource_seo', array_merge($params, array('path' => $path)));
    }
    return $res;
  }

  /**
   * Извлекает страницы с выборками фильтра
   */
  function getFilterSEOPages(){
    $res = $this->db
      ->where('path LIKE \'%param%\' AND path NOT LIKE \'%single/yes%\' AND
            (path LIKE \'%/price/%\' OR path LIKE \'%/filtres/%\' OR path LIKE \'%/color/%\' OR path LIKE \'%/size/%\')')
      ->get('resource_seo')
      ->result();
//      path LIKE '%param%' AND path NOT LIKE '%single/yes%' AND (path LIKE '%/price/%' OR path LIKE '%/filtres/%' OR path LIKE '%/color/%')
    return $res;
  }

  function getTemplates(){
    $result = $this->db->get('resource_templates')->result();
    foreach ($result as $res){
      $data[$res->category][] = $res;
    }
    return $data;
  }

  function getTemplate($id){
    $result = $this->db->where('id', $id)->get('resource_templates')->row();
    $result->categories = $this->db->query("select distinct category from resource_templates")->result();
    if ($result->category == 'Базовые'){
      $result->code = htmlentities(file_get_contents(APP_ROOT.'/views/'.$result->file));
    }else{
      $result->code = htmlentities(file_get_contents(MODULES_ROOT.'/'.$result->file));
    }

    return $result;
  }

}