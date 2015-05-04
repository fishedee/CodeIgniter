<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/WxSdk/Base.php');

require_once(dirname(__FILE__).'/WxSdk/Message.php');
require_once(dirname(__FILE__).'/WxSdk/User.php');
require_once(dirname(__FILE__).'/WxSdk/Menu.php');

require_once(dirname(__FILE__).'/WxSdk/OAuth.php');
require_once(dirname(__FILE__).'/WxSdk/Js.php');

require_once(dirname(__FILE__).'/WxSdk/Pay.php');

class CI_WxSdk{
	var $option;

	public function __construct($option)
    {
		$this->CI = & get_instance();
		$this->option = $option;
	}

	//基础接口
	public function checkSignature(){
		$qc = new WXSdk_Base();
		return $qc->checkSignature($this->option['appToken']);
	}

	public function getAccessToken(){
		$qc = new WXSdk_Base();
		return $qc->getAccessToken(
			$this->option['appId'],
			$this->option['appKey']
		);
	}

	public function getServerIp($accessToken){
		$qc = new WXSdk_Base();
		return $qc->getServerIp(
			$accessToken
		);
	}

	//用户接口
	public function getUserDetailInfo($accessToken,$openId){
		$qc = new WXSdk_User($accessToken);
		return $qc->getUserInfo(
			$openId
		);
	}

	public function getUserList($openId=null){
		$qc = new WXSdk_User($accessToken);
		return $qc->getUserList(
			$openId
		);
	}

	//Message接口
	public function getMessage(){
		$qc = new WXSdk_Message();
		return $qc->getMessage();
	}

	public function sendMessage($message){
		$qc = new WXSdk_Message();
		return $qc->sendMessage($message);
	}

	//Menu接口
	public function setMenu($accessToken,$data){
		$qc = new WXSdk_Menu($accessToken);
		return $qc->setMenu($data);
	}

	public function getMenu($accessToken){
		$qc = new WXSdk_Menu($accessToken);
		return $qc->getMenu();
	}

	public function delMenu($accessToken){
		$qc = new WXSdk_Menu($accessToken);
		return $qc->delMenu();
	}
	
	//OAuth接口
	public function getLoginUrl($callback,$callbackInfo,$scope){
		$qc = new WXSdk_OAuth();
		return $qc->getRedirectUrl(
			$this->option['appId'],
			$callback,
			$callbackInfo,
			$scope
		);
			
	}

	public function getPcLoginUrl($callback,$callbackInfo,$scope)
	{
		$qc = new WXSdk_OAuth();
		return $qc->getPcRedirectUrl(
			$this->option['appId'],
			$callback,
			$callbackInfo,
			$scope
		);
	}
	
	public function getLoginCallBackInfo()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getState();
	}

	
	
	public function getAccessTokenAndOpenId()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getAccessTokenAndOpenId(
			$this->option['appId'],
			$this->option['appKey']
		);
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new WXSdk_OAuth();
		return $qc->getUserInfo(
			$accessToken,
			$openId
		);
	}
	
	//Js接口
	public function getJsApiTicket($accessToken)
	{
		$qc = new WXSdk_Js($this->option['appId']);
		return $qc->getJsApiTicket(
			$accessToken
		);
	}

	public function getJsConfig($jsApiTicket,$url)
	{
		$qc = new WXSdk_Js($this->option['appId']);
		return $qc->getJsConfig(
			$jsApiTicket,
			$url
		);
	}

	//支付接口
	public function getOrderPayInfo($openId,$dealId,$dealDesc,$dealFee,$dealNotify){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->unifiedOrder(array(
			'openid'=>$openId,
			'out_trade_no'=>$dealId,
			'body'=>$dealDesc,
			'total_fee'=>$dealFee,
			'notify_url'=>$dealNotify,
			'trade_type'=>'JSAPI'
		));
	}

	public function getJsPayInfo($prepay_id){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->jsPayOrder(
			$prepay_id
		);
	}

	public function getPayCallBackInfo(){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->notifyOrder();
	}

	public function sendRedPack($openId,$money,$orderId,$shopName,$wishing,$actName,$remark){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->sendRedPack(array(
			'nick_name'=>$shopName,
			'send_name'=>$shopName,
			'mch_billno'=>$orderId,
			're_openid'=>$openId,
			'total_amount'=>$money,
			'total_num'=>1,
			'min_value'=>$money,
			'max_value'=>$money,
			'wishing'=>$wishing,
			'act_name'=>$actName,
			'remark'=>$remark
		));
	}

}