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

	$opts = (object) array();

	if(!empty($_GET['imagePath']))
		$opts->imagePath = $_GET['imagePath'];
	if(!empty($_GET['width']))
		$opts->width = $_GET['width'];
	if(!empty($_GET['height']))
		$opts->height = $_GET['height'];
	if(!empty($_GET['crop']))
		$opts->crop = $_GET['crop'];
	if(!empty($_GET['scale']))
		$opts->scale = $_GET['scale'];
	if(!empty($_GET['canvas_color']))
		$opts->scale = $_GET['canvas_color'];

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

		$opts = (array) $opts;

		$imagePath = urldecode($imagePath);

		# start configuration

		$s3bucket = 'joedesigns'; # your s3 bucket where to store everything
		$cacheFolder = 'cache'; # path to your cache folder, must be writeable by aws server
		// $pathToConvert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert

		$defaults = array(
			'crop' => false, 
			'scale' => false,
			'thumbnail' => false, 
			'max_only' => false, 
		   	'canvas_color' => 'transparent', 
		   	'quality' => 90, 
		   	'cache_http_minutes' => 20
		);

		# you shouldn't need to configure anything else beyond this point, unless your mad!

		$opts = (object) array_merge($defaults, $opts);    
		$purl = parse_url($imagePath);
		$finfo = pathinfo($imagePath);
		$ext = $finfo['extension'];

		$url = $this->get_url($s3bucket, $imagePath);

		$raw = file_get_contents($url);

		$im  = imagecreatefromstring($raw);
		
		$width = imagesx($im);
		$height = imagesy($im);

		if(empty($opts->width) && empty($opts->height)):
			$opts->width = $width;
			$opts->height = $height;
		elseif(empty($opts->width)):
			$opts->width = ($opts->height / $height) * $width;
		elseif(empty($opts->height)):
			$opts->height = ($opts->width / $width) * $height;
		endif;

		$original_aspect = $width / $height;
		$thumb_aspect = $opts->width / $opts->height;

		if($original_aspect >= $thumb_aspect):
		   $new_height = $opts->height;
		   $new_width = $width / ($height / $opts->height);
		else:
		   $new_width = $opts->width;
		   $new_height = $height / ($width / $opts->width);
		endif;

		$thumb = imagecreatetruecolor( $opts->width, $opts->height );

		imagecopyresampled($thumb,
		                   $im,
		                   0 - ($new_width - $opts->width) / 2, // Center the image horizontally
		                   0 - ($new_height - $opts->height) / 2, // Center the image vertically
		                   0, 0,
		                   $new_width, $new_height,
		                   $width, $height);

		header('Content-Type: image/jpeg');
		imagejpeg($thumb);
		imagedestroy($thumb);
		
	}
}

