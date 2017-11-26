<?php

class User_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }

  /**
   * Поиск
   * @author Alexey
   */
  function find() {
    $where = [
      'admin' => 0
    ];
    return $this->db
      ->where($where)
      ->order_by('tm_register DESC')
      ->get('user')
      ->result();
  }

  /**
   * Количество
   * @author Alexey
   */
  function count() {
    return $this->db->where(['admin' => 0])->count_all_results('user');
  }

  // ---- OLD -----
  /**
   * Получить инфу про юзера
   */
  function Get($id, $with_pass = false) {
    $id = (int)$id;
    if (!$id)
      return false;
    $with_pass
      ? $password = ", password"
      : $password = "";
    $res = $this->db->query("select id, username, t_kl, active, skidka, price_category {$password} from user where id={$id}")->row_array();
    if ($res['id']<>$id) {
      return false;
    }
    $query = "select t1.id, t1.name, t2.value 
              from user_options t1 
                left join user_values t2 on (t1.id=t2.option_id and t2.user_id={$id})
              where t1.t_kl in (0, {$res['t_kl']}) order by t1.priority";
    $query = $this->db->query($query);
    foreach ($query->result_array() as $v => $k) {
      $res['values'][$k['id']] = $k;
    }
    return $res;
  }
  
  /**
   * Поиск по имени (e-mail)
   */
  function getIdByName($username) {
    $username = trim($username);
    if ($username=='')
      return false;
    $id = $this->db->query("select id from user where username='$username'")->row()->id;
    return $id;
  }
  
  /**
   * Авторизация
   */
  function Auth($username, $password) {
    $username = trim($username);
    $password = trim($password);
    if ($username=='' || $password=='') {
      return false;
    }
    $query = $this->db->query("select * from user where username='{$username}' and password='{$password}' and active=1 and admin=0")->row_array();
    if ($query['username']<>'') {
      $_SESSION['username'] = $query['username'];
      $_SESSION['user_id']  = $query['id'];
      return true;
    }
    return false;
  }
  
  /**
   * Регистрация
   */
  function Register($params) {
    $username = trim($params['username']);
    $password = trim($params['password']);
    $t_kl     = (int)$params['t_kl'];
    $pvalues  = $params['values'];
    $vars = $this->user_model->getUserVars($t_kl);
    foreach ($vars as $k => $v) {
      $values[$v['id']] = htmlspecialchars($pvalues[$v['id']], ENT_QUOTES);
    }
    if ($username=='' || $password=='') {
      return false;
    }
    if ($this->getIdByName($username)>0) {
      return false;
    }
    // начинаем
    $this->db->trans_start();
    // получаем хэш
    $hash = $this->getHash();
    // вставляем общую инфу про юзера
    $insert = array(
      'username' => $username, 
      'password' => $password, 
      't_kl'     => (int)$params['t_kl'], 
      'hash'     => $hash, 
      'admin'    => 0, 
      'active'   => 0
    );
    $this->db->insert('user', $insert);
    $user_id = $this->db->insert_id();
    if (!$user_id) {
      return false;
    }
    if (empty($values)) {
      return false;
    }
    // вставляем доп параметры для юзера
    foreach ($values as $v => $k) {
      $insert = array(
        'user_id'   => $user_id, 
        'option_id' => $v, 
        'value'     => $k
      );
      $this->db->insert('user_values', $insert);
    }
    // вставляем баланс
    $insert = array(
      'user_id' => $user_id, 
      'balans'  => 0, 
    );
    $this->db->insert('user_balans', $insert);
    // заканчиваем транзакцию
    $ret = $this->db->trans_complete();
    if (!$ret) {
      return false;
    }
    return $hash;
  }
  
  /**
   * Получение хэша, проверка на уникальность
   */
  function getHash() {
    $hash = genPass(40);
    $isset = $this->db->query("select * from user where hash='{$hash}'")->num_rows();
    if (!$isset) {
      return $hash;
    }
    else {
      return $this->getHash();
    }
  }
  
  /**
   * Подтверждение регистрации
   */
  function registerConfirm($hash) { 
    $hash = trim($hash);
    $user = $this->db->query("select * from user where hash='{$hash}' and hash<>''")->row();
    if ($user->id > 0) {
      $ret = $this->db->simple_query("update user set active=1, changed=1 where id={$user->id}");
      $_SESSION['username'] = $user->username;
      $_SESSION['password'] = $user->password;
      $_SESSION['user_id']  = $user->id;
      $_SESSION['new_user'] = true;
      return $ret;
    }
    else {
      return false;
    }
  }
  
  /**
   * Выход
   */
  function Logout() {
    @session_unset();
    @session_destroy();
  }
  
  /**
   * Установка основных параметров юзера
   */
  function Set($user_id, $params) {
    $user_id = (int)$user_id;
    if (!$user_id) {
      return "Не указан пользователь.";
    }
    $array = array(
      'username'        => '', 
      'password'        => '', 
      't_kl'            => '', 
      'active'          => '', 
      'skidka'          => '', 
      'price_category'  => ''
    );
    $params = array_intersect_key($params, $array);
    $params['changed'] = 1;
    $ret = $this->db->update("user", $params, array('id' => $user_id));
    if (!$ret) {
      return "Ошибка при изменении основных параметров пользователя.";
    }
    return;
  }
  
  /**
   * Установка доп параметров юзера
   */
  function SetParam($user_id, $params) {
    $user_id = (int)$user_id;
    if (!$user_id) {
      return "Не указан пользователь.";
    }
    $this->db->trans_start();
    if (!empty($params))
    foreach ($params as $key => $value) {
      $option_id = (int)$key;
      $isset = $this->db->query("select * from user_values where option_id={$option_id} and user_id={$user_id}")->num_rows();
      $data = array(
        'user_id'   => $user_id,
        'option_id' => $option_id,
        'value'     => $value
      );
      if ($isset) {
        $this->db->update('user_values', $data, array('user_id' => $user_id, 'option_id' => $option_id));
      }
      else {
        $this->db->insert('user_values', $data);
      }
    }
    $this->db->update('user', array('changed' => 1), array('id' => $user_id));
    $ret = $this->db->trans_complete();
    if (!$ret) {
      return "Ошибка при изменении дополнительных параметров пользователя.";
    }
    return;
  }
  
  /**
   * Удаляем юзера
   */
  function Del($user_id) {
    $user_id = (int)$user_id;
    if (!$user_id) {
      return false;
    }
    $query = $this->db->query("select * from zakaz where user_id={$user_id}");
    if ($query->num_rows()) {
      return "Ошибка! У пользователя есть заказы";
    }
    $query = $this->db->query("select * from user_messages where user_id={$user_id}");
    if ($query->num_rows()) {
      return "Ошибка! У пользователя есть заявки";
    }
    $this->db->trans_start();
    $this->db->query("delete from user_values where user_id={$user_id}");
    $this->db->query("delete from user where id={$user_id}");
    $ret = $this->db->trans_complete();
    if (!$ret) {
      return "Ошибка БД";
    }
    return;
  }
  
  /**
   * Список возможны параметров для данного типа клиента
   */
  function getUserVars($t_kl) {
    $t_kl = (int)$t_kl;
    $query = $this->db->query(
      "select * 
      from user_options 
      where t_kl in (0, {$t_kl}) 
      order by priority, id"
    )->result_array();
    return $query;
  }
  
  /**
   * Вывод имени юзера по его id
   */
  function out_username($id) {
    $user = $this->Get($id);
    if ($user['t_kl']==USER_TYPE_UR) {
      $username = $user['values'][USER_V_NAME]['value'];
    }
    else {
      $f = mb_substr($user['values'][USER_V_FNAME]['value'], 0, 1);
      $m = mb_substr($user['values'][USER_V_MNAME]['value'], 0, 1);
      $username = "{$user['values'][USER_V_LNAME]['value']} {$f}.{$m}.";
    }
    return $username;
  }
  
  /**
   * Получение баланса юзера
   */
  function getBalans($user_id) {
    $user_id = (int)$user_id;
    if (!$user_id)
      return false;
    $query = $this->db->query("select balans from user_balans where user_id={$user_id}");
    if ($query->num_rows()>0)
      return $query->row()->balans;
    else {
      $this->db->query("insert into user_balans(user_id, balans) values({$user_id}, 0)");
      return 0;
    }
  }
  
  /**
   * Добавление платежа
   */
  function setPay($params) {
    $user_id = (int)$params['user_id'];
    $this->db->trans_start();
    $this->getBalans($user_id);
    $payment = array(
      'user_id'     => (int)$params['user_id'], 
      'summa'       => (float)$params['summa'], 
      'description' => trim($params['description']), 
      'way_id'      => (int)$params['way_id'], 
      'zakaz_id'    => (int)$params['zakaz_id']
    );
    $this->db->insert('user_payments', $payment);
    $this->db->query("update user_balans set balans=balans+{$payment['summa']} where user_id={$user_id}");
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  /**
   * Поиск платежей
   */
  function getPay($params) {
    $id      = (int)$params['id'];
    $user_id = (int)$params['user_id'];
    if ($id>0){ 
      $query = 
        "select t1.*, t2.name as way_name 
        from user_payments t1 
          inner join user_pay_way t2 on t1.way_id=t2.id 
        where t1.id={$id}";
      $res = $this->db->query($query)->row_array();
    }
    if ($user_id>0) {
      $query = 
        "select t1.*, t2.name as way_name 
        from user_payments t1 
          inner join user_pay_way t2 on t1.way_id=t2.id 
        where user_id={$user_id} 
        order by t1.id";
      $res = $this->db->query($query)->result_array();
    }
    return $res;
  }
  
  /**
   * Удаление платежа
   */
  function delPay($id, $user_id) {
    $id       = (int)$id;
    $user_id  = (int)$user_id;
    if (!$user_id) {
      return false;
    }
    $pay = $this->getPay($id);
    if (empty($pay)) {
      return false;
    }
    if ($pay['user_id']<>$user_id) {
      return false;
    }
    $summa = $pay['summa'];
    $this->db->trans_start();
    $this->db->query("delete from user_payments where id={$id} and user_id={$user_id}");
    $this->db->query("update user_balans set balans=balans-{$summa} where user_id={$user_id}");
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  /**
   * Удаление платежей по заказу
   */
  function delZakazPay($zakaz_id, $user_id) {
    $zakaz_id = (int)$zakaz_id;
    $user_id = (int)$user_id;
    if (!$user_id || !$zakaz_id)
      return false;
    $this->db->trans_start();
    $query = "select sum(summa) as summa from user_payments where zakaz_id>0 and zakaz_id={$zakaz_id} and user_id={$user_id}";
    $ret = $this->db->query("update user_balans set balans=balans-({$query}) where user_id={$user_id}");
    $ret = $ret && $this->db->query("delete from user_payments where zakaz_id>0 and zakaz_id={$zakaz_id} and user_id={$user_id}");
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  /**
   * Вопросы для защиты от авторегистраций
   */
  function registerQuestionGet() {
    $res = $this->db->query("select * from user_register_questions order by rand() limit 1")->row();
    return $res;
  }
  
  
  
  
  
  function addMessageQue($user_id, $text) {
    $user_id = (int)$user_id;
    if ($user_id<=0)
      return false;
    $ret = $this->db->simple_query("insert into user_messages(user_id, question) values($user_id, '$text')");
    return $ret;
  }
  
  function addMessageAns($q_id, $answer) {
    $q_id = (int)$q_id;
    if (!$q_id)
      return false;
    $ret = $this->db->simple_query("update user_messages set answer='{$answer}' where id={$q_id}");
    return $ret;
  }
  
  function delMessage($user_id, $q_id) {
    
  }
  
  function getMessage($id = 0, $user_id = 0) {
    $id = (int)$id;
    $user_id = (int)$user_id;
    if ($id>0 && $user_id>0) {
      $res = $this->db->query("select * from user_messages where id=$id and user_id=$user_id")->row_array();
    }
    elseif ($user_id>0) {
      $res = $this->db->query("select * from user_messages where user_id=$user_id order by tm desc")->result_array();
    }
    elseif ($id=-1) {
      $res = $this->db->query("select * from user_messages where answer is null or answer='' order by tm desc")->result_array();
    }
    elseif ($id=-2) {
      $res = $this->db->query("select * from user_messages order by tm desc")->result_array();
    }
    return $res;
  }
  
  function userExport() {
    $file = TMP.'export_user.dbf';
    $defs = array(
      array('id', "N", 10, 0),
      array('t_kl', "N", 1, 0),
      array('skidka', "N", 2, 0),
      array('lastname', "C", 1000),
      array('firstname', "C", 1000),
      array('middlename', "C", 1000),
      array('address', "C", 1000),
      array('uaddress', "C", 1000),
      array('phone', "C", 1000),
      array('fax', "C", 1000),
      array('inn', "C", 1000),
      array('kpp', "C", 1000),
      array('bank', "C", 1000),
      array('bik', "C", 1000),
      array('rs', "C", 1000),
      array('ks', "C", 1000),
      array('work_type', "C", 1000)
    );
    $db = dbase_create($file, $defs);
    if (!$db)
      return "Ошибка создания файла";
    $user = $this->Find();
    foreach ($user as $v => $k) {
      $user_detail = $this->Get($k['id']);
      $user_detail['t_kl']==1
        ? $lastname = $user_detail['values'][12]['value']
        : $lastname = $user_detail['values'][1]['value'];
      $array = array (
        $k['id'],
        $user_detail['t_kl'],
        $user_detail['skidka'],
        utf2win($lastname),
        utf2win($user_detail['values'][13]['value']),
        utf2win($user_detail['values'][14]['value']),
        utf2win($user_detail['values'][2]['value']),
        utf2win($user_detail['values'][3]['value']),
        utf2win($user_detail['values'][10]['value']),
        utf2win($user_detail['values'][11]['value']),
        utf2win($user_detail['values'][4]['value']),
        utf2win($user_detail['values'][5]['value']),
        utf2win($user_detail['values'][7]['value']),
        utf2win($user_detail['values'][9]['value']),
        utf2win($user_detail['values'][6]['value']),
        utf2win($user_detail['values'][8]['value']),
        utf2win($user_detail['values'][15]['value'])
      );
      dbase_add_record($db, $array);
    }
    dbase_close($db);
    return file_get_contents($file);
  }
  
  /**
   * Получение темы обращения
   */
  function getCallbackSubject($id) {
    $id = (int)$id;
    $res = $this->db->where('id', $id)->get('callback_subjects')->row();
    return $res;
  }
  
  /**
   * Добавление подписки
   */
  function addSubs($email) {
    $hash = md5(genPass(40) . $email . time());
    $this->db->delete('subscription', array(
      'email'   => $email,
      'active'  => 0
    ));
    $ret = $this->db->insert('subscription', array(
      'email' => $email,
      'hash'  => $hash
    ));
    if ($ret) {
      return $hash;
    }
  }
  
  /**
   * Поиск hash на подтверждение подписки
   */
  function getSubsHash($hash) {
    $res = $this->db->where(array(
      'hash'    => $hash,
      'active'  => 0
    ))->get('subscription')->row();
    return $res;
  }
  
  /**
   * Поиск e-mail в списке подписчиков
   */
  function getSubs($email) {
    $res = $this->db->where(array(
      'email'   => $email,
      'active'  => 1
    ))->get('subscription')->row();
    return $res;
  }
  
  /**
   * Внесение изменений в списке подписчиков
   */
  function setSubs($id, $params) {
    $res = $this->db->update('subscription', $params, array('id' => $id));
    return $res;
  }  
  
  /**
   * Получение списка подписчиков
   */
  function findSubs() {
    $res = $this->db->where(array(
      'active'  => 1
    ))->get('subscription')->result();
    return $res;
  }
  
  /**
   * Проверка IP-адреса
   */
  function checkAdminIPS() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $res = $this->db->query("SELECT * FROM admin_ips WHERE ip = '{$ip}'")->row();
    return $res->id;
  }
}
?>