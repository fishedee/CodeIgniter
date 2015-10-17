<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(dirname(__FILE__).'/DuiBa/DuiBa.php');

class CI_DuiBa{
	var $duiBa = null;
	var $appKey = null;
	var $appSecret = null;

	public function __construct($option){
		$this->appKey = $option['appKey'];
		$this->appSecret = $option['appSecret'];
		$this->duiBa = new DuiBa();
	}

	public function getAutoLoginUrl($clientId, $point){
		return $this->duiBa->buildCreditAutoLoginRequest(
			$this->appKey,
			$this->appSecret,
			$clientId,
			$point
		);
	}

	public function getOrderStatusUrl($orderNum, $bizId){
		return $this->duiBa->buildCreditOrderStatusRequest(
			$this->appKey,
			$this->appSecret,
			$orderNum,
			$bizId
		);
	}

	public function getCreditAuditUrl($passOrderNums, $rejectOrderNums){
		return $this->duiBa->buildCreditAuditRequest(
			$this->appKey,
			$this->appSecret,
			$passOrderNums,
			$rejectOrderNums
		);
	}

	public function checkCreditConsume($request_array){
		return $this->duiBa->parseCreditConsume(
			$this->appKey,
			$this->appSecret,
			$request_array
		);
	}

	public function checkCreditNotify($request_array){
		return $this->duiBa->parseCreditNotify(
			$this->appKey,
			$this->appSecret,
			$request_array
		);
	}
}
