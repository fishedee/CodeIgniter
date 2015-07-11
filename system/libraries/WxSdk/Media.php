<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_Media{

	var $accessToken;

	public function __construct($accessToken){
		$this->accessToken = $accessToken;
	}

	public function download($mediaId){
		return WXSdk_Util::applyJsonApi(
			'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->accessToken.'&media_id='.$mediaId,
			'get',
			array(),
			'text',
			30,
			'plain'
		);
	}
}