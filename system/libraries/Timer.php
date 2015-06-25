<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Timer{
	var $CI;
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function tick()
	{
		//不限脚本运行时间
		set_time_limit(0); 
		//计算现在有多少分钟
		$now = time();
		$nowMinutes = $now/60;
		$nowSeconds = $now%60;
		//遍历所有任务
		$timerTasks = $this->CI->config->item('timer');
		foreach( $timerTasks as $singleTimerTask ){
			$singleTimerTaskTime = $singleTimerTask['period'];
			$singleTimerTaskTask = $singleTimerTask['task'];
			$singleTimerTaskTime = ceil($singleTimerTaskTime /60);
			if( $nowMinutes % $singleTimerTaskTime == 0 ){
				try{
					$this->tickSingleTask($singleTimerTaskTask);
				}catch(Exception $e){
					log_message('error','run '.$singleTimerTask['task'].' fail '.$e->getMessage());
				}
			}
		}
		//调试
		log_message('DEBUG','timer tick finish');
	}
	
	public function tickSingleTask($task)
	{
		$taskPath = APPPATH.'timer/'.$task.'.php';
		if( !file_exists($taskPath))
			throw new CI_MyException(1,'unknown task path '.$taskPath);

		require_once($taskPath);

		log_message('INFO',$task.' finish!');
	}
}
?>