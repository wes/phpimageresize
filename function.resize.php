<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

/**
 * function by Wes Edling .. http://joedesigns.com
 * feel free to use this in any project, i just ask for a credit in the source code.
 * a link back to my site would be nice too.
 *
 *
 * Changes: 
 * 2012/01/30 - David Goodwin - call escapeshellarg on parameters going into the shell
 * 2012/07/12 - Whizzkid - Added support for encoded image urls and images on ssl secured servers [https://]
 * 2014/04/08 - Wes Edling - Added support for remote s3 upload and caching
 */

// Simple alias function, if neeeded
function resize($imagePath,$opts=array()){
	$r = new resizeClass();
	return $r->run($imagePath, $opts);
}

// Resize Class
class resizeClass {
	function __construct(){

	}

	function get_url($s3bucket, $imagePath){

		return 'https://s3.amazonaws.com/'.str_replace('//','/',$s3bucket.'/'.$imagePath);

	}

	function run($imagePath,$opts=array()){

		$imagePath = urldecode($imagePath);

		# start configuration

		$s3bucket = 'joedesigns'; # your s3 bucket where to store everything
		$cacheFolder = 'cache'; # path to your cache folder, must be writeable by aws server
		$pathToConvert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert

		$defaults = array(
			'crop' => false, 
			'scale' => 'false', 
			'thumbnail' => false, 
			'max_only' => false, 
		   	'canvas_color' => 'transparent', 
		   	'quality' => 90, 
		   	'cache_http_minutes' => 20
		);

		# you shouldn't need to configure anything else beyond this point, unless your mad!

		$opts = array_merge($defaults, $opts);    
		$purl = parse_url($imagePath);
		$finfo = pathinfo($imagePath);
		$ext = $finfo['extension'];

		$url = $this->get_url($s3bucket, $imagePath);

		$img = file_get_contents($url);

		$im = new Imagick();
	    $im->readImageBlob($img);
	    $im->setImageFormat("png24");
	    header("Content-Type: image/png");
	    $thumbnail = $im->getImageBlob();
	    echo $thumbnail;

		// $image = new Imagick($url);
  //  $image->thumbnailImage(100, 100);
  //  header( "Content-Type: image/jpg" );
  //  echo $image;

		// echo $img;


	// if(file_exists($imagePath) == false):
	// 	$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
	// 	if(file_exists($imagePath) == false):
	// 		return 'image not found';
	// 	endif;
	// endif;

	// if(isset($opts['w'])): $w = $opts['w']; endif;
	// if(isset($opts['h'])): $h = $opts['h']; endif;

	// $filename = md5_file($imagePath);

 //        if(!empty($w) and !empty($h)):
 //            $newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
 //        elseif(!empty($w)):
 //            $newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;	
 //        elseif(!empty($h)):
 //            $newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
 //        else:
 //            return false;
 //        endif;

	// $create = true;

 //    if(file_exists($newPath) == true):
 //        $create = false;
 //        $origFileTime = date("YmdHis",filemtime($imagePath));
 //        $newFileTime = date("YmdHis",filemtime($newPath));
 //        if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
 //            $create = true;
 //        endif;
 //    endif;

	// if($create == true):
	// 	if(!empty($w) and !empty($h)):

	// 		list($width,$height) = getimagesize($imagePath);
	// 		$resize = $w;
		
	// 		if($width > $height):
	// 			$resize = $w;
	// 			if(true === $opts['crop']):
	// 				$resize = "x".$h;				
	// 			endif;
	// 		else:
	// 			$resize = "x".$h;
	// 			if(true === $opts['crop']):
	// 				$resize = $w;
	// 			endif;
	// 		endif;

	// 		if(true === $opts['scale']):
	// 			$cmd = $pathToConvert ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
	// 			" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
	// 		else:
	// 			$cmd = $pathToConvert." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
	// 			" -size ". escapeshellarg($w ."x". $h) . 
	// 			" xc:". escapeshellarg($opts['canvas_color']) .
	// 			" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
	// 		endif;
						
	// 	else:
	// 		$cmd = $pathToConvert." " . escapeshellarg($imagePath) . 
	// 		" -thumbnail ". (!empty($h) ? 'x':'') . $w ."". 
	// 		(isset($opts['max_only']) && $opts['max_only'] == true ? "\>" : "") . 
	// 		" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);
	// 	endif;

	// 	$c = exec($cmd, $output, $return_code);
 //        if($return_code != 0) {
 //            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
 //            return false;
	// 	}
	// endif;

	// # return cache file path
	// return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

	}
}

