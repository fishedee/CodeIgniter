<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/QqSdk/OAuth.php');
require_once(dirname(__FILE__).'/QqSdk/Api.php');
class CI_QqSdk{
	var $appId;
	var $appKey;

	public function __construct($option)
    {
		$this->CI = & get_instance();
		$this->appId = $option['appId'];
		$this->appKey = $option['appKey'];
	}
	
	public function getLoginUrl($callback,$callbackInfo,$scope)
	{
		$qc = new QQSdk_OAuth();
		return $qc->getRedirectUrl(
			$this->appId,
			$callback,
			$callbackInfo,
			$scope
		);
	}
	
	public function getLoginCallBackInfo()
	{
		$qc = new QQSdk_OAuth();
		return $qc->getState();
	}
	
	public function getAccessToken($callback)
	{
		$qc = new QQSdk_OAuth();
		return $qc->getAccessToken(
			$this->appId,
			$this->appKey,
			$callback
		);
	}
	
	public function getOpenId($accessToken)
	{
		$qc = new QQSdk_OAuth();
		return $qc->getOpenId(
			$accessToken
		);
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new QQSdk_Api(
			$this->appId,
			$accessToken,
			$openId
		);
		return $qc->getUserInfo();
	}
	
	
	
	
}