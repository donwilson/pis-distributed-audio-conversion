<?php
	require_once(__DIR__ ."/config.php");
	
	use Pheanstalk\Pheanstalk;
	
	try {
		$pheanstalk = new Pheanstalk(BEANSTALKD_HOST);
		
		
	} catch(Exception $e) {
		die("Error (line ". $e->getLine() ."): ". $e->getMessage() ."\n");
	}