<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('FeedsDao', 'dao');
/**
 * Followストリーム用
 */
class FollowStream
{
	/**
	 * @var DbManager
	 */
	private $db = null;

	/**
	 * @var SpLogger
	 */
	private $logger = null;

	private $userInfo = array();
	private $followData = array();
	private $newIds = array();
	private $list = array();
	private $userIds = array();
	private $adviceIds = array();
	private $consultIds = array();
	private $replyIds = array();
	private $reviewIds = array();
	private $is_all = false;

	function __construct(&$db, &$logger, $userInfo, $followData, $is_all=false)
	{
		$this->db =& $db;
		$this->logger =& $logger;
		$this->is_all = ($is_all === false) ? false : true;
		if ($this->is_all) {
			$this->userInfo = array();
			$this->followData = array();
		} else {
			$this->userInfo =& $userInfo;
			$this->followData =& $followData;
		}
	}

	public function load($is_default=true)
	{
		$user_id = empty($this->userInfo) ? 0 : $this->userInfo['id'];

		$FeedsDao = new FeedsDao($this->db);
		$latestdate = $FeedsDao->getLatestDate($user_id);
		// デフォルトは60日前
		if (empty($latestdate) && $is_default) $latestdate = date('Y-m-d H:i:s', time() - (86400 * 60));
		if (empty($latestdate)) return true;

		// 相談窓口
		try
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$list = $AdvicesDao->getFeedListByFollowData($this->followData, $latestdate, 100, $this->is_all);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($user_id, $d, $d['createdate']);
					$this->db->commit();
				}
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		// 相談（アドバイスくださいのみ）
		try
		{
			$ConsultsDao = new ConsultsDao($this->db);
			$list = $ConsultsDao->getFeedListByFollowData($this->followData, $latestdate, 200, $this->is_all);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($user_id, $d, $d['createdate']);
					$this->db->commit();
				}
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		// 返信
		try
		{
			$ConsultReplysDao = new ConsultReplysDao($this->db);
			$list = $ConsultReplysDao->getFeedListByFollowData($this->followData, $latestdate, 200, $this->is_all);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($user_id, $d, $d['createdate']);
					$this->db->commit();
				}
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}


		// 評価
		try
		{
			$ConsultReviewsDao = new ConsultReviewsDao($this->db);
			$list = $ConsultReviewsDao->getFeedListByFollowData($this->followData, $latestdate, 200, $this->is_all);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($user_id, $d, $d['createdate']);
					$this->db->commit();
				}
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		return true;
	}

	public function getPageList(&$total, $offset, $limit)
	{
		if ($this->is_all) {
			$user_id = 0;
		} else {
			if (empty($this->userInfo) || empty($this->followData)) return null;
			$user_id = $this->userInfo['id'];
		}
		$FeedsDao = new FeedsDao($this->db);
		$this->list = $FeedsDao->getPageList($total, $offset, $limit, $user_id);

		return $this->list;
	}

	public function getNewList($last_datetime, $last_key)
	{
		if ($this->is_all) {
			$user_id = 0;
		} else {
			if (empty($this->userInfo) || empty($this->followData)) return null;
			$user_id = $this->userInfo['id'];
		}
		$FeedsDao = new FeedsDao($this->db);
		if ($last_datetime != '' && $last_key != '')
		{
			$FeedsDao->addWhereStr(FeedsDao::COL_CREATEDATE, $last_datetime, '>');
			$list = $FeedsDao->getNewListByIds($user_id);

			if (count($list) > 0) {
				$this->list = array();
				list($advice_id, $consult_id, $consult_reply_id, $consult_review_id) = explode('_', $last_key, 4);
				foreach ($list as $d) {
					if ($d['advice_id'] == $advice_id
						&& $d['consult_id'] == $consult_id
						&& $d['consult_reply_id'] == $consult_reply_id
						&& $d['consult_review_id'] == $consult_review_id) {
						break;
					}
					$this->list[] = $d;
				}
			}
		} else {
			$this->list = $FeedsDao->getNewListByIds($user_id);
		}

		return $this->list;
	}

	public function relatedItems()
	{
		if (count($this->list) > 0)
		{
			$user_ids = array();

			$this->reviewIds = Util::arraySelectKey('consult_review_id', $this->list);
			if (count($this->reviewIds) > 0) {
				$review_user_ids = Util::arraySelectKey('consult_review_user_id', $this->list);
				//$user_ids = array_merge($user_ids, $review_user_ids);
				$user_ids = $review_user_ids;
			}

			$this->replyIds = Util::arraySelectKey('consult_reply_id', $this->list);
			if (count($this->replyIds) > 0) {
				$reply_user_ids = Util::arraySelectKey('consult_reply_user_id', $this->list);
				$to_user_ids = Util::arraySelectKey('to_user_id', $this->list);
				$user_ids = array_merge($user_ids, $reply_user_ids, $to_user_ids);
			}

			$this->consultIds = Util::arraySelectKey('consult_id', $this->list);
			if (count($this->consultIds) > 0) {
				$consult_user_ids = Util::arraySelectKey('consult_user_id', $this->list);
				$user_ids = array_merge($user_ids, $consult_user_ids);
			}

			$this->adviceIds = Util::arraySelectKey('advice_id', $this->list);
			if (count($this->adviceIds) > 0) {
				$advice_user_ids = Util::arraySelectKey('advice_user_id', $this->list);
				$user_ids = array_merge($user_ids, $advice_user_ids);
			}

			$this->userIds = array_unique($user_ids);
		}
		return;
	}

	public function getUserSet()
	{
		$UsersDao = new UsersDao($this->db);
		$list = $UsersDao->getUserList($this->userIds);
		return Util::arrayKeyData('user_id', $list);
	}

	public function getAdviceSet()
	{
		$AdvicesDao = new AdvicesDao($this->db);
		$list = $AdvicesDao->getAdviceList($this->adviceIds);
		return Util::arrayKeyData('advice_id', $list);
	}

	public function getConsultSet()
	{
		$ConsultsDao = new ConsultsDao($this->db);
		$list = $ConsultsDao->getConsultList($this->consultIds);
		return Util::arrayKeyData('consult_id', $list);
	}

	public function getReplySet()
	{
		$ConsultReplysDao = new ConsultReplysDao($this->db);
		$list = $ConsultReplysDao->getReplyList($this->replyIds);
		return Util::arrayKeyData('consult_reply_id', $list);
	}

	public function getReviewSet()
	{
		$ConsultReviewsDao = new ConsultReviewsDao($this->db);
		$list = $ConsultReviewsDao->getReviewList($this->reviewIds);
		return Util::arrayKeyData('consult_review_id', $list);
	}

	public function getNewIds()
	{
		return $this->newIds;
	}

	public function getAdviceIds()
	{
		return $this->adviceIds;
	}

	public function getConsultIds()
	{
		return $this->consultIds;
	}
}
?>
