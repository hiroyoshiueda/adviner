<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('FollowsDao', 'dao');
Sp::import('FollowUtil', 'libs');
/**
 * ユーザー・フォローしている (Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserFollowingController extends BaseController
{
//	/**
//	 * フォローした人
//	 */
//	public function index()
//	{
//		if ($this->checkUserAuth() === false) return $this->loginPage();
//
//		$total = 0;
//		$limit = 20;
//		$offset = $this->form->getInt('offset');
//
//		$userInfo = $this->getUserInfo();
//
//		$user_list = array();
//
//		$FollowUtil = new FollowUtil($this->db);
//		$following = $FollowUtil->getFollowing($userInfo['id']);
//
//		if (count($following['user']) > 0)
//		{
//			$UsersDao = new UsersDao($this->db);
//			$user_list = $UsersDao->getUserPageList($total, $offset, $limit, $following['user']);
//		}
//
//		$this->form->set('user_list', $user_list);
//		$this->form->set('user_total', $total);
//		$this->form->set('user_limit', $limit);
//
//		$this->setLoadUserRank(new UserRanksDao($this->db));
//
//		$this->form->set('htitle', 'フォローした人');
//		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);
//
//		return $this->forward('user/following/user_following_index', APP_CONST_MAIN_FRAME);
//	}
	/**
	 * フォローした相談窓口
	 */
	public function advice()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$total = 0;
		$limit = 20;
		$offset = $this->form->getInt('offset');

		$userInfo = $this->getUserInfo();

		$advice_list = array();

		$FollowUtil = new FollowUtil($this->db);
		$following = $FollowUtil->getFollowing($userInfo['id']);

		if (count($following['advice']) > 0)
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$advice_list = $AdvicesDao->getNewPageListByAdvice($total, $offset, $limit, $following['advice']);
		}

		$this->form->set('advice_list', $advice_list);
		$this->form->set('advice_total', $total);
		$this->form->set('advice_limit', $limit);

		$this->form->set('htitle', 'フォローした相談窓口');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/following/user_following_advice', APP_CONST_MAIN_FRAME);
	}
}
?>
