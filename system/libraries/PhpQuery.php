<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpQuery/phpQuery.php');

class CI_PhpQuery{
	var $CI;
	
	public function __construct(){
	}
	
	public function parseString($str){

		$doc = phpQuery::newDocument($str);

		return $doc;
	}
};