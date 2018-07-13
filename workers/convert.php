<?php
	require_once(__DIR__ ."/../config.php");
	
	use Pheanstalk\Pheanstalk;
	
	define('TUBE_CONVERT', "rpi-convert-files");
	define('MAX_ATTEMPTS', 5);
	
	try {
		$pheanstalk = new Pheanstalk(BEANSTALKD_HOST);
		
		if(!$pheanstalk->getConnection()->isServiceListening()) {
			throw new Exception("beanstalkd server not available");
		}
		
		$pheanstalk->watch(TUBE_CONVERT);
		
		while($job = $pheanstalk->reserve()) {
			$data = json_decode($job->getData(), true);
			
			print_r($data);
			
			// sanity checks
			if(empty($data['source']) || empty($data['output'])) {
				// source not set or file doesn't exist
				print "One or both file paths for job #". $job->getId() ." not set\n";
				
				$pheanstalk->delete($job);
				
				continue;
			}
			
			if(defined('MAX_ATTEMPTS') && MAX_ATTEMPTS) {
				if(!empty($data['attempts']) && (intval($data['attempts']) > MAX_ATTEMPTS)) {
					// too many attempts
					print "Exceeded maximum amount of attempts for job #". $job->getId() ."\n";
					
					$pheanstalk->delete($job);
					
					continue;
				}
				
				if(!isset($data['attempts']) || !is_numeric($data['attempts'])) {
					$data['attempts'] = 0;
				}
				
				$data['attempts']++;
			}
			
			$source_file = get_absolute_path($data['source']);
			$output_file = get_absolute_path($data['output']);
			
			if($source_file === $output_file) {
				// source file doesn't exist
				print "Source and output file paths for job #". $job->getId() ." are the same\n";
				
				$pheanstalk->delete($job);
				
				continue;
			}
			
			if(!file_exists($source_file)) {
				// source file doesn't exist
				print "Source file path for job #". $job->getId() ." not found\n";
				
				$pheanstalk->delete($job);
				
				continue;
			}
			
			if(file_exists($output_file)) {
				// already exists?
				print "Output file path for job #". $job->getId() ." already exists\n";
				
				$pheanstalk->delete($job);
				
				continue;
			}
			
			if((SOURCE_DIR !== substr($source_file, 0, strlen(SOURCE_DIR))) || (DEST_DIR !== substr($output_file, 0, strlen(DEST_DIR)))) {
				// either path is outside of pre-defined source/destination folders
				print "One or both file paths for job #". $job->getId() ." resolve outside of pre-defined paths\n";
				
				$pheanstalk->delete($job);
				
				continue;
			}
			
			// ready to convert
			// ffmpeg -i "/san/pi_nas/source/101 Help Wanted.avi" -f mp4 -vcodec libx264 -preset fast -profile:v main -acodec aac "/san/pi_nas/converted/101 Help Wanted.mp4" -hide_banner
			
			$cmd = new \Tivie\Command\Command(\Tivie\Command\ESCAPE);
			
			$cmd->setCommand(FFMPEG_PATH)
				->addArgument(new Argument("-i", $source_file))
				->addArgument(new Argument("-f", "mp4"))
				->addArgument(new Argument("-vcodec", "libx264"))
				->addArgument(new Argument("-preset", "fast"))
				->addArgument(new Argument("-profile:v", "main"))
				->addArgument(new Argument("-acodec", "aac"))
				->addArgument(new Argument($output_file))
				->addArgument(new Argument("-hide_banner"));
			
			$result = $cmd->run();
			
			print "ffmpeg returned with exit code: ". $result->getExitCode() ."\n";
			
			$pheanstalk->delete($job);
		}
	} catch(Exception $e) {
		die("Error (line ". $e->getLine() ."): ". $e->getMessage() ."\n");
	}