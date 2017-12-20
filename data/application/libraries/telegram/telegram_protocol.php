<?php
/*
 * This file implements an extension of:
 *  https://github.com/gorebrau/PHP-telegram-bot-API
 *
 * Following additional features are implemented:
 *  - self-signed certificate are now supported
 *  - automatic actions are now sent before sending data (the result is, e.g., the "is sending a picture..." message on top of the Telegram client)
 *  - a get_file($file_id, $output_file) method is implemented, downloading file content from $file_id and storing it to $output_file
 *  - inline keyboards support (thanks to Daniele's support)
 *
 */

class ReplyKeyboardMarkup{
	public $keyboard;
	public $resize_keyboard;
	public $one_time_keyboard;
	public $selective;

	function __construct($resize_keyboard=FALSE, $one_time_keyboard = FALSE, $selective=FALSE){
		$this->keyboard=array();
		$this->keyboard[0]=array();
		$this->resize_keyboard=$resize_keyboard;
		$this->one_time_keyboard=$one_time_keyboard;
		$this->selective=$selective;
	}

	public function add_option($option){
		$this->keyboard = $option;
	}
}

class ReplyKeyboardRemove{
	public $remove_keyboard;
	public $selective;

	function __construct($remove_keyboard=TRUE, $selective = FALSE){
		$this->remove_keyboard=$remove_keyboard;
		$this->selective=$selective;
	}
}

class ForceReply{
	public $force_reply;
	public $selective;

	function __construct($force_reply=TRUE, $selective = FALSE){
		$this->force_reply=$force_reply;
		$this->selective=$selective;
	}
}

class InlineKeyboardMarkup{
	public $inline_keyboard;

	function __construct(){
		$this->inline_keyboard=array();
		$this->inline_keyboard[0]=array();
	}

	public function add_option($option){
		$this->inline_keyboard = $option;
	}
}

class telegram_bot {
	private $token;

	private function open_url($url, $method="GET", $data=null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if($method==="POST") {
			if(isset($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		//file_put_contents(LOG, print_r($data, 1), FILE_APPEND);
		return $result;
	}

	private function file_request($file_path) {
		$token = $this->token;
		return $this->open_url("https://api.telegram.org/file/bot$token/$file_path");
	}

	private function control_api($action, $data=NULL) {
		$token = $this->token;
		$response = json_decode($this->open_url("https://api.telegram.org/bot$token$action", "POST", $data));
		return $response;
	}

	function __construct($token) {
		$this->token=$token;
	}

	public function status() {
		$response = $this->control_api("/getme");
		return($response);
	}

	public function get_updates() {
		$response = $this->control_api("/getUpdates");
		return($response);
	}

	public function send_action($to, $action) {
		$data = array();
		$data["chat_id"]=$to;
		$data["action"]=$action;
		$response = $this->control_api("/sendChatAction", $data);
		return $response;
	}

	public function send_message($to, $msg, $id_msg=null, $reply=null, $type=null, $disable_preview=true) {
		$data = array();
		$data["chat_id"]=$to;
		$data["text"]=$msg;
		$data["disable_web_page_preview"]=(string)$disable_preview;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		if(isset($type)) $data["parse_mode"]=$type; // "Markdown" or "HTML"; see https://core.telegram.org/bots/api#formatting-options
		file_put_contents('/home/advert/tmp/post.log', "BOT::SEND_MESSAGE: " . print_r($data, 1), FILE_APPEND);
		$response = $this->control_api("/sendMessage", $data);
		file_put_contents('/home/advert/tmp/post.log', "BOT::RESPONSE: " . print_r($response, 1), FILE_APPEND);
		return $response;
	}

	public function send_location($to, $lat, $lon, $id_msg=null, $reply=null) {
		$data = array();
		$data["chat_id"]=$to;
		$data["latitude"]=$lat;
		$data["longitude"]=$lon;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendLocation", $data);
		return $response;
	}

	public function send_sticker($to, $sticker, $id_msg=null, $reply=null) {
		$data = array();
		$data["chat_id"]=$to;
		if(substr($sticker,0,1)=="@") $sticker=substr($sticker,1); // support for "@$filename"
		if(file_exists($sticker)) {
			if(class_exists('CurlFile', false)) $sticker=new CURLFile(realpath($sticker));
			else $sticker="@".$sticker;
		}
		$data["sticker"]=$sticker;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendSticker", $data);
		return $response;
	}

	public function send_video($to, $video, $caption=null, $id_msg=null, $reply=null) {
		$this->send_action($to, "upload_video");
		$data = array();
		$data["chat_id"]=$to;
		if(substr($video,0,1)=="@") $video=substr($video,1); // support for "@$filename"
		if(file_exists($video)) {
			if(class_exists('CurlFile', false)) $video=new CURLFile(realpath($video));
			else $video="@".$video;
		}
		$data["video"]=$video;
		if(isset($caption)) $data["caption"]=$caption;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendVideo", $data);
		return $response;
	}

	public function send_photo($to, $photo, $caption=null, $id_msg=null, $reply=null) {
		$this->send_action($to, "upload_photo");
		$data = array();
		$data["chat_id"]=$to;
		if(substr($photo,0,1)=="@") $photo=substr($photo,1); // support for "@$filename"
		if(file_exists($photo)) {
			if(class_exists('CurlFile', false)) $photo=new CURLFile(realpath($photo));
			else $photo="@".$photo;
		}
		$data["photo"]=$photo;
		if(isset($caption)) $data["caption"]=$caption;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendPhoto", $data);
		return $response;
	}

	public function send_audio($to, $audio, $id_msg=null, $reply=null) {
		$this->send_action($to, "upload_audio");
		$data = array();
		$data["chat_id"]=$to;
		if(substr($audio,0,1)=="@") $audio=substr($audio,1); // support for "@$filename"
		if(file_exists($audio)) {
			if(class_exists('CurlFile', false)) $audio=new CURLFile(realpath($audio));
			else $audio="@".$audio;
		}
		$data["audio"]=$audio;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendAudio", $data);
		return $response;
	}

	public function send_voice($to, $voice, $id_msg=null, $reply=null) {
		$this->send_action($to, "upload_audio");
		$data = array();
		$data["chat_id"]=$to;
		if(substr($voice,0,1)=="@") $audio=substr($voice,1); // support for "@$filename"
		if(file_exists($voice)) {
			if(class_exists('CurlFile', false)) $voice=new CURLFile(realpath($voice));
			else $voice="@".$voice;
		}
		$data["voice"]=$voice;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendVoice", $data);
		return $response;
	}

	public function send_document($to, $document, $caption=null, $id_msg=null, $reply=null) {
		$this->send_action($to, "upload_document");
		$data = array();
		$data["chat_id"]=$to;
		if(substr($document,0,1)=="@") $document=substr($document,1); // support for "@$filename"
		if(file_exists($document)) {
			if(class_exists('CurlFile', false)) $document=new CURLFile(realpath($document));
			else $document="@".$document;
		}
		$data["document"]=$document;
		if(isset($caption)) $data["caption"]=$caption;
		if(isset($id_msg)) $data["reply_to_message_id"]=$id_msg;
		if(isset($reply)) $data["reply_markup"]=$reply;
		$response = $this->control_api("/sendDocument", $data);
		return $response;
	}

	public function send_inline($inline_query_id, $results, $cache_time=null, $is_personal=null) {
		$data = array();
		$data["inline_query_id"]=$inline_query_id;
		$data["results"]=$results;
		if(isset($cache_time)) $data["cache_time"]=$cache_time;
		if(isset($is_personal)) $data["is_personal"]=$is_personal;
		$response = $this->control_api("/answerInlineQuery", $data);
		return $response;
	}

	public function edit_message($chatid=null, $message_id=null, $text, $inline_message_id=null, $parse_mode=null, $disable_web_page_preview=null, $reply_markup=null) {
		$data = array();
		$data["text"] = $text;
		if(isset($chatid)) $data["chat_id"] = $chatid;
		if(isset($message_id)) $data["message_id"] = $message_id;
		if(isset($inline_message_id)) $data["inline_message_id"] = $inline_message_id;
		if(isset($parse_mode)) $data["parse_mode"] = $parse_mode;
		if(isset($disable_web_page_preview)) $data["disable_web_page_preview"] = $disable_web_page_preview;
		if(isset($reply_markup)) $data["reply_markup"] = $reply_markup;
		$response = $this->control_api("/editMessageText", $data);
		return $response;
	}
	
	public function edit_caption($chatid=null, $message_id=null, $inline_message_id=null, $caption=null, $reply_markup=null) {
		$data = array();
		if(isset($chatid)) $data["chat_id"] = $chatid;
		if(isset($message_id)) $data["message_id"] = $message_id;
		if(isset($inline_message_id)) $data["inline_message_id"] = $inline_message_id;
		if(isset($caption)) $data["caption"] = $caption;
		if(isset($reply_markup)) $data["reply_markup"] = $reply_markup;
		$response = $this->control_api("/editMessageCaption", $data);
		return $response;
	}
	
	public function edit_replymarkup($chatid=null, $message_id=null, $inline_message_id=null, $reply_markup=null) {
		$data = array();
		if(isset($chatid)) $data["chat_id"] = $chatid;
		if(isset($message_id)) $data["message_id"] = $message_id;
		if(isset($inline_message_id)) $data["inline_message_id"] = $inline_message_id;
		if(isset($reply_markup)) $data["reply_markup"] = $reply_markup;
		$response = $this->control_api("/editMessageReplyMarkup", $data);
		return $response;
	}

	public function forward_message($to, $from, $msg_id) {
		$data = array();
		$data["chat_id"]=$to;
		$data["from_chat_id"]=$from;
		$data["message_id"]=$msg_id;
		$response = $this->control_api("/forwardMessage", $data);
		return $response;
	}

	public function set_webhook($url=null, $certificatefile=null) {
		$data = array();
		$data["url"]=$url;
		if($certificatefile!=null) {
			//$f = fopen($certificatefile, "r");
			//$certificate = fread($f,filesize($certificatefile));
			//fclose($f);
			//$data["certificate"] = $certificate;
			$data["certificate"] = $certificatefile;
		}
		$response = $this->control_api("/setWebhook", $data);
		return $response;
	}

	public function get_user_profile_photos($id_user, $offset=null, $limit=null) {
		$data = array();
		$data["user_id"]=$id_user;
		if(isset($offset)) $data["offset"]=$offset;
		if(isset($limit)) $data["limit"]=$limit;
		$response = $this->control_api("/getUserProfilePhotos", $data);
		return $response;
	}

	public function get_file($file_id, $output_file) {
		try {
			// getting file path
			$data = array();
			$data["file_id"] = $file_id;
			$response = $this->control_api("/getFile", $data);
			if ($response->ok != 1) return null; // no downloadable file like contact or location
			$file_path = $response->result->file_path;
			$ext = strrchr($file_path, '.');
			// getting file content
			$file_content = $this->file_request($file_path);
			// storing to file
			$output_file = "$output_file$ext";
			$fp = fopen($output_file, 'w');
			fwrite($fp, $file_content);
			fclose($fp);
			return $output_file;
		}
		catch(Exception $e) { }
		return null;
	}

	public function read_post_message(){
		return json_decode(file_get_contents('php://input'));
	}

}
?>
