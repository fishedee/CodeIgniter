<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_MyException extends Exception{ 
	var $data;
	
    public function __construct($code = 1,$message = '',$data = '') {  
        parent::__construct($message,$code);
		$this->data = $data;
		log_message('WARN','[file:'.parent::getFile().'][line:'.parent::getLine().'][code:'.parent::getCode().'][msg:'.parent::getMessage().']');
    }
	
	public function getData() {  
        return $this->data;
    }
}  
?>