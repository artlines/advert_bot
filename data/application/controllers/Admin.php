<?php
/**
 * @property Category_model $category_model
 * @property CI_URI $uri
 * @property View $view
 */
class Admin extends MX_Controller {
  
  private $params;
  private $id;      // object ident
  private $action;  // object action

  function __construct() {
    parent::__construct();
    include(ROOT."/ckeditor/ckeditor.php");
    $this->view->setLayout("admin.php");
    if ( $_SESSION['user_admin'] <> 1 ) {
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

  /**
   * Категории
   * @author Alexey
   */
  function category() {
    $this->methodСonstructor('category');
    return $this->view->render('/admin/category/main');
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

  function advert($action='') {
    if ($action){
      $this->id = (int)$this->uri->segment(4);
      $method = "_advert_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      return $this->$method($this->params);
    }

  }

  /**
   * Удаление Объявления
   */
  function _advert_del($params) {
    $res = $this->razdel_model->Del($this->id);
    echo get_json($res);
  }
  
  /**
   * Изменение информации о разделе
   */
  function _razdel_detail($params) {
    if ( $params['post']['action'] == 'save_common' ) {
      $ret = $this->razdel_model->Set($this->id, $params['post']);
      echo get_json($ret);
      return;
    }
    $params['content_type_fields'] = array(
      1 => array()
    );
    
    $dt['razdel']   = $this->razdel_model->Get($this->id);
    $dt['editor']   = getEditor('text', $dt['razdel']->text);
    $dt['modules']  = $this->razdel_model->moduleGet();
    $dt['template'] = $this->razdel_model->templateGet();
    $this->load->view("/admin/razdel/detail_{$dt['razdel']->content_type}", $dt);
  }
  

  ##########################################################################
  ###         SITE NEWS
  ##########################################################################
  /**
   * Управление новостями
   */
  function news() {
    $news = $this->news_model->Get(array());
    $this->view->set('news', $news);
    $this->view->render("/admin/news/main");
  }
  
  /**
   * Добавление новости
   */
  function news_add($action = '') {
    if ( $action=='add' ) {
      $insert['title'] = $this->input->post('title');
      $insert['date']  = $this->input->post('date');
      $this->news_model->Add($insert);
      return $this->load->view('/common/nyro_close');
    }
    $this->load->view('/admin/news/add', $dt);
  }
  
  /**
   * Изменение новости
   */
  function news_detail($id) {
    $action = $this->input->post("action");
    if ($action=='save') {
      $set['id']      = (int)$id;
      $set['text']    = $this->input->post('text');
      $set['date']    = $this->input->post('date');
      $set['small']   = $this->input->post('small');
      $set['title']   = $this->input->post('title');
      $set['htitle']  = $this->input->post('htitle');
      $set['active']  = $this->input->post('active');
      $ret = $this->news_model->Set($set);
      if ($ret) {
        echo 1;
      }
      return;
    }
    $dt = $this->news_model->Get(array('id' => $id));
    $dt['editor1'] = getEditor( 'small', $dt['small'] );
    $dt['editor2'] = getEditor( 'text', $dt['text'] );
    $this->load->view("/admin/news/detail", $dt);
  }
  
  /**
   * Удаление новости
   */
  function news_del($id) {
    $this->news_model->Del($id);
  }
  

  ##########################################################################
  ###         SHOP USERS
  ##########################################################################
  /**
   * Блок управления юзерами
   */
  function users($action = '') {
    $this->id = (int)$this->uri->segment(4);
    if ($action<>'') {
      $method = "_users_action_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      $params['CI'] = $this;
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = $this->input->post($key);
      }
      return $this->$method($params);
    }
    $users = $this->user_model->Find();
    $messages = $this->user_model->getMessage(-1);
    $this->view->set('user_id',  $this->id);
    $this->view->set('users',    $users);
    $this->view->set('messages', $messages);
    $this->view->render("/admin/users/main");
  }
  
  /**
   * Детализация по юзеру
   */
  function _users_action_detail($params) {
    $params['messages'] = $this->user_model->getMessage(0, $this->id);
    $params['user']     = $this->user_model->Get($this->id, true);
    $this->load->view("/admin/users/detail", $params);
    return;
  }
  
  /**
   * Смена статуса юзера
   */
  function _users_action_ch_status($params) {
    $user = $this->user_model->Get($this->id);
    $active = $user['active'];
    $set['active'] = 1 ^ $active;
    $this->user_model->Set($this->id, $set);
    $user = $this->user_model->Get($this->id);
    $user['active']
      ? $text = '<img src="/images/on.gif" border=0 title="Включен" />'
      : $text = '<img src="/images/off.gif" border=0 title="Выключен" />';
    echo $text;
    return;
  }
  
  /**
   * Добавление юзера
   */
  function _users_action_add($params) {
    if ($params['post']['username']) {
      $hash = $this->user_model->Register($params['post']);
      if ($hash) {
        $ret  = $this->user_model->registerConfirm($hash);
        $this->load->view('common/nyro_close');
        return;
      }
    }
    $this->load->view("/admin/users/add");
    return;
  }
  
  /**
   * Удаление юзера
   */
  function _users_action_del($params) {
    $ret = $this->user_model->Del($this->id); 
    echo $ret;
    return;
  }
  
  /**
   * Изменение юзера
   */
  function _users_action_submit($params) {
    $ret  = $this->user_model->Set($this->id, $params['post']);
    $ret .= $this->user_model->SetParam($this->id, $params['post']['user_values']);
    echo get_json($ret);
  }
  
  /**
   * Переписка с юзером юзера
   */
  function _users_action_messages($params) {
    $message_id = (int)$this->uri->segment(5);
    $newtext = trim($params['post']['newtext']);
    if ($newtext<>'') {
      $this->user_model->addMessageAns($message_id, $newtext);
    }
    $mess = $this->user_model->getMessage($message_id, $this->id);
    $this->load->view('/admin/message/full', $mess);
    return;
  }
  
  /**
   * Текстовая рассылка по юзерам
   */
  function _users_action_subscribe($params) {
    $users = $this->user_model->findSubs();
    foreach ($users as $user) {
      echo "{$user->email}<br />";
    }
    echo "<br /><br />";
    return $this->load->view('/admin/users/subscribe');
  }
  
  /**
   * Текстовая рассылка по юзерам
   */
  function _users_action_subscribeDo($params) {
    $message_body     = trim($params['post']['message_body']);
    $message_subject  = trim($params['post']['message_subject']);
    
    $count = 0;
    
    if ($message_body && $message_subject) {
      $users = $this->user_model->findSubs();
      foreach ($users as $user) {
        $count++;
        $this->load->library('email');
        $this->email->clear();
        $this->email->to($user->email);
        $this->email->from(EMAIL, NAME);
        $this->email->subject($message_subject);
        $this->email->message($message_body);
        $this->email->send();
      }
      echo "Отправлено {$count} сообщений.<br />";
    }
    else {
      $this->load->view('/common/error_message', array('message' => 'Не указаны тема или текст сообщения.'));
    }

    return $this->load->view('/admin/users/subscribe', $params);
  }

  /**
   * Управление платежами
   */
  function payment($action = '', $id = 0, $pay_id = 0) {
    $id = (int)$id;
    if ($action=='add_form') {
      $dt['id'] = $id;
      $this->load->view("/admin/payment_add_form", $dt);
      return;
    }
    if ($action=='add') {
      $summa = $this->input->post('summa');
      $description = $this->input->post('description');
      $this->user_model->setPay($id, $summa, $description, 1);
      $dt['id'] = $id;
      $dt['close'] = 1;
      $this->load->view("/admin/payment_add_form", $dt);
      return;
    }
    if ($action=='del') {
      $pay_id = (int)$pay_id;
      $this->user_model->delPay($pay_id, $id);
      return;
    }
  }
  
  /**
   * Показать подробную инфу о юзере
   */
  function user_full_info($id) {
    if (!$id) {
      echo "Пользователь не найден!";
      return;
    }
    $dt['user'] = $this->user_model->Get($id);
    $this->load->view("/admin/users/full_info", $dt);
  }
  
  ##########################################################################
  ###         SHOP CATALOG
  ##########################################################################
  /**
   * Управление каталогом
   */
  function catalog($action = '') {
    $this->id = (int)$this->uri->segment(4);
    if ($action<>'') {
      $method = "_catalog_action_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      $params['CI'] = $this;
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = $this->input->post($key);
      }
      return $this->$method($params);
    }
    $this->view->render("/admin/catalog/main");
  }
  
  /**
   * Управление каталогом - форма для поиска
   */
  function _catalog_action_list($params) {
    $params['cnt'] = count($this->_catalog_action_newTovarImagesFind());
    $this->load->view("/admin/catalog/list", $params);
    return;
  }
  
  /**
   * Управление каталогом - подробная информация
   */
  function _catalog_action_full($params) {
    $params['tovar'] = $this->tovar_model->Get($this->id);
    $params['productTypes'] = $this->config->item('productTypes');
    $params['editor'] = getEditor( 'descriptionHtml', $params['tovar']->description );
    $this->load->view("/admin/catalog/full", $params);
    return;
  }
  
  /**
   * Управление каталогом - список товаров
   */
  function _catalog_action_list_all($params) {
    $params['list']['page']   = max(1, $this->id);
    $params['list']['limit']  = 20;
    $params['list']['sort']   = 'tovar_id';
    $params['list']['sdir']   = 'desc';
    $params['list']['filter'] = array('anylike' => trim($params['post']['value']));
    $params['list'] = $this->tovar_model->Find($params['list']);
    $this->load->view("/admin/catalog/list_all", $params);
    return;
  }
  
  /**
   * Управление каталогом - сохрание подробной информации
   */
  function _catalog_action_save($params) {
    $ret = $this->tovar_model->Set($this->id, $params['post']);
    echo get_json($ret);
    return;
  }
  
  /**
   * Управление каталогом - удаление товара
   */
  function _catalog_action_del($params) {
    $ret = $this->tovar_model->Del($this->id);
    echo get_json($ret);
    return;
  }
  
  /**
   * Управление каталогом - товар/категория
   */
  function _catalog_action_category($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    // действия
    switch ($params['action']) {
      case 'set':
        $params['error_message'] = $this->tovar_model->SetCategory($this->id, $params['post']['category_id']);
        if (!$params['error_message']) {
          return $this->load->view("common/nyro_close");
        }
      break;
    }
    // загрузим товар
    $tovar = $this->tovar_model->Get($this->id);
    foreach ($tovar->category as $key => $item) {
      $this->tovar_category[$item->category_id] = $item->category_id;
    }
    // построим дерево
    $params['tree'] = $this->_catalog_action_category_makeTree(0);
    $this->load->view('/admin/catalog/category', $params);
    return;
  }
  
  /**
   * Управление каталогом - товар/категория - дерево категорий
   */
  function _catalog_action_category_makeTree($parent_id) {
    $text = '<ul style="padding-left:15px;">';
    $category = $this->tovar_model->categoryFind($parent_id);
    foreach ($category as $key => $item) {
      $this->tovar_category[$item->id]
        ? $checked = 1
        : $checked = 0;
      $action = in_check("category_id[{$item->id}]", $checked, $item->id);
      $text .= "<li>" . $action . "&nbsp;" . $item->name . "</li>";
      if ($item->cnt>0) {
        $text .= $this->_catalog_action_category_makeTree($item->id);
      }
    }
    $text .= '</ul>';
    return $text;
  }
  
  /**
   * Управление каталогом - товар/фильтры
   */
  private function _catalog_action_filters() {
    $this->load->model('filters_model');
    $this->params['id'] = (int)$this->uri->segment(4);
    if ($this->params['post']['action']) {
      return $this->_catalog_action_filters_save();
    }
    foreach ($this->tovar_model->getProductFilters($this->params['id']) as $product) {
      $this->params['product'][$product->value_id] = $product;
    }
    $this->params['filters'] = $this->filters_model->getFilters();
    foreach ($this->params['filters'] as $key => $item) {
      $this->params['filters'][$key]->values = $this->filters_model->getFilterValues($item->id);
    }
    return $this->load->view('/admin/catalog/filters', $this->params);
  }
  
  /**
   * Управление каталогом - товар/фильтры - save
   */
  private function _catalog_action_filters_save() {
    $this->tovar_model->setProductFilters($this->params['id'], $this->params['post']['values']);
    return $this->load->view("common/nyro_close");
  }
  
  /**
   * Управление каталогом - фото товара
   */
  function _catalog_action_photo($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    switch ($params['action']) {
      case 'add':
        if ($_FILES['photo']['name']<>'') {
          $params['error_message'] = $this->tovar_model->photoAdd($params['post'], $_FILES['photo']);
        }
        $this->load->view('/admin/catalog/photoAdd', $params);
        return;
      break;
      case 'del':
        foreach ($params['post']['photo_id'] as $key => $value) {
          if ($value=='true') {
            $this->tovar_model->photoDel($key);
          }
        }
        echo get_json('');
        return;
      break;
    }
    return;
  }
  
  /**
   * Создание товара
   */
  function _catalog_action_newTovar($params) {
    if (isset($params['post']['name'])) {
      $ret = $this->tovar_model->Add($params['post']);
      if (!$ret) {
        $params['new_id'] = $this->tovar_model->new_id;
        $this->load->view("/admin/catalog/newTovarOk", $params);
        $this->load->view("common/nyro_close");
        return;
      }
      $params['post']['error_message'] = $ret;
    }
    $copy_id = (int)$this->uri->segment(4);
    if ($copy_id) {
      $params['post'] = $this->tovar_model->Get($copy_id);
    }
    $this->load->view("/admin/catalog/newTovar", $params['post']);
    return;
  }
  
  /**
   * Создание списка товаров
   */
  function _catalog_action_newTovarList($params) {
    if (isset($params['post']['tovar_list'])) {
      $list = explode("\n", $params['post']['tovar_list']);
      $this->tovar_model->db->trans_start();
      $string_count = 0;
      $insert_count = 0;
      $update_count = 0;
      $error_count = 0;
      foreach ($list as $key => $item) {
        $item = trim($item);
        if (!$item) {
          continue;
        }
        $string_count++;
        $parts = explode("\t", $item);
        $set = array();
        if (count($parts)==5 && false) { // TODO
          $count = max((int)str_replace(",", ".", $parts[1]) , 0);
          $price = str_replace(" ", "", $parts[4]);
          $price = str_replace(chr(194), "", $price);
          $price = str_replace(chr(160), "", $price);
          $price = max((float)str_replace(",", ".", $price) , 0);
          $code  = trim($parts[3]);
          $set = array(
            'name'            => trim($parts[0]),
            'code_1c'         => trim($parts[2]),
            'code'            => $code,
            'price'           => $price,
            'manufacturer_id' => max(1, (int)$params['post']['manufacturer_id']),
            'description'     => $set['name'],
            'active'          => 1,
          );
          $tovarInfo = $this->tovar_model->GetByCode($set['code_1c']);
          if (!$tovarInfo->id) {
            $ret = $this->tovar_model->Add($set);
            if ($ret) {
              echo $set['code_1c']." - ".$ret."<br />";
              $error_count++;
            }
            else {
              $insert_count++;
            }
          }
          else {
            $tovarInfo->price  = $price;
            $params['post']['manufacturer_id'] ? $tovarInfo->manufacturer_id = (int)$params['post']['manufacturer_id'] : 0;
            $tovarInfo->active = 1;
            $ret = $this->tovar_model->Set($tovarInfo->id, (array)$tovarInfo);
            if ($ret) {
              echo $set['code_1c']." - ".$ret."<br />";
              $error_count++;
            }
            else {
              $update_count++;
            }
          }
          $array = array(
            'tovar_id' => ($this->tovar_model->new_id ? $this->tovar_model->new_id : $tovarInfo->id),
            'var_id'   => TOVAR_COUNT_FIELD, 
            'value'    => $count
          );
          $this->tovar_model->setParam($array);
          if ($params['post']['category_id']) {
            $this->tovar_model->SetCategory(max($tovarInfo->id, $this->tovar_model->new_id), $params['post']['category_id']);
          }
        }
        
        // для сайта типа каталог
        elseif (MY_LEVEL == SITE_LEVEL_CATALOG && count($parts) == 2) {
          $set = array(
            'name' => trim($parts[0]),
            'code' => trim($parts[1])
          );
          $_result = $this->tovar_model->Find(array('filter' => $set));
          $tovarInfo = array_shift($_result['search']);
          if ( ! $tovarInfo->id > 0 ) {
            $set = array_merge($set, array(
              'manufacturer_id' => (int)$params['post']['manufacturer_id'],
              'active'          => 1
            ));
            $ret = $this->tovar_model->Add($set);
            if ($ret) {
              echo $set['code']." - ".$ret."<br />";
              $error_count++;
            }
            else {
              $insert_count++;
            }
          }
          else {
            $update_count++;
          }
          if ($params['post']['category_id']) {
            $this->tovar_model->SetCategory(max($tovarInfo->id, $this->tovar_model->new_id), $params['post']['category_id']);
          }
        }
        
        // ошибочно количество полей
        else {
          echo "ERROR - ".$item."<br />";
          $error_count++;
        }
      }
      $this->tovar_model->db->trans_complete();
      echo "Всего строк:{$string_count}. Добавлено:{$insert_count}. Обновлено:{$update_count}. Ошибок:{$error_count}.";
    }
    return $this->load->view("/admin/catalog/newTovarList", $params['post']);
  }
  
  /**
   * Импорт фотографий товаров
   */
  function _catalog_action_newTovarImagesFind() {
    $files1 = (array)glob(ROOT."/pic_import/*.jpg");   
    $files2 = (array)glob(ROOT."/pic_import/*/*.jpg"); 
    $files3 = (array)glob(ROOT."/pic_import/*.JPG");   
    $files4 = (array)glob(ROOT."/pic_import/*/*.JPG"); 
    $files_array = array_merge($files1, $files2, $files3, $files4);
    $files_array = array_filter($files_array);
    return $files_array;
  }

  
  /**
   * Импорт фотографий товаров
   */
  function _catalog_action_newTovarImages($params) {
    $files = $this->_catalog_action_newTovarImagesFind();
    if ( empty($files) ) {
      echo "Не найдено файлов для экспорта.";
      return;
    }
    foreach ($files as $key => $file) {
      $file = trim($file);
      if (!$file) continue;
      $file_utf = win2utf($file);
      $parts = array_reverse(explode("/", $file_utf));
      $filename = $parts[0];
      $manufacturer = $parts[1];
      $ext = getExt($filename);
      $code = str_replace(".{$ext}", "", $filename);
      $tovar = $this->tovar_model->GetByCode($code);
      if (!$tovar->id) {
        $code = substr($code, 0, strrpos($code, "_"));
        $tovar = $this->tovar_model->GetByCode($code);
      }
      if ($this->tovar_model->rows>1) {
        echo "{$file_utf} count {$this->tovar_model->rows}<br />";
        continue;
      }
      if (!$tovar->id) {
        echo "{$file_utf} tovar code {$code} not found<br />";
        continue;
      }
      echo "{$file_utf} | found tovar id:{$tovar->id}<br>";
      $fileInfo = array(
        'size'      => filesize($file),
        'name'      => $file,
        'tmp_name'  => $file
      );
      $params = array(
        'id' => $tovar->id,
        'comment' => '' 
      );
      $ret = $this->tovar_model->photoAdd($params, $fileInfo);
      if ($ret) {
        echo "{$file_utf} | ERROR | {$ret}<br />";
        continue;
      }
      echo "{$file_utf} | upload tovar id:{$tovar->id} | OK<br>";
    }
    return;// max 463, 464=id:279
  }
  
  /**
   * Очистка каталога
   */
  function _catalog_action_clearCatalog($params) {
    return $this->tovar_model->clearCatalog();
  }
  
  
  ##########################################################################
  ###         SHOP ZAKAZ
  ##########################################################################
  /**
   * Управление заказами
   */
  function zakaz($action = '') {
    if ($action<>'') {
      $method = "_zakaz_action_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      $params['CI'] = $this;
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = $this->input->post($key);
      }
      return $this->$method($params);
    }
    
    $params = unserialize($this->tovar_model->getZakazConfig('menu_items'));
    $this->view->set('params', $params);
    
    $status = $this->tovar_model->getStatus();
    $status[-1] = array('id'=>-1, 'name'=>'-----Любой-----');
    $this->view->set('status', $status);
    
    $type_pay = $this->tovar_model->typePayList();
    $type_pay[-1] = array('id'=>-1, 'name'=>'-----Любой-----');
    $this->view->set('type_pay', $type_pay);
    
    $type_move = $this->tovar_model->typeMoveList();
    $type_move[-1] = array('id'=>-1, 'name'=>'-----Любой-----');
    $this->view->set('type_move', $type_move);
    
    $this->view->render("/admin/zakaz/main");
  }
  
  /**
   * Список заказов
   */
  function _zakaz_action_detail($params) {
    $values = array(
      'status'       => '',
      'type_move_id' => '',
      'type_pay_id'  => '',
      'date1'        => '',
      'date2'        => ''
    );
    $save = serialize(array_intersect_key($params['post'], $values));
    $this->tovar_model->setZakazConfig('menu_items', $save);
    $params['zakaz'] = $this->tovar_model->zakazFind($params['post']);
    $this->load->view('/admin/zakaz/detail', $params);
    return;
  }
  
  /**
   * Подробно о заказе
   */
  function _zakaz_action_full($params) {
    $id     = (int)$this->uri->segment(4);
    $action = $this->uri->segment(5);
    switch ($action) {
      case 'add_to_zakaz':
        $set['tovar_cnt'] = (int)$params['post']['new_tovar_cnt'];
        $set['tovar_id']  = (int)$params['post']['new_tovar_id'];
        $set['comment']   = '';
        if ($set['tovar_cnt'] && $set['tovar_id']) {
          $this->tovar_model->zakazItemSet($id, $set);
        }
      break;
      case 'save':
        foreach ((array)$params['post']['tovar'] as $item_id => $count) {
          $set = array (
            'tovar_cnt' => $count,
            'item_id'   => $item_id,
            'comment'   => $params['post']['comment'][$item_id]
          );
          $this->tovar_model->zakazItemSet($id, $set);
        }
      break;
      case 'del_item':
        $set = array(
          'item_id'   => (int)$this->uri->segment(6),
          'tovar_cnt' => 0
        );
        $this->tovar_model->zakazItemSet($id, $set);
      break;
      case 'ch_common':
        $this->tovar_model->zakazSet($id, $params['post']);
      break;
    }
    $params['zakaz'] = $this->tovar_model->zakazGet($id);
    $params['zakaz_detail'] = $this->tovar_model->zakazGetDetail($id);
    $this->load->view('/admin/zakaz/full', $params);
    return;
  }
  
  /**
   * Удалить заказ
   */
  function _zakaz_action_delete($params) {
    $id = (int)$this->uri->segment(4);
    $this->tovar_model->zakazDel($id);
    return;
  }
  
  /**
   * Поиск позиции заказа
   */
  function _zakaz_action_auto($params) {
    $search = array(
      'filter' => array(
        'code_1c like' => $params['post']['q']
      ),
      'limit' => 10,
      'sort'  => 'code_1c'
    );
    $data = $this->tovar_model->Find($search);
    $array = array();
    foreach ($data['search'] as $item) {
      echo "Код:{$item->code_1c} (цена:{$item->price}руб.)|{$item->id}\n";
    }
    return;
  }

  /**
   * Распечатка квитанции
   */
  function _zakaz_action_print($params) {
    $id = (int)$this->uri->segment(4);
    $params['zakaz'] = $this->tovar_model->zakazGet($id);
    $params['zakaz_detail'] = $this->tovar_model->zakazGetDetail($id);
    $this->load->view('/admin/zakaz/print', $params);
    return;
  }
  

  /**
   * Управление контрагентами
   */
  function shops($action = '', $id = '') {
    $id = (int)$id;
    switch ($action) {
      case 'add':
        $params['name']     = $this->input->post('name');
        $params['address']  = $this->input->post('address');
        $params['srok']     = $this->input->post('srok');
        $params['is_our']   = (int)$this->input->post('is_our');
        $params['priority'] = (int)$this->input->post('priority');
        if ($params['name']<>'') {
          $ret = $this->tovar_model->shopSet($id, $params);
          $dt['close'] = $ret;
        }
        $this->load->view("/admin/shops_add", $dt);
      return;
      case 'del':
        if ($id<=0)
          return false;
        $ret = $this->tovar_model->shopDel($id); 
        echo $ret;
      return;
      case 'detail':
        if (!$id)
          return false;
        $dt['shop'] = $this->tovar_model->shopGet($id);
        $this->load->view("/admin/shops_detail", $dt);
      return;
      case 'load_price':
        if ($id<=0)
          return false;
        if ($_FILES['price']['name']<>'') {
          $data = file_get_contents($_FILES['price']['tmp_name']);
          unlink($_FILES['price']['tmp_name']);
          $count = $this->tovar_model->loadXmlPrice($id, $data);
          unset($data);
          echo "Загружено $count позиций прайса";
        }
        else {
          $dt['id'] = $id;
          $this->load->view("/admin/shops_load_price", $dt);
        }
      return;
      case 'set_margin':
        $margin = $this->input->post('margin');
        $this->tovar_model->shopMarginSet($id, $margin);
        header("Location: /admin/shops/show/{$id}");
      return;
      case 'show':
        $this->view->set('id', $id);
    }
    $shops = $this->tovar_model->shopGet();
    $this->view->set('shops', $shops);
    $this->view->render("/admin/shops");
  }
  
  /**
   * Заявки на товар
   */
  function request($action = '', $id = 0) {
    if ($action=='detail') {
      $param['status'] = $this->input->post("status");
      $param['date1'] = $this->input->post("date1");
      $param['date2'] = $this->input->post("date2");
      $dt['request'] = $this->tovar_model->requestGet($param);
      $this->load->view("/admin/request_detail", $dt);
      return;
    }
    if ($action=='delete') {
      $id = (int)$id;
      if ($id>0) {
        $this->tovar_model->requestDel($id);
      }
      return;
    }
    if ($action=='status') {
      if (!$id)
        return;
      $res = $this->tovar_model->requestGet(array('id'=>$id));
      $dt['id'] = $id;
      $dt['status'] = in_select('status', $this->tovar_model->getTable('request_status'), $res[0]['status']);
      $this->load->view("/admin/request_status", $dt);
      return;
    }
    if ($action=='status_set') {
      if (!$id)
        return;
      $res = $this->tovar_model->requestGet(array('id'=>$id));
      if ($res[0]['id']>0) {
        $status = $this->input->post('status');
        $this->tovar_model->requestSet($id, $status);
        $this->load->view("/admin/request_status");
      }
      return;
    }
    $this->view->render("/admin/request");
  }

  ##########################################################################
  ###         SITE CONFIG
  ##########################################################################
  /**
   * Настройки сайта
   */
  function config($action = '') {
    if ($action<>'') {
      $method = "_config_action_{$action}";
      if (!method_exists($this, $method)) {
        echo "Ошибка! Метод {$method} не найден";
        return;
      }
      $params['CI'] = $this;
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = @trim($this->input->post($key));
      }
      return $this->$method($params);
    }
    $this->view->render("/admin/config/main");
  }
  
  /**
   * Общие настройки
   */
  function _config_action_detail($params) {
    if ($params['post']['action']=='set') {
      $value = $params['post']['value'];
      $key   = $params['post']['key'];
      if ($value<>'' && $key<>'') {
        $this->razdel_model->configSet($key, $value);
      }
    }
    $params['list'] = $this->razdel_model->configGet();
    $this->load->view("/admin/config/detail", $params);
    return;
  }
  
  /**
   * Установка пароля админа
   */
  function _config_action_set_password($params) {
    if (strlen($params['post']['admin_password1']) < 8) {
      exit(get_json("Введите не менее 8 символов")) ;
    }
    if ($params['post']['admin_password1'] <> $params['post']['admin_password2']) {
      exit(get_json("Пароль не совпадает с повтором")) ;
    }
    $res = $this->user_model->Set(1, array(
      'password' => md5($params['post']['admin_password1'])
    ));
    exit(get_json($res));
  }
  
  /**
   * Настройка производителей
   */
  function _config_action_manufacturer($params) {
    $params['action'] = $this->uri->segment(4);
    switch ($params['action']) {
      // Добавление
      case 'add':
        if ($params['post']['manufacturer_name']) {
          $this->tovar_model->manufacturerAdd($params['post']);
          $this->load->view('/common/nyro_close');
          return;
        }
        $this->load->view("/admin/config/manufacturer_act", $params);
        return;
      break;
      // Удаление
      case 'del':
        echo $this->tovar_model->manufacturerDel($params['post']['id']);
        return;
      break;
      // Редактирование
      case 'edit':
        $params['id'] = $this->uri->segment(5);
        if ($params['post']['manufacturer_name']) {
          $params['post']['pic']        = $_FILES['pic'];
          $params['post']['main_page']  = (int)$params['post']['main_page'];
          $this->tovar_model->manufacturerSet($params['id'], $params['post']);
          $this->load->view('/common/nyro_close');
          return;
        }
        $params['values'] = $this->tovar_model->manufacturerGet($params['id']);
        $this->load->view("/admin/config/manufacturer_act", $params);
        return;
      break;
    }
    $params['manufacturers'] = $this->tovar_model->manufacturerFind();
    $this->load->view("/admin/config/manufacturers", $params);
    return;
  }
  
  /**
   * Категории товаров
   */
  function _config_action_category($params) {
    $this->load->model('category_model');
    $params['action'] = $this->uri->segment(4);
    $method = "_config_action_category_{$params['action']}";
    if (method_exists($this, $method)) {
      $params['id'] = (int)$this->uri->segment(5);
      return $this->$method($params);
    }
    switch ($params['action']) {
      case 'add':
        $params['parent_id'] = (int)$this->uri->segment(5);
        if ($params['post']['name']) {
          $this->tovar_model->categoryAdd($params['parent_id'], $params['post']);
          $this->load->view('/common/nyro_close');
          return;
        }
        $params['id'] = $params['parent_id'];
        $this->load->view("/admin/config/category_act", $params);
        return;
      break;
      case 'edit':
        $params['id'] = (int)$this->uri->segment(5);
        if ($params['post']['name']) {
          $params['post']['products'] = (int)$params['post']['products'];
          $this->tovar_model->categorySet($params['id'], $params['post'], $_FILES['pic']);
          $this->load->view('/common/nyro_close');
          return;
        }
        $params['values'] = $this->tovar_model->categoryGet($params['id']);
        $this->load->view("/admin/config/category_act", $params);
        return;
      break;
      case 'edit-text':
        $params['id'] = (int)$this->uri->segment(5);
        if ($params['post']['text'] <> '') {
          return $this->tovar_model->categorySet($params['id'], $params['post']);
        }
        $params['values'] = $this->tovar_model->categoryGet($params['id']);
        $params['editor'] = getEditor('text', $params['values']->text, '100%');
        return $this->load->view("/admin/config/category-text", $params);
      break;
      case 'del':
        echo $this->tovar_model->categoryDel($params['post']['id']);
        return;
      break;
    }
    $params['categories'] = $this->_get_categories_tree();
    $this->load->view("/admin/config/category", $params);
    return;
  }
  
  /**
   * редактировать доп поля категории
   * @author Alexey
   */
  function _config_action_category_edit_options($params) {
    $params['category'] = $this->tovar_model->categoryGet($params['id']);
    $params['fields']   = in_table('field_id', ['table' => 'shop_category_fields', 'is_empty' => 1, 'id' => 'field']);
    return $this->load->view("/admin/config/category-edit-options", $params);
  }
    
  /**
   * редактировать доп поля категории
   * @author Alexey
   */
  function _config_action_category_edit_option($params) {
    $params['value'] = $this->category_model->getValue([
      'category_id' => (int)$params['id'],
      'field'       => $params['post']['field']
    ]);
    $params['text']  = getEditor('option-text', htmlspecialchars_decode($params['value']->value, ENT_QUOTES), '100%');
    return $this->load->view("/admin/config/category-edit-option", $params);
  }
  
  /**
   * Сохранить дополнительный параметр
   * @author Alexey
   */
  function _config_action_category_save_option($params) {
    $this->category_model->setValue([
      'category_id' => (int)$params['id'],
      'field'       => $params['post']['field'],
      'value'       => htmlspecialchars($params['post']['text'], ENT_QUOTES)
    ]);
    return print get_json('');
  }
  
  /**
   * Получить дерево категорий
   * @author Alexey
   */
  private function _get_categories_tree($parent_id = 0) {
    if ($parent_id == 0) {
      $this->categories = [];
    }
    $category = $this->tovar_model->categoryFind($parent_id);
    foreach ($category as $id => $item) {
      $this->categories[$id] = $item;
      if ($item->cnt > 0 ) {
        $this->_get_categories_tree($id);
      }
    }
    if ($parent_id == 0) {
      return $this->categories;
    }
  }
  
  /**
   * Список адресов магазинов
   */
  function _config_action_shops($params) {
    $this->action = $params['action'] = $this->uri->segment(4);
    if ($this->action <> '') {
      $method = "_config_action_shops_{$this->action}";
      if (method_exists($this, $method)) {
        return $this->$method($params);
      }
    }
    $params['shops'] = $this->tovar_model->findShop();
    return $this->load->view("/admin/config/shops", $params);
  }

  /**
   * Добавление магазина
   */
  function _config_action_shops_add($params) {
    if ($params['post']['addr']) {
      $params['error_message'] = $this->tovar_model->addShop($params['post']);
      if (!$params['error_message']) {
        return $this->load->view("/common/nyro_close");
      }
    }
    return $this->load->view("/admin/config/shops_add", $params);
  }

  /**
   * Редактирование магазина
   */
  function _config_action_shops_edit($params) {
    if ($params['post']['addr']) {
      $params['error_message'] = $this->tovar_model->editShop($params['post']['id'], $params['post']);
      echo get_json($params['error_message']);
      return;
    }
    $params['shop'] = $this->tovar_model->getShop($params['post']['id']);
    $params['text'] = getEditor('text', htmlspecialchars_decode($params['shop']->description), '100%');
    return $this->load->view("/admin/config/shops_edit", $params);
  }

  /**
   * Удаление магазина
   */
  function _config_action_shops_del($params) {
    $this->tovar_model->delShop($params['post']['id']);
  }
  
  /**
   * Управление фильтрами для каталога
   * @author Alexey
   */
  function _config_action_filters() {
    $this->load->model('filters_model');
    $this->action = $this->params['action'] = $this->uri->segment(4);
    if ($this->action <> '') {
      $method = "_config_action_filters_{$this->action}";
      if (method_exists($this, $method)) {
        return $this->$method();
      }
    }
    $params['items'] = $this->filters_model->getFilters();
    return $this->load->view("/admin/config/filters/main", $params);
  }

  /**
   * Добавление фильтра
   */
  function _config_action_filters_add() {
    if ($this->params['post']['name']) {
      $this->filters_model->addFilter($this->params['post']);
      return $this->load->view("/common/nyro_close");
    }
    return $this->load->view("/admin/config/filters/add", $this->params);
  }

  /**
   * Редактирование фильтра
   */
  function _config_action_filters_edit() {
    $id = (int)$this->uri->segment(5);
    if ($this->params['post']['name']) {
      $this->filters_model->setFilter($id, $this->params['post']);
      return print get_json('');
    }
    $this->params['filter'] = $this->filters_model->getFilter($id, ['values' => true]);
    array_push($this->params['filter']->values, ['']);
    return $this->load->view("/admin/config/filters/edit", $this->params);
  }

  /**
   * Удаление фильтра
   */
  function _config_action_filters_del() {
    $this->filters_model->delFilter($this->params['post']['id']);
  }
  
  ##########################################################################
  ###         SITE BANNERS
  ##########################################################################
  /**
   * Управление баннерами
   */
  function banners($action = '') {
    $this->load->model('banners_model');
    if ($action<>'') {
      $this->id = (int)$this->uri->segment(4);
      $method = "_banners_{$action}";
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
    $this->view->set('banners', $this->banners_model->Find());
    $this->view->render("/admin/banners/main");
  }
  
  /**
   * Просмотр баннера
   */
  function _banners_detail($params) {
    if ( $params['post']['name']<>'' ) {
      $ret = $this->banners_model->Set($this->id, $params['post']);
      echo get_json($ret);
      return;
    }
    $dt['banners'] = $this->banners_model->Get($this->id);
    return $this->load->view("/admin/banners/detail", $dt);
  }
  
  /**
   * Добавление баннера
   */
  function _banners_add($params) {
    if ( $params['post']['name']<>'' ) {
      $ret = $this->banners_model->Add($params['post']);
      if (!$ret) {
        echo "Ошибка БД!";
      }
      else {
        return $this->load->view("/common/nyro_close");
      }
    }
    $this->load->view('/admin/banners/add');
  }
  
  /**
   * Удаление баннера
   */
  function _banners_del($params) {
    $res = $this->banners_model->Del($this->id);
    echo get_json($res);
  }
  
  /**
   * Управление баннером - баннер/раздел
   */
  function _banners_razdel($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    $this->params = $params;
    // действия
    if ($params['action'] == 'set') {
      $params['error_message'] = $this->banners_model->setRazdel($this->id, $params['post']['razdel_id']);
    }
    // загрузим баннер
    $banner = $this->banners_model->Get($this->id);
    foreach ($banner->razdel as $key => $item) {
      $this->params['banner_razdel'][$item->razdel_id] = $item->razdel_id;
    }
    // построим дерево
    $params['tree'] = $this->_banners_razdel_makeTree(0);
    $this->load->view('/admin/banners/razdel', $params);
    return;
  }
  
  /**
   * Управление баннером - баннер/раздел - дерево разделов
   */
  function _banners_razdel_makeTree($parent_id) {
    $this->params['razdel'] = $this->razdel_model->Get(array(
      'parent_id'     => $parent_id,
      'content_type'  => CONTENT_TYPE_RAZDEL
    ));
    return $this->load->view('/admin/banners/razdel_tree', $this->params, true);
  }
  
  /**
   * Управление баннером - баннер/место
   */
  function _banners_places($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    // действия
    if ($params['action'] == 'set') {
      $params['error_message'] = $this->banners_model->setPlace($this->id, $params['post']['place_id']);
    }
    // загрузим баннер
    $banner = $this->banners_model->Get($this->id);
    foreach ($banner->places as $key => $item) {
      $params['banner_places'][$item->place_id] = $item->place_id;
    }
    $params['places'] = $this->banners_model->getPlaces();
    return $this->load->view('/admin/banners/places', $params);
  }
  
  /**
   * Управление баннером - изображение
   */
  function _banners_photo($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    switch ($params['action']) {
      case 'add':
        try {
          if ( !$_FILES['filename']['name'] ) {
            throw new Exception("Файл не найден!");
          }
          $this->banners_model->addFile($this->id, $_FILES['filename']);
          return $this->load->view('common/nyro_close');
        }
        catch (Exception $e) {
          $this->load->view('/common/error_message', array('message' => "Ошибка! ".$e->getMessage()));
          return $this->load->view('/admin/banners/photo_load', $params);
        }
      break;
      case 'del':
        try {
          echo get_json($this->banners_model->delFile($this->id));
          return;
        }
        catch (Exception $e) {
          echo get_json($e->getMessage());
          return;
        }
      break;
    }
    return $this->load->view('/admin/banners/photo_load', $params);
  }
  
  /**
   * Управление баннером - изображения в слайдере
   */
  function _banners_photoSlider($params) {
    $params['action'] = $this->uri->segment(5);
    $params['id']     = $this->id;
    switch ($params['action']) {
      case 'add':
        try {
          if ( !$_FILES['filename']['name'] ) {
            throw new Exception("Файл не найден!");
          }
          $this->banners_model->addSliderFile($this->id, $_FILES['filename'], $params['post']['link']);
          return $this->load->view('common/nyro_close');
        }
        catch (Exception $e) {
          $this->load->view('/common/error_message', array('message' => "Ошибка! ".$e->getMessage()));
          return $this->load->view('/admin/banners/photoSlider_load', $params);
        }
      break;
      case 'del':
        try {
          $res = '';
          foreach ($params['post']['sliderFile'] as $key => $item) {
            if ($item <> 'true') {
              continue;
            }
            $res .= $this->banners_model->delSliderFile($this->id, $key);
          }
          echo get_json($res);
          return;
        }
        catch (Exception $e) {
          echo get_json($e->getMessage());
          return;
        }
      break;
    }
    return $this->load->view('/admin/banners/photoSlider_load', $params);
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