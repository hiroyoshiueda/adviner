<?php
require_once SP_SMARTY_LIBS_DIR.'/Smarty.class.php';

/**
 * SpSmarty
 * @since Smarty 3.1.17
 */
class SpSmarty extends Smarty
{
	function __construct()
	{
		parent::__construct();
		$this->setTemplateDir(SP_TEMPLATE_DIR);
		$this->setCompileDir(SP_COMPILE_DIR);
		$this->addPluginsDir(SP_PLUGINS_DIR);
		// {$var nofilter}
		//$this->setDefaultModifiers(array('sp_escape'));
		$this->escape_html = true;
	}
	function addPlugins($dir)
	{
		$this->addPluginsDir($dir);
	}
}
?>