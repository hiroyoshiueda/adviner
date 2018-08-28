<?php
/**
 * @author hiroyoshi
 */
class SpController
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
	 * @var SpForm
	 */
	protected $form;

	/**
	 * @var SpResponse
	 */
	protected $resp;

	/**
	 * @param SpLogger $logger
	 * @param DbManager $db
	 * @param SpForm $form
	 * @param SpResponse $resp
	 */
	function __construct(&$logger, &$db, &$form, &$resp)
	{
		$this->logger =& $logger;
		$this->db =& $db;
		$this->form =& $form;
		$this->resp =& $resp;
	}

	/**
	 * @return SpForm
	 */
	protected function &getForm()
	{
		return $this->form;
	}

	/**
	 * @return SpResponse
	 */
	protected function &getResponse()
	{
		return $this->resp;
	}

	/**
	 * @return DbManager
	 */
	protected function &getDb()
	{
		return $this->db;
	}
	public function execute()
	{
		return;
	}
	public function preExecute()
	{
		return;
	}
	public function postExecute()
	{
		return;
	}
	public function afterExecute()
	{
		return;
	}
}
?>
