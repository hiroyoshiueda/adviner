<?php
class BaseBatch extends SimplityBatch
{
	public function preRun()
	{
		return true;
	}
	public function postRun()
	{
		return true;
	}
	public function exceptionRun($e)
	{
		$this->logger->exception($e);
		return true;
	}
}
?>