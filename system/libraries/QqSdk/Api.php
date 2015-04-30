<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class QQSdk_Api{
	var $CI;
	var $keysArr;
	
	public function __construct($appId,$accessToken,$openId){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
		$this->keysArr = array(
			"oauth_consumer_key" => $appId,
			"access_token" => $accessToken,
			"openid" => $openId
		);
	}
	
	private function applyApi($url,$type,$keysArgv){
		 //-------请求参数列表
		$keysArgv = array_merge($this->keysArr,$keysArgv);
		
		//------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$url,
			'type'=>$type,
			'data'=>$keysArgv,
			'responseType'=>'json'
		));
		
		if( $httpResponse['body']['ret'] != 0 )
			throw new CI_MyException(1,$httpResponse['body']['msg']);
		
		return $httpResponse['body'];
	}
	
	public function getSimpleUserInfo(){
        return $this->applyApi('https://openmobile.qq.com/user/get_simple_userinfo','get',array());
    }
	
	public function getUserInfo(){
        return $this->applyApi('https://graph.qq.com/user/get_user_info','get',array());
    }
	
}
?>