<?
class Portfolio extends Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('cmodules_model');
    $this->cmodules_model->module = 'portfolio';
  }
  
  function run($razdel_info) {
    $action = $this->uri->segment(2);
    $service_id = (int)$this->uri->segment(3);
    $method = "_action_{$action}";
    if (method_exists($this, $method)) {
      return $this->$method($razdel_info);
    }
    $where = array(
      'active' => 1
    );
    $this->cmodules_model->module = 'services';
    $result = $this->cmodules_model->Find($where);
    return $this->result(
      $razdel_info->title, 
      $this->load->view('list', array('services' => $result, 'serv_id' => $service_id), true)
    );
  }
  
  /**
   * подробно
   */
  function _action_item($razdel_info) {
    $id = (int)$this->uri->segment(3);
    $result = $this->cmodules_model->Get($id);
    $this->cmodules_model->module = 'services';
    $service = $this->cmodules_model->Get($result->service_id);
    return $this->result(
      "<a href='/{$razdel_info->url}' class='default standart'>" . $razdel_info->title . "</a> / " . 
      "<a href='/{$razdel_info->url}/load/{$result->service_id}' class='default standart'>" . $service->name . "</a> / " . 
      $result->name, 
      $result->text
    );
  }
  
  /**
   * Поиск элементов портфолио по услуге
   */
  function _action_list() {
    $service_id = (int)$this->uri->segment(3);
    $where = array(
      'active'      => 1,
      'service_id'  => $service_id
    );
    if (!$service_id) {
      unset($where['service_id']);
    }
    $result = $this->cmodules_model->Find($where);
    $text = $this->load->view('item_list', array('items' => $result), true);
    return $this->result(null, $text);
  }
  
  /**
   * Получение заголовка
   */
  function _action_get_title($razdel_info) {
    $service_id = (int)$this->uri->segment(3);
    if (!$service_id) {
      return $this->result(null, $razdel_info->title);
    }
    $this->cmodules_model->module = 'services';
    $result = $this->cmodules_model->Get($service_id);
    return $this->result(null, 
      "<a href='/{$razdel_info->url}' class='default standart'>" . $razdel_info->title . "</a> / " . $result->name
    );
  }
}

?>