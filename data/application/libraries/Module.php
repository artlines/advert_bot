<?php

class Module {
  
  /**
   * __construct
   * @author Alexey
   */
  function __construct() {
    foreach (is_loaded() as $var => $class) {
      $this->$var =& load_class($class);
    }
    foreach ($_POST as $key => $value) {
      $this->params['post'][$key] = $this->input->post($key);
    }
    foreach ($_GET as $key => $value) {
      $this->params['get'][$key] = $this->input->get($key);
    }
    
  }
}