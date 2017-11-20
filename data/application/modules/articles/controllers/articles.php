<?
class Articles extends Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('articles_model');
  }
  
  function run($razdel_info) {
    $id = $this->uri->segment(2);
    if (is_numeric($id)) {
      $data['articles'] = $this->articles_model->Get(array('id' => $id,'active' => true));
      $text  = $this->load->view('articles_full', $data, true);
      $title = $data['articles']['title'];
      return $this->result($title, $text);    }
    else {
      $data['articles'] = $this->articles_model->Get(array('active' => true));
      $mod->text = $this->load->view('articles', $data, true);
    }
    return $mod;
  }
}

?>