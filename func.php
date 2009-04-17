<?php
function resize($imagePath,$opts=null){
	
	## this is the only thing that needs configuring.. 
	$cacheFolder = $_SERVER['DOCUMENT_ROOT'].'/cache/';
	$blankImagePath = $_SERVER['DOCUMENT_ROOT'].'/gfx/blank.jpg';
	$waterMarkPath = $_SERVER['DOCUMENT_ROOT'].'/gfx/watermark.png';
	$water_offset_w = 35; # watermark offset horizontal
	$water_offset_h = 35; # watermark offset vertically
	$waterMarkLocation = 'southeast'; # location to watermark on top of image
	$quality = 80;
	## you shouldn't need to configure anything else beyond this point
	
	if(file_exists($imagePath) == false){
		$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
		if(file_exists($imagePath) == false){
			//return 'image not found: '.$imagePath;
			$imagePath = $blankImagePath;
		}
	}

	if($opts['w']){ $w = $opts['w']; }
	if($opts['h']){ $h = $opts['h']; }
	
	$fileParts = explode('.',$imagePath);
	$count = count($fileParts) - 1;
	$ext = $fileParts[$count];
	
	$imgPath = str_replace('.'.$ext,'',$imagePath);
	
	$filename = md5_file($imagePath);
	
	if($w and $h){
		$newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.($opts['scale'] == true ? "_scaled" : "").'.'.$ext;
	}elseif($w){
		$newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;	
	}elseif($h){
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
		if($w and $h){

			list($width,$height) = getimagesize($imagePath);
			
			$resize = $w;
			
			if($width > $height){
				$resize = $w;
				if($opts['crop'] == true){
					$resize = "x".$h;				
				}
			}else{
				$resize = "x".$h;
				if($opts['crop'] == true){
					$resize = $w;
				}
			}

			if($opts['scale'] == true){
				exec("convert ".$imagePath."  -resize ".$resize." -quality ".$quality." ".$newPath);				
			}else{
				exec("convert ".$imagePath."  -resize ".$resize." -size ".$w."x".$h." xc:".($opts['canvas-color']?$opts['canvas-color']:"transparent")." +swap -gravity center -composite -quality ".$quality." ".$newPath);
			}
							
		}elseif($w){
			exec("convert ".$imagePath." -thumbnail ".$w."".($opts['maxOnly'] == true ? "\>" : "")." -quality ".$quality." ".$newPath);
		}elseif($h){
			exec("convert ".$imagePath." -thumbnail x".$h."".($opts['maxOnly'] == true ? "\>" : "")." -quality ".$quality." ".$newPath);
		}

		if(isset($opts['watermark'])){
			list($water_w,$water_h) = getimagesize($waterMarkPath);
			exec("composite ".$waterMarkPath." -gravity ".$waterMarkLocation." -geometry ".$water_w."x".$water_h."+".$water_offset_w."+".$water_offset_h." $newPath $newPath");
		}

	}
	
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
	
}
?>