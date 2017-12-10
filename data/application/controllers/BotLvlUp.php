<?
class BotLvlUp extends MX_Controller {

  const TELEGRAM_BOTNAME = "useCoinsUpBot";
  const TELEGRAM_TOKEN = "498418840:AAFSNB0YQQtG4gIlwiqTQrZzuSEXGQefiN4";
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
    $this->load->model('bot_model');
  }
  /**
   * Основной метод обработки
   * Регистрация триггеров
   * Запуск бота
   */
  function index()
  {

  }

  /**
   * Триггер-старт
   */
  public function welcome($p)
  {

  }

  /**
   * Триггер на выборку объявлений
   */
  public function getAdvert($p)
  {

  }

  /**
   * Триггер на размещение
   */
  public function setAdvert($p)
  {

  }


  /**
   * Записать объявление
   */
  public function setAdvertText($p, $action)
  {

  }

  /**
   * Записать картиночки к объявлению
   */
  public function setAdvertPhoto($p)
  {

  }

  /**
   * Записать файлы к объявлению
   */
  public function setAdvertDocument($p)
  {

  }

  /**
   * Предоставить выбор региона
   */
  public function getRegion($p)
  {

  }

  /**
   * Предоставить выбор категории
   */
  public function getCategory($p, $regions)
  {

  }
  /**
   * Текстовый триггер
   * - всё, что не попало в обработку другими триггерами
   */
  public function text($p)
  {

  }

  /**
   * Очистить состояние юзера и начать сначала
   */
  public function clearState($p)
  {

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

  }

}

