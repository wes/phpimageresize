<?php
/**
 * function by Wes Edling .. http://joedesigns.com
 * feel free to use this in any project, i just ask for a credit in the source code.
 * a link back to my site would be nice too.
 *
 * Changes: 
 * 2012/01/30 - David Goodwin - call escapeshellarg on parameters going into the shell
 * 2012/07/12 - Whizzzkid - Added support for encoded image urls and images on ssl secured servers [https://]
 * 2012/07/12 - Whizzzkid - Code Cleaning...
 * 2012/07/28 - Whizzzkid - Added Compression Support upto 97% file size reduction achieved. Lots of code cleaned!
 */
/**
 * SECURITY:
 * It's a bad idea to allow user supplied data to become the path for the image you wish to retrieve, as this allows them
 * to download nearly anything to your server. If you must do this, it's strongly advised that you put a .htaccess file 
 * in the cache directory containing something like the following :
 * <code>php_flag engine off</code>
 * to at least stop arbitrary code execution. You can deal with any copyright infringement issues yourself :)
 */
/**
 * @param string $imagePath - either a local absolute/relative path, or a remote URL (e.g. http://...flickr.com/.../ ). See SECURITY note above.
 * @param array $opts  (w(pixels), h(pixels), crop(boolean), scale(boolean), thumbnail(boolean), maxOnly(boolean), canvas-color(#abcabc), output-filename(string), cache_http_minutes(int))
 * @return new URL for resized image.
 */
function resize($imagePath,$opts=null){
	$imagePath = urldecode($imagePath);
	
	// start configuration........
	$cacheFolder = 'cache/';							//path to your cache folder, must be writeable by web server
	$remoteFolder = $cacheFolder.'remote/';				//path to the folder you wish to download remote images into
	
	//setting script defaults
	$defaults['crop']				= false;
	$defaults['scale']				= false;
	$defaults['thumbnail']			= false;
	$defaults['maxOnly']			= false;
	$defaults['canvas-color']		= 'transparent';
	$defaults['output-filename']	= false;
	$defaults['cacheFolder']		= $cacheFolder;
	$defaults['remoteFolder']		= $remoteFolder;
	$defaults['quality'] 			= 80;
	$defaults['cache_http_minutes']	= 1;
	$defaults['compress']			= false;			//will convert to lossy jpeg for conversion...
	$defaults['compression']		= 40;				//[1-99]higher the value, better the compression, more the time, lower the quality (lossy)
	
	$opts = array_merge($defaults, $opts);
	$path_to_convert = 'convert';						//this could be something like /usr/bin/convert or /opt/local/share/bin/convert
	// configuration ends...
	
	//processing begins
	$cacheFolder = $opts['cacheFolder'];
	$remoteFolder = $opts['remoteFolder'];
	$purl = parse_url($imagePath);
	$finfo = pathinfo($imagePath);
	$ext = $finfo['extension'];
	// check for remote image..
	if(isset($purl['scheme']) && ($purl['scheme'] == 'http' || $purl['scheme'] == 'https')){
	// grab the image, and cache it so we have something to work with..
		list($filename) = explode('?',$finfo['basename']);
		$local_filepath = $remoteFolder.$filename;
		$download_image = true;
		if(file_exists($local_filepath)){
			if(filemtime($local_filepath) < strtotime('+'.$opts['cache_http_minutes'].' minutes')){
				$download_image = false;
			}
		}
		if($download_image){
			file_put_contents($local_filepath,file_get_contents($imagePath));
		}
		$imagePath = $local_filepath;
	}
	if(!file_exists($imagePath)){
		$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
		if(!file_exists($imagePath)){
			return 'image not found';
		}
	}
	if(isset($opts['w'])){ $w = $opts['w']; };
	if(isset($opts['h'])){ $h = $opts['h']; };
	$filename = md5_file($imagePath);
	// If the user has requested an explicit output-filename, do not use the cache directory.
	if($opts['output-filename']){
		$newPath = $opts['output-filename'];
	}else{
        if(!empty($w) and !empty($h)){
            $newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.($opts['crop'] == true ? "_cp" : "").($opts['scale'] == true ? "_sc" : "");
        }else if(!empty($w)){
            $newPath = $cacheFolder.$filename.'_w'.$w;	
        }else if(!empty($h)){
            $newPath = $cacheFolder.$filename.'_h'.$h;
        }else{
            return false;
        }
		if($opts['compress']){
			if($opts['compression'] == $defaults['compression']){
				$newPath .= '_comp.'.$ext;
			}else{
				$newPath .= '_comp_'.$opts['compression'].'.'.$ext;
			}
		}else{
			$newPath .= '.'.$ext;
		}
	}
	$create = true;
    if(file_exists($newPath)){
        $create = false;
        $origFileTime = date("YmdHis",filemtime($imagePath));
        $newFileTime = date("YmdHis",filemtime($newPath));
        if($newFileTime < $origFileTime){					# Not using $opts['expire-time'] ??
            $create = true;
        }
    }
	if($create){
		if(!empty($w) && !empty($h)){
			list($width,$height) = getimagesize($imagePath);
			$resize = $w;
			if($width > $height){
				$ww = $w;
				$hh = round(($height/$width) * $ww);
				$resize = $w;
				if($opts['crop']){
					$resize = "x".$h;				
				}
			}else{
				$hh = $h;
				$ww = round(($width/$height) * $hh);
				$resize = "x".$h;
				if($opts['crop']){
					$resize = $w;
				}
			}
			if($opts['scale']){
				$cmd = $path_to_convert." ".escapeshellarg($imagePath)." -resize ".escapeshellarg($resize)." -quality ". escapeshellarg($opts['quality'])." " .escapeshellarg($newPath);
			}else if($opts['canvas-color'] == 'transparent' && !$opts['crop'] && !$opts['scale']){
				$cmd = $path_to_convert." ".escapeshellarg($imagePath)." -resize ".escapeshellarg($resize)." -size ".escapeshellarg($ww ."x". $hh)." xc:". escapeshellarg($opts['canvas-color'])." +swap -gravity center -composite -quality ".escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
			}else{
				$cmd = $path_to_convert." ".escapeshellarg($imagePath)." -resize ".escapeshellarg($resize)." -size ".escapeshellarg($w ."x". $h)." xc:". escapeshellarg($opts['canvas-color'])." +swap -gravity center -composite -quality ".escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
			}
		}else{
			$cmd = $path_to_convert." " . escapeshellarg($imagePath).
			" -thumbnail ".(!empty($h) ? 'x':'').$w." ".($opts['maxOnly'] == true ? "\>" : "")." -quality ".escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
		}
		$c = exec($cmd, $output, $return_code);
        if($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            return false;
		}
		if($opts['compress']){
			$size = getimagesize($newPath);
			$mime = $size['mime'];
			if($mime == 'image/png' || $mime == 3){
				$picture = imagecreatefrompng($newPath);
			}else if($mime == 'image/jpeg' || $mime == 2){
				$picture = imagecreatefromjpeg($newPath);
			}else if($mime == 'image/gif' || $mime == 1){
				$picture = imagecreatefromgif($newPath);
			}else{			
				error_log("I do not support this format for now. Mime - $mime ", 0);
			}
			if(isset($picture)){
				$newP_arr = explode(".",$newPath);
				$newestPath = $newP_arr[0].".jpg";
				$qc = 100 - $opts['compression'];
				$status = imagejpeg($picture,"$newestPath",$qc);
				if($status){
					unlink($newPath);
					$newPath = $newestPath;
				}else{
					@unlink($newestPath);
					error_log("I failed to compress the image in jpeg format.", 0);
				}
				imagedestroy($picture);
			}else{
				error_log("Failed To extract picture data", 0);
			}
		}
	}
	// return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);	
}
