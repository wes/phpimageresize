<?php

/*
function by Wes Edling .. http://joedesigns.com
feel free to use this in any project, i just ask for a credit in the source code.
a link back to my site would be nice too.
*/

function resize($imagePath,$opts=null){

	# start configuration
	
	$cacheFolder = './cache/'; # path to your cache folder, must be writeable by web server
	$remoteFolder = $cacheFolder.'remote/'; # path to the folder you wish to download remote images into
	$quality = 90; # image quality to use for ImageMagick (0 - 100)
	
	$cache_http_minutes = 20; 	# cache downloaded http images 20 minutes

	$path_to_convert = 'convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert
	
	## you shouldn't need to configure anything else beyond this point

	# check for remote image..
	if(ereg('http://',$imagePath) == true):
		# grab the image, and cache it so we have something to work with..
		$finfo = pathinfo($imagePath);
		list($filename) = explode('?',$finfo['basename']);
		$local_filepath = $remoteFolder.$filename;
		$download_image = true;
		if(file_exists($local_filepath)):
			if(filemtime($local_filepath) < strtotime('+'.$cache_http_minutes.' minutes')):
				$download_image = false;
			endif;
		endif;
		if($download_image == true):
			$img = file_get_contents($imagePath);
			file_put_contents($local_filepath,$img);
		endif;
		$imagePath = $local_filepath;
	endif;

	if(file_exists($imagePath) == false){
		$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
		if(file_exists($imagePath) == false){
			return 'image not found';
		}
	}

	if(isset($opts['w'])){ $w = $opts['w']; }
	if(isset($opts['h'])){ $h = $opts['h']; }

	$fileParts = explode('.',$imagePath);
	$count = count($fileParts) - 1;
	$ext = $fileParts[$count];

	$imgPath = str_replace('.'.$ext,'',$imagePath);

	$filename = md5_file($imagePath);

	if(!empty($w) and !empty($h)){
		$newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
	}elseif(!empty($w)){
		$newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;	
	}elseif(!empty($h)){
		$newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
	}else{
		return false;
	}

	$create = true;

	if(file_exists($newPath) == true){

		$create = false;

		$origFileTime = date("YmdHis",filemtime($imagePath));
		$newFileTime = date("YmdHis",filemtime($newPath));

		if($newFileTime < $origFileTime){
			$create = true;
		}

	}

	if($create == true){
		if(!empty($w) and !empty($h)){

			list($width,$height) = getimagesize($imagePath);
		
			$resize = $w;
		
			if($width > $height){
				$resize = $w;
				if(isset($opts['crop']) && $opts['crop'] == true){
					$resize = "x".$h;				
				}
			}else{
				$resize = "x".$h;
				if(isset($opts['crop']) && $opts['crop'] == true){
					$resize = $w;
				}
			}
			$imagePath = "'".$imagePath."'";
			if(isset($opts['scale']) && $opts['scale'] == true){
				exec($path_to_convert." ".$imagePath."  -resize ".$resize." -quality ".$quality." ".$newPath);				
			}else{
				exec($path_to_convert." ".$imagePath."  -resize ".$resize." -size ".$w."x".$h." xc:".(isset($opts['canvas-color'])?$opts['canvas-color']:"transparent")." +swap -gravity center -composite -quality ".$quality." ".$newPath);
			}
						
		}elseif(!empty($w)){
			exec($path_to_convert." ".$imagePath." -thumbnail ".$w."".(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "")." -quality ".$quality." ".$newPath);
		}elseif(!empty($h)){
			exec($path_to_convert." ".$imagePath." -thumbnail x".$h."".(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "")." -quality ".$quality." ".$newPath);
		}
	}
	
	# return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
	
}

?>