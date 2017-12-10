<?

class Articles_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Получить статьи
   */
  function Get($params) {
    $url     = $params['url'];
    $limit  = (int)$params['limit'];
    $active = (int)$params['active'];
    $active ? $where = " and active=1" : 0;
    if ($url) {
      $res = $this->db->query("select * from resource where  url='{$url}' {$where}")->row_array();
      $res['small'] = htmlspecialchars_decode($res['content_top']);
      $res['text']  = htmlspecialchars_decode($res['content']);
    }
    else {
      $limit>0 ? $lim = " limit {$limit}" : $lim = "";
      $res = $this->db->query("select * from resource where type='articles' and url <> 'articles' {$where} order by tm_add desc {$lim}")->result_array();
      foreach ($res as $v => &$k) {
        $k['small'] = htmlspecialchars_decode($k['content_top']);
        $k['text']  = htmlspecialchars_decode($k['content']);
      }
    }
    return $res;
  }
  
  /**
   * Добавление статьи
   */
  function Add($params) {
    $insert = array(
      'title'  => $params['title'],
      'date'   => ($params['date'] ? $params['date'] : date("Y-m-d")),
      'active' => 0
    );
    $ret = $this->db->insert("articles", $insert);
    return $ret;
  }
  
  /**
   * Удаление статьи
   */
  function Del($id) {
    $id = (int)$id;
    if (!$id)
      return false;
    $this->db->where('id', $id);
    $ret = $this->db->delete('articles'); 
    return $ret;
  }
  
  /**
   * Изменение статьи
   */
  function Set($params) {
    $id = (int)$params['id'];
    if (!$id)
      return false;
    $update = array(
      'title'  => $params['title'],
      'small'  => $params['small'],
      'text'   => $params['text'],
      'date'   => $params['date'],
      'active' => ($params['active']=='true' ? 1 : 0)
    );
    $this->db->where('id', $id);
    $ret = $this->db->update('articles', $update);
    return $ret;
  }
  
}

?>
