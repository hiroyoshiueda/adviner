<?php
//define('DIR_SEP', DIRECTORY_SEPARATOR);
define('SP_DIR', dirname(__FILE__));

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.SP_DIR);

define('SP_LIBS_DIR', SP_DIR.'/libs');
define('SP_CONF_DIR', SP_DIR.'/conf');
define('SP_PLUGINS_DIR', SP_LIBS_DIR.'/smarty/plugins');
define('SP_SMARTY_LIBS_DIR', SP_DIR.'/Smarty-3.1.7/libs');

define('SP_FORM_DIR', APP_DIR.'/form');
define('SP_DB_DIR', APP_DIR.'/db');
define('SP_DAO_DIR', APP_DIR.'/dao');
define('SP_CONTROLLER_DIR', APP_DIR.'/controllers');
define('SP_TEMPLATE_DIR', APP_DIR.'/templates');
define('SP_COMPILE_DIR', APP_DIR.'/templates_c');

define('SP_PAGE_ARG', 'page');
define('SP_ERROR_TEMPLATE', 'common/error');
define('SP_DEFAULT_TEMPLATE', 'common/default_page');
define('SP_GET_SESSID', 'SPSESSID');

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
//mb_detect_order("ASCII,JIS,UTF-8,eucjp-win,sjis-win");
mb_detect_order("ASCII,sjis-win,UTF-8,JIS,eucjp-win");
date_default_timezone_set('Asia/Tokyo');

?>