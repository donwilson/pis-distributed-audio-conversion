<?php
	require_once(__DIR__ ."/vendor/autoload.php");
	
	require_once(__DIR__ ."/includes/utilities.php");
	
	define('BEANSTALKD_HOST', "pi0");
	
	define('SOURCE_DIR', "/san/pi_nas/source");
	define('DEST_DIR', "/san/pi_nas/converted");
	
	define('FFMPEG_PATH', "/san/pi_nas/tools/ffmpeg-4.0.1-armel-32bit-static/ffmpeg");
	define('SOX_PATH', "/usr/bin/sox");