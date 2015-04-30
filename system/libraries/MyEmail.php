<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
class CI_MyEmail{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	private function utf8Encode( $str ){
		return "=?utf-8?B?".base64_encode($str)."?=";
	}
	public function send( $address, $title,$message ){
		$mail    = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->Host       = $this->CI->config->item('email_host');
		$mail->Port       = $this->CI->config->item('email_port');
		$mail->Username   = $this->CI->config->item('email_user');
		$mail->Password   = $this->CI->config->item('email_pass');
		$mail->Charset 	  = 'UTF-8';
		$mail->SMTPDebug  = 1; 
		$mail->IsHTML(true); 
		$mail->SetFrom(
			$this->CI->config->item('email_user'), 
			$this->utf8Encode($this->CI->config->item('email_user_name'))
		);
		$mail->AddReplyTo(
			$this->CI->config->item('email_user'), 
			$this->utf8Encode($this->CI->config->item('email_user_name'))
		);
		foreach( $address as $singleAddress ){
			$mail->AddAddress(
				$singleAddress['user'], 
				$this->utf8Encode($singleAddress['name'])
			);
		}
		$mail->Subject    = $this->utf8Encode($title);
		$mail->MsgHTML($message);
		$result = $mail->Send();
		if( $result == false ){
			return array(
				'code'=>1,
				'msg'=>$mail->ErrorInfo,
				'data'=>array(
					'address'=>$address,
					'title'=>$title,
					'message'=>$message,
				)
			);
		}else{
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>''
			);
		}
	}
	
	public function asyncSend($address,$title,$message){
		$this->CI->load->library('http','','http');
		$result = $this->CI->http->localAjax(array(
			'url'=>$this->CI->config->item('email_async_url'),
			'type'=>'post',
			'async'=>true,
			'data'=>array(
				'address'=>$address,
				'title'=>$title,
				'message'=>$message
			),
		));
		return $result;
	}
	
	public function asyncSendReceiver(){
		$this->CI->load->library('argv','','argv');
		$result = $this->CI->argv->postRequireInput(array('address','title','message'));
		if( $result["code"] != 0 ){
			return $result;
		}
		return $this->send(
			$result['data']['address'],
			$result['data']['title'],
			$result['data']['message']
		);
	}
};