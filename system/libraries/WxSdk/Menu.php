<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_Menu{
	var $accessToken;

	public function __construct($accessToken){
		$this->accessToken = $accessToken;
	}

	public function setMenu($data){
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->accessToken,
			'post',
			$data,
			'json_origin'
		);
	}

	public function getMenu(){
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->accessToken,
			'get',
			array()
		);
	}

	public function delMenu(){
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->accessToken,
			'get',
			array()
		);
	}
}