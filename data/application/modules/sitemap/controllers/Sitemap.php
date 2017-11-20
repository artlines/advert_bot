<?
class Sitemap extends S_Module {

  function __construct() {
    parent::__construct();
    $this->load->model('razdel_model');
  }
  
  function runModule($razdel_info) {
    if ($razdel_info->url == 'sitemap.xml') {
      return $this->xml($razdel_info);
    }
    else {
      return $this->html($razdel_info);
    }
  }

  /**
   * XML карта сайта
   */
  function xml($razdel_info) {
    header("Content-type: text/xml;charset=utf-8");
    $data['razdel']  = $this->razdel_model->Get(array(
      'active'  => 1,
      'fields'  => 'url'
    ));
    $text = $this->load->view('sitemap', $data, true);
    return $this->result(null, $text);
  }
  
  /**
   * HTML карта сайта
   */
  function html($razdel_info) {
    $data['razdel']  = $this->razdel_model->Get(array(
      'active'  => 1,
      'fields'  => 'url, title, module_id'
    ));
    $data['categories'] = [];
    $result = $this->tovar_model->categoryFind(0);
    foreach ($result as $item) {
      $item->children = $this->tovar_model->categoryFind($item->id);
      $data['categories'][$item->id] = $item;
    }
    $data['tovar_model'] = $this->tovar_model;
    $text = $this->load->view('sitemap_html', $data, true);
    return $this->result($razdel_info->title, $text);
  }
}

?>