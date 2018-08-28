<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('FeedsDao', 'dao');
/**
 * あなたの相談
 */
class ActionStream
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

	function __construct(&$db, &$logger, &$userInfo)
	{
		$this->db =& $db;
		$this->logger =& $logger;
		$this->userInfo =& $userInfo;
	}

	public function load($is_default=true)
	{
		if (empty($this->userInfo) || empty($this->followData)) return true;

		$userInfo = $this->userInfo;

		$FeedsDao = new FeedsDao($this->db);
		$latestdate = $FeedsDao->getLatestDate($userInfo['id']);
		// デフォルトは30日前
		if (empty($latestdate) && $is_default) $latestdate = date('Y-m-d H:i:s', time() - (86400 * 30));
		if (empty($latestdate)) return true;

		// 相談窓口
		try
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$list = $AdvicesDao->getFeedListByFollowData($this->followData, $latestdate, 100);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($userInfo['id'], $d, $d['createdate']);
					$this->db->commit();
				}
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
		}

		// @todo 今のところ返信がない限り公開されないので単独では表示されない
//		// 相談
//		try
//		{
//			$consultsDao = new ConsultsDao($this->db);
//			$list = $consultsDao->getFeedListByFollowData($this->followData, $latestdate, 100);
//			if (count($list)>0)
//			{
//				foreach ($list as $d)
//				{
//					$this->db->beginTransaction();
//					$this->newIds[] = $FeedsDao->saveStream($userInfo['id'], $d, $d['createdate']);
//					$this->db->commit();
//				}
//			}
//		}
//		catch (SpException $e)
//		{
//			$this->logger->exception($e);
//		}
//		// 自分への相談
//		try
//		{
//			$consultsDao = new ConsultsDao($this->db);
//			$list = $consultsDao->getFeedListByConsulted($userInfo['id'], $latestdate, 100);
//			if (count($list)>0)
//			{
//				foreach ($list as $d)
//				{
//					$this->db->beginTransaction();
//					$this->newIds[] = $FeedsDao->saveStreamForUpdate($userInfo['id'], $d, $d['replydate']);
//					$this->db->commit();
//				}
//			}
//		}
//		catch (SpException $e)
//		{
//			$this->logger->exception($e);
//		}

		// 返信
		try
		{
			$ConsultReplysDao = new ConsultReplysDao($this->db);
			$list = $ConsultReplysDao->getFeedListByFollowData($this->followData, $latestdate, 200);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($userInfo['id'], $d, $d['createdate']);
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
			$list = $ConsultReviewsDao->getFeedListByFollowData($this->followData, $latestdate, 100);
			if (count($list)>0)
			{
				foreach ($list as $d)
				{
					$this->db->beginTransaction();
					$this->newIds[] = $FeedsDao->saveStream($userInfo['id'], $d, $d['createdate']);
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
		if (empty($this->userInfo)) return null;

		$ConsultsDao = new ConsultsDao($this->db);
		$this->list = $ConsultsDao->getMyActionPageList($total, $offset, $limit, $this->userInfo['id']);

		return $this->list;
	}

	public function getNewList()
	{
		if (empty($this->userInfo) || empty($this->newIds)) return null;

		$FeedsDao = new FeedsDao($this->db);
		$this->list = $FeedsDao->getNewListByIds($this->userInfo['id'], $this->newIds);

		return $this->list;
	}

	public function relatedItems()
	{
		if (count($this->list) > 0)
		{
			$user_ids = array();

			$this->adviceIds = Util::arraySelectKey('action_id', $this->list);

			$this->consultIds = Util::arraySelectKey('consult_id', $this->list);

			$consult_user_ids = Util::arraySelectKey('consult_user_id', $this->list);
			$user_ids = array_merge($user_ids, $consult_user_ids);

			$advice_user_ids = Util::arraySelectKey('advice_user_id', $this->list);
			$user_ids = array_merge($user_ids, $advice_user_ids);

			$this->userIds = $user_ids;
		}
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
		$list = $ConsultReplysDao->getListByConsultIds($this->consultIds);
		return Util::arrayKeyData('consult_id,consult_reply_id', $list);
	}

	public function getReviewSet()
	{
		$ConsultReviewsDao = new ConsultReviewsDao($this->db);
		$list = $ConsultReviewsDao->getListByConsultIds($this->consultIds);
		return Util::arrayKeyData('consult_id,consult_review_id', $list);
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
