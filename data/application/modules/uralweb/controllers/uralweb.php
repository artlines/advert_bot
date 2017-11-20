<?
class Uralweb extends Controller {

  function __construct() {
    parent::__construct();
  }
  
  function run($razdel_info) {
    header("Content-type: text/xml;charset=utf-8");
    $this->load->view('uralweb', $data);
    $mod->is_ajax = true;
    return $mod;
  }
}

?>