<?php

  /**
   * Выпадающий список. Данные берутся из таблицы
   */
  function in_table($name, $params) {
    $CI = &get_instance();
    $table = $params['table'];
    $check = $CI->db->simple_query("SHOW CREATE TABLE {$table}");
    if (!$check) {
      return "Таблица {$table} не найдена";
    }
    $params['name'] ? $fname = $params['name'] : $fname = 'name';
    
    $check = $CI->db->query("show fields from {$table} where Field='priority'")->num_rows();
    $order_by = ($check ? 'priority' : $fname);
    
    $CI->db->order_by($order_by);
    if ($params['where']) {
      $CI->db->where($params['where']);
    }
    $data = $CI->db->get($table)->result_array();
    return in_select($name, $data, $params);
  }
  
  /**
   * array debug function
   */
  function adebug($array, $ret=0) {
    $text = print_r($array, 1);
    $res = "<div style='border:1px solid #0000FF;'><pre>$text</pre></div>";
    if ($ret) {
      return $res;
    }
    print $res;
  }
  
  
  /**
   * input.file
   */
  function in_file($name) {
    return '<input type="file" name="'.$name.'" id="'.$name.'">';
  }
  
  /**
   * input.hidden
   */
  function in_hidden($name, $value) {
    return '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
  }
  
  /**
   * input.text
   */
  function in_text($name, $value = '', $width = '200px', $class = '', $id = '') {
    $CI = &get_instance();
    if (is_array($width)) {
      $def_params = array(
        'width' => '200px',
        'id'    => $name,
        'class' => ''
      );
      $params = array_merge($def_params, $width);
    }
    else {
      $params['value'] = $value;
      $params['width'] = $width;
      $params['class'] = $class;
      $params['id']    = ($id ? $id : $name);
    }
    $params['name']  = $name;
    $params['value'] = $value;
    return $CI->load->view('helper/in_text', $params, true);
  }
  
  /**
   * input.text readonly
   */
  function in_text_readonly($name, $value = '', $width = '200px', $class = '', $id = '') {
    $id<>'' ? 0 : $id = $name;
    return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" style="width:'.$width.';" class="'.$class.' form-control" readonly="readonly" />';
  }
  
  /**
   * input.textarea
   */
  function in_textarea($name, $value = '', $width = '200px', $height = '100px') {
    $CI = &get_instance();
    if (is_array($width)) {
      $def_params = array(
        'width'  => '200px',
        'id'     => $name,
        'class'  => '',
        'height' => '100px'
      );
      $params = array_merge($def_params, $width);
    }
    else {
      $params['value'] = $value;
      $params['class'] = $class;
      $params['width'] = $width;
      $params['id']    = ($id ? $id : $name);
    }
    $params['name']  = $name;
    $params['value'] = $value;
    return $CI->load->view('helper/in_textarea', $params, true);
  }
  
  /**
   * input.password
   */
  function in_password($name, $value = '', $width = '200px') {
    return '<input type="password" name="'.$name.'" id="'.$name.'" value="'.$value.'" style="width:'.$width.';" />';
  }
  
  /**
   * выпадающий список
   */
  function in_select($name, $array, $value = '', $width = '200px', $empty_val = false) {
    $fid   = 'id';
    $fname = 'name';
    // если третим параметром передали массив
    if (is_array($value)) {
      isset($value['width'])     ? $width = $value['width']           : $width = '200px';
      isset($value['is_empty'])  ? $is_empty = $value['is_empty']     : $is_empty = false;
      isset($value['empty_val']) ? $empty_val = $value['empty_val']   : $empty_val = "----Выберите значение----";
      isset($value['value'])     ? $fvalue = $value['value']          : $fvalue = null;
      isset($value['id'])        ? $fid   = $value['id']              : 0;
      isset($value['name'])      ? $fname = $value['name']            : 0;
      isset($value['multiple'])  ? $multiple = 'multiple'             : $multiple = '';
      isset($value['size'])      ? $size = "size='{$value['size']}'"  : $size = '';
      isset($value['class'])     ? $class = $value['class']           : $class = 'select_default';
      isset($value['only_opts']) ? $only_opts = $value['only_opts']   : $only_opts = false;
    }
    else {
      $fvalue = $value;
    }
    $text = ($only_opts ? "" : "<select name='{$name}' id='{$name}' style='width:{$width};' class='{$class} form-control' {$multiple} {$size}>");
    $is_empty
      ? $text .= "<option value=''>{$empty_val}</option>"
      : 0;
    if ( !empty($array)) {
      foreach ($array as $v => $k) {
        if (is_array($k)) {
          $fvalue==$k[$fid]
            ? $selected = 'selected' 
            : $selected = '';
          $text .= "<option value='{$k[$fid]}' {$selected}>{$k[$fname]}</option>";
        }
        else {
          $fvalue==$k->$fid
            ? $selected = 'selected' 
            : $selected = '';
          $text .= "<option value='{$k->$fid}' {$selected}>{$k->$fname}</option>";
        }
      }
    }
    $text .= ($only_opts ? "" : "</select>");
    return $text;
  }
  
  /**
   * checkbox
   */
  function in_check($name, $value, $val = '') {
    $value==1 
      ? $checked = 'checked' 
      : $checked = '';
    return "<input type='checkbox' name='{$name}' id='{$name}' value='{$val}' {$checked} />";
  }
  
  /**
   * серая обычная кнопка
   */
  function in_button($name, $value) {
    return "<input type='button' id='{$name}' name='{$name}' value='{$value}' class='button'>";
  }
  
  /**
   * input.submit
   */
  function in_submit($name, $value) {
    return "<input type='submit' id='{$name}' name='{$name}' value='{$value}' class='button'>";
  }
  
  /**
   * Красивая синяя кнопка
   */
  function in_blue_button($name, $value) {
    $text = 
      '<div class="blue_button" id="'.$name.'">
        <div class="value">'.$value.'</div>
      </div>';
    return $text;
  }
  
  /**
   * Кнопка удаления
   */
  function button_del($link, $class = '') {
    return "<a href='{$link}' style='border:0px;' class='{$class}'><img src='/images/base/delete.gif' style='border:0px;' /></a>";
  }
  
  /**
   * input.phone
   */
  function in_phone($name, $value = '', $params = array()) {
    $CI = &get_instance();
    $def_params = array(
      'width' => '200px',
      'class' => 'phone_number',
      'id'    => $name
    );
    $params = array_merge($def_params, $params);
    $params['name']  = $name;
    $params['value'] = $value;
    return $CI->load->view('helper/in_phone', $params, true);
  }
  
  /**
   * input.date
   */
  function in_date($name, $value = '', $params = array()) {
    $CI = &get_instance();
    $def_params = array(
      'width' => '200px',
      'class' => '',
      'id'    => $name
    );
    $params = array_merge($def_params, $params);
    $params['name']  = $name;
    $params['value'] = $value;
    return $CI->load->view('helper/in_date', $params, true);
  }
  
  /**
   * Загрузить FCKeditor
   */
  function GetEditor($name, $value='', $width="95%",  $height="400") {
    $CKEditor = new CKEditor();
    $CKEditor->returnOutput = true;
    $CKEditor->basePath = '/ckeditor/';
    $CKEditor->config['width']  = $width;
    $CKEditor->config['height'] = $height;
    $config['language'] = 'ru';
    return $CKEditor->editor($name, $value, $config);
  }
  
  /**
   * Форматировать дату
   */
  function format_date($date, $format = '') {
    $format==''
      ? $format = "d.m.Y"
      : 0;
    $ret = date($format, strtotime($date));
    return $ret;
  }
  
  /**
   * Всплывающий alert
   */
  function setAlert($text) {
    $text = 
     "<script>
      alert('{$text}');
      </script>";
    return $text;
  }
  
  /**
   * Сгенерировать парольную фразу
   */
  function genPass($len)  {
    $str = "";
    for($i=0 ; $i<$len; $i++)
    {
      $s[1] = rand(0, 9);
      $s[2] = chr(rand(ord('A'), ord('Z')));
      $s[3] = chr(rand(ord('a'), ord('z')));
      $num = rand(1,3);
      $str .= $s[$num];
    }
    return $str;
  }

  /**
   * Проверить формат e-mail
   */
  function mailFormat($email) {
    if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
      return 1;
    }
    else {
      return 0;
    }
  }
  
  /**
   * Обрезать лишнее - переносы, слэши и тп
   */
  function trimSL($str) {
    $array = array("\\", "/", " ", "\n", "\t");
    while(in_array($str[0], $array))
      $str = substr($str, 1);
    $cnt = strlen($str)-1;
    while(in_array($str[$cnt], $array))
    {
      $str = substr($str, 0, $cnt);
      $cnt = strlen($str)-1;
    }
    return $str;
  }
  
  /**
   * Рекурсивный trim
   * @author Alexey
   */
  function recursive_trim($value) {
    if (is_array($value)) {
      foreach ($value as $key => $item) {
        $value[$key] = recursive_trim($item);
      }
      return $value;
    }
    return trim($value);
  }
  
  function getExt($filename) {
    $array = explode(".", $filename);
    $count = count($array);
    return $array[$count-1];
  }

  function myUcfirst($str) {
    mb_internal_encoding("UTF-8");
    mb_language("ru");
    $str1 = ucfirst($str);
    if($str1==$str) {
      $first = mb_strtoupper(substr($str, 0, 1));
      $other = substr($str, 1);
      $str1 = $first.$other;
    }
    return $str1;
  }
  
  function conv($str) {
    $str = iconv("utf-8", "windows-1251", $str);
    $text=str_replace("%","\'",rawurlencode($str));
    return $text;
  }
  
  function utf2win($str) {
    $str = iconv("utf-8", "windows-1251", $str);
    return $str;
  }
  
  /**
   * win2utf
   */
  function win2utf($str) {
    if (is_array($str)) {
      foreach ($str as $key => $item) {
        $str[$key] = win2utf($item);
      }
      return $str;
    }
    $str = iconv("windows-1251", "utf-8", $str);
    return $str;
  }
  
  /**
   * cp866 to utf-8
   */
  function dos2utf($str) {
    if (is_array($str)) {
      foreach ($str as $key => $item) {
        $str[$key] = dos2utf($item);
      }
      return $str;
    }
    $str = iconv("cp866", "utf-8", $str);
    return $str;
  }
  
  function number($num) {
    $num = round($num, 2);
    $num = number_format($num, 2, '.', '');
    return $num;
  }
  
  function mb_chunk_split($string, $length, $delimiter) {
    $string = trim($string);
    mb_internal_encoding("UTF-8");
    mb_language("ru");
    $start = 0;
    $new_string = '';
    do {
      $part = mb_substr($string, $start, $length);
      $new_string .= $part.$delimiter;
      $start += $length;
    }
    while ($part<>'');
    return $new_string;
  }
  
  function config($key) {
    $CI = &get_instance();
    $val = $CI->db->query("select value from config t2 where t2.key='$key'")->row()->value;
    return $val;
  }
  
  function mail_admin($message, $subject = '') {
    $CI = &get_instance();
    $CI->load->library('email');
    $CI->email->clear();
    $CI->email->from(EMAIL, NAME);
    $CI->email->subject(($subject<>'' ? $subject : 'Сообщение администратору '.NAME));
    $CI->email->message($message);
    $CI->email->to(config('admin_email'));
    $CI->email->send();
  }
  
  /**
   * get_json()
   * печатает ответ для аякс запросов, в случае если возвращаемое функцией значение содержит текст ошибки (либо $eq) - возвращает ошибку, иначе - ошибки нет
   * @param mixed $data
   * @return
   */
  function get_json($answer, $eq = "") {
    if ($answer!=$eq)
      $r = array('err' => 1, 'err_str' => $answer);
    else
      $r = array('err' => 0);
    print json_encode($r);
  }
  
  /**
   * Перевести значение, которое обработано htmlspecialchars в нормальный вид
   */
  function unsafe($str) {
    $str = htmlspecialchars_decode($str);
    return $str;
  }
  
  /**
   * Возвращает русское название месяца 
   */
  function russianMonth($num, $case = 'nominative' ) {
    $num = (int)$num;
    $names['nominative'] = array(
      1  => 'Январь',
      2  => 'Февраль',
      3  => 'Март',
      4  => 'Апрель',
      5  => 'Май',
      6  => 'Июнь',
      7  => 'Июль',
      8  => 'Август',
      9  => 'Сентябрь',
      10 => 'Октябрь',
      11 => 'Ноябрь',
      12 => 'Декабрь'
    );
    $names['genitive'] = array(
      1  => 'Января',
      2  => 'Февраля',
      3  => 'Марта',
      4  => 'Апреля',
      5  => 'Мая',
      6  => 'Июня',
      7  => 'Июля',
      8  => 'Августа',
      9  => 'Сентября',
      10 => 'Октября',
      11 => 'Ноября',
      12 => 'Декабря'
    );
    return $names[$case][$num];
  }
  
  /**
    * @param  $icon   - значок в кнопке
          extlink            alert          clock       battery-3
          newwin             info           disk        circle-plus
          refresh            notice         calculator  circle-minus
          shuffle            help           zoomin      circle-close
          transfer-e-w       check          zoomout     circle-triangle-e
          transferthick-e-w  bullet         search      circle-triangle-s
          folder-collapsed   radio-off      wrench      circle-triangle-w
          folder-open        radio-on       gear        circle-triangle-n
          document           pin-w          heart       circle-arrow-e
          document-b         pin-s          star        circle-arrow-s
          note               play           link        circle-arrow-w
          mail-closed        pause          cancel      circle-arrow-n
          mail-open          seek-next      plus        circle-zoomin
          suitcase           seek-prev      plusthick   circle-zoomout
          comment            seek-end       minus       circle-check
          person             seek-start     minusthick  circlesmall-plus
          print              seek-first     close       circlesmall-minus
          trash              stop           closethick  circlesmall-close
          locked             eject          key         squaresmall-plus
          unlocked           volume-off     lightbulb   squaresmall-minus
          bookmark           volume-on      scissors    squaresmall-close
          tag                power          clipboard   grip-dotted-vertical
          home               signal-diag    copy        grip-dotted-horizontal
          flag               signal         contact     grip-solid-vertical
          calendar           battery-0      image       grip-solid-horizontal
          cart               battery-1      video       gripsmall-diagonal-se
          pencil             battery-2      script      grip-diagonal-se
    */
  function in_ui_button($name, $value, $params = array()) {
    $params['name']  = $name;
    $params['value'] = $value;
    $params['align']==''  ? $params['align']  = 'right'    : 0;
    $params['icon']==''   ? $params['icon']   = 'document' : 0;
    $params['value']==''  ? $params['value']  = 'OK'       : 0;
    $params['action']=='' ? $params['action'] = 'void(0);' : 0;
    $CI = &get_instance();
    $res = $CI->load->view('helper/in_ui_button', $params, true);
    return $res;
  }
  
  /**
   * кнока bootstrap c glyphicons
   */
  function in_bs_button($name, $value, $params = array()) {
    $params['name']  = $name;
    $params['value'] = $value;
    $params['type']==''   ? $params['type']   = 'button'   : 0;
    $params['align']==''  ? $params['align']  = 'right'    : 0;
    $params['icon']==''   ? $params['icon']   = 'tasks'    : 0;
    $params['value']==''  ? $params['value']  = 'OK'       : 0;
    $params['action']=='' ? $params['action'] = 'void(0);' : 0;
    $CI = &get_instance();
    $res = $CI->load->view('helper/in_bs_button', $params, true);
    return $res;
  }

  /**
   * получение координат для адреса
   */
  function getCoord($addr) {
    $CI = &get_instance();
    $keys = $CI->config->item('yandexMapKey');
    $addr = urlencode($addr);
    $url = "http://geocode-maps.yandex.ru/1.x/?geocode=".$addr."&key=".$keys[SERVER];
    $xml = file_get_contents($url);
    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $xml, $struct, $index);
    xml_parser_free($parser);
    $pos = $struct[$index['POS'][0]]['value'];
    $parts = explode(" ", $pos);
    $coord = new stdClass();
    $coord->lon = trim($parts[0]);
    $coord->lat = trim($parts[1]);
    return $coord;
  }
  
  /**
   * Кодирование массива для ссылки
   */
  function encodeLink($var) {
    $str = serialize($var);
    return encode($str);
  }
  
  /**
   * Кодирование проивольной строки в base64 без лишних симовлов
   * @author Alexey
   */
  function encode($str) {
    $str = base64_encode($str);
    $str = str_replace('+', '-', $str);
    $str = str_replace('/', '_', $str);
    $str = str_replace('=', '', $str);
    return $str;
  }
  
  /**
   * Раскодирование строки
   * @author Alexey
   */
  function decode($str) {
    $str = str_replace('-', '+', $str);
    $str = str_replace('_', '/', $str);
    $str = base64_decode($str);
    return $str;
  }
  
  /**
   * Раскодирование ссылки
   */
  function decodeLink($str) {
    $str = decode(decode);
    $var = unserialize($str);
    return $var;
  }
  
  /**
   * Добавить элемент crumbs в начало
   */
  function addCrumbsRev($title, $link) {
    return addCrumbs($title, $link, 'rev');
  }
  
  /**
   * Добавить элемент crumbs
   */
  function addCrumbs($title, $link, $dir = '') {
    $CI = &get_instance();
    $str = "<li><a href='/{$link}'>{$title}</a></li>";
    $CI->crumbs = ($dir == 'rev' ? $str . $CI->crumbs : $CI->crumbs . $str);
    return;
  }
  
  /**
   * Получить crumbs
   */
  function getCrumbs($params = []) {
    $CI = &get_instance();
    $params['crumbs'] = $CI->crumbs;
    $params['CI']     = $CI;
    return $CI->load->view('page/crumbs', $params, true);
  }
  
  /**
   * Составить ссылку из выбранных параметров
   */
  function makeLink($obj) {
    $points = array('catalog', 'param', 'sort');
    $link = "/";
    foreach ($points as $item) {
      if (!isset($obj->{$item})) {
        continue;
      }
      $link .= "{$item}/";
      foreach ($obj->{$item} as $key => $value) {
        if ( ! is_array($value) ) {
          $link .= "{$key}/{$value}/";
          continue;
        }
        if (is_array($value) && empty($value)) {
          continue;
        }
        if (isset($value['from']) && isset($value['to'])) {
          $link .= "{$key}/" . implode("-", (array)$value) . "/";
        }
        else {
          $link .= "{$key}/" . implode("_", (array)$value) . "/";
        }
      }
    }
    return $link;
  }
  
  /**
   * Получение контент блока
   */
  function content_block($name, $item) {
    if ( cache::$item['content_block'][$name]->{$item} ) {
      return cache::$item['content_block'][$name]->{$item};
    }
    $CI = &get_instance();
    $block = $CI->razdel_model->getBlockByName($name);
    cache::$item['content_block'][$name] = $block;
    return $block->{$item};
  }
  
  
  /**
   * Получить чистый url без имени домена и прочей шелухи 
   */
  function getUrlPath($url) {
    $parse = parse_url($url);
    $path = trimSL($parse['path']);
    return $path;
  }
  
  /**
   * Перевод строки в транслит
   * @param  string $string
   * @return string
   */
  function translite($string) {
    $table = array(
      'А' => 'A',  'Б' => 'B',  'В' => 'V',  'Г' => 'G',  'Д' => 'D',
      'Е' => 'E',  'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z',  'И' => 'I',
      'Й' => 'J',  'К' => 'K',  'Л' => 'L',  'М' => 'M',  'Н' => 'N',
      'О' => 'O',  'П' => 'P',  'Р' => 'R',  'С' => 'S',  'Т' => 'T',
      'У' => 'U',  'Ф' => 'F',  'Х' => 'H',  'Ц' => 'C',  'Ч' => 'CH',
      'Ш' => 'SH', 'Щ' => 'CSH','Ь' => '',   'Ы' => 'Y',  'Ъ' => '',
      'Э' => 'E',  'Ю' => 'YU', 'Я' => 'YA',

      'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',  'д' => 'd',
      'е' => 'e',  'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',  'и' => 'i',
      'й' => 'j',  'к' => 'k',  'л' => 'l',  'м' => 'm',  'н' => 'n',
      'о' => 'o',  'п' => 'p',  'р' => 'r',  'с' => 's',  'т' => 't',
      'у' => 'u',  'ф' => 'f',  'х' => 'h',  'ц' => 'c',  'ч' => 'ch',
      'ш' => 'sh', 'щ' => 'csh','ь' => '',   'ы' => 'y',  'ъ' => '',
      'э' => 'e',  'ю' => 'yu', 'я' => 'ya',
    );
    
    $string = str_replace(array_keys($table), array_values($table), $string);
    $string = preg_replace("/[^A-Za-z0-9]/", "-", $string);
    return $string;
  }

  /**
   * Test request type - ajax
   */
  function is_ajax() {
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
      return true;
    }
    else {
      return false;
    }
  }
  
  class cache {
    static $item;
  }
?>