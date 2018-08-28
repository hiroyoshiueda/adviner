<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('FollowsDao', 'dao');
/**
 * ユーザープロフィール
 * @author Hiroyoshi
 */
class ProfileController extends BaseController
{
	/**
	 * 詳細
	 */
	public function index()
	{
		$user_id = $this->form->getInt('user_id');
		if (empty($user_id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$UsersDao = new UsersDao($this->db);
		$UsersDao->addWhere(UsersDao::COL_USER_ID, $user_id);
		$UsersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_REGULAR);
		$user = $UsersDao->selectRow();
		if (empty($user)) return $this->notfound();
		if ($user['display_flag'] == UsersDao::DISPLAY_FLAG_OFF || $user['delete_flag'] == UsersDao::DELETE_FLAG_OFF)
		{
			$this->resp->setStatus(404);
			return $this->errorPage('ご指定ユーザーのプロフィールは見つかりません。');
		}
		$this->form->set('user', $user);

		$UserRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $UserRanksDao->getItem($user['user_id']));

		$AdvicesDao = new AdvicesDao($this->db);
		$advice_list = $AdvicesDao->getListOfPublic($user['user_id']);
		$this->form->set('advice_list', $advice_list);

		// 相談中リスト
		$ConsultsDao = new ConsultsDao($this->db);
		$this->form->set('consult_list', $ConsultsDao->getListOfPublic($user['user_id']));

		if (count($advice_list) > 0)
		{
			$advice_ids = Util::arraySelectKey('advice_id', $advice_list);
			$this->setGoodButton($advice_ids);
		}

		// フォロー状況
		$following = array();
		$following_advice = array();
		$FollowsDao = new FollowsDao($this->db);
		$follow_list = $FollowsDao->getList($user_id);
		if (count($follow_list) > 0)
		{
			foreach ($follow_list as $d)
			{
				if ($d['follow_advice_id'] > 0) {
					$following[] = $d['follow_advice_id'];
				}
			}
			if (count($following) > 0)
			{
				$total = 0;
				$AdvicesDao = new AdvicesDao($this->db);
				$following_advice = $AdvicesDao->getNewPageListByAdvice($total, 0, 10, $following);
			}
		}
		$this->form->set('following_advice', $following_advice);

		$this->form->set('htitle', $user['nickname']);
		$this->setTitle($this->form->get('htitle'), '', APP_CONST_SITE_TITLE4);

		$this->setSocialButton();

		return $this->forward('profile/profile_index', APP_CONST_MAIN_FRAME);
	}
}
?>
