<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/Util.php');
class WXSdk_Base{
    public function __construct(){
    }

    public function getAccessToken($appId,$appKey){
        $keysArr = array(
            "grant_type" => "client_credential",
            "appid" => $appId,
            "secret" => $appKey
        );

        return WXSdk_Util::applyJsonApi(
            'https://api.weixin.qq.com/cgi-bin/token',
            'get',
            $keysArr
        );
    }

    public function getServerIp($accessToken){
        return WXSdk_Util::applyJsonApi(
            'https://api.weixin.qq.com/cgi-bin/getcallbackip',
            'get',
            array(
                'access_token'=>$accessToken
            )
        );
    }

    public function checkSignature($token)
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return isset($_GET['echostr'])?$_GET['echostr']:true;
        }else{
            return false;
        }
    }
}