<?php
require_once dirname(__FILE__)."/config.php";
/**
 * Simplityフレームワーク
 */
class Simplity
{
	protected static $db = null;
	protected static $includePathList = array();
	protected static $confMap = array();
	protected static $debugInfo = array();
	public static $basePath = null;
	public static $baseName = null;

	public static function init()
	{
		self::doReadConf(SP_CONF_DIR.'/simplity.conf', self::$confMap);
		if (defined('APP_CONF')) self::doReadConf(APP_CONF, self::$confMap);
		self::arrayToDefine(self::$confMap);
		if (defined('APP_DIR')) self::setIncludePath(APP_DIR, false);
		self::autoLoader(self::getConf('auto_loader_dir'), SP_DIR);
		self::autoIncluder(self::getConf('auto_includer_file'), SP_DIR);
		if (defined('APP_DIR')) {
			self::autoLoader(self::getConf('app_loader_dir'), APP_DIR);
			self::autoIncluder(self::getConf('app_includer_file'), APP_DIR);
		}
		self::setIncludePath(self::getConf('auto_includer_path'), true);
	}
	public static function execute()
	{
		/**
		 * page=/plan/frame/layout/index -> /plan/frame/PlanFrameLayoutController.php#index()
		 */
		$page = '';
		if (isset($_REQUEST[SP_PAGE_ARG])) {
			$page = trim($_REQUEST[SP_PAGE_ARG]);
//			if (preg_match("/\.(png|gif|jpe?g)$/", $page)) {
//				header("HTTP/1.0 404 Not Found");
//				exit;
//			}
			if ($page == 'favicon.ico') {
				return;
			} else if ($page != '') {
				if (substr($page, -1) == '/') $page .= 'index';
				if (substr($page, 0, 1) == '/') $page = substr($page, 1);
			}
//		} else {
//			$page = 'index/index';
		}

		$classPath = '';
		$className = '';
		$methodName = '';
		if ($page == '') {
			$classPath = '';
			$className = 'Index';
			$methodName = 'index';
			$page = 'index/index';
		} else {
			$ary = explode('/', $page);
			$length = count($ary);
			for ($i=0; $i<$length; $i++) {
				if ($ary[$i] == '') continue;
				if ($i < $length - 1) {
					// 2階層下のクラス
					//if (!($i >= 3)) $classPath .= '/'.$ary[$i];
					$classPath .= '/'.$ary[$i];
					$className .= ucfirst($ary[$i]);
				} else {
					$methodName = $ary[$i];
				}
			}
			if ($className == 'Index') $classPath = '';
		}

		$conf = Sp::getConf();

		$logger = new SpLogger($conf);
		$db = new DbManager($logger);
		$form = null;
		$resp = new SpResponse();

		$classFile = APP_DIR.'/controllers'.$classPath.'/'.$className.'Controller.php';

		$is_notfound = false;

		if (!file_exists($classFile)) {
			$className = 'Index';
			$classPath = '';
			$classFile = APP_DIR.'/controllers'.$classPath.'/'.$className.'Controller.php';
			// 指定URL(クラス)が存在しない場合
			if ($conf['app_notfound_method']!='') {
				$methodName = $conf['app_notfound_method'];
				$logger->debug('Not Found: page='.$_REQUEST[SP_PAGE_ARG]);
				$is_notfound = true;
			}
		}

		include($classFile);
		$controllerName = $className.'Controller';

		try {
			$db->initConnect($conf);
			if ($is_notfound===false) $resp->sessionStart($conf['session_use']);
			/** @var SpForm $form */
			$form =& self::getFormObject($page);
			$form->set(SpForm::ENV_CLASS_PATH, $classPath);
			$form->set(SpForm::ENV_CLASS_NAME, $className);
			$form->set(SpForm::ENV_METHOD_NAME, $methodName);
			$form->set(SpForm::ENV_PAGE_PATH, $page);
			/** @var SpController $controller */
			$controller = new $controllerName($logger, $db, $form, $resp);
			if ($controller->preExecute()!==false) {
				$controller->$methodName();
			}
			$controller->postExecute();
			if ($resp->putBasicAuth()) {
				// output start
				$resp->putCookie();
				if ($resp->putHeader()) {
					$form->setMetaTag($resp->getCacheMeta());
					$form->output();
				} else if ($form->isOutputContents()) {
					$form->output();
				}
			} else {
				$logger->error("Error is Basic Auth. ".$_SERVER['PHP_AUTH_USER']." / ".$_SERVER['PHP_AUTH_PW']);
			}
			$controller->afterExecute();
		} catch (SpException $e) {
			$logger->exception($e);
			$form->forward(array($conf['error_template'], $conf['default_template']));
			$form->output();
		}
		$logger->output();
		if ($db !== null) $db->closeConnection();

		if ($conf['debug_info']=='1' && $_SERVER['REMOTE_ADDR']==$conf['debug_addr']) {
			self::$debugInfo['className'] = $className;
			self::$debugInfo['methodName'] = $methodName;
			self::$debugInfo['classFile'] = $classFile;
			self::$debugInfo['controllerName'] = $controllerName;
			self::$debugInfo['SP_PAGE_ARG'] = $_REQUEST[SP_PAGE_ARG];
			self::$debugInfo[get_class($form).' -> form'] = $form->getAll();
			self::$debugInfo[get_class($form).' -> sp'] = $form->getSpAll();
			if (count($_SESSION)>0) self::$debugInfo['SESSION'] = $_SESSION;
			self::debugInfo();
		}
		return;
	}

	/**
	 *
	 * @param SimplityBatch $batchClass
	 * @param array $argv
	 */
	public static function executeBatch($batchClass, $argv)
	{
		$conf = Sp::getConf();

		$logger = new SpLogger($conf);
		$db = new DbManager($logger);
		// バッチはスレーブを使わない
		$conf['slave_db'] = 0;
		$db->initConnect($conf);
		try {
			$batch = new $batchClass($logger, $db, $argv);
			$batch->preRun();
			if ($batch->run() === false) {
				throw new SpException($logger->getLastErrorMessage());
			}
			$batch->postRun();
		} catch (SpException $e) {
			$batch->exceptionRun($e);
			$logger->exception($e);
		}
		$logger->output();
		if ($db !== null) $db->closeConnection();
	}
	public static function &getFormObject($page)
	{
		$obj = null;
		$ary = explode('/', $page);
		for ($i=count($ary)-1; $i>=0; $i--) {
			$path = implode('/', $ary);
			if ($path!='') $path = '/' . $path;
			$name = join('', array_map('ucfirst', $ary));
			$formClass = $name . 'Form';
			SpForm::import($path . '/'.$formClass.'.php');
			if (class_exists($formClass)) {
				$obj = new $formClass;
				return $obj;
			}
			unset($ary[$i]);
		}
		$formClass = 'IndexForm';
		SpForm::import('/'.$formClass.'.php');
		if (class_exists($formClass)) {
			$obj = new $formClass;
		} else {
			$obj = new SpForm;
		}
		return $obj;
	}
	public static function autoLoader($autoLoaderDir, $path)
	{
		if (empty($autoLoaderDir)) return;
		$dirList = explode(',', $autoLoaderDir);
		foreach ($dirList as $dir) {
			self::loadDir($path.$dir);
		}
	}
	public static function autoIncluder($autoIncluder, $path)
	{
		if (empty($autoIncluder)) return;
		$fileList = explode(',', $autoIncluder);
		foreach ($fileList as $file) {
			include_once $path.$file;
		}
	}
	public static function import($module, $dispatch=true, $include=false)
	{
		if ($dispatch === true) {
			$dir = self::_getModuleDir($module);
		} else if ($dispatch === false) {
			$dir = '';
		} else if (strpos($dispatch, 'simplity')===false) {
			$dir = APP_DIR . '/' . $dispatch;
		} else {
			$dir = $dispatch;
		}
		if (substr($module, -4) != '.php') $module .= '.php';
		self::_import($module, $dir, $include);
		return true;
	}
	public static function _import($file, $dir, $include)
	{
		if ($dir != '' && substr($file, 0, 1) != '/') $file = '/'.$file;
		if ($include !== false) {
			include_once $dir.$file;
		} else {
			require_once $dir.$file;
		}
		return true;
	}
	public static function _getModuleDir($type)
	{
		if ($type == '') return '';
		if (Util::endsWidth($type, 'Dao')) return SP_DAO_DIR;
		if (Util::endsWidth($type, 'Controller')) return SP_CONTROLLER_DIR;
		if (Util::endsWidth($type, 'Form')) return SP_FORM_DIR;
		if (Util::startsWith($type, 'Util')) return SP_LIBS_DIR.'/util';
		return SP_DIR;
	}
	public static function setErrorException()
	{
		set_error_handler(create_function('$code, $msg', 'throw new SpException($msg, $code);'), E_ALL & ~E_NOTICE);
	}
//	public static function readConf($filePath)
//	{
//		self::readConf($filePath, self::$confMap);
//	}
	public static function getConf($key=null)
	{
		if ($key === null) {
			return self::$confMap;
		} else {
			return self::$confMap[$key];
		}
	}
	public static function setConf($key, $value)
	{
		self::$confMap[$key] = $value;
	}
	public static function setIncludePath($path = null, $set = true)
	{
		if ($path === null) self::$includePathList = explode(PATH_SEPARATOR, ini_get('include_path'));
		else if ($path == '') return;
		else self::$includePathList[] = $path;
		if ($set === false) return;
		ini_set('include_path', implode(PATH_SEPARATOR, self::$includePathList));
	}
	public static function loadDir($dir)
	{
		$fileType = "\\.php$";
		if (is_readable($dir) && is_dir($dir)) {
			if ($handle = opendir($dir)){
				while ($require = readdir($handle)) {
					if (preg_match("/$fileType/", $require)) {
						include_once($dir.'/'.$require);
					}
				}
				closedir($handle);
			}
		}
	}
	public static function doReadConf($filePath, &$map, $sep='=')
	{
		if (file_exists($filePath) === false) return;
		$lines = file($filePath);
		foreach ($lines as $line) {
			$line = ltrim($line);
			if ($line == '' || substr($line, 0, 1) == '#') continue;
			list($key, $val) = explode($sep, $line, 2);
			$key = trim($key);
			$val = trim($val);
			if (preg_match("/\{([^\}]+)\}/", $val, $m)) {
				$rep = defined($m[1]) ? constant($m[1]) : '';
				$val = str_replace('{'.$m[1].'}', $rep, $val);
			}
			$map[$key] = $val;
		}
		return;
	}
	public static function arrayToDefine(&$map)
	{
		foreach ($map as $key => $val) {
			if (defined($key) === false) define($key, $val);
		}
	}
	public static function debugInfo($debugInfo=null, $is_print=false)
	{
		if ($debugInfo===null) $debugInfo = self::$debugInfo;
		if (count($debugInfo)>0) {
			if ($is_print === false) {
				$hdrs = headers_list();
				foreach ($hdrs as $h) {
					if (preg_match("/^content\-type: ?text\/html/i", $h)) {
						$is_print = true;
						break;
					}
				}
			}
			if ($is_print) {
				echo "<table style=\"margin:15px;background-color:#fff;\">";
				foreach ($debugInfo as $k => $v) {
					echo "<tr>";
					echo "<td style=\"border:1px solid #ccc\">${k}</td>";
					echo "<td style=\"border:1px solid #ccc\">";
					if (is_object($v)) {
						var_dump($v);
					} else if (is_array($v)) {
						self::debugInfo($v, true);
					} else {
						echo htmlspecialchars($v);
					}
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
		}
	}
}

class Sp extends Simplity
{

}

abstract class SimplityBatch
{
	/**
	 * @var SpLogger
	 */
	protected $logger;

	/**
	 * @var DbManager
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $argv;

	function __construct(&$logger, &$db, &$argv)
	{
		$this->logger =& $logger;
		$this->db =& $db;
		$this->argv =& $argv;
	}
	public function run()
	{
		return true;
	}
	public function preRun()
	{
		return true;
	}
	public function postRun()
	{
		return true;
	}
	/**
	 *
	 * @param $e SpException
	 * @return boolean
	 */
	public function exceptionRun($e)
	{
		return true;
	}
}

function ddump($var)
{
	var_dump($var);
}

function println($var)
{
	if (php_sapi_name() == 'cli') {
		print($var."\n");
	} else {
		print($var."\n--><br>");
	}
}
?>