<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/PHPExcel.php');
class CI_Excel{
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	private function flitNoUtf8( $str )
	{
		$result = "";
		for( $i = 0 ; $i < strlen($str) ; ){
			$length = 0;
			$code =ord($str[$i]);
			if($code < 0x80 ){
				if( $code < 0x20 && $code > 0x7e )
					break;
				$length = 1;
			}else if( ($code & 0xE0) == 0xC0){
				$length = 2;
			}else if( ($code & 0xF0) == 0xE0 ){
				$length = 3;
			}else if (($code & 0xFC) == 0xF8){
				$length = 4;
			}else if (($code & 0xFE) == 0xFC){
				$length = 5;
			}else{
				break;
			}
			if( $i + $length > strlen($str))
				break;
			$result = $result.substr($str,$i,$length);
			$i += $length;
		}
		return $result;
	}
	
	public function exportFromUser($title,$data)
	{	
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("fish")
									 ->setLastModifiedBy("fish")
									 ->setTitle($title)
									 ->setSubject($title)
									 ->setDescription($title)
									 ->setKeywords($title)
									 ->setCategory($title);
		//设置Excel高度
		$columnName = $data[0];
		$columnIndex = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$objPHPExcel->setActiveSheetIndex(0);
		for( $i = 0 ; $i < count($columnName) ; $i++ ){
			$curColumnIndex = substr( $columnIndex , $i , 1 );
			$objPHPExcel->getActiveSheet()->getColumnDimension($curColumnIndex)->setWidth(40);
		}
		
		//设置Excel头部
		for( $i = 0 ; $i < count($columnName) ; $i++ ){
			$curColumnIndex = substr( $columnIndex , $i , 1 ).'1';
			$objPHPExcel->getActiveSheet()->setCellValue($curColumnIndex,$columnName[$i]);
		}
		//设置Excel数据
		for( $i = 1 ; $i < count($data) ; $i++){
			for( $j = 0 ; $j < count($columnName) ; $j++ ){
				$curColumnIndex = substr( $columnIndex , $j , 1 ).($i+1);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($curColumnIndex,$this->flitNoUtf8($data[$i][$j]),PHPExcel_Cell_DataType::TYPE_STRING);
			}
		}
		//exit(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$title.'.xls"');
		header('Cache-Control: max-age=0');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}