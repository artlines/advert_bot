<?
class Search extends Controller {

  function __construct() {
    parent::__construct();
  }
  
  function run($razdel_info) {
    $search = trim($this->input->post('search'));
    $search = htmlspecialchars($search, ENT_QUOTES);
    $data['title']  = 'Результаты поиска по строке "'.$search.'"';
    $data['result'] = $this->razdel_model->search($search);
    $this->load->view('search', $data);
    $mod->is_ajax = true;
    return $mod;
  }
}

?>