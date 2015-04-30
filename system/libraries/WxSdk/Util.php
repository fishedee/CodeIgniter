<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_Util{

    private static function is_assoc($arr){
    	foreach (array_keys($arr) as $k => $v) {
		    if ($k !== $v)
		      return true;
		}
		return false;
    }

    private static function arrayToXmlInner($arr){
    	$xml = '';
    	foreach ($arr as $key=>$val)
        {	
        	$xml.="<".$key.">";
        	if (is_numeric($val))
        	{
        	 	$xml.= $val; 

        	}
        	else if(is_array($val))
        	{
        	 	if( self::is_assoc($val) ){
        	 		$xml.= self::arrayToXmlInner($val);
        	 	}else{
        	 		foreach($val as $singleVal){
        	 			$xml.= "<item>".self::arrayToXmlInner($singleVal)."</item>";
        	 		}
        	 	}
        	}
        	else
        	{
        		$xml.= "<![CDATA[".$val."]]>";
        	}
        	$xml.="</".$key.">"; 
        }
        return $xml;
    }

    /**
	 * 	作用：array转xml
	 */
	public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        $xml.=self::arrayToXmlInner($arr);
        $xml.="</xml>";
        return $xml; 
    }
	
	/**
	 * 	作用：将xml转为array
	 */
	public static function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	 * 	作用：调用Json类型的API，输入是urlencode，输出是json
	 */
	public static function applyJsonApi($url,$type,$keysArgv,$keysArgvType='text'){
		$CI = & get_instance();
		$CI->load->library('http');
		$httpResponse = $CI->http->ajax(array(
			'url'=>$url,
			'type'=>$type,
			'data'=>$keysArgv,
			'dataType'=>$keysArgvType,
			'responseType'=>'json'
		));
		
		if( isset($httpResponse['body']['errcode']) && $httpResponse['body']['errcode'] != 0 )
			throw new CI_MyException(1,$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		
		return $httpResponse['body'];
	}

	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public static function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	private static function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if( $v == '')
				continue;
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = "";
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}

	/**
	 * 	作用：生成签名
	 */
	public static function getSign($Obj) 
	{
		$Parameters = array();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);

		//签名步骤二：拼接参数
		$String = self::formatBizQueryParaMap($Parameters, false);

		//签名步骤二：sha1哈希
		return sha1($String);
	}
}