<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * View Class
 */
class View {
  private $layoutVars = array();

  private $vars = array();
  private $layout = 'layout/layout_main';
  private $title = '';
  private $razdel = '';
  private $rightTitle = '';
  private $left_menu = array();

  function setLayout($template){
    $this->layout = $template;
  }

  function setTitle($title){
    $this->title = $title;
  }

  function setRazdel($razdel){
    $this->razdel = $razdel;
  }

  function setSubRazdel($subrazdel_id){
    $this->subrazdel_id = $subrazdel_id;
  }

  function setMenu($menu){
    $CI = &get_instance();
    //$this->layoutVars['menu'] = $CI->load->view($menu, '', true);
    //return $CI->menu->setMenu($menu);
  }
  
  function set($varName, $value){
    $this->vars[$varName] = $value;
  }

  function setHelp($str){
    $this->setGlobal('ecc_help', $str);
  }

  function setGlobal($varName, $value){
    $this->layoutVars[$varName] = $value;
  }
  
  function setInfo($str) {
    $this->set('sys_info_message', $str);
  }

  function setError($str) {
    $this->set('sys_error_message', $str);
  }

  /**
   * Fetch template and return it.
   *
   * @param String $template
   */
  function fetch($template){
    /* @var CI CI_Base */
    $CI = &get_instance();

    $content = $CI->load->view($template, $this->vars, true);

    $this->layoutVars['text'] = $content;
    $this->layoutVars['title'] = $this->title;
    $this->layoutVars['rightTitle'] = $this->rightTitle;
    $this->layoutVars['widgets'] = $this->widgets;
    $this->layoutVars['razdel'] = $this->razdel;
    $this->layoutVars['subrazdel_id'] = $this->subrazdel_id;

    return $CI->load->view($this->layout, $this->layoutVars, true);
  }

  /**
   * Renders template to $content.
   *
   * @param String $template
   */
  function render($template='default.php'){
    echo $this->fetch($template);
  }
}
?>