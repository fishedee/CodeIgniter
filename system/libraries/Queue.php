<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Queue{
	var $CI;
	var $option;
	var $redis;
	var $prefix;

	public function __construct(){
		$this->CI = & get_instance();
		$queueDriver = $this->CI->config->item('queue_driver');
		$queueSavePath = $this->CI->config->item('queue_savepath');
		$queueSavePrefix = $this->CI->config->item('queue_saveprefix');

		if( $queueDriver != "redis" ){
			return;
		}
		if( preg_match('#(?:tcp://)?([^:?]+)(?:\:(\d+))?(\?.+)?#', $queueSavePath, $matches))
		{
			isset($matches[3]) OR $matches[3] = ''; // Just to avoid undefined index notices below
			$queueSavePathArray = array(
				'host' => $matches[1],
				'port' => empty($matches[2]) ? NULL : $matches[2],
				'password' => preg_match('#auth=([^\s&]+)#', $matches[3], $match) ? $match[1] : NULL,
				'database' => preg_match('#database=(\d+)#', $matches[3], $match) ? (int) $match[1] : NULL,
				'timeout' => preg_match('#timeout=(\d+\.\d+)#', $matches[3], $match) ? (float) $match[1] : NULL
			);
		}else{
			throw new CI_MyException(1,'queue的save_path配置错误'.$queueSavePath);
		}
		$this->prefix = $queueSavePrefix;

		$redis = new Redis();
		if( !$redis->connect($queueSavePathArray['host'], $queueSavePathArray['port'], $queueSavePathArray['timeout']))
		{
			log_message('error', 'Queue: Unable to connect to Redis with the configured settings.');
		}

		if( isset($queueSavePathArray['password']) && !$redis->auth($queueSavePathArray['password']) ){
			log_message('error', 'Queue: Unable to authenticate to Redis instance.');
		}

		if( isset($queueSavePathArray['database']) && !$redis->select($queueSavePathArray['database']) ){
			log_message('error', 'Queue: Unable to select Redis database with index '.$queueSavePathArray['database']);
		}
		$this->redis = $redis;
	}

	private function getHttp(){
		$method = $_SERVER['REQUEST_METHOD'];
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if(substr($key, 0, 5) != 'HTTP_') {
				continue;
			}
			$key = substr($key, 5); 
			$key = strtolower($key); 
			$key = str_replace('_', ' ', $key); 
			$key = ucwords($key); 
			$key = str_replace(' ', '-', $key); 
			
			if( !isset($headers[$key]) ){
				$headers[$key] = array();
			}
			$headers[$key][] = $value; 
		}
		return array(
			"method"=>$method,
			"url"=>$url,
			"header"=>$headers,
		);
	}

	public function produce(){
		$args = func_get_args();
		if( count($args) < 1 ){
			throw new CI_MyException(1,"至少需要一个参数,queueKey");
		}
		$key = $this->prefix.$args[0];
		$data = array_slice($args,1);
		$data = array_merge(array($this->getHttp()),$data);
		$data = json_encode($data);
		$this->redis->lpush($key,$data);
	}

	public function publish(){
		return call_user_func_array(array($this,'produce'),func_get_args());
	}
}
?>
