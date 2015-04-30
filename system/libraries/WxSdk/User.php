<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_User{
	var $accessToken;

	public function __construct($accessToken){
		$this->accessToken = $accessToken;
	}

	public function getUserInfo($openId){
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/user/info',
			'get',
			array(
				'access_token'=>$this->accessToken,
				'openid'=>$openId,
				'lang'=>'zh_CN'
			)
		);
	}

	public function getUserList($openId = null){
		$argv = array(
			'access_token'=>$this->accessToken
		);
		if( $openId != null )
			$argv['next_openid'] = $openId;
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/user/get',
			'get',
			$argv
		);
	}
}