<?php
class SpResponse {

	const SC_NOT_FOUND = 404;
	const CTYPE_HTML = 'text/html';
	const CTYPE_XML = 'text/xml';
	const CTYPE_JSON = 'application/json';
	const CTYPE_RSS = 'application/rss+xml';

	private $headers = array();
	private $cookies = array();
	private $location = null;
	private $status = 200;
	private $statusMsg = array(401=>'Unauthorized', 404=>'Not Found', 500=>'Internal Server Error');
	private $cacheLevel = 0;
	private $cacheMeta = array();
	private $basicAuth = false;
	private $basicUser = '';
	private $basicPass = '';
	private $basicRealm = '';

	function __construct()
	{
		$this->setContentType(self::CTYPE_HTML, 'utf-8');
	}

	public function setHeader($name, $value, $rep=true)
	{
		if ($rep === false) {
			if (isset($this->headers[$name])) {
				$this->headers[$name] .= "\n".$value;
				return;
			}
		}
		$this->headers[$name] = $value;
	}
	public function putHeader()
	{
		if ($this->status != 200) {
			if (PHP_SAPI == 'cgi-fcgi') {
				header("Status: ".$this->status." ".$this->statusMsg[$this->status]);
			} else {
				header("HTTP/1.0 ".$this->status." ".$this->statusMsg[$this->status]);
			}
			return false;
		} else if ($this->location !== null) {
			header("Location:".$this->location);
			return false;
		}
		if (count($this->headers) == 0) return true;
		foreach ($this->headers as $n => $v) {
			if ($v == '') {
				header($n);
			} else {
				$vary = explode("\n", $v);
				if (count($vary) == 1) {
					header($n.':'.$v);
				} else {
					foreach ($vary as $vv) {
						header($n.':'.$vv, false);
					}
				}
			}
		}
		return true;
	}
	public function setCookie($name, $value=null, $expire=0, $path=null, $domain=null, $secure=false) {
		$this->cookies[$name] = array(
			'name' => $name,
			'value' => $value,
			'expire' => $expire,
			'path' => $path,
			'domain' => $domain,
			'secure' => $secure
		);
	}
	public function putCookie() {
		if (count($this->cookies) == 0) return;
		foreach ($this->cookies as $n => $a) {
			setcookie($a['name'], $a['value'], $a['expire'], $a['path'], $a['domain'], $a['secure']);
		}
	}
	public function sendRedirect($location) {
		if (!preg_match("/^https?:/i", $location)) {
			$prot = ($_SERVER['HTTPS']) ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			if (substr($location, 0, 1) == '/') {
				$path = '';
			} else {
				//$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$path = $_SERVER['REQUEST_URI'];
				if (substr($path, -1, 1) != '/') {
					$path = dirname($path).'/';
				}
			}
			$location = $prot.'://'.$host.$path.$location;
		}
		$this->location = $location;
	}
	public function setBasicAuth($user, $pass, $realm='Admin Area')
	{
		if ($user=='') return;
		$this->basicAuth = true;
		$this->basicUser = $user;
		$this->basicPass = $pass;
		$this->basicRealm = $realm;
	}
	public function putBasicAuth()
	{
		if ($this->basicAuth===false) return true;
		if (!isset($_SERVER['PHP_AUTH_USER']) || ($_SERVER['PHP_AUTH_USER']!=$this->basicUser || $_SERVER['PHP_AUTH_PW']!=$this->basicPass)) {
			header('WWW-Authenticate: Basic realm="'.$this->basicRealm.'"');
			header('HTTP/1.0 401 Unauthorized');
			return false;
		}
		return true;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}
	public function setContentType($type, $charset='utf-8')
	{
		$this->setHeader('Content-Type', $type.'; charset='.$charset);
	}
	public function sessionStart($session_use)
	{
		if ($session_use != '1') return true;
		// use memcached
		if (Sp::getConf('session_save_handler') == 'memcache') {
			ini_set('session.save_handler', 'memcache');
			ini_set('session.save_path', Sp::getConf('session_save_path'));
		} else {
			$name = Sp::getConf('session_name');
			if ($name != '') session_name($name);
			$save_path = Sp::getConf('session_save_path');
			if ($save_path != '') {
				if (file_exists($save_path) === false) mkdir($save_path);
				session_save_path($save_path);
			}
		}

		$lifetime = (int)Sp::getConf('session_lifetime');
		$path = Sp::getConf('session_path');
		$domain = Sp::getConf('session_domain');
		$secure = Sp::getConf('session_secure');
//		session_set_cookie_params($lifetime, $path, $domain);
		ini_set('session.cookie_lifetime', $lifetime);
		if ($path != '') ini_set('session.cookie_path', $path);
		if ($domain != '') ini_set('session.cookie_domain', $domain);
		if ($secure != '') ini_set('session.cookie_secure', ($secure ? true : false));

		if (isset($_REQUEST[SP_GET_SESSID]) && $_REQUEST[SP_GET_SESSID] != '') {
			session_id($_REQUEST[SP_GET_SESSID]);
		}

		return session_start();
	}
	public function sessionEnd()
	{
		return session_destroy();
	}
	public function sessionId()
	{
		return session_id();
	}
	public function sessionName()
	{
		return session_name();
	}
	public function sessionChangeId($delete_old=true)
	{
		return @session_regenerate_id($delete_old);
	}
	public function noCache($level=10, $meta=true)
	{
		$this->cacheLevel = $level;
		$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
//		$this->setHeader('Cache-Control', 'no-cache, must-revalidate, post-check=0, pre-check=0');
//		$this->setHeader('Cache-Control', 'post-check=0, pre-check=0', false);
		$this->setHeader('Pragma', 'no-cache');
		$this->setHeader('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT');
		if ($meta === false) return;
		$this->cacheMeta[] = array('equiv' => 'Pragma', 'content' => 'no-cache');
		$this->cacheMeta[] = array('equiv' => 'Cache-Control', 'content' => 'no-cache');
		$this->cacheMeta[] = array('equiv' => 'Expires', 'content' => 'Thu, 19 Nov 1981 08:52:00 GMT');
	}
	public function getCacheMeta()
	{
		return $this->cacheMeta;
	}
}
?>