<?php
	require_once(__DIR__ ."/vendor/autoload.php");
	
	require_once(__DIR__ ."/includes/utilities.php");
	
	define('BEANSTALKD_HOST', "pi0");
	
	define('SOURCE_DIR', "/san/pi_nas/source");
	
	define('DOX_PATH', "/usr/bin/sox");
	define('FFMPEG_PATH', "/usr/bin/ffmpeg");