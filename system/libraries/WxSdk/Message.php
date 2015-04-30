<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_Message{

	public function __construct(){
	}

	public function getMessage(){
		$post_data = file_get_contents('php://input');
		$message = WXSdk_Util::xmlToArray($post_data);
		return $message;
	}

	public function sendMessage($message){
		echo WXSdk_Util::arrayToXml($message);
	}
}