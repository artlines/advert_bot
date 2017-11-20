<?php
/**
 * Description of S_Model
 *
 * @author Alexey
 */
class S_Model extends CI_Model {
  
  protected $params;
  
  /**
   * __construct
   * @author Alexey
   */
  function __construct() {
    parent::__construct();
    $this->params = new stdClass();
  }
}
