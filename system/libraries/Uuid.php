<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Uuid{
	var $CI;
	
	public function __construct(){
	}
	
	public function generate(){
		$curTime = floor(microtime(true)*10000);
		$uniqueId = sha1(uniqid("",true));
		return dechex($curTime)."0".$uniqueId;
	}
};