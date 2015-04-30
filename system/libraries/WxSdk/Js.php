<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_Js{
	var $appId;
	
	public function __construct($appId){
		$this->appId = $appId;
	}

    public function getJsConfig($jsApiTicket,$url){
    	if( strpos($url,'#') !== false )
    		$url = substr($url,0,strpos($url,'#'));
    	$result = array();
    	$result['appId'] = $this->appId;
    	$result['timestamp'] = time().'';
    	$result['nonceStr'] =WXSdk_Util::createNoncestr();
    	$result['signature'] = WXSdk_Util::getSign(array(
    		'jsapi_ticket'=>$jsApiTicket,
    		'noncestr'=>$result['nonceStr'],
    		'timestamp'=>$result['timestamp'],
    		'url'=>$url
    	));
    	return $result;
    }

    public function getJsApiTicket($accessToken){
    	$keysArr = array(
            "access_token" => $accessToken,
            "type" => 'jsapi'
        );

    	return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/ticket/getticket',
			'get',
			$keysArr
		);
    }
}
?>