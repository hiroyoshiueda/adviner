<?php
/**
 * Enter description here...
 *
 */
class PathBuilder {
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $pathStr = '';
	
	/**
	 * Enter description here...
	 *
	 * @var boolean
	 */
	private $autoMakeDir = true;
	
	/**
	 * Enter description here...
	 *
	 * @var String
	 */
	private $separator = '/';
	
	/**
	 * Enter description here...
	 *
	 * @param String $path
	 * @param boolean $autoMakeDir
	 * @return PathBuilder
	 */
	function PathBuilder($path='', $autoMakeDir=true) {
		$this->pathStr = str_replace('\\', $this->separator, $path);
		$this->autoMakeDir = $autoMakeDir;
		if ($this->autoMakeDir) $this->mkDirAll();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $dir
	 */
	public function append($dir) {
		if ($this->pathStr != '') $this->pathStr .= $this->separator;
		$this->pathStr .= $dir;
		if ($this->autoMakeDir) $this->mkDir();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isDir() {
		return $this->_isDir($this->pathStr);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function isFile() {
		return $this->_isFile($this->pathStr);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function toString() {
		return $this->pathStr;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function mkDir() {
		if ($this->_isDir($this->pathStr) === false) {
			return mkdir($this->pathStr);
		}
		return true;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return boolean
	 */
	public function mkDirAll() {
		$pathAry = explode($this->separator, $this->pathStr);
		$len = count($pathAry);
		$path = '';
		for ($i=0; $i<$len; $i++) {
			$dir = $pathAry[$i];
			$path .= $dir.$this->separator;
			if ($i == 0 || $dir == '') continue;
			if ($this->_isDir($path) === false) {
				if (mkdir($path) == false) return false;
			}
		}
		return true;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $path
	 * @return boolean
	 */
	private function _isDir($path) {
		return (file_exists($path) && is_dir($path));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $path
	 * @return boolean
	 */
	private function _isFile($path) {
		return (file_exists($path) && is_file($path));
	}
}
?>