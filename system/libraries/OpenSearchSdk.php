<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/OpenSearchSdk/CloudsearchClient.php');
require_once(dirname(__FILE__).'/OpenSearchSdk/CloudsearchSearch.php');
require_once(dirname(__FILE__).'/OpenSearchSdk/CloudsearchDoc.php');

class CI_OpenSearchSdk{
	var $opensearch;

	private function handleReturnBack($data){
		$data = json_decode($data,true);
		if( $data['status'] != 'OK')
			throw new CI_MyException(1,'open search error '.json_encode($data));
		return $data;
	}

	public function __construct($option){
		$this->opensearch = new CloudsearchClient($option['appId'],$option['appKey'],array('host'=>$option['host']),'aliyun');
	}

	public function setDocs($db,$table,$data){
		$this->cloudsearchDoc = new CloudsearchDoc($db,$this->opensearch);
		return $this->handleReturnBack( $this->cloudsearchDoc->update($data,$table) );
	}

	public function search($dbs,$option){
		$this->cloudsearchSearch = new CloudsearchSearch($this->opensearch);
		$option['indexes'] = $dbs;
		$option['format'] = 'json';
		return $this->handleReturnBack( $this->cloudsearchSearch->search($option) );
	}

}