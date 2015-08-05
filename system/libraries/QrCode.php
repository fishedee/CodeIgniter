<?php 
	class CI_QrCode{
		public function __construct(){
			require_once('phpqrcode/phpqrcode.php');
		}

		public function createCodeToFile($info , $qrFileName , $errorCorrectionLevel='L',$matrixPointSize='3',$margin='5'){
			QRcode::png($info , $qrFileName , $errorCorrectionLevel,$matrixPointSize,$margin);
		}
	}
 ?>
