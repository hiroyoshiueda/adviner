<?php
/**
 * Enter description here...
 *
 */
class UtilShell {
	/**
	 * Enter description here...
	 *
	 * @param String $shellpath
	 * @param String $arg
	 * @param boolean $background
	 * @return boolean
	 */
	public static function exec($shellpath, $arg, $background = false) {
		if ($arg != '') $arg = ' ' . $arg;
		
		$command = "nohup /bin/sh " . $shellpath . $arg . " > /dev/null";
		
		if ($background) $command .= " &";
		
		system($command);
		
		return true;
	}
}
?>