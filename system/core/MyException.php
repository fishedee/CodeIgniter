<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_MyException extends Exception{
    public function __construct($code = 1,$message = '',$tip = '') {  
        parent::__construct($message,$code);
		log_message('WARN','[file:'.parent::getFile().'][line:'.parent::getLine().'][code:'.parent::getCode().'][msg:'.parent::getMessage().'][tip:'.$tip.']');
    }
	
	public function getData() {  
        return '';
    }
}  
?>