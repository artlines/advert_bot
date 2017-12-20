<?
/**
 * @property Bot_model $bot_model
 * @property telegram_bot $bot
 */
class Bot extends MX_Controller {

  const STEPS = [
    "getRegion" => "Выбрать регион",
    "getCategory" => "Выбрать категорию",
    "search" => "Найти по ключевым словам",
    "setAdvert" => [
      "action" => "Разместить",
    ],
    "setAdvertText" => [
      "title" => "Введите заголовок:",
      "content" => "Введите текст:",
    ],
    "setAdvertFile" => [
      "photo" => "Добавьте картинки:",
    ],
    "getAdvert" => [
      "action" => "Смотреть",
    ],
    "clearState" => "Начать сначала",
    "finish" => "Опубликовать",
    'helpMe' => 'Помощь'
  ];

  private $admin_tg = null;
  private $token_tg = null;
  private $bot;
  private $chat_id = null;
  private $message = null;
  private $photo = null;
  private $user    = null;
  private $callback = false;
  private $commands = [
    '/start'      => 'welcome',
    'Смотреть'    => 'getAdvert',
    'Разместить'  => 'setAdvert',
    'Выберите регион:' => 'Advert',
    'Теперь выберите категорию:' => 'Advert',
    'Введите заголовок:' => 'setAdvertText',
    'Введите текст:' => 'setAdvertText',
    'Добавьте картинки:' => 'setAdvertPhoto',
    'Опубликовать' => 'finish',
    'Начать сначала' => 'clearState',
    'Помощь' => 'helpMe'
  ];

  function __construct()
  {
    parent::__construct();
    $this->load->library('telegram/telegram.php');
    $this->load->model('bot_model');
    $this->admin_tg = config('admin_tg');
    $this->token_tg = config('token_tg');
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
    $this->bot = new telegram_bot($this->token_tg);
    $data = $this->bot->read_post_message();
    if ($data->message) {
      $this->message  = $data->message->text;
      $this->photo    = $data->message->photo;
      $this->chat_id  = $data->message->chat->id;
      $this->user     = $data->message->from;
    }
    elseif ($data->callback_query) {
      $this->message  = $data->callback_query->message->text;
      $this->chat_id  = $data->callback_query->message->chat->id;
      $this->user     = $data->callback_query->message->chat;
      $this->callback = true;
    }

    $user_state = $this->_get_user_state(['user_id' => $this->user->id]);
    $this->commands['Выберите регион:'] = $user_state->user_type.$this->commands['Выберите регион:'];
    $this->commands['Теперь выберите категорию:'] = $user_state->user_type.$this->commands['Теперь выберите категорию:'];
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
    }elseif($user_state->previous_action){
      //если не нашли в настоящем, опираемся на прошлое
      $method = $this->commands[$user_state->previous_action];
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
    $answer = $this->load->view('welcome_message', ['admin' => $this->admin_tg], true);

    $data = [
      'user_id'         => $this->user->id,
      'previous_action' => self::STEPS['start'],
      'first_name'      => $this->user->first_name,
      'last_name'       => $this->user->last_name,
      'username'        => $this->user->username ?: $this->user->first_name . ' ' . $this->user->last_name,
    ];
    $this->_get_user_state($data, $post);

    $keyboard = [[self::STEPS['getAdvert']['action']], [self::STEPS['setAdvert']['action']], [self::STEPS['clearState']],[self::STEPS['helpMe']]];
    $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true]);
    $this->bot->send_message($this->chat_id, $answer, null, $reply, "HTML");
  }

  /**
   * Триггер на выборку объявлений
   */
  public function getAdvert($post)
  {
    //return $this->welcome($post);
    $this->load->model('adverts_model');
    $data = [
      'user_id' => $this->user->id,
      'previous_action' => self::STEPS['getAdvert']['action'],
      'user_type' => 'get'
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

    elseif ($user_state->category_id >= 0 && $type == 'category' && !$post->nextText) {
      $category = $this->bot_model->getCategory($ident);
      $message = "Вы выбрали категорию: " . $category->name;
      $this->_get_user_state([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
        'previous_action' => self::STEPS['getCategory'],
      ]);
      $this->bot->send_message($callback->message->chat->id, $message);
      $post->nextText = true;
      return $this->getAdvert($post);
    }

    else{
      $data = [
        'active' => 1,
        'category_id' => $user_state->category_id,
        'region_id' => $user_state->region_id
      ];

      $adverts = $this->adverts_model->find($data);
      $this->log($adverts);
      if (!empty($adverts)){
        $answer = $this->load->view('adverts_list', ['adverts' => $adverts], true);
      }else{
        $answer = 'По Вашему запросу ничего не найдено.';
      }
      $keyboard = [[self::STEPS['clearState']],[self::STEPS['helpMe']]];
      $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
      $this->bot->send_message($this->chat_id, $answer, null, $reply, "HTML");
    }
    return;
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
    $advert = $this->bot_model->getAdvertText($user_state->advert_id);
    $advert_file = $this->bot_model->getAdvertFiles($user_state);
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

    elseif ($user_state->category_id >= 0 && $type == 'category' && !$post->nextText) {
      $category = $this->bot_model->getCategory($ident);
      $message = "Вы выбрали категорию: " . $category->name;
      $this->_get_user_state([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
        'previous_action' => self::STEPS['getCategory'],
      ]);
      $this->bot->send_message($callback->message->chat->id, $message);
      $post->nextText = true;
      return $this->setAdvert($post);
    }
    
    elseif(!$advert->title){
        $data = [
          'user_id' => $this->user->id,
          'previous_action' => self::STEPS['setAdvertText']['title'],
        ];
        $this->_get_user_state($data);
        $answer = 'Введите заголовок:';
        $keyboard = [
          [self::STEPS['clearState']],[self::STEPS['helpMe']],
        ];
    }

    elseif(!$advert->content){
        $data = [
          'user_id' => $this->user->id,
          'previous_action' => self::STEPS['setAdvertText']['content'],
        ];
        $this->_get_user_state($data);
        $answer = 'Введите текст:';
        $keyboard = [
          [self::STEPS['clearState']],[self::STEPS['helpMe']],
        ];
      }

      elseif (!$advert_file) {
        $data = [
          'user_id' => $this->user->id,
          'previous_action' => self::STEPS['setAdvertFile']['photo'],
        ];
        $this->_get_user_state($data);
        $answer = 'Добавьте картинки:';
        $keyboard = [
          [self::STEPS['clearState']],[self::STEPS['helpMe']],
        ];
      }

      else{
        $data = [
          'user_id' => $this->user->id,
          'previous_action' => self::STEPS['setAdvertFile']['photo'],
        ];
        $this->_get_user_state($data);
        $answer = 'Добавьте еще или завершите публикацию.';
        $keyboard = [
          [self::STEPS['finish']],
        ];
      }

      $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
      $this->bot->send_message($this->chat_id, $answer, null, $reply);
      return;
  }

  /**
   * Записать объявление
   */
  public function setAdvertText($post)
  {
    $actions = array_flip(self::STEPS['setAdvertText']);
    $user_state = $this->_get_user_state(['user_id' => $this->user->id]);
    $data = [
      'user_id' => $this->user->id,
      $actions[$user_state->previous_action] => $this->message,
    ];

    $advert_id = $user_state->advert_id ?: 0;
    $data['category_id'] = $user_state->category_id;
    $data['region_id'] = $user_state->region_id;

    if($this->bot_model->editAdvertText($data, $advert_id)){
      return $this->setAdvert($post);
    }
    return;
  }

  /**
   * Записать картиночки к объявлению
   */
  public function setAdvertPhoto($post)
  {

    $user_state = $this->_get_user_state(['user_id' => $this->user->id]);
    $file_id = $this->photo[3]->file_id ?: $this->photo[0]->file_id;
    $file_path = PHOTO_DIR.$file_id;
    $filename = $this->bot->get_file($file_id, $file_path);
    /*для файла
      $file_id = $post->message->document->file_id;
      $file_path = FILE_DIR.$post->message->document->file_name;
      $filename = $p->bot()->get_file($file_id, $file_path);
    */
    $data = [
      'link' => $filename,
      'advert_id' => $user_state->advert_id,
      'type' => 'photo'
    ];

    if($this->bot_model->setAdvertFiles($data)){
      return $this->setAdvert($post);
    }
    return;
  }

  /**
   * Предоставить выбор региона
   */
  public function getRegion($post) {
    $answer = "Выберите регион:";
    $regions = $this->bot_model->getRegions();
    $reply = $this->getInlineKeyboard($regions, 'region');
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
    $this->bot_model->cleanUserState($this->user->id);
    return $this->welcome($post);
  }

  /**
   * Завершить публикацию объявления
   */
  public function finish($p)
  {
    $answer = config('finish');
    $keyboard = [
      [self::STEPS['clearState']],[self::STEPS['helpMe']],
    ];
    $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
    $this->bot->send_message($this->chat_id, $answer, null, $reply);
    return;
  }

  /**
   * @param null $post
   * @return void
   */

  public function helpMe($post)
  {
    $answer = $this->load->view('help_me_message', ['admin' => $this->admin_tg], true);
    $keyboard = [
      [self::STEPS['clearState']],[self::STEPS['helpMe']],
    ];
    $reply = json_encode(["keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true]);
    $this->bot->send_message($this->chat_id, $answer, null, $reply, "HTML");
    return;
  }

  private function _get_user_state($data, $post = null)
  {
    return $this->bot_model->changeUserData($data);
  }

}

