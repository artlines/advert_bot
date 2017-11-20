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
      'active' => 1
    );
    $result = $this->cmodules_model->Find($where);
    return $this->result($razdel_info->title, $this->load->view('list', array('services' => $result), true));
  }
  
  /**
   * подробно
   */
  function _action_item($razdel_info) {
    $id = (int)$this->uri->segment(3);
    $result = $this->cmodules_model->Get($id);
    return $this->result($razdel_info->title . " / " . $result->name, $result->text);
  }
}

?>