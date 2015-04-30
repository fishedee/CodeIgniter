<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Uedit {
	
	var $CI;
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function control()
	{	
		include(dirname(__FILE__).'/uedit/controller.php');
	}
};