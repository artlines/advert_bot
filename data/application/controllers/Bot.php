<?
class Bot extends MX_Controller {

  const TELEGRAM_BOTNAME = "useCoinsBot";
  const TELEGRAM_TOKEN = "460714669:AAErsCG--kFH1S2tjimjL9rvYBwA7a2glfk";
  const SINGLE_TRIGGER = FALSE;
  const STATUS_SUCCESS = "success";
  const STATUS_ERROR = "error";
  const STEPS = [
    "start" => "/start",
    "finish" => "Опубликовать",
    "clearState" => "Начать сначала",
    "getRegion" => "Выбрать регион",
    "getCategory" => "Выбрать категорию",
    "search" => "Найти по ключевым словам",
    "setAdvert" => [
      "action" => "Разместить",
    ],
    "setAdvertText" => [
      "title" => "Ввести заголовок объявления",
      "content" => "Ввести текст объявления",
    ],
    "setAdvertFile" => [
      "photo" => "Добавить фото",
      "file" => "Добавить фaйл",
    ],
    "getAdvert" => [
      "action" => "Смотреть",
      "textAnswer" => "Выборочка",
    ]
  ];

  function __construct()
  {
    parent::__construct();
    $this->load->library('telegram/telegram.php');
    $this->load->library('pagination/InlineKeyboardPagination.php');
    $this->load->model('bot_model');
  }
  /**
   * Основной метод обработки
   * Регистрация триггеров
   * Запуск бота
   */
  function index()
  {
    $bot = new telegram_bot(self::TELEGRAM_TOKEN);
    $data = $bot->read_post_message();
    $message = $data->message;
    $chatid = $message->chat->id;

    $ts = new telegram_trigger_set(self::TELEGRAM_BOTNAME, $chatid, self::SINGLE_TRIGGER);

    $ts->register_trigger_any("trigger_text", "waiting_for_input");
    $ts->register_trigger_photo("trigger_setAdvertPhoto", null);
    $ts->register_trigger_document("trigger_setAdvertDocument", null);
    $ts->register_trigger_text_command("trigger_welcome", [self::STEPS['start']], 0, null);
    $ts->register_trigger_text_command("trigger_setAdvert", [self::STEPS['setAdvert']['action']], "in_chat");
    $ts->register_trigger_text_command("trigger_getAdvert", [self::STEPS['getAdvert']['action']], "in_chat");
    $ts->register_trigger_text_command("trigger_getRegion", [self::STEPS['getRegion']], "in_chat");
    $ts->register_trigger_text_command("trigger_clearState", [self::STEPS['clearState']], "in_chat");
    $ts->register_trigger_text_command("trigger_finish", [self::STEPS['finish']], 0, null);
    $ts->register_trigger_error("trigger_err", "*");

    $response = $ts->run($bot, $message);

  }

  /**
   * Триггер-старт
   */
  public function welcome($p)
  {
    try {

      $answer = "Добро пожаловать!";
      $post = $p->bot()->read_post_message();
      $data = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['start'],
        'first_name' => $post->message->from->first_name,
        'last_name' => $post->message->from->last_name,
        'username' => $post->message->from->first_name.' '.$post->message->from->last_name,
      ];
      $this->_get_user_state($data);

      $keyboard = [[self::STEPS['getAdvert']['action']],[self::STEPS['setAdvert']['action']]];
      $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
      $p->bot()->send_message($p->chatid(), $answer, null, $reply);

      return [__METHOD__ => self::STATUS_SUCCESS];
    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Триггер на выборку объявлений
   */
  public function getAdvert($p)
  {
    try{

      $post = $p->bot()->read_post_message();
      $data = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['getAdvert']['action'],
        'user_type' => 'get'
      ];
      $user_state = $this->_get_user_state($data);
      $regions = $this->bot_model->getRegionsById($user_state->region_id);
      $categories = $this->bot_model->getCategoriesById($user_state->category_id);

      if (!$user_state->region_id){
        return $this->getRegion($p);
      }elseif (!$user_state->category_id && $regions){
        return $this->getCategory($p, $regions);
      }else{
        $answer = 'Выборочка объявлений';
        $keyboard = [[self::STEPS['clearState']]];
        $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
        return $p->bot()->send_message($p->chatid(), $answer, null, $reply);
      }

    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Триггер на размещение
   */
  public function setAdvert($p)
  {
    try{

      $post = $p->bot()->read_post_message();
      $data = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['setAdvert']['action'],
        'user_type' => 'set'
      ];
      $user_state = $this->_get_user_state($data);
      if($user_state->region_id){
        $regions = $this->bot_model->getRegionsById($user_state->region_id);
      }

      if (!$user_state->region_id){
        return $this->getRegion($p);
      }elseif (!$user_state->category_id && $regions){
        return $this->getCategory($p, $regions);
      }else{

        $advert = $this->bot_model->getAdvertText($user_state->advert_id);
        $advert_file = $this->bot_model->getAdvertFiles($user_state);

        if(!$advert || !$advert->title){
          $data = [
            'user_id' => $post->message->from->id,
            'previous_action' => self::STEPS['setAdvertText']['title'],
          ];
          $this->_get_user_state($data);
          $answer = 'Введите заголовок';
          $keyboard = [
            [self::STEPS['clearState']],
          ];
        }elseif(!$advert->content){
          $data = [
            'user_id' => $post->message->from->id,
            'previous_action' => self::STEPS['setAdvertText']['content'],
          ];
          $this->_get_user_state($data);
          $answer = 'Введите текст';
          $keyboard = [
            [self::STEPS['clearState']],
          ];
        }elseif ($advert->content && $advert->title && !$advert_file){
          $data = [
            'user_id' => $post->message->from->id,
            'previous_action' => self::STEPS['setAdvertText']['file'],
          ];
          $this->_get_user_state($data);
          $answer = 'Добавьте картинки или файлы';
          $keyboard = [
            [self::STEPS['clearState']],
          ];
        }elseif($advert_file){
          $data = [
            'user_id' => $post->message->from->id,
            'previous_action' => self::STEPS['setAdvertText']['file'],
          ];
          $this->_get_user_state($data);
          $answer = 'Добавьте еще или завершите публикацию.';
          $keyboard = [
            [self::STEPS['finish']],
          ];
        }

        $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
        $p->bot()->send_message($p->chatid(), $answer, null, $reply);

        return [__METHOD__ => 'success'];
      }

    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }


  /**
   * Записать объявление
   */
  public function setAdvertText($p, $action)
  {
    try{

      $post = $p->bot()->read_post_message();
      $data = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['setAdvert']['action'],
        $action => $post->message->text,
      ];
      $user_state = $this->_get_user_state($data);
      $advert_id = $user_state->advert_id ?: 0;
      unset($data['previous_action']);
      $data['category_id'] = $user_state->category_id;

      if($this->bot_model->editAdvertText($data, $advert_id)){
        return $this->setAdvert($p);
      }else{
        return false;
      }


    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Записать картиночки к объявлению
   */
  public function setAdvertPhoto($p)
  {
    try{

      $post = $p->bot()->read_post_message();
      $user = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['setAdvertFile']['photo']
      ];
      $user_state = $this->_get_user_state($user);
      $file_id = $post->message->photo[3]->file_id ?: $post->message->photo[0]->file_id;
      $file_path = PHOTO_DIR.$file_id;
      $filename = $p->bot()->get_file($file_id, $file_path);
      $data = [
        'link' => $filename,
        'advert_id' => $user_state->advert_id,
        'type' => 'photo'
      ];

      if($this->bot_model->setAdvertFiles($data)){
        return $this->setAdvert($p);
      }else{
        return false;
      }

    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Записать файлы к объявлению
   */
  public function setAdvertDocument($p)
  {
    try{

      $post = $p->bot()->read_post_message();
      $user = [
        'user_id' => $post->message->from->id,
        'previous_action' => self::STEPS['setAdvertFile']['file']
      ];
      $user_state = $this->_get_user_state($user);
      $file_id = $post->message->document->file_id;
      $file_path = FILE_DIR.$post->message->document->file_name;
      $filename = $p->bot()->get_file($file_id, $file_path);
      $data = [
        'link' => $filename,
        'advert_id' => $user_state->advert_id,
        'type' => 'file'
      ];

      if($this->bot_model->setAdvertFiles($data)){
        return $this->setAdvert($p);
      }else{
        return false;
      }

    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Предоставить выбор региона
   */
  public function getRegion($p)
  {
    try {
      $answer = "Выберите регион или введите несколько регионов через запятую";
      $regions = $this->bot_model->getRegions();

      foreach ($regions as $region){
        $list[] = $region->name;
      }
    /*  $chunked = array_chunk($list, 3);
      $count = array_keys($chunked);
      $items         = $count; // required.
      $command       = 'regionPage'; // optional. Default: pagination
      $selected_page = 1;            // optional. Default: 1
      $labels        = [              // optional. Change button labels (showing defaults)
        'default'  => '%d',
        'first'    => '« %d',
        'previous' => '‹ %d',
        'current'  => '· %d ·',
        'next'     => '%d ›',
        'last'     => '%d »',
      ];
      $ikp = new InlineKeyboardPagination($items, $command);
      $ikp->setMaxButtons(6, true); // Second parameter set to always show 7 buttons if possible.
      $ikp->setLabels($labels);
      $pagination = $ikp->getPagination($selected_page);

      $chunked = array_chunk($list, 3);
      foreach ($chunked as $key => $chunk){
        $chunked[$key] = implode(',', $chunk);
      }
      $chunked[] = $pagination['keyboard'][0]['text'];
      file_put_contents(LOG, print_r($chunked, 1));*/
      $reply = json_encode(["keyboard" => [$list]]);
      $p->bot()->send_message($p->chatid(), $answer, null, $reply);
      $p->state()->movetostate("in_chat");

      return [__METHOD__ => self::STATUS_SUCCESS];
    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Предоставить выбор категории
   */
  public function getCategory($p, $regions)
  {
    try {
      $answer = "Отлично, найдены регионы: ".$regions['list'].'. ';
      $answer .= "Теперь выберите каетгорию или введите несколько каетгорий через запятую: ";
      $categories = $this->bot_model->getCategories();

      foreach ($categories as $category){
        $list[] = $category->name;
      }
      //знает только категории и позволяет их ввести
      $keyboard = [$list];

      $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
      $p->bot()->send_message($p->chatid(), $answer, null, $reply);
      $p->state()->movetostate("in_chat");

      return [__METHOD__ => self::STATUS_SUCCESS];
    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }
  /**
   * Текстовый триггер
   * - всё, что не попало в обработку другими триггерами
   */
  public function text($p)
  {
    try {
      $post = $p->bot()->read_post_message();
      $data = [
        'user_id' => $post->message->from->id,
        'previous_action' => $post->message->text
      ];

      if(in_array($data['previous_action'], self::STEPS)
        || in_array($data['previous_action'], self::STEPS['setAdvertFile'])
      ){
        return true;
      }

      $regions = $this->bot_model->getRegionsByName($post->message->text);
      $categories = $this->bot_model->getCategoriesByName($post->message->text);
      $data['category_id'] = $categories['ids'];
      $data['region_id'] = $regions['ids'];
      $user_state = $this->_get_user_state($data);
      $user_type = $user_state->user_type;
      $method = $user_type.'Advert';
      $method_text = $method.'Text';

      switch ($user_state->previous_action){

        case self::STEPS[$method]['action']:
          $data['previous_action'] = self::STEPS[$method]['action'];
          $user_state = $this->_get_user_state($data);

          if(!$user_state->category_id){
            $regions = $this->bot_model->getRegionsById($user_state->region_id);
            return $this->getCategory($p, $regions);
          }elseif($user_state->category_id && $user_state->region_id){
            return $this->$method($p, $user_state);
          }
          break;

        case self::STEPS[$method_text]['title']:
          return $this->setAdvertText($p, 'title');
          break;

        case self::STEPS[$method_text]['content']:
          return $this->setAdvertText($p, 'content');
          break;

        default:
          return true;
          break;
      }

      return [__METHOD__ => self::STATUS_SUCCESS];
    }catch(Exception $e) {
      return [__METHOD__ => $e->getMessage()];
    }
  }

  /**
   * Очистить состояние юзера и начать сначала
   */
  public function clearState($p)
  {
    $post = $p->bot()->read_post_message();
    $this->bot_model->cleanUserState($post->message->from->id);
    return $this->welcome($p);
  }

  private function _get_user_state($data)
  {
    return $this->bot_model->changeUserData($data);
  }

  /**
   * Завершить публикацию объявления
   */
  public function finish($p)
  {
    $answer = "Спасибо, Ваше объявление добавлено и ожидает модерации!";
    $keyboard = [
      [self::STEPS['clearState']],
    ];
    $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
    $p->bot()->send_message($p->chatid(), $answer, null, $reply);
    return [__METHOD__ => self::STATUS_SUCCESS];
  }

  /**
   * При возникновении ошибки
   */
  public function error($p)
  {
    $answer = "Что-то пошло не так, попробуйте еще раз!";
    $p->bot()->send_message($p->chatid(), $answer);
    return;
  }
}

function trigger_welcome($p) {
  $bot = new Bot($p);
  return $bot->welcome($p);
}
function trigger_getRegion($p) {
  $bot = new Bot($p);
  return $bot->getRegion($p);
}
function trigger_setAdvert($p) {
  $bot = new Bot($p);
  return $bot->setAdvert($p);
}
function trigger_setAdvertPhoto($p) {
  $bot = new Bot($p);
  return $bot->setAdvertPhoto($p);
}
function trigger_setAdvertDocument($p) {
  $bot = new Bot($p);
  return $bot->setAdvertDocument($p);
}
function trigger_getAdvert($p) {
  $bot = new Bot($p);
  return $bot->getAdvert($p);
}
function trigger_text($p) {
  $bot = new Bot($p);
  return $bot->text($p);
}
function trigger_clearState($p) {
  $bot = new Bot($p);
  return $bot->clearState($p);
}
function trigger_finish($p) {
  $bot = new Bot($p);
  return $bot->finish($p);
}
function trigger_err($p) {
  $bot = new Bot($p);
  return $bot->error($p);
}

