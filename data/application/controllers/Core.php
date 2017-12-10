<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Core extends S_Module {

  public $page;
  public $template;
  public $module;
  protected $URL;
  protected $requestURI;

  /**
   * __construct
   * @author Alexey
   */
  function __construct() {
    parent::__construct();
    $this->URL = $this->uri->uri_string();
    $this->requestURI = $_SERVER['REQUEST_URI'];
    $this->load->model('core_model');
  }

  /**
   * index action
   */
  function index() {
    try {
      ini_set("error_reporting", E_ALL & ~E_NOTICE);
      $this->_checkPage();
      $this->_loadLibraries();
      $this->_loadPage();
      $this->_handleCoreActions();
      $this->_loadTemplate();
      $this->_loadModule();
/*      $this->_setSeoInfo();
      $this->_loadMapKeys();
      $this->_loadMainMenu();
      $this->_loadCategories();
      $this->_loadShopAddr();
      $this->_loadBanners();*/
      $this->_renderPage();
    } catch (Exception $ex) {
      //file_put_contents(ROOT . '/../../tmp/debug.txt', $ex->getMessage());
      show_404('page');
    }
  }

  /**
   * проверка страницы
   * @author Alexey
   */
  private function _checkPage() {
    // проверка на конечный символ
    if ($this->requestURI && substr($this->requestURI, -1, 1) != '/' && is_ajax() === false) {
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: " . $this->requestURI . '/');
      exit();
    }

    if (substr_count($this->URL, 'index_php?') > 0) {
      $url = str_replace('index_php?', '', $this->URL);
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: /" . trim($url, '/'));
    }

    if (substr_count($this->URL, '/param/price/') > 0) {
      $url = str_replace('/param/price/', '?price=', $this->URL);
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: /" . trim($url, '/'));
    }

    $url = explode('?', $_SERVER['REQUEST_URI']);
    if (!$_SERVER['argv'][1] && !count($_POST) && !preg_match("/\/$/", $url[0])) {
      $url[0] .= '/';
      $url = implode('?', $url);
      header("HTTP/1.1 301 Moved Permanently");
      header("Location: {$url}");
      exit;
    }

    if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
      header('Location: http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'').'://' . substr($_SERVER['HTTP_HOST'], 4).$_SERVER['REQUEST_URI']);
      exit;
    }
  }

  /**
   * Поиск страницы
   * @author Alexey
   */
  private function _loadPage() {
    //  выбираемые поля
    $select = array(
      'id', 'module_id', 'template_id', 'parent_id', 'title', 'url', 'name', 'active','type'
    );
    $select = implode(", ", $select);

    // посмотрим прямой url
    $params = array(
      'url'     => ($this->uri->uri_string() ? $this->uri->uri_string() : ''),
      'active'  => 1,
      'fields'  => $select
    );
    $pages = $this->razdel_model->Get($params);
    $this->page = array_shift($pages);

    // если не нашли по прямому урлу, то посмотрим по подстроке - может быть метод какого-то модуля
    if (!$this->page->id) {
      $params = array(
        'url_like' => $this->uri->uri_string(),
        'active'   => 1,
        'fields'   => $select
      );
      $pages = $this->razdel_model->Get($params);
      $this->page = array_shift($pages);
    }

    // обработчик 404
    if ( !$this->page->id ) {
      $this->page = $this->_404_override();
    }

    // если уж совсем ничего, то показываем, что не нашли ничего
    if ( !$this->page->id ) {
      throw new Exception('Page not fount', 404);
    }
    addCrumbs($this->page->title, $this->page->url);
  }

  /**
   * _404_override
   * @author Alexey
   */
  private function _404_override(){
    header("HTTP/1.1 404 Not Found");
    $page = $this->razdel_model->Get(MODULE_ID_SITEMAP);
    $page->name = $page->title = $page->menu_text = "Ошибка 404. Не найдено.";
    return $page;
  }

  /**
   * загружаем инфу про шаблон
   * @author Alexey
   */
  private function _loadTemplate() {
    $this->template = $this->razdel_model->templateGet($this->page->template_id);
    if ( ! $this->template->file ) {
      throw new Exception("Шаблон не найден");
    }
  }

  /**
   * загрузка модуля
   * @author Alexey
   */
  private function _loadModule() {
    // загружаем инфу про модуль
    $module = $this->razdel_model->moduleGet($this->page->module_id);
    if ( ! $module->file ) {
      throw new Exception("Модуль не найден");
    }

    // запуск модуля
    $class = $module->file;
    $mod = $this->load->module($class);
    $this->module = $mod->{$class}->runModule($this->page);

    // если загрузка асинхронно и отрисовывать все остальное не надо
    if ($this->module->is_ajax) {
      if ($this->module->error_message) {
        $this->load->view('/common/error_message', array('message' => $this->module->error_message));
      }
      print($this->module->text);
      exit();
    }
    $error_message = $this->load->view('/common/error_message', array('message' => $this->module->error_message), true);
    $this->view->set('text', $error_message . $this->module->text);
    $this->view->set('vars', $this->module->vars);
  }

  /**
   * SEO для страниц
   * @author Alexey
   */
  private function _setSeoInfo() {
    // SEO title и текст
    $path = getUrlPath($this->URL);
    $seo = $this->razdel_model->getSeoPage($path);
    $this->view->set('seo', $seo);

    // title
    if ($seo->title) {
      $title = $seo->title;
    }
    elseif ($this->module->vars->htitle) {
      $title = $this->module->vars->htitle;
    }
    elseif ($this->module->title) {
      $title = $this->module->title;
    }
    else {
      $title = $this->page->title;
    }

    // keywords
    if ($seo->keywords) {
      $keywords = $seo->keywords;
    }
    elseif ($this->module->vars->keywords <> '') {
      $keywords = $this->module->vars->keywords;
    }
    else {
      $keywords = config('keywords');
    }

    // description
    if ($seo->description) {
      $description = $seo->description;
    }
    elseif ($this->module->vars->description <> '') {
      $description = $this->module->vars->description;
    }
    else {
      $description = config('description');
    }

    // h1
    if ($seo->h1) {
      $h1 = $seo->h1;
    }
    elseif ($this->module->vars->h1 <> '') {
      $h1 = $this->module->vars->h1;
    }
    else {
      $h1 = $title;
    }

    $this->view->setTitle($title);
    $this->view->set('keywords',    $keywords);
    $this->view->set('description', $description);
    $this->view->set('h1',          $h1);
    $this->view->set('seo_text',    $seo->text);
    $this->view->set('footer_text', $seo->footer_text);
    return;
  }

  /**
   * отрисуем страницу
   * @author Alexey
   */
  private function _renderPage() {
    $this->view->set('page', $this->page);
    $this->view->set('parent_page', $this->parentPage);
    $this->view->setLayout($this->template->file);
    $this->view->render("/page/index.php");
  }

  /**
   * загрузка главного меню
   * @author Alexey
   */
  private function _loadMainMenu() {
    // $main_menu
    $params = [
      'active'    => 1,
      'fields'    => 'id, url, menu_text, module_id',
      'in_menu'   => 1,
      'parent_id' => 0
    ];
    $main_menu = $this->razdel_model->Get($params);
    $this->view->set('main_menu', $main_menu);

    // $footer_menu
    $params = [
      'active'          => 1,
      'fields'          => 'id, url, menu_text, module_id',
      'in_footer_menu'  => 1,
      'parent_id'       => 0
    ];
    $footer_menu = $this->razdel_model->Get($params);
    $this->view->set('footer_menu', $footer_menu);
  }

  /**
   * ключ для карты
   * @author Alexey
   */
  private function _loadMapKeys() {
    // ключ для карты гугла
    $keys = $this->config->item('googleMapKey');
    $this->view->set('googleMapKey', $keys[SERVER]);

    // ключ для карты яндекса
    $keys = $this->config->item('yandexMapKey');
    $this->view->set('yandexMapKey', $keys[SERVER]);
  }

  /**
   * загрузить категории
   * @author Alexey
   */
  private function _loadCategories() {
    $categories = $this->tovar_model->categoryFind(0);
    $this->view->set('categories', $categories);
  }

  /**
   * Загрузить адреса магазинов
   * @author Alexey
   */
  private function _loadShopAddr() {
    $shops = $this->tovar_model->findShop();
    $this->view->set('shops', $shops);
    $city = $this->tovar_model->findCity();
    $this->view->set('city', $city);
  }

  /**
   * Баннеры
   * @author Alexey
   */
  private function _loadBanners() {
    $this->load->module('banners');
    $banners = $this->banners->runModule($this->page);
    $this->view->set('banners', $banners);
  }

  /**
   * Загрузка и конфигурация общих библиотек
   * @author Alexey
   */
  private function _loadLibraries() {
    $this->load->library('email');
    $this->email->initialize([
      'charset'   => 'utf-8',
      'mailtype'  => 'html'
    ]);
  }

  /**
   * Общие действия, не относящиеся к модулям
   * @author Alexey
   */
  private function _handleCoreActions() {
    $action = $this->input->get('core-action');
    $action = array_map(function($item) {
      return ucfirst($item);
      }, explode("-", $action)
    );
    $action = implode("", $action);
    $method = "coreAction{$action}";
    if (method_exists($this->core_model, $method)) {
      $result = $this->page->params = $this->core_model->$method($this->params);
      foreach ((array)$result as $key => $item) {
        $this->view->set($key, $item);
      }
    }
  }

}
