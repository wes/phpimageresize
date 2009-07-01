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
		body { background: #121212; color: #ffffff; font-family: lucida grande; text-align: center; }
		.block { margin: 20px; }
	</style>
</head>

<body>

	<div class='block'>
		<img src='<?=resize('images/dog.jpg',array('w'=>300))?>' border='0' />
	</div>

	<div class='block'>
		<img src='<?=resize('images/dog.jpg',array('w'=>300,'h'=>300))?>' border='0' />
	</div>

	<div class='block'>
		<img src='<?=resize('images/dog.jpg',array('w'=>300,'h'=>300,'crop'=>true))?>' border='0' />
	</div>

	<div class='block'>
		<img src='<?=resize('images/dog.jpg',array('w'=>300,'h'=>300,'scale'=>true))?>' border='0' />
	</div>

</body>
</html>