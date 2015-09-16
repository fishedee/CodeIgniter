<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Captcha/CaptchaBuilder.php');

class CI_Captcha{
	var $builder;

	public function __construct(){
		$this->builder = new CaptchaBuilder();
	}

	public function create($word,$width,$height){
		$this->builder->setPhrase($word);
		$this->builder->build($width, $height);
		$picture = $this->builder->inline();
		return $picture;
	}
}
