<?php
	require_once(__DIR__ ."/../config.php");
	
	use Pheanstalk\Pheanstalk;
	
	/**
	 * Get the desired output full path of the converted file. Returns false on error or if output file exists
	 * @param string $filename Source filename
	 * @return string|false
	 */
	function getDesiredFilepath($filename) {
		if(!preg_match("#^([0-9]+])\s+(.+?)\.(avi|mkv|wmv)$#si", $filename, $match)) {
			return false;
		}
		
		$raw_season = substr(trim($match[1]), 0, 1);
		$raw_episode = substr(trim($match[1]), 1, 2);
		
		$desired_filename = "SpongeBob SquarePants - S". str_pad($raw_season, 2, "0", STR_PAD_LEFT) ."E". str_pad($raw_episode, 2, "0", STR_PAD_LEFT) ." - ". trim($match[2]) .".mp4";
		$desired_path = DEST_DIR . $desired_filename;
		
		if(file_exists($desired_path)) {
			return false;
		}
		
		return $desired_path;
	}
	
	try {
		$pheanstalk = new Pheanstalk(BEANSTALKD_HOST);
		
		foreach(Finder::findFiles("*.avi", "*.wmv", "*.mkv") as $key => $file) {
			print $key ."\n";
		}
	} catch(Exception $e) {
		die("Error (line ". $e->getLine() ."): ". $e->getMessage() ."\n");
	}