<?php
class Telegram
{
	
	function __construct()
	{
		include_once("telegram_state.php");
		include_once("telegram_protocol.php");
		include_once("telegram_triggers.php");
	}
}

?>
