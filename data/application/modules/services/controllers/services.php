<?
class Services extends Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('cmodules_model');
    $this->cmodules_model->module = 'services';
  }
  
  function run($razdel_info) {
    $action = $this->uri->segment(2);
    $method = "_action_{$action}";
    if (method_exists($this, $method)) {
      return $this->$method($razdel_info);
    }
    $where = array(
      'active'    => 1,
      'parent_id' => 0
    );
    $result = $this->cmodules_model->Find($where);
    return $this->result($razdel_info->title, $this->load->view('list', array('services' => $result), true));
  }
  
  /**
   * подробно
   */
  function _action_item($razdel_info) {
    $id = (int)$this->uri->segment(3);
    $where = array(
      'active'    => 1,
      'parent_id' => $id
    );
    $vars->left_menu = $this->cmodules_model->Find($where);
    foreach ($vars->left_menu as $key => $item) {
      $vars->left_menu[$key]->url = 'services/item/' . $item->id;
    }
    $result = $this->cmodules_model->Get($id);
    $parent = ($result->parent_id > 0 ? $this->cmodules_model->Get($result->parent_id) : NULL);
    return $this->result(
      "<a href='/{$razdel_info->url}' class='default standart'>" . $razdel_info->title . "</a> / " . 
      ($parent <> NULL ? "<a href='/{$razdel_info->url}/item/{$parent->id}' class='default standart'>" . $parent->name . "</a> / " : "") .
      $result->name, 
      $result->text, 
      $vars
    );
  }
}

?>