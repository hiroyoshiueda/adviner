<?php
Sp::import('Validate', SP_LIBS_DIR);
Sp::import('ValidateException', SP_LIBS_DIR);
/**
 * Enter description here...
 *
 */
class SpForm
{
//	const ERROR_MESSAGE = '__ERROR_MESSAGE__';
//	const JS_ALERT = '__JS_ALERT__';
	const ENV_CLASS_PATH = 'env_class_path';
	const ENV_CLASS_NAME = 'env_class_name';
	const ENV_METHOD_NAME = 'env_method_name';
	const ENV_PAGE_PATH = 'env_page_path';
	const CAST_INT = 'int';
	const CAST_FLOAT = 'float';
	const OUTPUT_CHARSET_UTF8 = 'UTF-8';
	const OUTPUT_CHARSET_SJIS = 'sjis-win';
	protected $f = array();
	protected $sp = array();
	protected $styles = array();
	protected $scripts = array();
	protected $footer_scripts = array();
	protected $meta_tags = array();
	protected $smarty_plugins = array();
	protected $template_dir = '';
	protected $compile_dir = '';
	protected $template_name = '';
	protected $template_base = '';
	protected $base_url = '';
	protected $page_url = '';
	protected $validates = array();
	protected $validate_errors = array();
	protected $parameters = array();
	protected $parameter_str = '';
	protected $uniforms = array();
	protected $names = array();
	protected $output_charset = '';
	protected $title = '';

	function __construct()
	{
		if (is_array($_REQUEST) && count($_REQUEST) > 0) {
			SpFilter::execute($_REQUEST);
			$this->f = $_REQUEST;

		}
		if (is_array($_SESSION) && count($_SESSION) > 0) {
			SpFilter::execute($_SESSION);
			foreach ($_SESSION as $k => $v) {
				$this->f[$k] = $v;
			}
		}
		SpFilter::execute($_COOKIE);

		$this->page_url = $_SERVER['REQUEST_URI'];
		$pos = strpos($this->page_url, '?');
		if ($pos === false) {
			$this->base_url = $this->page_url;
		} else {
			$this->base_url = substr($this->page_url, 0, $pos);
		}
	}
	public static function import($file)
	{
		if (Util::isFile(SP_FORM_DIR.$file) === false) return;
		include_once SP_FORM_DIR.$file;
	}
	public function set($key, $var)
	{
		$this->f[$key] = $var;
		return;
	}
	public function add($key, $var)
	{
		$this->f[$key] = (isset($this->f[$key])) ? $this->f[$key].$var : $var;
	}
	public function setAll(&$array)
	{
		if (count($array)==0) return;
		foreach ($array as $key => $var) {
			$this->f[$key] = $var;
		}
		return;
	}
	public function setDefault($key, $var)
	{
		if (isset($this->f[$key]) === false || Util::isEmpty($this->f[$key])) {
			$this->f[$key] = $var;
		}
		return;
	}
	public function setDefaultAll(&$array)
	{
		if (count($array)==0) return;
		foreach ($array as $key => $var) {
			$this->setDefault($key, $var);
		}
		return;
	}
	public function get($key, $default='') {
		if (isset($this->f[$key]) === false) return $default;
		//if (Util::isEmpty($this->f[$key]) && $default !== null) return $default;
		return $this->f[$key];
	}
	public function getInt($key, $default=0) {
		if (isset($this->f[$key]) === false) return $default;
		//if (Util::isEmpty($this->f[$key])) return $default;
		return (int)$this->f[$key];
	}
	public function &getAll()
	{
		return $this->f;
	}
	public function getToArray($keys)
	{
		$arr = array();
		foreach ($keys as $key) {
			$arr[$key] = $this->f[$key];
		}
		return $arr;
	}
	public function getToString($key, $sep=",")
	{
		if (isset($this->f[$key])) {
			return (is_array($this->f[$key])) ? implode($sep, $this->f[$key]) : (string)$this->f[$key];
		}
		return '';
	}
	public function getToStringEnclose($key, $open, $end)
	{
		$str = '';
		if (isset($this->f[$key])) {
			$str = '';
			if (is_array($this->f[$key])) {
				foreach ($this->f[$key] as $val) {
					$str .= $open . $val . $end;
				}
			} else {
				$str = $open . $this->f[$key] . $end;
			}
		}
		return $str;
	}
	public function setSp($key, $var)
	{
		$this->sp[$key] = $var;
	}
	public function getSp($key)
	{
		return $this->sp[$key];
	}
	public function &getSpAll()
	{
		return $this->sp;
	}
	public function getCookie($key, $default='')
	{
		if (isset($_COOKIE[$key]) === false) return $default;
		return $_COOKIE[$key];
	}
	public function getCookieInt($key, $default=0)
	{
		if (isset($_COOKIE[$key]) === false) return $default;
		return (int)$_COOKIE[$key];
	}
	public function getClassPath()
	{
		return $this->get(self::ENV_CLASS_PATH);
	}
	public function getClassName()
	{
		return $this->get(self::ENV_CLASS_NAME);
	}
	public function getPagePath()
	{
		return $this->get(self::ENV_PAGE_PATH);
	}
	public function setSession($key, $var)
	{
		$_SESSION[$key] = $var;
		$this->set($key, $var);
	}
	public function clearSession($key)
	{
		unset($_SESSION[$key], $this->f[$key]);
	}
	public function isPostMethod()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'POST');
	}
	public function isGetMethod()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'GET');
	}
	public function forward($forward)
	{
		$this->setTemplateName($forward[0]);
		if (empty($forward[1])) $forward[1] = $forward[0];
		$this->setTemplateBase($forward[1]);
		return $forward[1];
	}
	public function setTemplateDir($template_dir)
	{
		$this->template_dir = $template_dir;
	}
	public function setCompileDir($compile_dir)
	{
		$this->compile_dir = $compile_dir;
	}
	public function setTemplateName($template_name)
	{
		$this->template_name = $template_name;
	}
	public function setTemplateBase($template_base)
	{
		$this->template_base = $template_base;
	}
//	public function setErrorMsg($msg, $useJsAlert=false)
//	{
//		$this->f[self::ERROR_MESSAGE] = $msg;
//		$this->f[self::JS_ALERT] = ($useJsAlert) ? 1 : 0;
//	}
	public function setValidateToErrorMsg()
	{
		$err = array();
		if ($this->isValidateErrors()) {
			foreach ($this->validate_errors as $msgs) {
				if (is_array($msgs)) {
					foreach ($msgs as $msg) {
						$err[] = $msg;
					}
				}
			}
			if (count($err) > 0) $this->setErrorMsg($err);
		}
		return;
	}
	public function setStyleSheet($href, $ver='')
	{
		if ($ver != '') $ver = '?_='.$ver;
		$this->styles[] = array('href' => $href, 'ver' => $ver);
	}
	public function setScript($src, $code='', $ver='')
	{
		if ($ver != '') $ver = '?_='.$ver;
		$this->scripts[] = array('src' => $src, 'code' => $code, 'ver' => $ver);
	}
	public function setFooterScript($src, $code='', $ver='')
	{
		if ($ver != '') $ver = '?_='.$ver;
		$this->footer_scripts[] = array('src' => $src, 'code' => $code, 'ver' => $ver);
	}
	public function setMetaTag($tags)
	{
		$this->meta_tags = $tags;
	}
	public function setSmartyPlugins($dir)
	{
		$this->smarty_plugins[] = $dir;
	}
	public function setTitle($title)
	{
		$this->title = $title;
	}
	public function setBaseUrl($url)
	{
		$this->base_url = $url;
	}
	public function getBaseUrl()
	{
		return $this->base_url;
	}
	public function getPageUrl()
	{
		return $this->page_url;
	}
	public function getFullUrl()
	{
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
		return $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	}
	public function setParameter($key, $value)
	{
		$this->parameters[] = array('key' => $key, 'value' => $value);
	}
	public function setParameterForm($key)
	{
		$this->parameters[] = array('key' => $key, 'value' => $this->f[$key]);
	}
	public function getParameterQueryString($is_empty=false)
	{
		if (count($this->parameters)==0) return '';
		$qstr = '';
		foreach ($this->parameters as $d) {
			if ($qstr != '') $qstr .= '&';
			if ($is_empty === false) $qstr .= $d['key'].'='.$d['value'];
			else if ($d['value'] != '') $qstr .= $d['key'].'='.$d['value'];
		}
		return $qstr;
	}
	public function outputCharset($charset)
	{
		$this->output_charset = $charset;
	}
	public function isOutputContents()
	{
		return ($this->template_name != '');
	}
	public function output()
	{
		$smarty = new SpSmarty();
		if ($this->template_dir!='') $smarty->template_dir = $this->template_dir;
		if ($this->compile_dir!='') $smarty->compile_dir = $this->compile_dir;
		if (count($this->smarty_plugins) > 0) {
			foreach ($this->smarty_plugins as $dir) {
				$smarty->addPlugins($dir);
			}
		}
		$smarty->assign('form', $this->f);

		$this->sp['title'] = $this->title;
		$this->sp['meta_keywords'] = isset($this->f['meta_keywords']) ? $this->f['meta_keywords'] : '';
		$this->sp['meta_description'] = isset($this->f['meta_description']) ? $this->f['meta_description'] : '';
		$this->sp['styles'] = $this->styles;
		$this->sp['scripts'] = $this->scripts;
		$this->sp['footer_scripts'] = $this->footer_scripts;
		$this->sp['meta_tags'] = $this->meta_tags;
		$this->sp['parameters'] = $this->parameters;
		$this->sp['base_url'] = $this->getBaseUrl();
		$this->sp['page_url'] = $this->getPageUrl();
		$this->sp['full_url'] = $this->getFullUrl();
		if (Sp::getConf('session_use') == '1') $this->sp['SPSID'] = 'SPSID='.session_id();
		$this->sp['page_template'] = $this->template_name.'.tpl';
		$this->sp['base_template'] = $this->template_base.'.tpl';

		foreach ($this->sp as $k => $v) {
			$smarty->assign($k, $v);
		}

//		if (Util::isEmpty($this->f[self::ERROR_MESSAGE]) === false) {
//			$error_template = ($this->f[self::JS_ALERT] == 1) ? 'error_alert.tpl' : 'error_msg.tpl';
//			$smarty->assign('error_template', $error_template);
//			if (is_array($this->f[self::ERROR_MESSAGE])) $this->f[self::ERROR_MESSAGE] = implode("\n", $this->f[self::ERROR_MESSAGE]);
//			$smarty->assign('error_message', $this->f[self::ERROR_MESSAGE]);
//		}

		if ($this->output_charset=='') {
			$smarty->display($this->template_base.'.tpl');
		} else {
			mb_http_output($this->output_charset);
			ob_start("mb_output_handler");
			$smarty->assign('__output_charset__', $this->output_charset);
			$smarty->display($this->template_base.'.tpl');
			ob_end_flush();
		}
	}
	public function getContents()
	{
		ob_start();
		$this->output();
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	public function getTemplateContents(&$tpl_vars, $template, $page_template='')
	{
		$smarty = new SpSmarty();
		if ($this->template_dir!='') $smarty->template_dir = $this->template_dir;
		if ($this->compile_dir!='') $smarty->compile_dir = $this->compile_dir;
		if (count($this->smarty_plugins) > 0) {
			foreach ($this->smarty_plugins as $dir) {
				$smarty->addPlugins($dir);
			}
		}
		$smarty->assign($tpl_vars);
		if ($page_template!='') $smarty->assign('page_template', $page_template.'.tpl');
		return $smarty->fetch($template.'.tpl');
	}
	public function getValidates($key)
	{
		if (isset($this->validates[$key])) return $this->validates[$key];
		return array();
	}
	public function validate($validates)
	{
		$is_ok = true;
		if (count($validates) == 0) return $is_ok;
		foreach ($validates as $valid) {
			$key = $valid[0];
			$name = $valid[1];
			$type = $valid[2];
			$opt = isset($valid[3]) ? $valid[3] : '';
			if (Validate::$type($this->f[$key], $opt) === false) {
				if (!isset($this->validate_errors[$key])) $this->validate_errors[$key] = array();
				$this->validate_errors[$key][] = $name;
				$is_ok = false;
			}
		}
		return $is_ok;
	}
	public function setValidateErrors($key, $msg)
	{
		if (!isset($this->validate_errors[$key])) $this->validate_errors[$key] = array();
		$this->validate_errors[$key][] = $msg;
	}
	public function getValidateErrors()
	{
		return $this->validate_errors;
	}
	public function isValidateErrors()
	{
		return (count($this->validate_errors) > 0);
	}
	public function initValidateErrors()
	{
		$this->validate_errors = array();
	}

	/**
	 * 入力値の変換
	 * int -> intval
	 * float -> floatval
	 * md5 -> 32桁の英数字
	 * a -> 「全角」英数字を「半角」に変換します。
	 * K -> 「半角カタカナ」を「全角カタカナ」に変換します。
	 * H -> 「半角カタカナ」を「全角ひらがな」に変換します。
	 * V -> 濁点付きの文字を一文字に変換します。"K", "H" と共に使用します。
	 * @param unknown_type $uniforms
	 * @return unknown_type
	 */
	public function uniform($uniforms)
	{
		if (empty($uniforms) || count($uniforms) == 0) return;
		foreach ($uniforms as $col => $opt) {
			if (isset($this->f[$col]) && $opt!='') $this->f[$col] = SpFilter::uniform($this->f[$col], $opt);
		}
		return;
	}
	public function getUniforms($key)
	{
		if (isset($this->uniforms[$key])) return $this->uniforms[$key];
		return array();
	}
	public function getNames($key)
	{
		if (isset($this->names[$key])) return $this->names[$key];
		return array();
	}
	public function getValues($names)
	{
		$values = array();
		foreach ($names as $name) {
			if (isset($this->f[$name])) {
				if (is_array($this->f[$name])) {
					$values[$name] = implode(',', $this->f[$name]);
				} else {
					$values[$name] = $this->f[$name];
				}
			}
		}
		return $values;
	}
}
?>