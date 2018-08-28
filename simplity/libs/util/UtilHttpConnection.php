<?php
class UtilHttpConnection {
	const HTTP_OK = 200;
	const CONNECT_TIMEOUT = 15;
	const STREAM_TIMEOUT = 5;
	private $fp = null;
	private $method = 'GET';
	private $requestProperty = array();
	private $headerFields = array();
	private $data = null;
	private $errorMessage = '';
	private $responseCode = 0;
	private $responseMessage = '';
	private $postData = array();
	private $isRedirect = false;
	public function init()
	{
		$this->fp = null;
		$this->method = 'GET';
		$this->requestProperty = array();
		$this->headerFields = array();
		$this->data = null;
		$this->errorMessage = '';
		$this->responseCode = 0;
		$this->responseMessage = '';
		$this->postData = array();
		$this->isRedirect = false;
	}
	public function setRequestProperty($key, $val)
	{
		$this->requestProperty[$key] = $val;
	}
	public function setMethod($method)
	{
		$this->method = $method;
	}
	public function setPostData($key, $val)
	{
		$this->postData[] = $key . '=' . urlencode($val);
	}
	public function connect($url, $isRedirect=false)
	{
		$this->isRedirect = $isRedirect;
		$m = array();
		if (!preg_match("/^(https?):\/\/([^\/]+)(.*)/i", $url, $m)) {
			return false;
		}
		$port = 80;//($m[1] == 'https') ? 443 : 80;
		$host = $m[2];
		$path = ($m[3] == '') ? '/' : $m[3];

		$this->fp = fsockopen($host, $port, $errno, $errstr, self::CONNECT_TIMEOUT);
		if (!$this->fp) {
			$this->errorMessage = $errstr."(".$errno.")";
			return false;
		}

//		stream_set_blocking($this->fp, 1);

		$out  = $this->method." ".$path." HTTP/1.1\r\n";
		$out .= "Host: ".$host."\r\n";
		if (!isset($this->requestProperty['Connection'])) {
			$this->requestProperty['Connection'] = 'Close';
		}
		$param = '';
		if ($this->method == 'POST') {
			$param = implode('&', $this->postData);
			$this->requestProperty['Content-Length'] = strlen($param);
		}
		foreach ($this->requestProperty as $key => $val) {
			if ($val == '') continue;
			$out .= $key . ": " . $val . "\r\n";
		}
		$out .= "\r\n";
		if ($this->method == 'POST') {
			$out .= $param . "\r\n";
		}
//print($out);
		fwrite($this->fp, $out);

		$header_sec = true;
		$header_status = false;

		stream_set_timeout($this->fp, self::STREAM_TIMEOUT);
		$info = stream_get_meta_data($this->fp);

		//list($usec, $sec) = explode(" ", microtime());
		//$startsec = (float)$usec + (float)$sec;

		while (!feof($this->fp)) {
			if (isset($info['timed_out']) && $info['timed_out']) break;
			$line = fgets($this->fp);
			$info = stream_get_meta_data($this->fp);
			if ($header_sec && trim($line) == '') {
				$header_sec = false;
				continue;
			}
			if ($header_sec == false) {
				$this->data .= $line;
			} else {
				if ($header_status === false) {
					if (preg_match("/HTTP\/.+ ([0-9]{3}) (.*)/i", $line, $m)) {
						$this->responseCode = (int)$m[1];
						$this->responseMessage = trim($m[2]);
						$header_status = true;
						continue;
					}
				}
				$hd = explode(':', $line, 2);
				$key = trim($hd[0]);
				$val = trim($hd[1]);
				if ($key == '' || $val == '') continue;
				$this->headerFields[$key] = $val;
			}
		}
		fclose($this->fp);
		$this->fp = null;
		if ($this->isRedirect && $this->responseCode == 301 && $this->headerFields['Location'] != '') {
			$url = $this->headerFields['Location'];
			$this->init();
			$this->connect($url);
		}
	}
	public function getHeaderFields() {
		return $this->headerFields;
	}
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}
	public function getResponseCode() {
		return $this->responseCode;
	}
	public function getResponseMessage() {
		return $this->responseMessage;
	}
	public function getData() {
		return $this->data;
	}
}
?>