<?php
/**
 * common parent module
 * @author Alexey
 */
class S_Module extends MX_Controller {
  
  // params
  protected $params = [];
  
  // url segments
  protected $segments;
  
  function __construct() {
    parent::__construct();
    foreach ($_REQUEST as $key => $value) {
      $this->params['post'][$key] = $this->input->post($key);
      $this->params['get'][$key]  = $this->input->get($key);
    }
    $this->segments = $this->uri->segment_array();
  }
  
  /**
   * Вспомогательная функция для возврата результата
   */
  protected function result($title, $text = '', $vars = '') {
    $mod = new stdClass();
    $mod->title = $title;
    $mod->text  = $text;
    $mod->vars  = $vars;
    if ($title === null) {
      $mod->is_ajax = true;
    }
    return $mod;
  }
}
