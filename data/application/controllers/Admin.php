<?php

class Admin extends MX_Controller {
  
  private $params;
  private $id;      // object ident
  private $action;  // object action

  function __construct() {
    parent::__construct();
    include(ROOT . "/ckeditor/ckeditor.php");
    $this->view->setLayout("admin.php");
    if ( ! $this->session->user_admin ) {
      header("Location: /login");
      exit();
    }
    unset($this->user_id);
    
    $params['CI'] = $this;
    foreach ($_REQUEST as $key => $value) {
      $this->params['post'][$key] = recursive_trim($this->input->post($key));
    }
  }
  
  function index() {
    $this->view->render("/page/index");
  }

  ##########################################################################
  ### CATEGORY
  ##########################################################################

  /**
   * Категории
   * @author Alexey
   */
  function category() {
    $this->load->model('category_model');
    $this->methodСonstructor('category');
  }

  /**
   * Категории - список
   * @author Alexey
   */
  function _category_default() {
    $items = $this->category_model->find();
    $this->view->set('items', $items);
    return $this->view->render('/admin/category/main');
  }

  /**
   * Добавить категорию - форма
   * @author Alexey
   */
  function _category_addForm() {
    return $this->load->view('/admin/category/addForm');
  }

  /**
   * Добавить категорию
   * @author Alexey
   */
  function _category_add() {
    try {
      $this->category_model->add($this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Редактировать категорию - форма
   * @author Alexey
   */
  function _category_editForm() {
    $id = (int)$this->params['post']['id'];
    $info = $this->category_model->get($id);
    $this->load->view('/admin/category/editForm', $info);
  }

  /**
   * Редактировать категорию
   * @author Alexey
   */
  function _category_edit() {
    $id = (int)$this->uri->segment(4);
    try {
      $this->category_model->set($id, $this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Удалить категорию
   * @author Alexey
   */
  function _category_del() {
    $id = (int)$this->params['post']['id'];
    $this->category_model->del($id);
  }
  /**
   * Конструктов групп методов
   * @author Alexey
   */
  protected function methodСonstructor($type) {
    $this->action   = $this->uri->segment(3);
    $this->id       = (int)$this->uri->segment(4);
    if (!$this->action) {
      $this->action = 'default';
    }
    $method = "_{$type}_{$this->action}";
    if (!method_exists($this, $method)) {
      echo "Ошибка! Метод {$method} не найден";
      return null;
    }
    return $this->$method($this->params);
  }
  
  ####################
  ### SITE advert ####
  ####################

  const ADVERT_PAGINATION_SIZE = 20;

  /**
   * Объявления
   * @author Alexey
   */
  function adverts() {
    $this->load->model('adverts_model');
    $this->methodСonstructor(__FUNCTION__);
  }

  /**
   * Страница Объявления
   * @author Alexey
   */
  function _adverts_default() {
    $this->load->library('telegraph');

    /*$res = $this->telegraph->createAccount([
      'short_name'   => 'Adverts',
      'author_name'  => 'Crypty Bot Advert'
    ]); adebug($res);exit;*/
    $count = $this->adverts_model->count();
    $this->view->set('count', $count);
    return $this->view->render('/admin/adverts/main');
  }

  /**
   * Поиск
   * @author Alexey
   */
  function _adverts_search() {
    $items = $this->adverts_model->find([
      'limit'   => self::ADVERT_PAGINATION_SIZE,
      'search'  => $this->params['post']['text'],
      'offset'  => (int)$this->params['post']['page'] * self::ADVERT_PAGINATION_SIZE
    ]);
    $this->load->view('/admin/adverts/list', ['items' => $items]);
  }

  /**
   * редактирование - форма
   * @author Alexey
   */
  function _adverts_editForm() {
    $id = (int)$this->params['post']['id'];
    $info = $this->adverts_model->get($id);
    $this->load->view('/admin/adverts/editForm', $info);
  }

  /**
   * Редактировать Объявление
   * @author Alexey
   */
  function _adverts_edit() {
    $id = (int)$this->uri->segment(4);
    try {
      $this->adverts_model->set($id, $this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }
  ##########################################################################
  ### USERS
  ##########################################################################

  /**
   * Пользователи
   * @author Alexey
   */
  function users() {
    $this->load->model('user_model');
    $this->methodСonstructor('users');
  }

  /**
   * Страница польователей
   * @author Alexey
   */
  function _users_default() {
    $count = $this->user_model->count();
    $this->view->set('count', $count);
    return $this->view->render('/admin/users/main');
  }

  /**
   * Поиск
   * @author Alexey
   */
  function _users_search() {
    $items = $this->user_model->find(['limit' => 30, 'search' => $this->params['post']['text']]);
    $this->load->view('/admin/users/list', ['items' => $items]);
  }

  /**
   * редактирование - форма
   * @author Alexey
   */
  function _users_editForm() {
    $id = (int)$this->params['post']['id'];
    $info = $this->user_model->get($id);
    $this->load->view('/admin/users/editForm', $info);
  }

  /**
   * Редактировать категорию
   * @author Alexey
   */
  function _users_edit() {
    $id = (int)$this->uri->segment(4);
    try {
      $this->user_model->set($id, $this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  ##########################################################################
  ### Настройки
  ##########################################################################

  /**
   * Настройки
   * @author Alexey
   */
  function config() {
    $this->load->model('category_model');
    $this->methodСonstructor('config');
  }

  ##########################################################################
  ### ГЕО
  ##########################################################################

  /**
   * Гео - главная страница
   * @author Alexey
   */
  function geo() {
    $this->load->model('geo_model');
    $this->methodСonstructor('geo');
  }

  /**
   * Гео - области
   * @author Alexey
   */
  function region() {
    $this->load->model('geo_model');
    $this->methodСonstructor('geo_region');
  }

  /**
   * Гео - города
   * @author Alexey
   */
  function city() {
    $this->load->model('geo_model');
    $this->methodСonstructor('geo_city');
  }

  /**
   * Гео - список
   * @author Alexey
   */
  function _geo_default() {
    $items = $this->geo_model->find();
    $this->view->set('items', $items);
    return $this->view->render('/admin/geo/main');
  }

  /**
   * Добавить область - форма
   * @author Alexey
   */
  function _geo_region_addForm() {
    return $this->load->view('/admin/geo/region_addForm');
  }

  /**
   * Добавить город - форма
   * @author Alexey
   */
  function _geo_city_addForm() {
    return $this->load->view('/admin/geo/city_addForm');
  }

  /**
   * Добавить область
   * @author Alexey
   */
  function _geo_region_add() {
    try {
      $this->geo_model->add($this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Добавить город
   * @author Alexey
   */
  function _geo_city_add() {
    try {
      $this->geo_model->addCity($this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Редактировать область - форма
   * @author Alexey
   */
  function _geo_region_editForm() {
    $id = (int)$this->params['post']['id'];
    $info = $this->geo_model->get($id);
    $this->load->view('/admin/geo/region_editForm', $info);
  }

  /**
   * Редактировать город - форма
   * @author Alexey
   */
  function _geo_city_editForm() {
    $id = (int)$this->params['post']['id'];
    $info = $this->geo_model->getCity($id);
    $this->load->view('/admin/geo/city_editForm', $info);
  }

  /**
   * Редактировать город
   * @author Alexey
   */
  function _geo_city_edit() {
    $id = (int)$this->uri->segment(4);
    try {
      $this->geo_model->setCity($id, $this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Редактировать область
   * @author Alexey
   */
  function _geo_region_edit() {
    $id = (int)$this->uri->segment(4);
    try {
      $this->geo_model->set($id, $this->params['post']);
    }
    catch (Exception $e) {
      echo json_encode(['err' => $e->getMessage()]);
    }
    echo json_encode(['err' => '']);
  }

  /**
   * Удалить город
   * @author Alexey
   */
  function _geo_city_del() {
    $id = (int)$this->params['post']['id'];
    $this->geo_model->delCity($id);
  }

  /**
   * Удалить область
   * @author Alexey
   */
  function _geo_region_del() {
    $id = (int)$this->params['post']['id'];
    $this->geo_model->del($id);
  }

  ##########################################################################
  ###         SITE services and reviews
  ##########################################################################
  /**
   * Общий функцонал для модулей услуг и отзывов
   */
  function cmodules($module, $action = '') {
    $this->model = $this->load->model("cmodules_model");
    $this->model->module = $this->module = $module;
    if ($action<>'') {
      $this->id = (int)$this->uri->segment(5);
      $method = "_cmodules_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      $params['CI'] = $this;
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = $this->input->post($key);
      }
      try {
        return $this->$method($params);
      }
      catch (Exception $e) {
        return $this->load->view('/common/error_message', array('message' => "Ошибка! ".$e->getMessage()));
      }
    }
    $this->view->set('list', $this->model->Find());
    $this->view->render("/admin/cmodules/main");
    return;
  }
  
  /**
   * Просмотр
   */
  function _cmodules_detail($params) {
    if ( $params['post']['name']<>'' ) {
      $ret = $this->model->Set($this->id, $params['post']);
      echo get_json($ret);
      return;
    }
    $dt['item'] = $this->model->Get($this->id);
    $dt['editor1'] = getEditor( 'e_pretext', $dt['item']->pretext );
    $dt['editor2'] = getEditor( 'e_text', $dt['item']->text );
    $dt['dopFields'] = $this->config->item('cmodules');
    return $this->load->view("/admin/cmodules/detail", $dt);
  }
  
  /**
   * Добавление 
   */
  function _cmodules_add($params) {
    if ( $params['post']['name']<>'' ) {
      $ret = $this->model->Add($params['post']);
      if (!$ret) {
        echo "Ошибка БД!";
      }
      else {
        return $this->load->view("/common/nyro_close");
      }
    }
    $this->load->view('/admin/cmodules/add');
  }
  
  /**
   * Управление - изображение
   */
  function _cmodules_photo($params) {
    $params['action'] = $this->uri->segment(6);
    $params['id']     = $this->id;
    if ($params['action'] == 'add') {
      try {
        if ( !$_FILES['filename']['name'] ) {
          throw new Exception("Файл не найден!");
        }
        $this->model->addFile($this->id, $_FILES['filename']);
        return $this->load->view('common/nyro_close');
      }
      catch (Exception $e) {
        $this->load->view('/common/error_message', array('message' => "Ошибка! ".$e->getMessage()));
        return $this->load->view('/admin/cmodules/photo_load', $params);
      }
    }
    return $this->load->view('/admin/cmodules/photo_load', $params);
  }
  
  /**
   * Удаление
   */
  function _cmodules_del($params) {
    $res = $this->model->Del($this->id);
    echo get_json($res);
  }
  
  /**
   * SEO-раздел
   */
  function seo($action = 'main') {
    $method = "_seo_{$action}";
    if (!method_exists($this, $method)) {
      echo "Ошибка! Метод {$method} не найден";
      return;
    }
    $params['CI'] = $this;
    foreach ($_REQUEST as $key => $value) {
      $params['post'][$key] = trim($this->input->post($key));
    }
    return $this->$method($params);
  }
  
  /**
   * SEO - основная страница
   */
  function _seo_main($params) {
    return $this->view->render('/admin/seo/main');
  }
  
  /**
   * Обработка URL
   */
  function _seo_showURL($params) {
    $path = getUrlPath($params['post']['url']);
    $page = (array)$this->razdel_model->getSeoPage($path);
    $page['editor']         = getEditor('textHTML',       $page['text'],        '95%', '300');
    $page['editor_footer']  = getEditor('footerTextHTML', $page['footer_text'], '95%', '300');
    if (!$page['id']) {
      $page['path'] = $path;
    }
    return $this->load->view('/admin/seo/detail', $page);
  }
  
  /**
   * Сохранение параметров для выбранного урла
   */
  function _seo_saveURL($params) {
    $res = $this->razdel_model->setSeoPage($params['post']);
    if (!$res) {
      echo get_json('Ошибка БД');
    }
    echo get_json('');
  }
  
  /**
   * Выход
   */
  function logout() {
    $this->user_model->logout();
    header("location: /admin");
    exit;
  }
}
?>