<?php
class Text extends S_Module {
  

  function __construct() {
    parent::__construct();
  }
  
  function runModule($razdel_info) { 
    $params = array(
      'fields' => 'text', 
      'active' => 1
    );
    $mod = new stdClass();
    $razdel = $this->razdel_model->Get($razdel_info->id, $params);
    $mod->text    = $razdel->text;
    $mod->is_ajax = $this->params['post']['ajax'];
    return $mod;
  }
}

?>