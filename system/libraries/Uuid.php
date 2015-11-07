<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(dirname(__FILE__).'/Uuid/Uuid.php');
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class CI_Uuid{
	var $CI;
	
	public function __construct(){
	}
	
	public function vMe(){
		$curTime = floor(microtime(true)*10000);
		$uniqueId = sha1($this->v4());
		return dechex($curTime)."0".$uniqueId;
	}

	public function v4(){
		return UUid::uuid4()->toString();
	}

	public function v1(){
		return UUid::uuid1()->toString();
	}
};