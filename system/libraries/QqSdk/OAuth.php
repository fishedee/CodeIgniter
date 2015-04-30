<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class QQSdk_OAuth{
	var $CI;
	var $VERSION = "2.0";
    var $GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    var $GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    var $GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
	
	public function __construct(){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
	}
	
	public function getState(){
		return $_GET['state'];
	}
	
	public function getRedirectUrl($appid,$callback,$state,$scope){
        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );
		
		return $this->GET_AUTH_CODE_URL.'?'.http_build_query($keysArr);
    }
	
	public function getAccessToken($appId,$appKey,$callback){
        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $appId,
            "redirect_uri" => $callback,
            "client_secret" => $appKey,
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_ACCESS_TOKEN_URL,
			'data'=>$keysArr,
			'responseType'=>'text'
		));
		if( isset($httpResponse['body']['error']))
			throw new CI_MyException(1,$httpResponse['body']['error_description']);
		
		return $httpResponse['body'];
    }
	
	public function getOpenId($accessToken){
        //-------请求参数列表
        $keysArr = array(
            "access_token" => $accessToken
        );
		
		//------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_OPENID_URL,
			'data'=>$keysArr,
			'responseType'=>'jsonp'
		));
		
		if( isset($httpResponse['body']['error']))
			throw new CI_MyException(1,$httpResponse['body']['error_description'].' accessToken:'.$accessToken);
		
		return $httpResponse['body'];
    }
	
}
?>