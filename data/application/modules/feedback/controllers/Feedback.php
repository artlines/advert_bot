<?php
class Feedback extends S_Module {

  function __construct() {
    parent::__construct();
  }
  
  function runModule($razdel_info) {
    $this->params['page'] = $razdel_info->params;
    $method = $this->uri->segment(2);
    $params['CI'] = $this;
    $params['url'] = $razdel_info->url;
    foreach ($_REQUEST as $key => $value) {
      $params['post'][$key] = $this->input->post($key);
    }
    
    if ($method) {
      if (method_exists($this, $method)) {
        $this->action = $this->uri->segment(3);
        return $this->$method($params);
      }
      else {
        echo "Метод не найден!";
        return;
      }
    }
    return $this->feedback($params);
    
    $keys = $this->config->item('yandexMapKey');
    $data['mapKey'] = $keys[SERVER];
    $data['main_title'] = config('main_title');
    $data['email']      = config('email');
    $data['phone1']     = config('phone1');
    $data['phone2']     = config('phone2');
    $data['address']    = config('address');
    $data['worktime']   = config('worktime');
    $mod->text = $this->load->view('contacts', $data, true);
    return $mod;
  }
  
  /**
   * Обратная связь
   */
  function feedback($params) {
    exit;
    $antispam = $this->user_model->registerQuestionGet();
    $_SESSION['register_question_answer'] = $antispam->answer;
    $params['question'] = $antispam->question;
    
    $mod->title = "Помогите нам стать лучше";
    $mod->text = $this->load->view('feedback', $params, true);
    return $mod;
  }
  
  /**
   * Обратная связь - отправка формы
   */
  function feedbackSend($params) {
    $params['post'] = array_map("trim", $params['post']);
    
    $subject = $this->user_model->getCallbackSubject($params['post']['subject']);
    
    if (!$params['post']['name']) {
      echo get_json('Пожалуйста, введите ваше имя!');
      exit();
    }
    if (!$params['post']['phone']) {
      echo get_json('Пожалуйста, введите номер вашего телефона!');
      exit();
    }
    if (!$params['post']['email']) {
      echo get_json('Пожалуйста, введите ваш e-mail!');
      exit();
    }
    if (!$params['post']['message']) {
      echo get_json('Пожалуйста, введите непустое сообщение!');
      exit();
    }
    if ($_SESSION['register_question_answer']=='' || $_SESSION['register_question_answer']<>$params['post']['answer']) {
      echo get_json('Неверный ответ на контрольный вопрос!');
      exit();
    }

    $message = $this->load->view("feedback_message", $params['post'], true);
    
    $this->load->library('email');
    $this->email->clear();
    $this->email->from(EMAIL, NAME);
    $this->email->subject(NAME . ' ' . $subject->name);
    $this->email->message($message);
    $this->email->to($subject->email);
    $this->email->send();
    
    exit(get_json(''));
  }
  
  /**
   * Обратная связь - отправка формы из виджета
   */
  function sendMessage($params) {
    $params['post'] = array_map("trim", $params['post']);
    
    if ($params['post']['feedback_name'] == '') {
      echo get_json('Пожалуйста, введите ваше имя!');
      exit();
    }
    if ($params['post']['feedback_phone'] == '') {
      echo get_json('Пожалуйста, введите номер вашего телефона!');
      exit();
    }/*
    if (!$params['post']['feedback_email']) {
      echo get_json('Пожалуйста, введите ваш e-mail!');
      exit();
    }
    if (!$params['post']['feedback_message']) {
      echo get_json('Пожалуйста, введите непустое сообщение!');
      exit();
    }*/
    $params['post']['feedback_message'] = $params['post']['feedback_product'];

    $message = $this->load->view("feedback_message", $params['post'], true);
    
    $this->email->clear();
    $this->email->from(EMAIL, NAME);
    $this->email->subject(NAME);
    $this->email->message($message);
    $this->email->to(config('feedback_email'));
    $this->email->send();
    
    exit(get_json(''));
  }
  
  /**
   * Отправка заявки
   */
  function sendRequest() {
    $message = trim($this->input->post('text'));
    $this->load->library('email');
    $this->email->clear();
    $this->email->from(EMAIL, NAME);
    $this->email->subject(NAME);
    $this->email->message($message);
    $this->email->to(config('feedback_email'));
    $this->email->send();
    echo get_json('');
    return $this->result(null);
  }
  
  /**
   * Загрузка карты
   * @author Alexey
   */
  function loadMap() {
    $text = $this->load->view('loadMap', $this->params, true);
    return $this->result(null, $text);
  }
  
}

?>