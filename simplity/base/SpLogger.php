<?php
define('SP_LOGTYPE_INFO', 'info');
define('SP_LOGTYPE_ERROR', 'error');
define('SP_LOGTYPE_ACCESS', 'access');
define('SP_LOGTYPE_REQUEST', 'request');
define('SP_LOGTYPE_QUERY', 'query');
define('SP_LOGTYPE_DEBUG', 'debug');

class SpLogger
{
	private $_logDir = '';
	private $_logTypes = array();
	private $_logFiles = array();
	private $_logStdout = false;
	private $_logBuffer = array();
	private $_logDelete = true;
	private $_name = '';
	private $_date = '';
	private $_uid = '';
	private $_queryTime = 0;
	private $_flush = false;
	private $_lastError = '';
	private $_days = 0;

	function __construct($conf)
	{
		$this->_logDir = $conf['log_dir'];
		$this->_name = $conf['log_name'];
		$t = explode(',', $conf['log_type']);
		foreach ($t as $type) {
			$this->_logTypes[$type] = $type;
		}
		$this->_days = $conf['log_days']>0 ? $conf['log_days'] : 31;
		$this->_logStdout = (empty($conf['log_stdout'])) ? false : true;
		if (empty($conf['log_delete'])) $this->_logDelete = false;
		$this->_date = date("Y-m-d");
		$this->_flush = (PHP_SAPI == 'cli') ? true : false;
		$this->_uid = mt_rand(10000000, 99999999);
		$this->_init();
		$this->request();
		$this->access();
	}
	public function info($msg)
	{
		if (isset($this->_logTypes[SP_LOGTYPE_INFO]) === false) return;
		$filePath = $this->_getLogFile(SP_LOGTYPE_INFO);
		$msg = $this->_format($msg);
		return $this->_writeTextFile($filePath, $msg);
	}
	public function debug($msg)
	{
		if (isset($this->_logTypes[SP_LOGTYPE_DEBUG]) === false) return;
		$filePath = $this->_getLogFile(SP_LOGTYPE_INFO);
		$msg = $this->_arrayToString($msg);
		$msg = $this->_convertEncoding($msg);
		$msg = $this->_format("[DEBUG] ".$msg);
		return $this->_writeTextFile($filePath, $msg);
	}
	public function error($msg, $file='', $line='')
	{
		if (isset($this->_logTypes[SP_LOGTYPE_ERROR]) === false) return;
		$filePath = $this->_getLogFile(SP_LOGTYPE_ERROR);
		$msg = $this->_arrayToString($msg);
		$msg = $this->_convertEncoding($msg);
		$msg = "*** エラーが発生しました。\n".$msg;
		$msg = $this->_format($msg, $file, $line);
		$this->_lastError = $msg;
		return $this->_writeTextFile($filePath, $msg);
	}
	public function exception(&$e)
	{
		if (isset($this->_logTypes[SP_LOGTYPE_ERROR]) === false) return;
		if (method_exists($e, 'getStackTrace')) {
			return $this->error($e->getStackTrace());
		} else {
			return $this->error($e->getMessage());
		}
	}
	public function query($msg=null)
	{
		if (isset($this->_logTypes[SP_LOGTYPE_QUERY]) === false) return;
		$this->_queryTime = $this->_getMicrotime();
		$filePath = $this->_getLogFile(SP_LOGTYPE_QUERY);
		$msg = $this->_format($msg);
		return $this->_writeTextFile($filePath, $msg);
	}
	public function queryResult()
	{
		if (isset($this->_logTypes[SP_LOGTYPE_QUERY]) === false) return;
		$now = $this->_getMicrotime() - $this->_queryTime;
		$msg = "--> ".number_format($now, 4)."(sec)";
		$this->_queryTime = 0;
		$filePath = $this->_getLogFile(SP_LOGTYPE_QUERY);
		$msg = $this->_format($msg);
		return $this->_writeTextFile($filePath, $msg);
	}
	public function request()
	{
		if (isset($this->_logTypes[SP_LOGTYPE_REQUEST]) === false) return;
		$filePath = $this->_getLogFile(SP_LOGTYPE_REQUEST);
		$msg = $this->_format($this->_requestFormat());
		return $this->_writeTextFile($filePath, $msg);
	}
	public function access()
	{
		if (isset($this->_logTypes[SP_LOGTYPE_ACCESS]) === false) return;
		$filePath = $this->_getLogFile(SP_LOGTYPE_ACCESS);
		$msg = $this->_format($this->_accessFormat());
		return $this->_writeTextFile($filePath, $msg);
	}
	public function output()
	{
		if (count($this->_logBuffer) == 0) return;
		echo "<br />".join('<br />', $this->_logBuffer);
	}
	public function getLastErrorMessage()
	{
		return $this->_lastError;
	}
	public function deleteLogDir($date)
	{
		$dt = explode('-', $date);
		$timestamp = mktime(0,0,0,(int)$dt[1],(int)$dt[2],$dt[0]);
		$log_stamp = $timestamp - ($this->_days * 86400);
		$date_list = $this->_getLogDirList($this->_logDir);
		if (count($date_list) > 0) {
			foreach ($date_list as $date_dir) {
				$dt = explode('-', $date_dir);
				$timestamp = mktime(0,0,0,(int)$dt[1],(int)$dt[2],$dt[0]);
				if ($timestamp < $log_stamp) {
					$this->_cleanLogFile($this->_logDir.'/'.$date_dir);
					@rmdir($this->_logDir.'/'.$date_dir);
					$this->debug('Delete log dir --> '.$this->_logDir.'/'.$date_dir);
				}
			}
		}
	}
	private function _init()
	{
		$this->_logFiles = array(
			SP_LOGTYPE_INFO => $this->_name."_".SP_LOGTYPE_INFO."_".$this->_date.".log",
			SP_LOGTYPE_ERROR => $this->_name."_".SP_LOGTYPE_ERROR."_".$this->_date.".log",
			SP_LOGTYPE_ACCESS => $this->_name."_".SP_LOGTYPE_ACCESS."_".$this->_date.".log",
			SP_LOGTYPE_REQUEST => $this->_name."_".SP_LOGTYPE_REQUEST."_".$this->_date.".log",
			SP_LOGTYPE_QUERY => $this->_name."_".SP_LOGTYPE_QUERY."_".$this->_date.".log"
		);
//		if (Util::isDir($this->_logDir) === false) @mkdir($this->_logDir, 0755);
		$dir = $this->_logDir.'/'.$this->_date;
		if (Util::isDir($dir) === false) {
			@mkdir($dir, 0755, true);
			// その日のログディレクトリ作成時に過去分を削除
			if ($this->_logDelete !== false) {
				$this->deleteLogDir($this->_date);
			}
		}
		return;
	}
	private function _getLogDirList($dir)
	{
		$list = array();
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (($file = readdir($handle)) !== false) {
					if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $file)) {
						if (is_dir($dir.'/'.$file)) $list[] = $file;
					}
        		}
        		closedir($handle);
			}
		}
		return $list;
	}
	private function _cleanLogFile($dir)
	{
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..") continue;
					@unlink($dir."/".$file);
        		}
        		closedir($handle);
			}
		}
		return;
	}
	private function _getLogFile($logType)
	{
		return $this->_logDir.'/'.$this->_date.'/'.$this->_logFiles[$logType];
	}
	private function _format($message, $file='', $line='')
	{
		if ($message == '') return '';
		$msg  = date("Y-m-d H:i:s");
		$msg .= " [{$this->_uid}] ";
		$msg .= $message;
		$msg .= "\n";
		return $msg;
	}
	private function _requestFormat()
	{
		if (!is_array($_REQUEST) || count($_REQUEST) == 0) return '';
		$req = $_SERVER['REQUEST_METHOD'];
		if ($req === null) $req = "none";
		$msg = '';
		$msg .= "(".$req.") ";
		foreach ($_REQUEST as $k => $v) {
			if (is_array($v)) {
				$k = $k."[]";
				$v = implode(',', $v);
			}
			$msg .= $k.'='.$v."\t";
		}
		return $msg;
	}
	private function _accessFormat()
	{
		if (isset($_SERVER['REQUEST_URI']) === false) return '';
		$marr = array();
		$marr[] = $_SERVER['REQUEST_URI'];
		$marr[] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$marr[] = $_SERVER['REMOTE_ADDR'];
		$marr[] = $_SERVER['HTTP_USER_AGENT'];
		return implode("\t", $marr);
	}
	private function _getMicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	private function _convertEncoding($msg)
	{
		if ($msg === null || $msg == '') return $msg;
		try {
			$msg = @mb_convert_encoding($msg, "UTF-8", "auto");
		} catch (Exception $e) {}
		return $msg;
	}
	private function _writeTextFile($filePath, $text)
	{
		if ($text == '') return true;
		if (!($fp = @fopen($filePath, 'ab'))) return false;
		fwrite($fp, $text);
		fclose($fp);
		if ($this->_logStdout) {
			if ($this->_flush) {
				echo $text;
			} else {
				$this->_logBuffer[] = $text;
			}
		}
		//@chmod($filePath, 0666);
		return true;
	}
	private function _arrayToString($array)
	{
		if (is_array($array) === false && is_object($array) === false) return $array;
		return var_export($array, true);
//		$str = "(array) {\n";
//		foreach ($array as $k => $v) {
//			$str .= "\t${k} => ${v}\n";
//		}
//		$str .= "}\n";
//		return $str;
	}
}
?>