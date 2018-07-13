<?php
	/**
	 * Return the absolute path of a file path (realpath but without file_exists check)
	 * @link http://php.net/manual/en/function.realpath.php#84012 Original author
	 * @param string $path Partial or full filepath
	 * @return string
	 */
	function get_absolute_path($path) {
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = [];
		
		foreach($parts as $part) {
			if('.' == $part) {
				continue;
			}
			
			if('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		
		return implode(DIRECTORY_SEPARATOR, $absolutes);
	}