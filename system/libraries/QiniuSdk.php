<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/QiniuSdk/Auth.php');
class CI_QiNiuSdk{
	var $auth;
	var $CI;
	
	public function __construct($option)
    {
		$this->auth = new Qiniu\Auth(
			$option['accessKey'],
			$option['secertKey']
		);
		$this->CI = & get_instance();
	}

	public function uploadString($uploadToken,$file,$domain){
		$this->CI->load->library('http','','http');
		$result = $this->CI->http->ajax(array(
			'type'=>'post',
			'url'=>'http://up.qiniu.com/',
			'dataType'=>'form',
			'data'=>array(
				'token'=>$uploadToken,
				'file'=>$file
			),
			'responseType'=>'json',
			'timeout'=>30
		));

		if( isset($result['body']['error']) )
			throw new CI_MyException(1,$result['body']['error']);

		return $domain.$result['body']['key'];
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