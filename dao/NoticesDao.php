<?php
/**
 * 通知管理
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class NoticesDao extends BaseDao
{
	const TABLE_NAME = 'notices';

const COL_NOTICE_ID = "notice_id";
const COL_USER_ID = "user_id";
const COL_STATUS = "status";
const COL_NOTICE_TYPE = "notice_type";
const COL_FROM_USER_ID = "from_user_id";
const COL_FROM_USERNAME = "from_username";
const COL_ADVICE_ID = "advice_id";
const COL_ADVICE_TITLE = "advice_title";
const COL_CONSULT_ID = "consult_id";
const COL_POST_BODY = "post_body";
const COL_REPLY_BODY = "reply_body";
const COL_CREATEDATE = "createdate";

	const STATUS_UNREAD = 0;
	const STATUS_READ = 1;

	const NOTICE_TYPE_CONSULT = 1;
	const NOTICE_TYPE_ADVICE = 2;
	const NOTICE_TYPE_NOT_ADVICE = 3;
	const NOTICE_TYPE_REPLY = 4;
	const NOTICE_TYPE_REVIEW = 5;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addOrder(self::COL_NOTICE_ID, 'DESC');
		return $this->select();
	}

	public function getItem($id)
	{
		$this->addWhere(self::COL_NOTICE_ID, $id);
		return $this->selectRow();
	}

	public function delete($user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		return $this->doDelete();
	}

	public function getPageList(&$total, $offset, $limit, $user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addOrder(self::COL_NOTICE_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getUnreadListOfNew($user_id, $last_notice_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_UNREAD);
		if ($last_notice_id > 0) {
			$this->addWhere(self::COL_NOTICE_ID, $last_notice_id, '>');
		}
		$this->addOrder(self::COL_NOTICE_ID, 'DESC');
		return $this->select();
	}

	public function getUnreadPageList(&$total, $offset, $limit, $user_id)
	{
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_UNREAD);
		$this->addOrder(self::COL_NOTICE_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function getUnreadTotal($user_id)
	{
		$this->addSelectCount(self::COL_NOTICE_ID, 'total');
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_UNREAD);
		return $this->selectId();
	}

	public function read($user_id, $notice_id)
	{
		$this->addValue(self::COL_STATUS, self::STATUS_READ);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_NOTICE_ID, $notice_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_UNREAD);
		return $this->doUpdate();
	}
	public function reads($user_id, $notice_ids)
	{
		$this->reset();
		$this->addValue(self::COL_STATUS, self::STATUS_READ);
		$this->addWhere(self::COL_USER_ID, $user_id);
		if (is_array($notice_ids)) $this->addWhereIn(self::COL_NOTICE_ID, $notice_ids);
		$this->addWhere(self::COL_STATUS, self::STATUS_UNREAD);
		return $this->doUpdate();
	}
	/**
	 * 相談された場合の通知
	 * @param DbManager $db
	 * @param int $user_id
	 * @param int $from_user_id
	 * @param string $from_user_name
	 * @param int $advice_id
	 * @param string $advice_title
	 * @param int $consult_id
	 * @param string $consult_body
	 */
	public static function postConsult(&$db, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $consult_body)
	{
		return self::_postMessage($db, self::NOTICE_TYPE_CONSULT, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $consult_body);
	}

	/**
	 * アドバイスされた場合の通知
	 * @param DbManager $db
	 * @param int $user_id
	 * @param int $from_user_id
	 * @param string $from_user_name
	 * @param int $advice_id
	 * @param string $advice_title
	 * @param int $consult_id
	 * @param string $reply_body
	 */
	public static function postAdvice(&$db, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body)
	{
		return self::_postMessage($db, self::NOTICE_TYPE_ADVICE, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body);
	}

	/**
	 * アドバイスできない場合の通知
	 * @param DbManager $db
	 * @param int $user_id
	 * @param int $from_user_id
	 * @param string $from_user_name
	 * @param int $advice_id
	 * @param string $advice_title
	 * @param int $consult_id
	 * @param string $reply_body
	 */
	public static function postNotAdvice(&$db, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body)
	{
		return self::_postMessage($db, self::NOTICE_TYPE_NOT_ADVICE, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body);
	}

	/**
	 * 通常返信の通知
	 * @param DbManager $db
	 * @param int $user_id
	 * @param int $from_user_id
	 * @param string $from_user_name
	 * @param int $advice_id
	 * @param string $advice_title
	 * @param int $consult_id
	 * @param string $reply_body
	 */
	public static function postReply(&$db, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body)
	{
		return self::_postMessage($db, self::NOTICE_TYPE_REPLY, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $reply_body);
	}

	/**
	 * 評価された場合の通知
	 * @param DbManager $db
	 * @param int $user_id
	 * @param int $from_user_id
	 * @param string $from_user_name
	 * @param int $advice_id
	 * @param string $advice_title
	 * @param int $consult_id
	 * @param string $review_body
	 */
	public static function postReview(&$db, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $review_body)
	{
		return self::_postMessage($db, self::NOTICE_TYPE_REVIEW, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $review_body);
	}

	private static function _postMessage(&$db, $notice_type, $user_id, $from_user_id, $from_username, $advice_id, $advice_title, $consult_id, $post_body)
	{
		$noticesDao = new NoticesDao($db);
		$noticesDao->addValue(self::COL_USER_ID, $user_id);
		$noticesDao->addValue(self::COL_NOTICE_TYPE, $notice_type);
		$noticesDao->addValue(self::COL_FROM_USER_ID, $from_user_id);
		$noticesDao->addValueStr(self::COL_FROM_USERNAME, $from_username);
		$noticesDao->addValue(self::COL_ADVICE_ID, $advice_id);
		$noticesDao->addValueStr(self::COL_ADVICE_TITLE, $advice_title);
		$noticesDao->addValue(self::COL_CONSULT_ID, $consult_id);
		$noticesDao->addValueStr(self::COL_POST_BODY, $post_body);
		$noticesDao->addValue(self::COL_CREATEDATE, self::DATE_NOW);
		return $noticesDao->doInsert();
	}
}
?>