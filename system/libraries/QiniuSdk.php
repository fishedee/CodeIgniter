<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/QiniuSdk/Auth.php');
class CI_QiNiuSdk{
	var $auth;
	
	public function __construct($option)
    {
		$this->auth = new Qiniu\Auth(
			$option['accessKey'],
			$option['secertKey']
		);
	}

	public function getUploadToken($bucket,$policy,$expires=3600){
		return $this->auth->uploadToken(
			$bucket,
			null,
			$expires,
			$policy
		);
	}

	public function getDownloadUrl($url,$expires=3600){
		return $this->auth->privateDownloadUrl(
			$url,
			$expires
		);
	}
}