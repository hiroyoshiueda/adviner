<?php
Sp::import('list/ListController', 'controllers');
/**
 * おすすめの本(Controller)
 * @author Hiroyoshi
 */
class RssController extends ListController
{
	/**
	 * 人気本
	 */
	public function popular()
	{
		$this->_getList('popular');

		$this->resp->setContentType(SpResponse::CTYPE_RSS);

		return $this->forward('rss/rss_rss20', APP_CONST_EMPTY_FRAME);
	}

	/**
	 * 新着本
	 */
	public function newarrivals()
	{
		$this->_getList('newarrivals');

		$this->resp->setContentType(SpResponse::CTYPE_RSS);

		return $this->forward('rss/rss_rss20', APP_CONST_EMPTY_FRAME);
	}
}
?>
