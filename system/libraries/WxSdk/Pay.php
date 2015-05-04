<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_Pay{
	var $CI;
	var $appId;
	var $appKey;
	var $mchId;
	var $mchKey;
	var $sslCert;
	var $sslKey;
	
	public function __construct($appId,$appKey,$mchId,$mchKey,$sslCert = '',$sslKey = ''){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
		$this->appId = $appId;
		$this->appKey = $appKey;
		$this->mchId = $mchId;
		$this->mchKey = $mchKey;
		$this->sslCert = $sslCert;
		$this->sslKey = $sslKey;
	}

	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	private function createNoncestr( $length = 32 ) 
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
	private function formatBizQueryParaMap($paraMap, $urlencode)
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
	private function getSign($Obj)
	{
		$Parameters = array();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->mchKey;
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}

	/**
	 * 	作用：array转xml
	 */
	private function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
	
	/**
	 * 	作用：将xml转为array
	 */
	private function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	* 作用：默认
	*/
	private function post($url,$isSSL,$postData)
	{
		//输入参数
		$postData["appid"] = $this->appId;
		$postData["mch_id"] = $this->mchId;    
		$postData["nonce_str"] = $this->createNoncestr();
		$postData["sign"] = $this->getSign($postData);
		$request = $this->arrayToXml($postData);

		//执行网络操作
		$ssl = array();
		if( $isSSL ){
			$ssl['cert'] = $this->sslCert;
			$ssl['key'] = $this->sslKey;
		}
		$response = $this->CI->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>$request,
			'dataType'=>'plain',
			'ssl'=>$ssl
		));

		//输出参数
		$result = $this->xmlToArray($response['body']);
		if( $result['return_code'] != 'SUCCESS')
			throw new CI_MyException(1,'调用微信支付接口失败[url:'.$url.'][request:'.$request.'][response:'.$response['body'].']');

		if( $result['result_code'] != 'SUCCESS')
			throw new CI_MyException(1,'调用微信支付接口失败[url:'.$url.'][request:'.$request.'][response:'.$response['body'].']');

		return $result;
	}

	/**
	* 统一支付接口
	*/
	public function unifiedOrder($data){
		$argv = array_merge($data,array(
			'spbill_create_ip'=> $_SERVER['REMOTE_ADDR']
		));
		return $this->post('https://api.mch.weixin.qq.com/pay/unifiedorder',false,$argv);
	}

	/**
	* 通用通知接口
	*/
	public function notifyOrder(){
		$response = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notifyData = $this->xmlToArray($response);
		
		if( $notifyData['return_code'] != 'SUCCESS')
			throw new CI_MyException(1,'微信支付通知接口失败[response:'.$response.']');

		$notifySign = $notifyData['sign'];
		unset($notifyData['sign']);
		$notifyMySign = $this->getSign($notifyData);
		if( $notifySign != $notifyMySign )
			throw new CI_MyException(1,'微信支付通知签名失败[response:'.$response.']');	

		if( $notifyData['result_code'] != 'SUCCESS')
			throw new CI_MyException(1,'微信支付通知接口失败[response:'.$response.']');	

		return $notifyData;
	}

	/**
	*  Js API 接口参数
	*/
	public function jsPayOrder($prepay_id){
		$jsApiObj = array();
		$jsApiObj["appId"] = $this->appId;
	    $jsApiObj["timeStamp"] = time().'';
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=$prepay_id";
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
	    return $jsApiObj;
	}

	/**
	*红包接口
	*/
	public function sendRedPack($data){
		$argv = array_merge(array(
			'wxappid'=>$this->appId,
			'client_ip'=> $_SERVER['REMOTE_ADDR']
		),$data);
		return $this->post('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack',false,$argv);
	}

}