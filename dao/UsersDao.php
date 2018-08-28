<?php
/**
 * ユーザーデータ
 * @author Hiroyoshi
 */
class UsersDao extends BaseDao
{
	const TABLE_NAME = 'users';

const COL_USER_ID = "user_id";
const COL_DELETE_FLAG = "delete_flag";
const COL_DISPLAY_FLAG = "display_flag";
const COL_STATUS = "status";
const COL_USER_KEY = "user_key";
const COL_LOGIN = "login";
const COL_PASSWORD = "password";
const COL_EMAIL = "email";
const COL_NICKNAME = "nickname";
const COL_SEARCHNAME = "searchname";
const COL_OPEN_LOGIN = "open_login";
const COL_OPEN_ID = "open_id";
const COL_OPEN_URL = "open_url";
const COL_OPEN_IMAGE_URL = "open_image_url";
const COL_BIRTHDAY = "birthday";
const COL_BIRTHDAY_PUBLIC = "birthday_public";
const COL_GENDER = "gender";
const COL_GENDER_PUBLIC = "gender_public";
const COL_ZIP = "zip";
const COL_AREA = "area";
const COL_URL = "url";
const COL_MELMAGA_SYSTEM = "melmaga_system";
const COL_MELMAGA_BASIC = "melmaga_basic";
const COL_PROFILE_MSG = "profile_msg";
const COL_PROFILE_FILE = "profile_file";
const COL_PROFILE_PATH = "profile_path";
const COL_PROFILE_SIZE = "profile_size";
const COL_PROFILE_S_FILE = "profile_s_file";
const COL_PROFILE_S_PATH = "profile_s_path";
const COL_PROFILE_S_SIZE = "profile_s_size";
const COL_PROFILE_B_FILE = "profile_b_file";
const COL_PROFILE_B_PATH = "profile_b_path";
const COL_PROFILE_B_SIZE = "profile_b_size";
const COL_CHANGE_EMAIL = "change_email";
const COL_CONSULT_STATUS = "consult_status";
const COL_CONSULT_MAIL_TO = "consult_mail_to";
const COL_CONSULT_REPLY_TO = "consult_reply_to";
const COL_ADVICE_REPLY_TO = "advice_reply_to";
const COL_CONSULT_REVIEW_TO = "consult_review_to";
const COL_SIGNUP_FB_SHARE = "signup_fb_share";
const COL_USER_POINT = "user_point";
const COL_CHARGE_RATE = "charge_rate";
const COL_LOGINDATE = "logindate";
const COL_CREATEDATE = "createdate";
const COL_LASTUPDATE = "lastupdate";
const COL_DELETEDATE = "deletedate";

	const STATUS_TEMP = 0;
	const STATUS_REGULAR = 1;

	const OPEN_LOGIN_NORMAL = 0;
	const OPEN_LOGIN_TWITTER = 1;
	const OPEN_LOGIN_MIXI = 2;
	const OPEN_LOGIN_FACEBOOK = 3;

	function __construct(&$db, $options=array())
	{
		parent::__construct($db, self::TABLE_NAME, $options);
	}

	public function getList($status)
	{
		$this->addWhere(self::COL_STATUS, $status);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_CREATEDATE, 'DESC');
		return $this->select();
	}

	public function getItem($id, $status=-1)
	{
		$this->addWhere(self::COL_USER_ID, $id);
		if ($status>-1) $this->addWhere(self::COL_STATUS, $status);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function delete($id)
	{
		$this->addValue(self::COL_DELETE_FLAG, self::DELETE_FLAG_OFF);
		$this->addValue(self::COL_DELETEDATE, self::DATE_NOW);
		$this->addWhere(self::COL_USER_ID, $id);
		return $this->doUpdate();
	}

	public function getItemByLogin($login)
	{
		$this->addWhereStr(self::COL_LOGIN, $login);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		//$this->addWhere(self::COL_OPEN_LOGIN, self::OPEN_LOGIN_NORMAL);
		return $this->selectRow();
	}

	public function getItemByLoginAndEmail($login, $email)
	{
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addWhere(self::COL_OPEN_LOGIN, self::OPEN_LOGIN_NORMAL);
		$this->addWhereStr(self::COL_LOGIN, $login);
		$this->addWhereStr(self::COL_EMAIL, $email);
		return $this->selectRow();
	}

	public function getItemByTempKey($key)
	{
		$this->addWhereStr(self::COL_TEMP_KEY, $key);
		$this->addWhere(self::COL_STATUS, self::STATUS_TEMP);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$ts = time() - APP_CONST_REGIST_FIRST_TIME;
		$this->addWhereStr(self::COL_CREATEDATE, date('Y-m-d H:i:s', $ts), '>=');
		return $this->selectRow();
	}

	public function getItemByTempKeyRegular($key)
	{
		$this->addWhereStr(self::COL_TEMP_KEY, $key);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function getItemByUserIdAndLogin($id, $login)
	{
		$this->addWhere(self::COL_USER_ID, $id);
		$this->addWhereStr(self::COL_LOGIN, $login);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function isDuplicationByNickname($nickname, $id=0)
	{
		$this->addSelectCount('*', 'cnt');
		$this->addWhereStr(self::COL_NICKNAME, $nickname);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if ($id > 0) $this->addWhere(self::COL_USER_ID, $id, '!=');
		return ($this->selectId()>0);
	}

	public function isDuplicationByEmail($email, $id=0, $display_flag=-1, $delete_flag=-1)
	{
		$this->addSelectCount('*', 'cnt');
		$this->addWhereStr(self::COL_EMAIL, $email);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		if ($display_flag>-1) $this->addWhere(self::COL_DISPLAY_FLAG, $display_flag);
		if ($delete_flag>-1) $this->addWhere(self::COL_DELETE_FLAG, $delete_flag);
		if ($id > 0) $this->addWhere(self::COL_USER_ID, $id, '!=');
		return ($this->selectId()>0);
	}

//	public function loadPennameAndEmail(&$penname_arr, &$email_arr, $id=0)
//	{
//		$this->addSelect(self::COL_PENNAME);
//		$this->addSelect(self::COL_EMAIL);
//		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
//		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
//		if ($id > 0) $this->addWhere(self::COL_USER_ID, $id, '!=');
//		$penname_arr = array();
//		$email_arr = array();
//		$rows = array();
//		$dataSet = $this->doSelect();
//		while ($dataSet->next()) {
//			$rows = $dataSet->getRowSet();
//			$penname_arr[] = $rows[self::COL_PENNAME];
//			$email_arr[] = $rows[self::COL_EMAIL];
//		}
//		return;
//	}

	public function getPageListOnAdmin(&$total, $limit, $offset, $orders)
	{
		foreach ($orders as $order_col => $order) {
			$this->addOrder($order_col, $order);
		}
		return $this->selectPage($offset, $limit, $total);
	}

	public function getListByIds($user_ids)
	{
		$user_ids = array_unique($user_ids);
		$cnt = count($user_ids);
		if ($cnt == 0) {
			return array();
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_USER_ID, $user_ids[0]);
		} else {
			$this->addWhereIn(self::COL_USER_ID, $user_ids);
		}
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->select();
	}

	public function getUser($user_id)
	{
		$this->addSelect(self::COL_USER_ID);
		$this->addSelect(self::COL_LOGIN);
		$this->addSelect(self::COL_EMAIL);
		$this->addSelect(self::COL_NICKNAME);
		$this->addSelect(self::COL_PROFILE_PATH);
		$this->addSelect(self::COL_PROFILE_S_PATH);
		$this->addSelect(self::COL_PROFILE_B_PATH);
		$this->addWhere(self::COL_USER_ID, $user_id);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->selectRow();
	}

	public function getUserList($user_ids, $display_flag=0, $delete_flag=0)
	{
		$this->addSelect(self::COL_USER_ID);
		$this->addSelect(self::COL_LOGIN);
		$this->addSelect(self::COL_EMAIL);
		$this->addSelect(self::COL_NICKNAME);
		$this->addSelect(self::COL_PROFILE_PATH);
		$this->addSelect(self::COL_PROFILE_S_PATH);
		$this->addSelect(self::COL_PROFILE_B_PATH);

		$cnt = count($user_ids);
		if ($cnt > 1) {
			$user_ids = array_unique($user_ids);
			$this->addWhereIn(self::COL_USER_ID, $user_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_USER_ID, $user_ids[0]);
		} else {
			return array();
		}
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		if ($display_flag > -1) $this->addWhere(self::COL_DISPLAY_FLAG, $display_flag);
		if ($delete_flag > -1) $this->addWhere(self::COL_DELETE_FLAG, $delete_flag);
		return $this->select();
	}
	public function getUserPageList(&$total, $offset, $limit, $user_ids, $orders=array('user_id'=>'desc'))
	{
		$this->addSelect(self::COL_USER_ID);
		$this->addSelect(self::COL_LOGIN);
		$this->addSelect(self::COL_EMAIL);
		$this->addSelect(self::COL_NICKNAME);
		$this->addSelect(self::COL_PROFILE_PATH);
		$this->addSelect(self::COL_PROFILE_S_PATH);
		$this->addSelect(self::COL_PROFILE_B_PATH);

		$cnt = count($user_ids);
		if ($cnt > 1) {
			$user_ids = array_unique($user_ids);
			$this->addWhereIn(self::COL_USER_ID, $user_ids);
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_USER_ID, $user_ids[0]);
		} else {
			return array();
		}
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if (is_array($orders)) {
			foreach ($orders as $col => $order) {
				if ($col != '') $this->addOrder($col, $order);
			}
		}
		return $this->selectPage($offset, $limit, $total);
	}
	public function getUserListLarge($user_ids)
	{
//		$this->addSelect(self::COL_USER_ID);
//		$this->addSelect(self::COL_LOGIN);
//		$this->addSelect(self::COL_EMAIL);
//		$this->addSelect(self::COL_NICKNAME);
//		$this->addSelect(self::COL_PROFILE_PATH);
//		$this->addSelect(self::COL_PROFILE_S_PATH);
//		$this->addSelect(self::COL_PROFILE_B_PATH);
//		$this->addSelect(self::COL_PROFILE_MSG);
		$user_ids = array_unique($user_ids);
		$cnt = count($user_ids);
		if ($cnt == 0) {
			return array();
		} else if ($cnt == 1) {
			$this->addWhere(self::COL_USER_ID, $user_ids[0]);
		} else {
			$this->addWhereIn(self::COL_USER_ID, $user_ids);
		}
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		return $this->select();
	}

	/**
	 * Facebook IDで検索
	 */
	public function getUserListByFacebookId(&$total, $limit, $offset, $ids, $user_id, $is_follow=false)
	{
		$this->addSelect('u.'.self::COL_USER_ID);
		$this->addSelect('u.'.self::COL_LOGIN);
		$this->addSelect('u.'.self::COL_EMAIL);
		$this->addSelect('u.'.self::COL_NICKNAME);
		$this->addSelect('u.'.self::COL_PROFILE_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_B_PATH);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'u.user_id=f.follow_user_id AND f.user_id='.$user_id);

		if (count($ids)>1) {
			$this->addWhereStrIn('u.'.self::COL_OPEN_ID, $ids);
		} else {
			$this->addWhereStr('u.'.self::COL_OPEN_ID, $ids[0]);
		}
		$this->addWhere('u.'.self::COL_OPEN_LOGIN, self::OPEN_LOGIN_FACEBOOK);
		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if ($is_follow) {
			$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);
		}
		$this->addOrder('u.'.self::COL_USER_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function initUserListByFacebookId($ids, $user_id, $is_follow=false)
	{
		$this->addSelect('u.'.self::COL_USER_ID);
		$this->addSelect('u.'.self::COL_LOGIN);
		$this->addSelect('u.'.self::COL_EMAIL);
		$this->addSelect('u.'.self::COL_NICKNAME);
		$this->addSelect('u.'.self::COL_PROFILE_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_B_PATH);
		$this->addSelect('u.'.self::COL_OPEN_ID);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'u.user_id=f.follow_user_id AND f.user_id='.$user_id);

		if (count($ids)>1) {
			$this->addWhereStrIn('u.'.self::COL_OPEN_ID, $ids);
		} else {
			$this->addWhereStr('u.'.self::COL_OPEN_ID, $ids[0]);
		}
		$this->addWhere('u.'.self::COL_OPEN_LOGIN, self::OPEN_LOGIN_FACEBOOK);
		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if ($is_follow) {
			$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);
		}
		$this->addOrder('u.'.self::COL_USER_ID, 'DESC');
	}

	/**
	 * Facebook IDで相談窓口を検索
	 */
	public function getAdviceListByFacebookId(&$total, $limit, $offset, $ids, $user_id, $is_follow=false)
	{
		$this->addSelect('u.'.self::COL_USER_ID);
		$this->addSelect('u.'.self::COL_LOGIN);
		$this->addSelect('u.'.self::COL_EMAIL);
		$this->addSelect('u.'.self::COL_NICKNAME);
		$this->addSelect('u.'.self::COL_PROFILE_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_B_PATH);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_ID);
		$this->addSelect('a.'.AdvicesDao::COL_CATEGORY_ID);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_STATUS);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_BODY);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TAG);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TAG_SEARCH);
		$this->addSelect('a.'.AdvicesDao::COL_CONSULT_TOTAL);
		$this->addSelect('a.'.AdvicesDao::COL_PV_TOTAL);
		$this->addSelect('a.'.AdvicesDao::COL_PV_TODAY);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'u.user_id=a.advice_user_id');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'a.advice_id=f.follow_advice_id AND f.user_id='.$user_id);

		if (count($ids)>1) {
			$this->addWhereStrIn('u.'.self::COL_OPEN_ID, $ids);
		} else {
			$this->addWhereStr('u.'.self::COL_OPEN_ID, $ids[0]);
		}

		$this->addWhere('u.'.self::COL_OPEN_LOGIN, self::OPEN_LOGIN_FACEBOOK);
		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if ($is_follow) {
			$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);
		}
		$this->addWhere('a.'.AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_OK);
		$this->addWhere('a.'.AdvicesDao::COL_DISPLAY_FLAG, AdvicesDao::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.AdvicesDao::COL_DELETE_FLAG, AdvicesDao::DELETE_FLAG_ON);
		$this->addOrder('a.'.AdvicesDao::COL_ADVICE_ID, 'DESC');
		return $this->selectPage($offset, $limit, $total);
	}

	public function initAdviceListByFacebookId($ids, $user_id, $is_follow=false)
	{
		$this->addSelect('u.'.self::COL_USER_ID);
		$this->addSelect('u.'.self::COL_LOGIN);
		$this->addSelect('u.'.self::COL_EMAIL);
		$this->addSelect('u.'.self::COL_NICKNAME);
		$this->addSelect('u.'.self::COL_PROFILE_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_B_PATH);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_ID);
		$this->addSelect('a.'.AdvicesDao::COL_CATEGORY_ID);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_STATUS);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TITLE);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_BODY);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TAG);
		$this->addSelect('a.'.AdvicesDao::COL_ADVICE_TAG_SEARCH);
		$this->addSelect('a.'.AdvicesDao::COL_CONSULT_TOTAL);
		$this->addSelect('a.'.AdvicesDao::COL_PV_TOTAL);
		$this->addSelect('a.'.AdvicesDao::COL_PV_TODAY);
		$this->addSelect('f.'.FollowsDao::COL_FOLLOW_ID);

		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(AdvicesDao::TABLE_NAME, 'a', 'u.user_id=a.advice_user_id');
		$this->addTableJoin(FollowsDao::TABLE_NAME, 'f', 'a.advice_id=f.follow_advice_id AND f.user_id='.$user_id);

		if (count($ids)>1) {
			$this->addWhereStrIn('u.'.self::COL_OPEN_ID, $ids);
		} else {
			$this->addWhereStr('u.'.self::COL_OPEN_ID, $ids[0]);
		}

		$this->addWhere('u.'.self::COL_OPEN_LOGIN, self::OPEN_LOGIN_FACEBOOK);
		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		if ($is_follow) {
			$this->addWhereStr('f.'.FollowsDao::COL_FOLLOW_ID, null);
		}
		$this->addWhere('a.'.AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_OK);
		$this->addWhere('a.'.AdvicesDao::COL_DISPLAY_FLAG, AdvicesDao::DISPLAY_FLAG_ON);
		$this->addWhere('a.'.AdvicesDao::COL_DELETE_FLAG, AdvicesDao::DELETE_FLAG_ON);
		$this->addOrder('a.'.AdvicesDao::COL_ADVICE_ID, 'DESC');
	}

	public function getItemJoinUserProfile($user_id)
	{
		$this->addSelect('u.'.self::COL_USER_ID);
		$this->addSelect('u.'.self::COL_NICKNAME);
		$this->addSelect('u.'.self::COL_BIRTHDAY);
		$this->addSelect('u.'.self::COL_BIRTHDAY_PUBLIC);
		$this->addSelect('u.'.self::COL_GENDER);
		$this->addSelect('u.'.self::COL_GENDER_PUBLIC);
		$this->addSelect('u.'.self::COL_PROFILE_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_S_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_B_PATH);
		$this->addSelect('u.'.self::COL_PROFILE_MSG);
		$this->addSelect('up.*');

		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(UserProfilesDao::TABLE_NAME, 'up', 'u.user_id=up.user_id');

		$this->addWhere('u.'.self::COL_USER_ID, $user_id);
		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		return $this->selectRow();
	}

	public function getNewListOfPublic($limit)
	{
		$this->addSelect(self::COL_USER_ID);
		$this->addSelect(self::COL_NICKNAME);
		$this->addSelect(self::COL_PROFILE_PATH);
		$this->addSelect(self::COL_PROFILE_S_PATH);
		$this->addSelect(self::COL_PROFILE_B_PATH);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_USER_ID, 'DESC');
		$this->addLimit($limit);
		return $this->select();
	}

	public function getPopularListOfPublic($limit)
	{
		$this->addSelect(self::COL_USER_ID);
		$this->addSelect(self::COL_NICKNAME);
		$this->addSelect(self::COL_PROFILE_PATH);
		$this->addSelect(self::COL_PROFILE_S_PATH);
		$this->addSelect(self::COL_PROFILE_B_PATH);
		$this->addWhere(self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere(self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere(self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);
		$this->addOrder(self::COL_USER_POINT, 'DESC');
		$this->addOrder(self::COL_USER_ID, 'DESC');
		$this->addLimit($limit);
		return $this->select();
	}

	/**
	 * キーワード検索用
	 */
	public function getPageListOnSearch(&$total, $offset, $limit, $orderby, $words)
	{
		$this->setTable(self::TABLE_NAME, 'u');
		$this->addTableJoin(UserRanksDao::TABLE_NAME, 'ur', 'u.user_id=ur.user_id');

		if (is_array($words))
		{
			foreach ($words as $word) {
				$wh  = '(u.'.self::COL_NICKNAME.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR u.'.self::COL_SEARCHNAME.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ' OR u.'.self::COL_PROFILE_MSG.' LIKE '.$this->quoteString('%'.$word.'%');
				$wh .= ')';
				$this->addWhere('', $wh);
			}
		}

		$this->addWhere('u.'.self::COL_STATUS, self::STATUS_REGULAR);
		$this->addWhere('u.'.self::COL_DISPLAY_FLAG, self::DISPLAY_FLAG_ON);
		$this->addWhere('u.'.self::COL_DELETE_FLAG, self::DELETE_FLAG_ON);

		if (is_array($orderby))
		{
			foreach ($orderby as $col => $order) {
				$this->addOrder($col, $order);
			}
		}

		return $this->selectPage($offset, $limit, $total);
	}

	public static function getUserSet(&$db, $user_ids)
	{
		$UsersDao = new UsersDao($db);
		$UsersDao->addSelect(UsersDao::COL_USER_ID);
		$UsersDao->addSelect(UsersDao::COL_LOGIN);
		$UsersDao->addSelect(UsersDao::COL_EMAIL);
		$UsersDao->addSelect(UsersDao::COL_NICKNAME);
		$UsersDao->addSelect(UsersDao::COL_PROFILE_PATH);
		$UsersDao->addSelect(UsersDao::COL_PROFILE_S_PATH);
		$UsersDao->addSelect(UsersDao::COL_PROFILE_B_PATH);

		if (is_array($user_ids)) {
			if (count($user_ids) > 1) {
				$UsersDao->addWhereIn(UsersDao::COL_USER_ID, $user_ids);
			} else {
				$UsersDao->addWhere(UsersDao::COL_USER_ID, $user_ids[0]);
			}
		} else {
			$UsersDao->addWhere(UsersDao::COL_USER_ID, $user_ids);
		}

		$UsersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$UsersDao->addWhere(UsersDao::COL_DISPLAY_FLAG, UsersDao::DISPLAY_FLAG_ON);
		$UsersDao->addWhere(UsersDao::COL_DELETE_FLAG, UsersDao::DELETE_FLAG_ON);
		return $UsersDao->selectKeySet(UsersDao::COL_USER_ID);
	}
}
?>