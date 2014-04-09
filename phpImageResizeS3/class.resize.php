<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

/**
 * resizeClass by Wes Edling .. http://joedesigns.com
 * feel free to use this in any project
 * isn't opensource great?  contribute to this project on github
*/

// Resize Class
class resizeClass {

	var $fileinfo;

	var $s3;

	function __construct(){

		if (!class_exists('S3'))
			require_once('S3.php');

		$this->s3 = (object) array(
			'bucket' => 'joedesigns', # your s3 bucket where to store everything
			'cache_folder' => 'cache', # folder in the bucket to cache
			'access_key' => $_SERVER['AccessKeyS3'], # aws api access key
			'secret_key' => $_SERVER['SecretKeyS3'] # aws api secret key
		);

		$this->s3->api = new S3($this->s3->access_key, $this->s3->secret_key);

		$this->s3->api->putBucket($this->s3->bucket, S3::ACL_PUBLIC_READ);

	}

	function run($imagePath,$opts=array()){

		$imagePath = urldecode($imagePath);

		$opts = $this->_get_inputs($opts);

		$this->fileinfo = (object) pathinfo($imagePath);

		$filename = $this->_get_filename($imagePath, $opts);

		$url = $this->_get_url($imagePath);

		$raw = file_get_contents($url);

		$im  = imagecreatefromstring($raw);
		
		$width = imagesx($im);
		$height = imagesy($im);

		$rgb = $this->_hex2rgb(str_replace('#','',$opts->canvas_color));

		if(empty($opts->width) && empty($opts->height)):
			$opts->width = $width;
			$opts->height = $height;
		elseif(empty($opts->width)):
			$opts->width = ($opts->height / $height) * $width;
		elseif(empty($opts->height)):
			$opts->height = ($opts->width / $width) * $height;
		endif;

		$dst = imagecreatetruecolor($opts->width, $opts->height);
		imagefill($dst, 0, 0, imagecolorallocate($dst, $rgb[0], $rgb[1], $rgb[2]));

		$mode = isset($opts->crop) && $opts->crop == true ? 'fill' : 'fit';

		$thumb = $this->_scale_image($im, $dst, $mode);

		$file_data = $this->_output_image($thumb);

		$s3_dest = $this->s3->cache_folder.'/'.$filename;

		$obj = $this->s3->api->putObject($file_data, $this->s3->bucket, $s3_dest, S3::ACL_PUBLIC_READ);

		return 'https://s3.amazonaws.com/'.$this->s3->bucket.'/'.$s3_dest;
		
	}

	private function _get_filename($imagePath,$opts){
		$opts = (array) $opts;
		return md5($this->s3->bucket.$imagePath.implode(',',$opts)).(!empty($this->fileinfo->extension) ? '.'.$this->fileinfo->extension : '');

	}

	private function _get_url($imagePath){

		return 'https://s3.amazonaws.com/'.str_replace('//','/',$this->s3->bucket.'/'.$imagePath);

	}

	private function _get_inputs($opts){

		$opts = (object) $opts;

		// read get params if set
		if(isset($opts->read_get_params) && $opts->read_get_params == true):
			if(!empty($_GET['width']))
				$opts->width = $_GET['width'];
			if(!empty($_GET['height']))
				$opts->height = $_GET['height'];
			if(!empty($_GET['crop']))
				$opts->crop = $_GET['crop'];
			if(!empty($_GET['scale']))
				$opts->scale = $_GET['scale'];
			if(!empty($_GET['canvas_color']))
				$opts->canvas_color = $_GET['canvas_color'];
		endif;

		// override with inputs
		if(!empty($opts->width))
			$opts->width = $opts->width;
		if(!empty($opts->height))
			$opts->height = $opts->height;
		if(!empty($opts->crop))
			$opts->crop = $opts->crop;
		if(!empty($opts->scale))
			$opts->scale = $opts->scale;
		if(!empty($opts->canvas_color))
			$opts->canvas_color = $opts->canvas_color;

		// defaults
		if(empty($opts->canvas_color))
			$opts->canvas_color = '#ff0000';

		return $opts;

	}

	private function _scale_image($sim, $dim, $op = 'fit') {
	    $sw = imagesx($sim);
	    $sh = imagesy($sim);
	    $dw = imagesx($dim);
	    $dh = imagesy($dim);
	    $new_width = $dw;
	    $new_height = round($new_width*($sh/$sw));
	    $new_x = 0;
	    $new_y = round(($dh-$new_height)/2);
	    $next = $op == 'fill' ? $new_height < $dh : $new_height > $dh;
	    if($next):
	        $new_height = $dh;
	        $new_width = round($new_height*($sw/$sh));
	        $new_x = round(($dw - $new_width)/2);
	        $new_y = 0;
	    endif;
	    imagecopyresampled($dim, $sim , $new_x, $new_y, 0, 0, $new_width, $new_height, $sw, $sh);
	    return $dim;
	}

	private function _hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   return array($r, $g, $b);
	}

	private function _output_image($thumb){

		ob_start();

		if($this->fileinfo->extension == 'jpg' || $this->fileinfo->extension == 'jpeg'):
			// header('Content-Type: image/jpeg');
			imagejpeg($thumb);
		elseif($this->fileinfo->extension == 'gif'):
			// header('Content-Type: image/gif');
			imagegif($thumb);
		elseif($this->fileinfo->extension == 'png'):
			// header('Content-Type: image/png');
			imagepng($thumb);
		endif;

		imagedestroy($thumb);

		$page = ob_get_contents();

		ob_end_clean();

		return $page;

	}

}

