<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Test extends PHPUnit_Framework_TestCase{

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct($name = NULL,$data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$class = new ReflectionClass($this);
		log_message('debug', "Test Class Initialized ".$class->getName());
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		if(property_exists($CI,$key) === true )
			return $CI->$key;

		return parent::$key;
	}
}