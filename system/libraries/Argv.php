<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Argv{
	var $CI;
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function isUrl($s){
		return preg_match('/^http[s]?:\/\/'.
			'(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184
			'|'. // 允许IP和DOMAIN（域名）
			'([0-9a-z_!~*\'()-]+\.)*'. // 域名- www.
			'([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域名
			'[a-z]{2,6})'.  // first level domain- .com or .museum
			'(:[0-9]{1,4})?'.  // 端口- :80
			'((\/\?)|'.  // a slash isn't required if there is no file name
			'(\/[0-9a-zA-Z_!~\'\(\)\[\]\.;\?:@&=\+\$,%#-\/^\*\|]*)?)$/',
			$s) == 1;
	}

	/**
	*@deprecated
	*/
	public function getOptionInput($input){
		$result = array();
		foreach( $input as $singleInput ){
			$result[] = array(
				$singleInput,
				'option'
			);
		}
		try{
			$data = $this->checkGet($result);
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>$data
			);
		}catch( Exception $e ){
			return array(
				'code'=>$e->getCode(),
				'msg'=>$e->getMessage(),
				'data'=>''
			);
		}
	}

	/**
	*@deprecated
	*/
	public function getRequireInput($input){
		$result = array();
		foreach( $input as $singleInput ){
			$result[] = array(
				$singleInput,
				'require'
			);
		}
		try{
			$data = $this->checkGet($result);
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>$data
			);
		}catch( Exception $e ){
			return array(
				'code'=>$e->getCode(),
				'msg'=>$e->getMessage(),
				'data'=>''
			);
		}
	}

	/**
	*@deprecated
	*/
	public function postOptionInput($input){
		$result = array();
		foreach( $input as $singleInput ){
			$result[] = array(
				$singleInput,
				'option'
			);
		}
		try{
			$data = $this->checkPost($result);
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>$data
			);
		}catch( Exception $e ){
			return array(
				'code'=>$e->getCode(),
				'msg'=>$e->getMessage(),
				'data'=>''
			);
		}
	}

	/**
	*@deprecated
	*/
	public function postRequireInput($input){
		$result = array();
		foreach( $input as $singleInput ){
			$result[] = array(
				$singleInput,
				'require'
			);
		}
		try{
			$data = $this->checkPost($result);
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>$data
			);
		}catch( Exception $e ){
			return array(
				'code'=>$e->getCode(),
				'msg'=>$e->getMessage(),
				'data'=>''
			);
		}
	}
	
	public function checkGet($input){
		foreach( $input as $key=>$singleInput ){
			$input[$key][1] = $input[$key][1].'|get';
		}
		return $this->check($input);
	}
	public function checkPost($input){
		foreach( $input as $key=>$singleInput ){
			$input[$key][1] = $input[$key][1].'|post';
		}
		return $this->check($input);
	}
	public function check( $input ){
		$result = array();
		foreach( $input as $singleInput ){
			$fieldName = $singleInput[0];
			$fieldRule = $singleInput[1];
			unset($fieldDefaultValue);
			if( isset($singleInput[2]))
				$fieldDefaultValue = $singleInput[2];
			$fieldRule = explode('|',$fieldRule);
		
			//获取字段数据
			$method = 'get';
			if( in_array('get',$fieldRule) )
				$method = 'get';
			if( in_array('post',$fieldRule) )
				$method = 'post';
			$isXssFilt = true;
			if( in_array('noxss',$fieldRule) )
				$isXssFilt = false;
			$isRequire = true;
			if( in_array('option',$fieldRule))
				$isRequire = false;
			
			$fieldValue = $this->CI->input->$method($fieldName,$isXssFilt);
			if( $fieldValue === null ){
				if( $isRequire == false ){
					if( isset($fieldDefaultValue))
						$fieldValue = $fieldDefaultValue;
					else
						continue;
				}else{
					throw new CI_MyException(1,"请输入".$method."参数".$fieldName,'所有参数为：'.json_encode($this->CI->input->$method()));
				}
			}			
			
			//开始校验
			if( in_array('url',$fieldRule) && $this->isUrl($fieldValue) == false )
				throw new CI_MyException(1,"请输入url参数".$fieldName);
			
			//记录返回数据
			$result[$fieldName] = $fieldValue;
		}
		return $result;
	}
}
?>
