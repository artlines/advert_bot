<?php
class Catalog extends S_Module {
  
  // выбранная категория
  protected $category;
  
  // выбранный товар
  protected $product;

  // список категорий для товара
  protected $categories = [];
  
  function __construct() {
    parent::__construct();
    $this->load->model('tovar_model');
    $this->load->model('user_model');
    $this->load->model('category_model');
    $this->load->model('filters_model');
    $this->load->helper('url_helper');
    $this->user_id = (int)$_SESSION['user_id'];
    if (!isset($_SESSION['zakaz'])) {
      $_SESSION['zakaz'] = [
        'summa' => 0,
        'count' => 0
      ];
    }
    define(ADMIN_ID, 0);
    //define('ADMIN_ID', $this->user_model->checkAdminIPS());
  }
  
  /**
   * Основной обработчик модуля
   */
  function runModule($razdel_info) {
    define('CATALOG_URL', $razdel_info->url);
   
    $params = $this->params;

    $method = $this->uri->segment(2);
    if (method_exists($this, $method)) {
      $this->action = $this->uri->segment(3);
      return $this->$method($params);
    }
    
    $check = $this->_checkCategory();
    if ($check !== null) {
      return $check;
    }
    
    $check = $this->_checkProduct();
    if ($check !== null) {
      return $check;
    }
    
    if ($method) {
      header("Location: /404");
      return $this->result(null);
    }
    
    return $this->indexPage();
  }

  /**
   * indexPage
   */
  function indexPage() {
    //$category = current($this->tovar_model->categoryFind(0));
    header("Location: /");
    return $this->result(null);
  }
    
  /**
    * Полный прайс лист
    */
  function price($params) {
    $filter = array('active' => 1);
    $params['catalog'] = $this->tovar_model->Find(array(
      'filter' => $filter, 
      'limit' => 10000000
    ));
    $title = 'Прайс-лист';
    $text  = $this->load->view('price', $params, true);
    addCrumbs($title, '/' . CATALOG_URL . '/price/');
    return $this->result($title, $text);
  }
  
  /**
   * Подготовка параметров для фильтров
   */
  function categoryFilterPrepare($params) {
    // получение товаров
    $res = $this->tovar_model->Find(array(
      'filter' => array(
        'category_id'   => $this->category->id,
        'brand'         => $this->param['brand'],
        'price_from'    => $this->param['price']['from'],
        'price_to'      => $this->param['price']['to'],
        'filters'       => $this->param['filters'],
        'color'         => $this->param['color'],
        'size'          => $this->param['size'],
        'with_photo'    => true,
        'with_ostatok'  => $_SESSION['city']
      ),
      'limit'  => 10000000
    ), true);
    return $this->result(null, json_encode($res));
  }
  
  /**
   * Каталог по категориям
   */
  function category($params) {
    $params['page_url']   = $this->uri->uri_string();
    $params['page_type']  = 'common';
    
    $params['title'] = $title = $this->category->name;
    
    $params['page'] = (int)$this->input->get('page');
    $params['page_config'] = array(
      'filters'   => true,
      'sorters'   => true,
      'paginator' => true
    );
    $params['valid_limit']    = $this->config->item('valid_limit');
    $params['valid_sort']     = $this->config->item('valid_sort');
    $params['default_limit']  = $this->config->item('default_limit');
    $params['sort']       = ($params['get']['sort-type'] ? $params['get']['sort-type'] : $this->config->item('default_sort'));
    $params['sort_name']  = $params['valid_sort'][$params['sort']];
    $this->params['get']['on-page'] = ($this->params['get']['on-page'] ?: $params['default_limit']);
    
    // получение товаров
    $params['products'] = $this->tovar_model->Find([
      'filter' => [
        'category_id' => $this->category->id,
        'brand'       => $this->param['brand'],
        'price_from'  => $this->param['price']['from'],
        'price_to'    => $this->param['price']['to'],
        'filters'     => $this->params['get']['filter'],
        'active'      => 1
      ],
      'page'  => $params['page'],
      'sort'  => $params['sort'],
      'limit' => $this->params['get']['on-page']
    ]);
    $_SESSION['localNavList'] = $params['tovars']['list'];
    $params['categories'] = $this->tovar_model->categoryFind(0);
    foreach ($params['categories'] as $key => $item) {
      if ($item->cnt == 0) {
        continue;
      }
      $params['categories'][$key]->children = $this->tovar_model->categoryFind($item->id);
    }
    
    $params['filters'] = $this->_prepareCategoryFilters($this->category->id);

    $text = $this->load->view('catalog-menu', $params, true);
    if ($this->category->products) {
      $text .= $this->load->view('product-list', array_merge($params, $this->params), true);
    }
    else {
      $text .= $this->load->view('category-text', $params, true);
    }
    $this->_categoryCrumbs();
    return $this->result($title, $text, $this);
  }

  /**
   * Хлебные крошки для категории
   * @author Alexey
   */
  function _categoryCrumbs() {
    $counter = 0;
    $parent_id = $this->category->parent_id;
    $crumbs = [[
      'title' => $this->category->title,
      'url'   => CATALOG_URL . $this->category->url
    ]];
    while ($parent_id) {
      $category = $this->tovar_model->categoryGet($parent_id);
      $parent_id = $category->parent_id;
      $crumbs[] = [
        'title' => $category->title,
        'url'   => CATALOG_URL . $category->url
      ];
      $counter++;
      if ($counter > 10) {
        break;
      }
    }
    foreach (array_reverse($crumbs) as $item) {
      addCrumbs($item['title'], $item['url']);
    }
  }
  
  /**
   * Фильтры для категории
   * @author Alexey
   */
  function _prepareCategoryFilters($id) {
    $filters = [];
    
    $brands = $this->tovar_model->findCatergoryManufacturers($id);
    if (!$brands) {
      return [];
    }
    $filters['brands'] = new stdClass();
    $filters['brands']->name = 'Бренды';
    foreach ($brands as $item) {
      $filters['brands']->values[$item->id] = $item->manufacturer_name;
    }
    
    $options = $this->filters_model->getCategoryFilters($id);
    foreach ($options as $item) {
      if (!$filters[$item->id]) {
        $filters[$item->id] = new stdClass();
      }
      $filters[$item->id]->name = $item->name;
      $filters[$item->id]->values[$item->value_id] = $item->value;
    }
    return $filters;
  }
  
  /**
   * проверка на код товара в последнем сегменте url
   * @author Alexey
   */
  protected function _checkProduct() {
    $segment = end($this->segments);
    list($product_id) = explode('-', $segment);
    if (!is_numeric($product_id)) {
      return null;
    }
    $this->product = $this->tovar_model->Get($product_id);
    if (!$this->product->id) {
      return null;
    }
    if (str_replace($product_id, '', $segment) == '') {
      redirect('/' . $this->uri->uri_string() . '-' . $this->product->translite, 'location', 301);
    }
    return $this->tovarFull($this->params);
  }
  
  /**
   * проверка на код категории в последнем сегменте url
   * @author Alexey
   */
  protected function _checkCategory() {
    $segment = end($this->segments);
    $this->category = $this->params['category'] = $this->tovar_model->categoryGet($segment);
    if (!$this->category->id) {
      return null;
    }
    return $this->category($this->params);
  }
  
  /**
   * Дополнительные параметры категории
   * @author Alexey
   */
  function categoryOptions($params) {
    $result = $this->category_model->getValue([
      'category_id' => (int)$params['post']['category_id'],
      'field'       => preg_replace('/[^a-z]+/', '', $params['post']['field'])
    ])->value;
    $result = htmlspecialchars_decode($result);
    return $this->result(null, $result);
  }
  
  /**
   * Поиск параметров товара
   */
  function tovarParams($params) {
    $tovar_id = (int)$params['post']['tovar_id'];
    $tovarInfo = $this->tovar_model->Get($tovar_id);
    $param = array_shift(explode(" ", $this->input->post("param")));
    switch($param) {
      case 'color':
        $this->load->view('tovar_colors', array('tovar' => $tovarInfo));
      break;
      case 'size':
        $this->load->view('tovar_size', array('tovar' => $tovarInfo));
      break;
    }
    return $this->result(null, null, $this);
  }
  
  /**
   * Страница акций
   */
  function sales($params) {
    $this->url_prefix = '/' . CATALOG_URL . '/sales/';
    
    $params['pre_link'] = makeLink($this);
    $params['page']     = (int)$this->nav['page'];
    $params['page_config'] = array(
      'sorters'   => true,
      'paginator' => true
    );
    $params['tovars'] = $this->tovar_model->Find(array(
      'filter' => array(
        'vars' => array(TOVAR_FIELD_ACTION => true)
      ),
      'page'   => $params['page']
    ));
    $_SESSION['localNavList'] = $params['tovars']['list'];
    $title = 'Акции и скидки';
    addCrumbs($title, '/' . CATALOG_URL . '/sales');
    $text = $this->load->view('tovar_list', $params, true);    
    return $this->result($title, $text, $this);
  }
  
  /**
   * Товары для первой страницы
   */
  function firstPage() {
    $params['page_config'] = array(
      'filters'   => false,
      'sorters'   => false,
      'paginator' => false
    );
    $params['products'] = $this->tovar_model->Find(array(
      'filter' => array(
        'with_photo' => true, 
        //'vars'       => array(TOVAR_FIELD_LATEST => 1)
      ),
      'sort'   => 'rand()',
      'limit'  => 10
    ));
    $text = $this->load->view('product-list-items', $params, true);
    
    return $this->result(null, $text);
  }
  
  /**
   * Подготовка хэша для поиска
   */
  function prepareSearchHash($params) {
    $search['search_string'] = $params['post']['search_string'];
    return $this->result(null, encodeLink($search));
  }
  
  /**
   * Поиск по каталогу
   */
  function search($params) {
    $search = $this->input->get('word');
     
    $vars = new stdClass();
    $params['title'] = $title = 'Результаты поиска по строке "'.$search.'"';
    addCrumbs($title, '/' . CATALOG_URL . '/search/?word=' . $search);

    $params['page'] = (int)$this->nav['page'];
    $params['page_type']  = 'common';
    
    // поиск товаров
    $params['products'] = $this->tovar_model->Find(array(
      'filter' => array(
        'active' => 1,
        'search' => $search
      ),
      'page' => $params['page']
    ));
    $_SESSION['localNavList'] = $params['tovars']['list'];
    $params['page_config']['paginator'] = true;
    
    $params['categories'] = $this->tovar_model->categoryFind(0);

    $text = 
      $this->load->view('catalog-menu', $params, true) .
      $this->load->view('product-list', $params, true);
    return $this->result($title, $text, $vars);
  }
  
  /**
   * спецпредложения
   */
  function specialOffers($params) {
    $this->load->model('offers_model');
    $title = "Спецпредложения";
    addCrumbs($title, '/' . CATALOG_URL . '/specialOffers/');
    $params['offers'] = $this->offers_model->Find(array(
      'active' => 1
    ));
    $text = $this->load->view('offers', $params, true);
    return $this->result($title, $text);
  }
  
  /**
   * спецпредложения на главную
   */
  function specialOffersMain($params) {
    $this->load->model('offers_model');
    $params['offers'] = $this->offers_model->Find(array(
      'main_page' => 1,
      'active'    => 1
    ));
    $text = $this->load->view('offers', $params, true);
    return $this->result(null, $text);
  }
  
  /**
   * Рекурсивный поиск родительской директории
   */
  private function _iterateCategory($id) {
    if (!$id) {
      return $this->categories;
    }
    $this->categories[$id] = $this->tovar_model->categoryGet($id);
    if ($this->categories[$id]->parent_id > 0) {
      $this->_iterateCategory($this->categories[$id]->parent_id);
    }
    return $this->categories;
  }
  
  /**
   * Карточка товара - загрузка фото
   */
  function _tovarFull_photoAdd($params) {
    if (!ADMIN_ID) {
      exit;
    }
    $tovar_id = (int)$this->uri->segment(3);
    $this->tovar_model->photoAdd($params['post'], $_FILES['photo']);
    header("location: /catalog/tovarFull/{$tovar_id}");
  }
  
  /**
   * Карточка товара - удаление фото
   */
  function _tovarFull_photoDel($params) {
    if (!ADMIN_ID) {
      exit;
    }
    $tovar_id = (int)$this->uri->segment(3);
    $photo_id = (int)$this->uri->segment(5);
    $this->tovar_model->photoDel($photo_id);
    header("location: /catalog/tovarFull/{$tovar_id}");
  }
  
  /**
   * быстрый просмотр
   * @author Alexey
   */
  function productView($params) {
    $product_id = (int)$this->uri->segment(3);
    if (!$product_id) {
      return $this->result(null);
    }
    $params['product'] = $productInfo = $this->tovar_model->Get($product_id);
    $_SESSION['recent'][$product_id] = $productInfo;
    $text = $this->load->view('modal-quick-view' . $params['post']['view'], $params, true);
    return $this->result(null, $text);
  }
  
  /**
   * Карточка товара
   */
  function tovarFull($params) {
    if (!$this->product->id) {
      return $this->_checkProduct(); 
    }
    $tovar_info = $this->product;
    if ($tovar_info->active <> 1) {
      return $this->result("Товар не найден");
    }
    
    if ($action <> '') {
      $method = "_tovarFull_{$action}";
      if (method_exists($this, $method)) {
        $this->$method($params);
      }
    }
    
    $navArray = explode(",", $_SESSION['localNavList']);
    $currentKey = array_search($tovar_id, $navArray);
    $params['prevTovar'] = $navArray[$currentKey - 1];
    $params['nextTovar'] = $navArray[$currentKey + 1];
    $params['curNavPos'] = $currentKey + 1;
    $params['cntNavPos'] = count($navArray);
        
    $_SESSION['recent'][$tovar_id] = $tovar_info;
    
    $params['product'] = $tovar_info;
    $title = $tovar_info->name;
    $text  = $this->load->view('product-full', $params, true);
    
    // categories
    $result = $this->_iterateCategory(current($tovar_info->category)->category_id);
    $names = [];
    foreach (array_reverse($result) as $item) {
      $names[] = $item->translite_name;
      $link = implode("/", $names);
      addCrumbs($item->name, CATALOG_URL . "/" . $link);
    }
    addCrumbs($title, CATALOG_URL . "/{$link}/{$tovar_info->id}-{$tovar_info->translite}");
    return $this->result($title, $text);
  }
  
  /**
   * поиск похожих товаров
   */
  function findSimilarProducts($params) {
    $tovar_id = (int)$this->uri->segment(3);
    $params['page_config'] = array(
      'filters'   => false,
      'sorters'   => false,
      'paginator' => false
    );
    $params['tovars'] = $this->tovar_model->findSimilarProducts($tovar_id);
    $text = $this->load->view('tovar_list', $params, true);
    return $this->result(null, $text);
  }
  
  /**
   * поиск товаров в комплект
   */
  function findKitProducts($params) {
    $tovar_id = (int)$this->uri->segment(3);
    $params['page_config'] = array(
      'filters'   => false,
      'sorters'   => false,
      'paginator' => false
    );
    $params['tovars'] = $this->tovar_model->findKitProducts($tovar_id);
    $text = ($params['tovars']['count'] == 0 ? 'NOT_FOUND' : $this->load->view('tovar_list', $params, true));
    return $this->result(null, $text);
  }
  
  /**
   * поиск ранее просмотренных товаров
   */
  function findRecentProducts($params) {
    $tovar_id = (int)$this->uri->segment(3);
    $params['recent'] = $_SESSION['recent'];
    unset($params['recent'][$tovar_id]);
    if ( ! empty($params['recent']) ) {
      $text = $this->load->view('recent_tovar', $params, true);
    }
    else {
      $text = 'NOT_FOUND';
    }
    return $this->result(null, $text);
  }
  
  /**
   * Управление заказом
   */
  function zakaz($params) {
    $method = $this->uri->segment(3);
    $method = "_zakaz_{$method}";
    if (method_exists($this, $method)) {
      $mod = $this->$method($params);
      $mod->is_ajax = true;
      return $mod;
    }
    $method = "zakaz_{$method}";
    if (method_exists($this, $method)) {
      return $this->$method($params);
    }
    exit(0);
  }
  
  /**
   * Добавление товара к заказу
   */
  function _zakaz_addToZakaz($params) {
    $count    = (float)str_replace(',', '.', $params['post']['prod-count']);
    $tovar_id = (int)$params['post']['id'];
    $variant  = $params['post']['variant'];
    $tovar_info = $this->tovar_model->Get($tovar_id, ['variant' => $variant]);
    $count = max($tovar_info->dop[PRODUCT_FIELD_MIN_ORDER]->value, $count);
    if ($count > 0 && $tovar_info->id > 0 && $tovar_info->active) {
      $_SESSION['zakaz']['count'] += $count;
      $_SESSION['zakaz']['summa'] += $count * round($tovar_info->price * (1 - $tovar_info->discount_size / 100));
      $_SESSION['zakaz']['detail'][$tovar_info->id][$variant] += $count;
    }
    return $this->result(null);
  }
  
  
  /**
   * Управление корзиной
   */
  function cart($params) {
    $action = $this->uri->segment(3);
    $method = "_cart_{$action}";
    if (method_exists($this, $method)) {
      $mod = $this->$method($params);
      $mod->is_ajax = true;
      return $mod;
    }
    $method = "cart_{$action}";
    if (method_exists($this, $method)) {
      return $this->$method($params);
    }
    show_404('page');
    exit(0);
  }
  
  /**
   * загрузка инфы по корзине 
   */
  function _cart_loadItems($params) {
    foreach ((array)$_SESSION['zakaz']['detail'] as $key => $item) {
      foreach ((array)$item as $variant => $count) {
        $keyname = encode("{$key}_{$variant}");
        $params['pay_table'][$keyname] = $this->tovar_model->Get($key, ['variant' => $variant]);
        $params['pay_table'][$keyname]->count    = $count;
        $params['pay_table'][$keyname]->variant  = $variant;
      }
    }
    $params['tovar_count'] = (int)$_SESSION['zakaz']['count'];
    $params['tovar_summa'] = (float)$_SESSION['zakaz']['summa'];
    $text = $this->load->view('cart_block_items', $params, 1);
    return $this->result(null, $text);
  }
  
  /**
   * загрузка инфы по корзине 
   */
  function _cart_loadItogs($params) {
    $params['tovar_count'] = (int)$_SESSION['zakaz']['count'];
    $params['tovar_summa'] = (float)$_SESSION['zakaz']['summa'];
    $text = $this->load->view('cart_block_itogs', $params, 1);
    return $this->result(null, $text);
  }
  
  /**
   * Корзина и заказ
   */
  function cart_full($params) {
    $params['pay_table'] = array();
    foreach ((array)$_SESSION['zakaz']['detail'] as $key => $item) {
      foreach ((array)$item as $variant => $count) {
        $keyname = encode("{$key}_{$variant}");
        $params['pay_table'][$keyname] = $this->tovar_model->Get($key, ['variant' => $variant]);
        $params['pay_table'][$keyname]->count    = $count;
        $params['pay_table'][$keyname]->variant  = $variant;
      }
    }
    
    $text = $this->load->view('cart_full', $params, true);
    addCrumbs($mod->title, '/' . CATALOG_URL . '/cart/full');
    return $this->result("Оформление заказа", $text);
  }
  
  /**
   * Оформление заказа
   */
  function _cart_contactInfo($params) {
    $this->user_id = (int)$_SESSION['user_id'];
    $params['userInfo'] = $this->user_model->Get($this->user_id);
    $values = $params['userInfo']['values'];
    $params['userInfo']['t_kl']==USER_TYPE_UR
      ? $params['address'] = $values[USER_V_ADDR]['value']
      : $params['address'] = $values[USER_V_CITY]['value']." ".$values[USER_V_STREET]['value']." ".$values[USER_V_ROOM]['value'];
    $params['typePay']  = $this->tovar_model->typePayList();
    $params['typeMove'] = $this->tovar_model->typeMoveList();
    $text = $this->load->view('cart_contactInfo', $params, true);
    return $this->result(null, $text);
  }
  
  /**
   * Отправка заказа
   */
  function _cart_zakazSend($params) {
    $this->user_id = (int)$_SESSION['user_id'];
    $email                    = trim($params['post']['email']);
    $password                 = $params['post']['password'];
    $contacts['name']         = $params['post']['name'];
    $contacts['address']      = $params['post']['address'];
    $contacts['phone']        = $params['post']['phone'];
    $contacts['comment']      = $params['post']['comment'];
    $contacts['type_move_id'] = (int)$params['post']['type_move_id'];
    $contacts['type_pay_id']  = (int)$params['post']['type_pay_id'];
    
    if (!$contacts['phone']) {
      return $this->result(null, get_json('Телефон обязателен для заполнения.'));
    }
    
    if (!$contacts['address'] && $contacts['type_move_id']==ZAKAZ_TYPE_MOVE_COURIER) {
      return $this->result(null, get_json('Для оформления доставки введите свой адрес.'));
    }
    
    if ($_SESSION['zakaz']['count']==0) {
      return $this->result(null, get_json('Заказ должен быть непустой.'));
    }
    
    // если пользователь ввел свой e-mail и он уже есть в базе, то проверяем пароль
    /*$user_id = $this->user_model->getIdByName($email);
    if ($user_id && !$this->user_id) {
      $auth = $this->user_model->Auth($email, $password);
      if (!$auth) {
        echo get_json('Пароль неверен. <a href="/user/recovery" target="_blank">Восстановить пароль</a>.');
        return;
      }
    }
    
    // если ввели email и такого юзера нет в базе, то регистрируем
    $userData = array();
    if (!$user_id && $email<>'') {
      $userData = array(
        'username' => $email,
        'password' => genPass(8),
        't_kl'     => USER_TYPE_FIZ,
        'values'   => array(
          USER_V_PHONE => $contacts['phone'],
          USER_V_FIO   => $contacts['name']
        )
      );
      $hash = $this->user_model->Register($userData);
      $this->user_model->registerConfirm($hash);
    }
    */
    
    $zakaz_id = $this->tovar_model->zakazSend($_SESSION['zakaz']['detail'], $params['post']['comment'], $contacts);
    if (!$zakaz_id) {
      return $this->result(null, get_json('Ошибка при оформлении заказа! Пожалуйста, свяжитесь с нами!'));
    }
    $_SESSION['last_zakaz'] = $zakaz_id;
    return $this->result(null, get_json(''));
  }
  
    /**
   * заказ отправлены
   */
  function cart_zakazSuccess($params) {
    $zakaz_id = (int)$_SESSION['last_zakaz'];
    if (!$zakaz_id) {
      return $this->result("Ошибка! Заказ не найден!", "");
    }
    $zakaz = $this->tovar_model->zakazGet($zakaz_id);
    $zakaz->detail = $this->tovar_model->zakazGetDetail($zakaz_id);
    $zakaz_number = $zakaz->number;
    return $this->result(
      "Заказ №{$zakaz->number} принят.",
      $this->load->view('cart_zakazSuccess', $zakaz, true)
    );
  }
  
  /**
   * Изменение позиции в заказе
   */
  function _cart_itemChange($params) {
    $count    = (int)$params['post']['cnt'];
    $keyname  = $params['post']['id'];
    list($tovar_id, $variant) = explode('_', decode($keyname));
    $tovar_id = (int)$tovar_id;
    $count    = ($count >= 0 ? $count : 1);
    $tovar_info = $this->tovar_model->Get($tovar_id, ['variant' => $variant]);
    if ($tovar_info->id > 0 && $tovar_info->active) {
      $summa = $count * $tovar_info->price;
      if ($count > 0) {
        $_SESSION['zakaz']['detail'][$tovar_id][$variant] = $count;
      }
      else {
        unset($_SESSION['zakaz']['detail'][$tovar_id][$variant]);
      }
      $count_itog = 0;
      $summa_itog = 0;
      foreach ($_SESSION['zakaz']['detail'] as $tovar_id => $item) {
        foreach ((array)$item as $variant => $count) {
          $tovar_info = $this->tovar_model->Get($tovar_id, ['variant' => $variant]);
          $summa_itog += $count * $tovar_info->price;
          $count_itog += $count;
        }
      }
      $_SESSION['zakaz']['count'] = $count_itog;
      $_SESSION['zakaz']['summa'] = $summa_itog;
      $res = array(
        'summa'      => $summa,
        'summa_itog' => $summa_itog
      );
    }
    else {
      $res = array(
        'summa'      => 0,
        'summa_itog' => 0
      );
    }
    return $this->result(null, json_encode($res));
  }
  
  /**
   * Страница бренда
   */
  function brand($params) {
    $id = (int)$this->uri->segment(3);
    $params['brandInfo'] = $this->tovar_model->manufacturerGet($id);
    $text = $this->load->view('brand', $params, true);
    return $this->result($params['brandInfo']->manufacturer_name, $text);
  }
  
  /**
   * Товары для страницы бренда
   */
  function brandPage() {
    $params['page_config'] = array(
      'filters'   => false,
      'sorters'   => false,
      'paginator' => false
    );
    $params['tovars'] = $this->tovar_model->Find(array(
      'filter' => array(
        'with_photo'      => true, 
        'manufacturer_id' => (int)$this->uri->segment(3)
      ),
      'sort'   => 'rand()',
      'limit'  => 4
    ));
    $text = $this->load->view('tovar_list', $params, true);
    
    return $this->result(null, $text, $this);
  }
  
  /**
   * Карусель брендов
   */
  function brandCarousel($params) {
    $brands = $this->tovar_model->manufacturerFind(array(
      'main_page' => 1
    ));
    $text = $this->load->view('brandCarousel', array('brands' => $brands));
    return $this->result(null, $text);
  }
  
  /**
   * экспорт прайс-листа в csv в формате для ganzara.ru
   */
  function catalogGanzaraCSV($params) {
    $time = date("Y-m-d.H_i_s");
    header("http/1.0 200 OK");
    header("Content-Type: text/csv; charset=windows-1251");
    header("Content-Disposition: attachment; filename=" . SERVER . ".{$time}.csv");
    $filter = array('active' => 1);
    $params['catalog'] = $this->tovar_model->Find(array('filter' => $filter, 'limit' => 10000000));
    $text = $this->load->view('catalog/GanzaraCSV', $params, true);
    $text = str_replace("&quot;", "", $text);
    echo utf2win($text);
    $mod->is_ajax = true;
    return $mod;
  }
  
  /**
   * Экспорт прайс листа в нужных форматах
   */
  function priceExport($params) {
    $default_charset = 'utf-8';
    $default_format  = 'csv';
    // смотрим параметры выгрузки
    $view    = $this->uri->segment(3);
    $format  = ($this->uri->segment(4) ? $this->uri->segment(4) : $default_format);
    $charset = ($this->uri->segment(5) ? $this->uri->segment(5) : $default_charset);
    // если не указали получателя выгрузки
    if (!$view) {
      show_404('page');
      exit(1);
    }
    // форматы прайсов
    switch ($format) {
      case 'xml':
        header("Content-Type: text/xml; charset={$charset}");
      break;
      case 'csv':
        $time = date("Y-m-d.H_i_s");
        header("Content-Type: text/csv; charset={$charset}");
        header("Content-Disposition: attachment; filename=" . SERVER . ".{$time}.csv");
      break;
    }
    $params['CI'] = $this;
    $params['category'] = $this->tovar_model->categoryFindAll();
    $filter = array('active' => 1, 'price >' => 0, 'with_category' => 1);
    $params['catalog'] = $this->tovar_model->Find(array('filter' => $filter, 'limit' => 10000000));
    $params['charset'] = $charset;
    $this->load->view("priceExport_{$view}", $params);
    $mod->is_ajax = true;
    return $mod;
  }
  
  /**
   * Пересобрать поисковые строки для всех товаров
   */
  function resetSearchString() {
    set_time_limit(0);
    $catalog = $this->tovar_model->Find(array(
      'filter' => array(), 
      'limit' => 10000000
    ));
    foreach ($catalog['search'] as $item) {
      $this->tovar_model->resetSearchString($item->id);
      $count++;
    }
    echo "count:" . count($catalog['search']) . " iterated:{$count}";
    exit(0);
  }
  
  /**
   * Сохранения товара в личный кабинет пользователя
   */
  function saveTovarToUser($params) {
    $this->tovar_model->saveUserTovar(array(
      'user_id'   => $this->user_id,
      'tovar_id'  => $params['post']['id']
    ));
    return $this->result(null);
  }
  
  /**
   * Наличие товара в магазинах
   */
  function tovarAvailability($params) {
    $tovar_id = (int)$this->uri->segment(3);
    if (!$tovar_id) {
      return $this->result(null, 'Товар не найден');
    }
    $res = $this->tovar_model->getTovarOstatok($tovar_id, 0, 0, $_SESSION['city']);
    $text = $this->load->view('tovarOstatok', $res, true);
    return $this->result(null, $text);
  }
  
  /**
   * Запрос цены на товар
   */
  function getPrice($params) {
    $tovar_id = (int)$this->uri->segment(3);
    $text = $this->load->view("getPriceForm", array('tovar_id' => $tovar_id), true);
    return $this->result(null, $text);
  }
  
  /**
   * Запрос цены на товар - отправка формы
   */
  function getPriceSendForm($params) {
    $tovar_id = (int)$this->uri->segment(3);
    $params['tovarInfo'] = $this->tovar_model->Get($tovar_id);
    if (!$tovar_id) {
      return $this->result(null, get_json('Товар не найден'));
    }
    if (!$params['post']['company']) {
      return $this->result(null, get_json('Укажите название компании'));
    }
    if (!$params['post']['fio']) {
      return $this->result(null, get_json('Укажите Ваше имя'));
    }
    if (!$params['post']['phone']) {
      return $this->result(null, get_json('Укажите контактный телефон'));
    }
    $text = $this->load->view("getPriceSendForm", $params, true);
    mail_admin($text, 'Запрос цены');
    return $this->result(null, get_json(''));
  }
  
  /**
   * Изменение товара
   */
  function tovarSave($params) {
    if (!ADMIN_ID) {
      exit;
    }
    $tovar_id = (int)$this->uri->segment(3);
    
    if ($params['post']['action'] == 'text') {
      $ret = $this->tovar_model->Set($tovar_id, array(
        'price'       => $params['post']['price'],
        'description' => $params['post']['description'],
        'active'      => 1
      ));
      return $this->result(null, get_json($ret));
    }
  }
  
  function getWithoutPhoto($params) {
    $filter = array(
      'active'     => 1, 
      'price >'    => 0, 
      'with_photo' => 0
    );
    $params['catalog'] = $this->tovar_model->Find(array('filter' => $filter, 'limit' => 10000000));
    $text = $this->load->view('price', $params, true);
    return $this->result("Товары без фото", $text);
  }

  /**
   * Импорт каталога
   */
  function importCatalog() {
    exit;
    $file = file("http://87.224.216.43/exportShop.txt");
    if (empty($file)) {
      return $this->result(null);
    }
    foreach ($file as $item) {
      $item = unserialize(base64_decode($item));
      foreach ($item as $key => $value) {
        $item[$key] = iconv("cp1251", "utf-8", $value);
      }
      if (!$item['T_PAR_3']) {
        continue;
      }
      
      $category[$item['T_PAR_3']] = array(
        'id'        => $item['T_PAR_3'],
        'name'      => $item['N_PAR_3'],
        'parent_id' => NULL,
        'level'     => 1
      );
      $category[$item['T_PAR_2']] = array(
        'id'        => $item['T_PAR_2'],
        'name'      => $item['N_PAR_2'],
        'parent_id' => $item['T_PAR_3'],
        'level'     => 2
      );
      $manufacturer[$item['T_PAR_1']] = array(
        'id'    => $item['T_PAR_1'],
        'name'  => $item['N_PAR_1'],
      );
        
      $tovar[$item['TOVCOD']] = array(
        'code'            => $item['T_PAR_0'],
        'parent_code'     => $item['LINK'],
        'code_1c'         => $item['TOVCOD'],
        'name'            => $item['N_PAR_1'] . " " . $item['N_PAR_0'],
        'price'           => $item['PRICEE'],
        'manufacturer_id' => $item['T_PAR_1'],
        'description'     => $item['TOV_NAME'],
        'active'          => 1
      );
      
      $tovarCategory[$item['TOVCOD']] = $item['T_PAR_2'];
    }
    
    if (!empty($category)) {
      $this->db->query("CREATE TEMPORARY TABLE tmp_category (id integer, name text, parent_id integer, level integer)");
      foreach ($category as $item) {
        $this->db->insert('tmp_category', $item);
      }
      $this->db->trans_start();
      $this->db->query("DELETE FROM shop_tovar_category WHERE category_id NOT IN (SELECT id FROM tmp_category)");
      $this->db->query("DELETE FROM shop_category WHERE id NOT IN (SELECT id FROM tmp_category) AND level = 2");
      $this->db->query("DELETE FROM shop_category WHERE id NOT IN (SELECT id FROM tmp_category) AND level = 1");
      $this->db->query("
        INSERT INTO shop_category (id, name, parent_id, level)
        SELECT id, name, parent_id, level
        FROM tmp_category
        WHERE id NOT IN (SELECT id FROM shop_category)
      ");
      $this->db->trans_complete();
      $this->db->query("DROP TABLE tmp_category");
    }
    
    if (!empty($manufacturer)) {
      $this->db->query("CREATE TEMPORARY TABLE tmp_manufacturer (id integer, name text)");
      foreach ($manufacturer as $item) {
        $this->db->insert('tmp_manufacturer', $item);
      }
      $this->db->query("
        INSERT INTO shop_tovar_manufacturer (id, manufacturer_name)
        SELECT id, name
        FROM tmp_manufacturer
        WHERE id NOT IN (SELECT id FROM shop_tovar_manufacturer)
      ");
      $this->db->query("DROP TABLE tmp_manufacturer");
    }
    
    $this->db->trans_start();
    if (!empty($tovar)) {
      $this->db->query("DELETE FROM shop_tovar_category");
      $this->db->query("UPDATE shop_tovar SET active = 0");
      foreach ($tovar as $item) {
        $isset = $this->tovar_model->GetByCode($item['code_1c']);
        if ($isset->id) {
          $this->tovar_model->Set($isset->id, array(
            'name'        => $item['name'],
            'parent_code' => $item['parent_code'],
            'active'      => 1
          ));
          $item_id = $isset->id;
        }
        else {
          $this->tovar_model->Add($item);
          $item_id = $this->tovar_model->new_id;
        }
        $this->db->insert('shop_tovar_category', array(
          'tovar_id'    => $item_id,
          'category_id' => $tovarCategory[$item['code_1c']]
        ));
      }
    }
    $this->db->trans_complete();
    $this->db->query("OPTIMIZE TABLE shop_tovar");
    $this->db->query("OPTIMIZE TABLE shop_tovar_category");
    $this->db->query("OPTIMIZE TABLE shop_category");
  }
  
  /**
   * Логирование действий в файл
   */
  function log($message) {
    if ( ! is_resource($this->logger) ) {
      $this->logger = fopen(LOG_DIR . date("Y-m-d") . ".log", 'a');
      $this->uniq_id = strtoupper(uniqid());
      if (DEBUG) echo "<pre>";
    }
    if ( ! is_resource($this->logger) ) {
      throw new Exception("log file not found opened");
    }
    if (DEBUG) echo $message . "\n";
    fwrite($this->logger, date("Y-m-d H:i:s") . " [{$this->uniq_id}] " . $message . "\n");
    return;
  }
  
  /**
   * деструктор
   */
  function __desctruct() {
    parent::__desctruct();
    if ( is_resourse($this->logger) ) {
      fclose($this->logger);
    }
    return;
  }
}

?>