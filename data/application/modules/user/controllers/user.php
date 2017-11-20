<?php

class User extends Controller {

  function __construct() {
    parent::__construct();
    $this->user_id = (int)$_SESSION['user_id'];
  }
  
  function run() {
    $params = array();
    $method = $this->uri->segment(2);
    if (method_exists($this, $method)) {
      $this->action = $this->uri->segment(3);
      foreach ($_REQUEST as $key => $value) {
        $params['post'][$key] = $this->input->post($key);
      }
      return $this->$method(&$params);
    }
    // Меню личного кабинета
    if ($this->user_id) {
      $mod->text = $this->load->view('tabsUserCabinet', &$params, true);
    }
    else {
      $mod->text = $this->load->view('user_no_auth', &$params, true);
    }
    return $mod;
  }
  
  /**
   * Верхний блок юзерских кнопок
   */
  function userButtonBlock() {
    if ($this->user_id) {
      $params['user'] = $this->user_model->Get($this->user_id);
      $view = 'userButtonBlockAuth';
    }
    else {
      $view = 'userButtonBlockNoAuth';
    }
    $text = $this->load->view($view, $params, true);
    return $this->result(null, $text);
  }
  
  /**
   * Вход в систему
   */
  function login($params) {
    $auth = $this->user_model->Auth($params['post']['username'], $params['post']['password']);
    if (!$auth) {
      $mod->error_message = "Неверный логин или пароль";
    }
    else {
      $this->user_id = $_SESSION['user_id'];
      header("location: /user");
      exit(0);
    }
    $mod->text = $this->load->view('user_no_auth', &$params, true);
    return $mod;
  }
  
  /**
   * Вход в систему
   */
  function ajaxLogin($params) {
    $auth = $this->user_model->Auth($params['post']['username'], $params['post']['password']);
    if (!$auth) {
      echo get_json("Неверный логин или пароль");
    }
    else {
      $this->user_id = $_SESSION['user_id'];
      echo get_json("");
    }
    exit(0);
  }
  
  /**
   * Блок авторизации
   */
  function getAuthBlock($params) {
    if ($this->action=='auth') {
      $username = $this->input->post('username');
      $password = $this->input->post('password');
      $auth = $this->user_model->Auth($username, $password);
      if (!$auth) {
        $dt['error_message'] = "Неверный логин или пароль";
      }
      else {
        $this->user_id = $_SESSION['user_id'];
      }
    }
    if ($_SESSION['user_id']>0) {
      $user = $this->user_model->Get($this->user_id);
      $user['t_kl']==USER_TYPE_FIZ
        ? $dt['user'] = "{$user['values'][USER_V_LNAME]['value']} {$user['values'][USER_V_FNAME]['value']} {$user['values'][USER_V_MNAME]['value']}"
        : $dt['user'] = "{$user['values'][USER_V_NAME]['value']}";
      $this->load->view('/page/user_menu', $dt);
    }
    else {
      $this->load->view('/page/user_no_auth', $dt);
    }
    return $this->result(null);
  }
  
  /**
   * Закончить сессию
   */
  function logout() {
    $this->user_model->Logout();
    header("Location: /");
    return;
  }
  
  /**
   * Смена пароля
   */
  function chpass($params) {
    if (!$this->user_id) {
      exit();
    }
    if ($this->action=='submit') {
      $password   = $params['post']["password"];
      $password2  = $params['post']["password2"];
      if ( strlen($password)<6 ) {
        exit(get_json("Пароль должен быть не менее 6-ти символов!"));
      }
      if ( $password<>$password2 ) {
        exit(get_json("Пароль должен совпадать с повтором!"));
      }
      $ret = $this->user_model->Set($this->user_id, array('password' => $password));
      exit(get_json($ret));
    }
    $this->load->view('user_chpass', &$params);
    $mod->is_ajax = true;
    return $mod;
  }
  
  /**
   * Восстановление пароля
   */
  function recovery($params) {
    if ($this->action=='submit') {
      $username = trim($params['post']['username_recovery']);
      $user = $this->user_model->getIdByName($username);
      if (!$user) {
        $mod->error_message = "Пользователь с e-mail {$username} не найден!";
      }
      else {
        $user = $this->user_model->Get($user, true);
        $this->view->set('username', $username);
        $params['info_message'] = "Пароль отправлен Вам на e-mail!";
        
        $message = "Ваш пароль на ".NAME.": {$user['password']}<br /><br />Спасибо за использование нашего интернет-магазина!";
        $this->load->library('email');
        $this->email->to($user['username']);
        $this->email->from(EMAIL, NAME);
        $this->email->subject('Напоминание пароля на '.NAME);
        $this->email->message($message);
        $this->email->send();
      }
    }
    $title = 'Восстановление пароля';
    $text = $this->load->view('user_recovery', &$params, true);
    addCrumbs($title, '/user/recovery/');
    return $this->result($title, $text);
  }
  
  /**
   * Изменение параметров пользователя
   */
  function config($params) {
    if (!$this->user_id) {
      exit(0);
    }
    if ($this->action=='submit') {
      $user = $this->user_model->Get($this->user_id);
      $new  = $params['post']['register'];
      foreach ($user['values'] as $key => $item) {
        if ($item['value']<>$new[$item['id']]) {
          $update[$item['id']] = $new[$item['id']];
        }
      }
      $ret = $this->user_model->setParam($this->user_id, $update);
      echo get_json($ret);
      exit();
    }
    $user = $this->user_model->Get($this->user_id);
    $this->load->view('user_config', $user);
    $mod->is_ajax = true;
    return $mod;
  }

  /**
   * Управление заказами пользователя
   */
  function zakaz($params) {
    if (!$this->user_id) {
      exit();
    }
    $this->load->model('tovar_model');
    $action   = $this->uri->segment(3);
    $zakaz_id = (int)$this->uri->segment(4);
    
    // просмотр заказа
    if ($action=='view' && $zakaz_id>0) {
      $params['info']   = $this->tovar_model->zakazGet($zakaz_id);
      $params['id']     = $zakaz_id;
      $params['detail'] = $this->tovar_model->zakazGetDetail($zakaz_id);
      if ($params['info']->user_id <> $this->user_id) {
        return $this->result(null);
      }
      $this->load->view('user_zakaz_view', &$params);
      return $this->result(null);
    }
    
    // отмена заказа
    if ($action=='cancel' && $zakaz_id>0) {
      $this->tovar_model->zakazCancel($this->user_id, $zakaz_id);
      return $this->result(null);
    }
    
    // отображение списка заказов юзера
    $params['zakazes'] = $this->tovar_model->zakazFind(array('user_id'=>$this->user_id));
    $params['CI'] = &$this;
    $this->load->view('user_zakaz', &$params);
    return $this->result(null);
  }
  
  /**
   * Сохраненные товары юзера
   */
  function saved($params) {
    $params['tovars'] = $this->tovar_model->getUserTovar($this->user_id);
    $text = $this->load->view('user_saved', &$params, true);
    return $this->result(null, $text);
  }
  
  /**
   * Регистрация пользователя
   */
  function register($params) {
    $action = $this->action;
    $t_kl   = (int)$this->uri->segment(4);
    
    $username   = trim($this->input->post('username'));
    $password   = trim($this->input->post('password'));
    $password2  = trim($this->input->post('password2'));
    
    #------------------------------------------
    if ($action=='loadVars') {
      $mod->is_ajax = true;
      $user_types = array(USER_TYPE_FIZ, USER_TYPE_UR);
      if (!in_array($t_kl, $user_types)) {
        $mod->error_message = "Неверные параметры";
        return $mod;
      }
      $antispam = $this->user_model->registerQuestionGet();
      $_SESSION['register_question_answer'] = $antispam->answer;
      $params['question'] = $antispam->question;
      $params['vars'] = $this->user_model->getUserVars($t_kl);
      $params['t_kl'] = $t_kl;
      $mod->text = $this->load->view('/page/user_register_vars', &$params, true);
      return $mod;
    }
    #------------------------------------------
    if ($action=='submit' && $t_kl>0) {
      $_SESSION['register_vars'] = $params['post']['register'];
      $_SESSION['register_vars']['username'] = $params['post']['username'];
      // проверка валидности email и пароля
      $valid = true;
      if ($_SESSION['register_question_answer']<>$params['post']['answer'] || !$params['post']['answer']) {
        $mod->error_message = 'Неверное число защиты от авторегистрации!';
        $valid = false;
      }
      if ($username=='') {
        $mod->error_message = 'Поле e-mail обязательно для заполнения!';
        $valid = false;
      }
      if (!preg_match("/^[A-Za-z0-9.\-_]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/", $username)) {
        $mod->error_message = 'Недопустимые символы для e-mail!';
        $valid = false;
      }
      if (strlen($password)<6) {
        $mod->error_message = 'Пароль должен быть не менее 6-ти символов длиной!';
        $valid = false;
      }
      if ($password<>$password2) {
        $mod->error_message = 'Пароль не совпадает с повтором!';
        $valid = false;
      }
      if ($this->user_model->getIdByName($username)) {
        $mod->error_message = 'Пользователь с таким e-mail уже существует!';
        $valid = false;
      }
      // проверка фио
      if ($t_kl==USER_TYPE_FIZ) {
        $fio = $params['post']['register'][USER_V_FIO];
        if (strlen($fio)<3) {
          $mod->error_message = 'Некорректно заполнено ФИО (Должно содержать не менее 3-х символов)!';
          $valid = false;
        }
      }
      // проверка параметров юр.лица
      if ($t_kl == USER_TYPE_UR) {
        $inn  = $_REQUEST['register'][USER_V_INN];
        $kpp  = $_REQUEST['register'][USER_V_KPP];
        $addr = $_REQUEST['register'][USER_V_ADDR];
        $cont = $_REQUEST['register'][USER_V_CONT];
        if (strlen($inn) <> 10) {
          $mod->error_message = 'Поле ИНН должно быть 10 символов длиной.';
          $valid = false;
        }
        if (strlen($kpp) <> 9) {
          $mod->error_message = 'Поле КПП должно быть 9 символов длиной.';
          $valid = false;
        }
        if (strlen($addr) < 1) {
          $mod->error_message = 'Некорректно заполнено Адрес доставки (должно быть непустое)!';
          $valid = false;
        }
      }
      if ($valid) {
        $newUser = array(
          'username' => $username,
          'password' => $password,
          't_kl'     => $t_kl,
          'values'   => $_REQUEST['register']
        );
        $hash = $this->user_model->Register($newUser);
        if (!$hash) {
          $mod->error_message = "Ошибка при регистрации пользователя.";
          return $mod;
        }
        $this->load->library('email');
        $link = "http://".SERVER."/user/register/hash/{$hash}";
        $message = "<b>Здравствуйте!</b><br /> 
        Спасибо за регистрацию в нашем интернет магазине ".NAME."! <br /><br /> 
        Для активации Вашей учетной записи перейдите по 
        <a href='{$link}'>ссылке,</a><br>
        либо скопируйте строку <br>\"{$link}\"<br>
        в поле ввода адреса вашего браузера.";//adebug($message);
        $this->email->from(EMAIL, NAME);
        $this->email->to($username);
        $this->email->subject('Регистрация в интернет-магазине '.NAME);
        $this->email->message($message);
        $this->email->send();
        //
        $mod->title = "Подтверждение регистрации";
        $mod->text = $this->load->view('/page/user_register_submit', &$params, true);
        return $mod;
      }
    }
    #------------------------------------------
    if ($action=='hash') {
      $hash = filter_var($this->uri->segment(4), FILTER_VALIDATE_REGEXP, array(
        'options' => array('regexp' => '/^[A-Za-z0-9]{40}$/')
      ));
      $ok = $this->user_model->registerConfirm($hash);
      if ($ok) {
        $mod->text = 
          '<b>Вам аккаунт успешно подтвержден!</b><br /><br />' .
          'Спасибо за регистрацию в нашем интернет-магазине!<br /><br />';
      }
      else
        $mod->text = $this->load->view('/page/user_register_not', &$params, true);
      return $mod;
    }
    #------------------------------------------
    $params['t_kl'] = ($t_kl ? $t_kl : USER_TYPE_FIZ);
    $mod->title = "Регистрация пользователя";
    $mod->text = $this->load->view('/page/user_register', &$params, true);
    $mod->vars->crumbs = "&nbsp;/&nbsp;&nbsp;<a href='/user/register/'>{$mod->title}</a>";
    return $mod;
  }
  
  /**
   * Проверка авторизованности
   */
  function checkAuth() {
    if ($_SESSION['user_id']>0) {
      return $this->result(null, 1);
    }
    else {
      return $this->result(null, 0);
    }
  }
  
  /**
   * Проверка email
   */
  function checkMail($params) {
    $email = $params['post']['email'];
    $user_id = $this->user_model->getIdByName($email);
    return $this->result(null, json_encode(array('user_id' => $user_id)));
  }
  
  /**
   * Переписка с админом
   */
  function letter($action = '', $id = 0) {
    $id = (int)$id;
    if ((int)$this->user_id<=0) {
      show_404('page');
      return;
    }
    if ($action=='new') {
      $this->load->view("/page/user_letter_new");
      return;
    }
    if ($action=='save_new') {
      $newtext = $this->input->post('newtext');
      $this->user_model->addMessageQue($this->user_id, $newtext);
      return;
    }
    if ($action=='full') {
      $mess = $this->user_model->getMessage($id, $this->user_id);
      if (!isset($mess['id']) && $mess['id']<=0) {
        show_404('page');
        return;
      }
      $this->view->setTitle("Вопрос #$id");
      $this->view->set('mess', $mess);
      $this->view->render("/page/user_letter_full");
      return;
    }
    $mess = $this->user_model->getMessage(0, $this->user_id);
    $this->view->set('mess', $mess);
    $this->view->setTitle("Общение с администратором");
    $this->view->render("/page/user_letter");
  }
  
  /**
   * Подписка на новости
   */
  function subscribe($params) {
    $action = $this->uri->segment(3);
    if ($action == 'hash') {
      return $this->_subscribe_hash($params);
    }
    
    $email = $params['post']['user_mail'];
    
    if (!mailFormat($email)) {
      return $this->result(null, get_json('Неверный формат e-mail!'));
    }
    $isset = $this->user_model->getSubs($email);
    if ($isset->id) {
      return $this->result(null, get_json('У вас уже есть активная подписка на новости!'));
    }
    
    $hash = $this->user_model->addSubs($email);
    
    if (!$hash) {
      return $this->result(null, get_json('Ошибка!'));
    }
    
    $this->load->library('email');
    $link = "http://".SERVER."/user/subscribe/hash/{$hash}";
    $message = "<b>Здравствуйте!</b><br /> 
    Спасибо за подписку на новости нашего интернет магазина ".NAME."! <br /><br /> 
    Для активации подписки перейдите по 
    <a href='{$link}'>ссылке,</a><br>
    либо скопируйте строку <br>\"{$link}\"<br>
    в поле ввода адреса вашего браузера.";
    
    $this->email->from(EMAIL, NAME);
    $this->email->to($params['post']['user_mail']);
    $this->email->subject('Активация подписки в интернет-магазине '.NAME);
    $this->email->message($message);
    $this->email->send();
    return $this->result(null, get_json(''));
  }
  
  /**
   * Активация подписки
   */
  function _subscribe_hash($params) {
    $hash = $this->uri->segment(4);
    $subs = $this->user_model->getSubsHash($hash);
    if (!$subs->id) {
      show_404('page');
      return;
    }
    $res = $this->user_model->setSubs($subs->id, array('active' => 1));
    if ($res) {
      return $this->result('Подтверждение подписки', 'Спасибо! Ваш e-mail успешно подтвержден!');
    }
  }
  
}

?>