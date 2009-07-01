<?php
# include the function here
include 'function.resize.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<title>PHP Image Resize - Example</title>
	<style>
		body { 
			background: #121212; 
			color: #ffffff; 
			font-family: lucida grande; 
			text-align: center; 
		}
		#main { margin: auto; width: 400px; text-align: left; }
		.block { margin: 20px; }
		pre { text-align: left; width: 400px; background: #010101; padding: 10px; }
	</style>
</head>

<body>

<div id='main'>

	<div class='block'>
		<?php $settings = array('w'=>300); ?>
		<div><img src='<?=resize('images/dog.jpg',$settings)?>' border='0' /></div>
		<div><pre><code><?php print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300); ?>
		<div><img src='<?=resize('images/dog.jpg',$settings)?>' border='0' /></div>
		<div><pre><code><?php print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300,'crop'=>true); ?>
		<div><img src='<?=resize('images/dog.jpg',$settings)?>' border='0' /></div>
		<div><pre><code><?php print_r($settings)?></code></pre></div>
	</div>

	<div class='block'>
		<?php $settings = array('w'=>300,'h'=>300,'scale'=>true); ?>
		<div><img src='<?=resize('images/dog.jpg',$settings)?>' border='0' /></div>
		<div><pre><code><?php print_r($settings)?></code></pre></div>
	</div>

</div>

</body>
</html>