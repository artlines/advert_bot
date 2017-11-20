<?php
class Banners extends S_Module {

  function __construct() {
    parent::__construct();
    $this->load->model('banners_model');
  }
  
  function runModule($razdel_info) {
    $where = array(
      'razdel_id' => $razdel_info->id,
      'active'    => 1
    );
    $result = $this->banners_model->Find($where);
    foreach ($result as $item) {
      $banners[$item->alias] = $item;
    }
    foreach ((array)$banners as $key => $item) {
      $view = 'banner' . ($item->slider ? 'Slider' : '') . ($item->filetype == 'swf' ? '_swf' : '');
      $banners[$key]->text = $this->load->view($view, array('banner' => $item), true);
    }
    return $banners;
  }
}

?>