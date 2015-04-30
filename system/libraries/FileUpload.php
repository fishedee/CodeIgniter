<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_FileUpload{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	
	//上传文件，移动到默认的上传位置，并生成上传的URL
	public function simpleFile( $field,$allowTypes ){
		$option = array(
			'upload_path'=>$this->CI->config->item('upload_path'),
			'max_size'=>$this->CI->config->item('upload_max_size'),
		);
		$option['field'] = $field;
		$option['allowed_types'] = $allowTypes;
		
		$result = $this->file($option);
			
		return $this->CI->config->item('upload_url').$result['file_name'];
	}
	
	//上传文件
	public function file( $option ){
		if( !isset($option['upload_path']))
			throw new CI_MyException(1,'缺少上传保存路径');
		if( !isset($option['field']))
			throw new CI_MyException(1,'缺少上传字段');
			
		$config = array();
		$config['upload_path'] = $option['upload_path'];
		$config['encrypt_name'] = true;
		$config['overwrite'] = false;
		if( isset($option['allowed_types']) )
			$config['allowed_types'] = $option['allowed_types'];
		if( isset($option['max_size']))
			$config['max_size'] = $option['max_size']/1024;
		$this->CI->load->library('upload', $config);
		if( ! $this->CI->upload->do_upload($option['field'])){
			throw new CI_MyException(1,$this->CI->upload->display_errors());
		}
		
		$data = $this->CI->upload->data();
		return $data;
	}
	
	private function imageBase64Upload( $option ){
		//校验文件字段
		if( !isset($option['upload_path']))
			throw new CI_MyException(1,'缺少上传保存路径');
		if( isset($_POST[$option['field']]) == false )
			throw new CI_MyException(1,'请选择文件上传');

		$data = base64_decode($_POST[$option['field']]);
		
		//校验文件大小
		if( isset($option['max_size']) && $option['max_size'] < strlen($data))
			throw new CI_MyException(1,'文件超过'.($option['max_size']/1024).'KB');
		
		//校验上传文件夹
		if( is_dir($option['upload_path']) === false )
			throw new CI_MyException(1,'上传文件夹不存在！请确定上传文件夹是否合法！');
		if( is_writeable($option['upload_path']) === false )
			throw new CI_MyException(1,'上传文件夹不可写！请确定上传文件夹是否有恰当的权限！');

		//保存文件
		$uniqueName = md5(uniqid());
		$fileName = $uniqueName;
		$fileAddress = $option['upload_path'].'/'.$fileName ;
		$file = @fopen($fileAddress,"wb");
		if($file === false )
			throw new CI_MyException(1,'打开文件失败'.$fileAddress);
		fwrite($file,$data);
		fclose($file);
		
		//读取图片文件信息
		$result = @getimagesize($fileAddress);
		if( $result == false ){
			$isImage = false;
			$imageWidth = 0;
			$imageHeight = 0;
			$imageFormat = '';
			$imageSizeStr = '';
			$fileType = '';
			$fileExt = '';
		}else{
			$isImage = true;
			$imageWidth = $result[0];
			$imageHeight = $result[1];
			if( $result[2] == 1 )
				$imageFormat = 'gif';
			else if( $result[2] == 2 )
				$imageFormat = 'jpg';
			else
				$imageFormat = 'png';
			$fileType = 'image/'.$imageFormat;
			$fileExt = '.'.$imageFormat;
			$imageSizeStr = $result[3];
			@rename($fileAddress,$fileAddress.'.'.$imageFormat);
			$fileAddress = $fileAddress.'.'.$imageFormat;
			$fileName = $fileName.'.'.$imageFormat;
		}
		
		return array(
			'file_name'=>$fileName,
			'file_type'=>$fileType,
			'file_path'=>$option['upload_path'],
			'full_path'=>$fileAddress,
			'raw_name'=>$uniqueName,
			'orig_name'=>$fileName,
			'client_name'=>$fileName,
			'file_ext'=>$fileExt,
			'file_size'=>strlen($data),
			'is_image'=>$isImage,
			'image_width'=>$imageWidth,
			'image_height'=>$imageHeight,
			'image_type'=>$imageFormat,
			'image_size_str'=>$imageSizeStr,
		);
	}
	
	//上传图片，移动到默认的上传位置，并生成上传的URL
	public function simpleImage($field){
		$option = array(
			'upload_path'=>$this->CI->config->item('upload_path'),
			'max_size'=>$this->CI->config->item('upload_max_size'),
		);
		$option['field'] = $field;
		
		$result = $this->image($option);
			
		return $this->CI->config->item('upload_url').$result['file_name'];
	}
	
	//上传图片
	public function image( $option ){
		if( !isset($option['field']))
			throw new CI_MyException(1,'缺少上传字段');

		$option['allowed_types'] = 'jpg|jpeg|gif|bmp|png|gif';
		if( isset($_FILES[$option['field']]))
			$result = $this->file( $option );
		else
			$result = $this->imageBase64Upload( $option );
		
		if( $result['is_image'] != true ){
			@unlink($result['full_path']);
			throw new CI_MyException(1,'上传的是非图片文件');
		}
		
		return $result;
	}
}
?>