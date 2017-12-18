<?
/**
 * @property Bot_model $bot_model
 * @property telegram_bot $bot
 */
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

  private $commands = [
    '/start'      => 'welcome',
    'Смотреть'    => 'getAdvert',
    'Разместить'  => 'setAdvert',
    'Выберите регион:' => 'setAdvert',
    'Теперь выберите категорию:' => 'setAdvert'
  ];

  private $bot;
  private $chat_id = null;
  private $message = null;
  private $user    = null;
  private $callback = false;

  function __construct()
  {
    parent::__construct();
    $this->load->library('telegram/telegram.php');
    $this->load->library('pagination/InlineKeyboardPagination.php');
    $this->load->model('bot_model');
  }

  /**
   * Логи
   * @author Alexey
   */
  private function log($message) {
    if (is_array($message) || is_object($message)) {
      $message = print_r($message, 1);
    }
    file_put_contents('/home/advert/tmp/post.log', date("Y-m-d H:i:s") . $message . "\n", FILE_APPEND);
  }

  /**
   * Основной метод обработки
   * Регистрация триггеров
   * Запуск бота
   */
  function index()
  {
    $input = file_get_contents('php://input');
    $input = json_decode($input, true);
    $this->log($input);

    $this->bot = new telegram_bot(self::TELEGRAM_TOKEN);
    $data = $this->bot->read_post_message();
    if ($data->message) {
      $this->message  = $data->message->text;
      $this->chat_id  = $data->message->chat->id;
      $this->user     = $data->message->from;
    }
    elseif ($data->callback_query) {
      $this->message  = $data->callback_query->message->text;
      $this->chat_id  = $data->callback_query->message->chat->id;
      $this->user     = $data->callback_query->message->chat;
      $this->callback = true;
    }

    $method = $this->commands[$this->message];
    $this->log($method);
    if (method_exists($this, $method)) {
      try {
        $this->log("Run {$method}");
        return $this->$method($data);
      }
      catch (Exception $e) {
        $this->log("Error: " . $e->getMessage());
      }
    }
    return;
  }

  /**
   * Триггер-старт
   */
  public function welcome($post)
  {
    $this->clearState($post);
    $answer = "Добро пожаловать!";
    $data = [
      'user_id'         => $this->user->id,
      'previous_action' => self::STEPS['start'],
      'first_name'      => $this->user->first_name,
      'last_name'       => $this->user->last_name,
      'username'        => $this->user->first_name . ' ' . $this->user->last_name,
    ];
    $this->_get_user_state($data, $post);

    $keyboard = [[self::STEPS['getAdvert']['action']], [self::STEPS['setAdvert']['action']]];
    $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
    $this->bot->send_message($this->chat_id, $answer, null, $reply);
  }

  /**
   * Триггер на выборку объявлений
   */
  public function getAdvert($p)
  {
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
  }

  /**
   * Триггер на размещение
   */
  public function setAdvert($post)
  {
    //return $this->welcome($post);
    $data = [
      'user_id'         => $this->user->id,
      'previous_action' => self::STEPS['setAdvert']['action'],
      'user_type'       => 'set'
    ];
    $user_state = $this->_get_user_state($data);

    $callback = $post->callback_query;
    list($type, $ident) = explode('_', $callback->data);

    if (!$user_state->region_id && !$this->callback) {
      return $this->getRegion($post);
    }
    elseif (!$user_state->region_id && $type == 'region') {
      $region = $this->bot_model->getRegion($ident);
      $message = "Вы выбрали регион: " . $region->name;
      $this->_get_user_state([
        'user_id' => $this->user->id,
        'region_id' => $region->id,
        'previous_action' => self::STEPS['getRegion'],
      ]);
      $this->bot->send_message($callback->message->chat->id, $message);
      return $this->getCategory($post);
    }
    elseif (!$user_state->category_id && $type == 'category') {
      $category = $this->bot_model->getCategory($ident);
      $message = "Вы выбрали категорию: " . $category->name;
      $this->log("'cat: ' {$category->name}");
      $this->_get_user_state([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
        'previous_action' => self::STEPS['getCategory'],
      ]);
      $this->bot->send_message($callback->message->chat->id, $message);
      //return $this->getCategory($post);
    }
    else {
    return;
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
      }
      elseif ($advert->content && $advert->title && !$advert_file) {
        $data = [
          'user_id' => $post->message->from->id,
          'previous_action' => self::STEPS['setAdvertText']['file'],
        ];
        $this->_get_user_state($data);
        $answer = 'Добавьте картинки или файлы';
        $keyboard = [
          [self::STEPS['clearState']],
        ];
      }
      elseif($advert_file) {
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
  public function getRegion($post) {
    $answer = "Выберите регион:";
    $regions = $this->bot_model->getRegions();
    $reply = $this->getInlineKeyboard($regions, 'region');
    $this->log($reply);
    $this->bot->send_message($this->chat_id, $answer, null, json_encode(['inline_keyboard' => $reply]));
  }

  /**
   * Предоставить выбор категории
   */
  public function getCategory($post)
  {
    $answer = "Теперь выберите категорию:";
    $categories = $this->bot_model->getCategories();
    $reply = $this->getInlineKeyboard($categories, 'category');
    $this->log($reply);
    $this->bot->send_message($this->chat_id, $answer, null, json_encode(['inline_keyboard' => $reply]));
  }

  /**
   * Получить инлайн-клавиатуру из массива значений
   */
  public function getInlineKeyboard($data, $type)
  {
    foreach ($data as $d){
      $list[$d->id] = $d->name;
    }
    $chunked = array_chunk($list, 2, true);
    $reply = [];
    foreach ($chunked as $key => $chunk) {
      foreach ($chunk as $k => $ch){
        $reply[$key][] = [
          'text' => $ch,
          'callback_data' => $type .'_'. $k
        ];
      }
    }
    return $reply;
  }



  /**
   * Очистить состояние юзера и начать сначала
   */
  public function clearState($post)
  {
    return $this->bot_model->cleanUserState($this->user->id);
  }

  private function _get_user_state($data, $post = null)
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
