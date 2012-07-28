PHPImageResize Script
=====================

This is a beautiful scrip that will allow you to simply resize, compress and cache your images on the fly. The script is dynamic and flexible and is being updated continuously.

Using The Script
----------------

You just need to include a simple function file in your project and you are good to go. So you basically start by including the file in your project.

	<?php require_once 'function.resize.php'; ?>

And create folder called `cache` - with writable permissions (777) and another folder inside `cache` called `remote`

Basic Resizing
--------------

You need to define the basic settings for your image as an array for the scrip to process:

Resizing by width/height:
-------------------------

	<?php
		$settings = array('w'=>300);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Resizing By Width and Height
----------------------------

	<?php
		$settings = array('w'=>300, 'h'=>300);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Resizing and giving a custom canvas color
-----------------------------------------

	<?php
		$settings = array('w'=>300, 'h'=>300, 'canvas-color'=>'#ff0000');
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Resizing by Cropping
--------------------

	<?php
		$settings = array('w'=>300, 'h'=>300, 'crop'=> true);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Scaling the image
-----------------

	<?php
		$settings = array('w'=>300, 'h'=>300, 'scale'=> true);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Resizing images from remote location
------------------------------------

	<?php
		$settings = array('w'=>300, 'h'=>300);
	?>
	<img src='<?=resize('REMOTE_ADDR',$settings)?>' border='0' />

Compressing Images to JPEG (lossy)
----------------------------------

	<?php
		$settings = array('w'=>300, 'h'=>300, 'compress'=>true);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

Setting Compression Level (1-99)Low to High
-------------------------------------------

	<?php
		$settings = array('w'=>300, 'h'=>300, 'compress'=>true, 'compression'=>70);
	?>
	<img src='<?=resize($IMG_PATH,$settings)?>' border='0' />

[Check Out The Example Here!](http://clients.nishantarora.in/projects/PHPImageResize/example.php?img=images/dog.png)

Forked By [Whizzzkid](http://nishantarora.in/)

Original Source [Wes](https://github.com/wes/phpimageresize)

Cheers!...
==========