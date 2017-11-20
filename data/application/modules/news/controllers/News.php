<?php
class News extends S_Module {

  function __construct() {
    parent::__construct();
    $this->load->model('news_model');
  }
  
  function runModule($razdel_info) {
    $id = $this->uri->segment(2);
    if (is_numeric($id)) {
      $data['news'] = $this->news_model->Get(array('id' => $id,'active' => true));
      $text  = $this->load->view('news_full', $data, true);
      return $this->result($data['news']['title'], $text);
    }
    else {
      $data['news'] = $this->news_model->Get(array('active' => true));
      $text = $this->load->view('news', $data, true);
      return $this->result($razdel_info->title, $text);
    }
    return $mod;
  }
}

?>