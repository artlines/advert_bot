<?
class Review extends Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('cmodules_model');
    $this->cmodules_model->module = 'review';
  }
  
  function run($razdel_info) {
    $action = $this->uri->segment(2);
    $method = "_action_{$action}";
    if (method_exists($this, $method)) {
      return $this->$method($razdel_info);
    }
    $page = ($action > 0 ? (int)$action : 1);
    $where = array(
      'active' => 1
    );
    $_on_page = 4;
    $limit = array($_on_page, ($page - 1) * $_on_page);
    $result = $this->cmodules_model->Find($where, $limit);
    $res = array(
      'review' => $result,
      'page'   => $page,
      'pages'  => ceil($this->cmodules_model->count / $_on_page)
    );
    return $this->result($razdel_info->title, $this->load->view('list', $res, true));
  }
  
  /**
   * подробно
   */
  function _action_item($razdel_info) {
    $id = (int)$this->uri->segment(3);
    $result = $this->cmodules_model->Get($id);
    return $this->result($razdel_info->title . " / " . $result->firstname . " " . $result->lastname, $result->text);
  }
}

?>