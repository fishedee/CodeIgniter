<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_OAuth{
	var $CI;
	var $VERSION = "2.0";
	var $GET_AUTH_CODE_URL_PC = "https://open.weixin.qq.com/connect/qrconnect";
    var $GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    var $GET_ACCESSTOKEN_AND_OPENID_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	
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
			"appid" => $appid,
			"redirect_uri" => $callback,
            "response_type" => "code",
            "scope" => $scope,
            "state" => $state,
        );
		
		return $this->GET_AUTH_CODE_URL.'?'.http_build_query($keysArr);
    }

    public function getPcRedirectUrl($appid,$callback,$state,$scope){
    	$keysArr = array(
			"appid" => $appid,
			"redirect_uri" => $callback,
            "response_type" => "code",
            "scope" => $scope,
            "state" => $state,
        );
		
		return $this->GET_AUTH_CODE_URL_PC.'?'.http_build_query($keysArr);
    }
	
	public function getAccessTokenAndOpenId($appId,$appKey){
        $keysArr = array(
            "grant_type" => "authorization_code",
            "appid" => $appId,
            "secret" => $appKey,
            "code" => $_GET['code']
        );

        return WXSdk_Util::applyJsonApi(
        	$this->GET_ACCESSTOKEN_AND_OPENID_URL,
        	'get',
        	$keysArr
        );
    }

    public function getUserInfo($accessToken,$openId){
        return WXSdk_Util::applyJsonApi(
        	'https://api.weixin.qq.com/sns/userinfo',
        	'get',
        	array(
        		'access_token'=>$accessToken,
        		'openid'=>$openId,
        		'lang'=>'zh_CN'
        	)
        );
    }

}
?>