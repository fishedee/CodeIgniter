<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Http{
	var $CI;
	
	public function __construct(){
		$this->CI = & get_instance();
	}
	
	private function getFieldData($dataType,$data){
		if( $dataType == 'text' ){
			if( is_array($data))
				$data = http_build_query($data);
			else
				throw new CI_MyException(1,'不合法的data'.$data.'与dataType'.$dataType);
		}else if( $dataType == 'json'){
			if( is_array($data))
				$data = json_encode($data);
			else
				throw new CI_MyException(1,'不合法的data'.$data.'与dataType'.$dataType);
		}else if( $dataType == 'json_origin'){
			if( is_array($data))
				$data = json_encode($data,JSON_UNESCAPED_UNICODE);
			else
				throw new CI_MyException(1,'不合法的data'.$data.'与dataType'.$dataType);
		}else if( $dataType == 'plain'){
			$data = $data;
		}else if( $dataType == 'form'){
			if( is_array($data) == false )
				throw new CI_MyException(1,'不合法的data'.$data.'与dataType'.$dataType);
		}else{
			throw new CI_MyException(1,'未确定的data type'.$dataType);
		}
		return $data;
	}
	
	private function getResponseData($dataType,$data){
		if( $dataType == 'text'){
			$temp = $data;
			parse_str($temp,$data);
		}else if( $dataType == 'json'){
			$data = json_decode($data,TRUE);
		}else if( $dataType == 'jsonp'){
			$lpos = strpos($data, "(");
            $rpos = strrpos($data, ")");
            $data  = substr($data, $lpos + 1, $rpos - $lpos -1);
			$data = json_decode($data,TRUE);
		}else if( $dataType == 'plain'){
			$data = $data;
		}else{
			throw new CI_MyException(1,'未确定的response type'.$dataType);
		}
		return $data;
	}
	
	private function getUrlWithData( $url , $data ){
		if( is_array($data) == false )
			throw new CI_MyException(1,'不合法的url data'.$data);
		if( count($data) == 0 )
			return $url;
		if( strpos($url,'?') == false )
			$url .= '?';
		else
			$url .= '&';
		$url .= http_build_query($data);
		return $url;
	}
	
	public function localAjax( $option ){
		//处理option
		$defaultOption = array(
			'url'=>'',
			'header'=>array(),
			'type'=>'',
			'data'=>array(),
			'dataType'=>'text',
			'timeout'=>5,
			'async'=>false,
		);
		foreach( $option as $key=>$value )
			$defaultOption[$key] = $value;
		$defaultOption['url'] = 'http://localhost'.$option['url'];
		$defaultOption['header'][] = 'Host: '.$_SERVER['HTTP_HOST'];
		//业务逻辑
		return $this->ajax($defaultOption);
	}
	
	public function ajax($option){
		//处理option
		$defaultOption = array(
			'url'=>'',
			'header'=>array(),
			'type'=>'get',
			'data'=>array(),
			'dataType'=>'text',
			'responseType'=>'plain',
			'timeout'=>5,
			'async'=>false,
			'ssl'=>array(),
		);
		foreach( $option as $key=>$value )
			$defaultOption[$key] = $value;
		
		//处理参数
		$url = trim($defaultOption['url']);
		$data = $defaultOption['data'];
		$dataType = $defaultOption['dataType'];
		$responseType = $defaultOption['responseType'];
		$header = $defaultOption['header'];
		$type = strtolower($defaultOption['type']);
		$isAsync = $defaultOption['async'];
		$ssl = $defaultOption['ssl'];
		if( $isAsync == false )
			$timeout = $defaultOption['timeout']*1000;
		else
			$timeout = 50;
		
		//配置curl
		$curl = curl_init();
		if( $type == 'get'){
			$url = $this->getUrlWithData($url,$data);
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'GET');
		}else if( $type == 'post'){
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl,CURLOPT_POSTFIELDS,$this->getFieldData($dataType,$data));
		}else if( $type == 'delete' ){
			$url = $this->getUrlWithData($url,$data);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}else if( $type == 'put' ){
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($curl,CURLOPT_POSTFIELDS,$this->getFieldData($dataType,$data));
		}else{
			throw new CI_MyException(1,'未确定的HTTP Type'.$type);
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_TIMEOUT_MS , $timeout);
		curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if( strncmp($url,'https',5) == 0 ){
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);

			if( isset($ssl['cert'])){
				curl_setopt($curl,CURLOPT_SSLCERTTYPE,'PEM');
				curl_setopt($curl,CURLOPT_SSLCERT, $ssl['cert']);
			}
			if( isset($ssl['key'])){
				curl_setopt($curl,CURLOPT_SSLKEYTYPE,'PEM');
				curl_setopt($curl,CURLOPT_SSLKEY, $ssl['key']);
			}
			if( isset($ssl['certkey']) ){
				curl_setopt($curl,CURLOPT_SSLKEYTYPE,'PEM');
				curl_setopt($curl,CURLOPT_SSLCERT,$ssl['certkey']);
				var_dump($ssl['certkey']);
			}

		}
		if(count($header) != 0 )
			curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
		
		//执行curl
		$data = curl_exec($curl);
		$headerData = curl_getinfo($curl);
		curl_close($curl);
		if( $isAsync == false && $data === false )
			throw new CI_MyException(1,'连接服务器失败 '.$url.' 错误描述：'.curl_error($curl));
		
		//返回结果
		return array(
			'header'=>$headerData,
			'body'=>$this->getResponseData($responseType,$data)
		);
	}
};