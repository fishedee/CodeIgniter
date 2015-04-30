<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Enum{
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function setEnum( &$object , $enum )
	{
		$values = array();
		$names = array();
		foreach( $enum as $singleEnum ){
			$object->$singleEnum[1] = $singleEnum[0];
			$values[] = $singleEnum[0];
			$names[$singleEnum[0]] = $singleEnum[2];
		}
		$object->values = $values;
		$object->names = $names;
	}
	
}