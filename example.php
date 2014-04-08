<?php
# include the function here
include 'function.resize.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<title>PHP Image Resize with S3 Support - Example</title>
	<style>
		body { 
			background: #ffffff; 
			color: #121212; 
			font-family: lucida grande; 
			text-align: center; 
		}
		h1 { font-size: 15px; text-align: center; }
		#main { margin: auto; width: 600px; text-align: left; }
		.block { margin: 20px; background: #fafafa; padding: 20px; text-align: center; border: 1px solid #cacaca; }
		pre { text-align: left; background: #010101; padding: 10px; font-size: 11px; }
		pre code { text-align: left; color: #ffffff; }
		.block p { color: #343434; font-size: 12px; }
	</style>
</head>

<body>

<div id='main'>

	<h1>PHP Image Resizer with S3 Support</h1>

	<div class='block'>
		<?php $settings = array('w'=>300); ?>
		<div><img src='<?=resize('/dog.jpg',$settings)?>' border='0' /></div>
		<p>Image resized by width only</p>
		<div><pre><code>src: /dog.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300); ?>
		<div><img src='<?=resize('/dog.jpg',$settings)?>' border='0' /></div>
		<p>Image resized by width and height</p>
		<div><pre><code>src: /dog.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>240,'h'=>240,'canvas-color'=>'#ff0000'); ?>
		<div><img src='<?=resize('/dog.jpg',$settings)?>' border='0' /></div>
		<p>Image resized by width and height and custom canvas color</p>
		<div><pre><code>src: /dog.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300,'crop'=>true); ?>
		<div><img src='<?=resize('/dog.jpg',$settings)?>' border='0' /></div>
		<p>Image cropped &amp; resized by width and height</p>
		<div><pre><code>src: /dog.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300,'scale'=>true); ?>
		<div><img src='<?=resize('/dog.jpg',$settings)?>' border='0' /></div>
		<p>Image scaled by width and height</p>
		<div><pre><code>src: /dog.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>100,'h'=>100,'crop'=>true); ?>
		<div><img src='<?=resize('http://farm4.static.flickr.com/3210/2934973285_fa4761c982.jpg',$settings)?>' border='0' /></div>
		<p>Image cropped &amp; resized by width and height from a remote location.</p>
		<div><pre><code>src: http://farm4.static.flickr.com/3210/2934973285_fa4761c982.jpg<?php echo "\n\n"; print_r($settings)?></code></pre></div>
	</div>

</div>

</body>
</html>
