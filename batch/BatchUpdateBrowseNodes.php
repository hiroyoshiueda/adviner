<?php
Sp::import('AmazonProductAdvertisingAPI', 'libs');
/**
 * Amazon BrowseNode
 * @author Hiroyoshi
 */
class BatchUpdateBrowseNodes extends BaseBatch
{
	/**
	 * エントリーポイント
	 */
	public function run()
	{
		try
		{
			$apaAPI = new AmazonProductAdvertisingAPI();
			$apaAPI->setParams('Operation', 'BrowseNodeLookup');
			// 本:465392 > ジャンル別:465610
			$apaAPI->setParams('BrowseNodeId', '465610');
			$res = $apaAPI->getRequest();
			var_dump($res);
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		return true;
	}
}
?>
