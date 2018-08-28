<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('AdviceRanksDao', 'dao');
Sp::import('CategorysDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('FollowsDao', 'dao');
/**
 * 相談詳細
 * @author Hiroyoshi
 */
class AdviceConsultController extends BaseController
{

	/**
	 * 相談詳細
	 */
	public function index()
	{
		$consult_id = $this->form->getInt('consult_id');
		if (empty($consult_id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$ConsultsDao = new ConsultsDao($this->db);
		$consult = $ConsultsDao->getItemAndUser($consult_id);
		if (empty($consult)) return $this->notfound();

		if ($consult['public_flag'] == ConsultsDao::PUBLIC_FLAG_PRIVATE)
		{
			return $this->errorPage('この相談は非公開です。');
		}

		if ($consult['advice_id'] > 0 && $consult['advice_user_id'] > 0)
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$advice = $AdvicesDao->getItemAndUser($consult['advice_id'], $consult['advice_user_id']);
			$this->form->set('advice', $advice);
		}

		$this->form->set('consult', $consult);

		$UserRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $UserRanksDao->getItem($consult['consult_user_id']));

		$this->form->set('htitle', $consult['nickname'].' さんの相談');

		$this->setTitle('[相談]' . $this->getBodyToTitle($consult['consult_body']));
		$this->setDescription($consult['consult_body']);

		$this->form->setScript($this->form->get('JS_URL').'/js/jquery/jquery.autosize-min.js');

		$this->setGoodButton($consult['advice_id'], $consult['consult_id']);

		return $this->forward('advice/consult/advice_consult_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * スレッド詳細
	 */
	public function thread()
	{
		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		if (empty($advice_id) || empty($consult_id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$ConsultsDao = new ConsultsDao($this->db);
		$consult = $ConsultsDao->getItemByAdviceId($consult_id, $advice_id);
		if (empty($consult)) return $this->notfound();

		// 相談者かアドバイザーならTRUE
		$is_owner = (isset($userInfo['id']) && ($userInfo['id'] == $consult['advice_user_id'] || $userInfo['id'] == $consult['consult_user_id'])) ? true : false;
		// 有料相談
		if ($is_owner && $consult['advice_charge_flag'] == 1)
		{
			if ($this->isForceHTTPS()) return $this->forceHTTPS();
		}
		$this->form->set('consult', $consult);

		$AdvicesDao = new AdvicesDao($this->db);
		$advice = $AdvicesDao->getItemByUserId($consult['advice_id'], $consult['advice_user_id']);
		if (empty($advice)) return $this->notfound();
		$this->form->set('advice', $advice);

		// 相談履歴
		$reply_list = array();
		$review_list = array();
		if ($is_owner || $consult['latest_reply_id']>0)
		{
			$ConsultReplysDao = new ConsultReplysDao($this->db);
			$reply_list = $ConsultReplysDao->getListByConsultIds(array($consult['consult_id']));

			$ConsultReviewsDao = new ConsultReviewsDao($this->db);
			$review_list = $ConsultReviewsDao->getListByConsultIds(array($consult['consult_id']));
		}
		$this->form->set('reply_list', $reply_list);
		$this->form->set('review_list', $review_list);

		$UsersDao = new UsersDao($this->db);
		$user_list = $UsersDao->getUserListLarge(array($consult['advice_user_id'], $consult['consult_user_id']));
		$user_set = Util::arrayKeyData('user_id', $user_list);
		$this->form->set('user_set', $user_set);

		$UserRanksDao = new UserRanksDao($this->db);
		$this->form->set('advice_user_rank', $UserRanksDao->getItem($consult['advice_user_id']));

		// PVカウントアップ
//		if ($this->isNotUserAgent() === false && $this->isNotIp() === false && $this->setUniqCount($advice_id, APP_CONST_UNIQ_ADVICE_COOKIE_NAME) !== false)
//		if ($this->isNotUserAgent() === false && $this->setUniqCount($advice_id, APP_CONST_UNIQ_ADVICE_COOKIE_NAME) !== false)
		if ($this->isNotUserAgent() === false)
		{
			$advicesDao = new AdvicesDao($this->db);
			$advicesDao->updateCountPv($advice_id);

			$adviceRanksDao = new AdviceRanksDao($this->db);
			$adviceRanksDao->updateCountPv($advice_id);
		}

		// フォロー状況
		$follow_id = 0;
		if (isset($userInfo['id']))
		{
			$FollowsDao = new FollowsDao($this->db);
			$follow_id = $FollowsDao->getFollowIdByFollowUserId($userInfo['id'], $consult['advice_user_id']);
		}
		$this->form->set('follow', array('follow_id'=>$follow_id));

		$this->form->set('htitle', $user_set[$consult['consult_user_id']]['nickname'].' さんの相談');

		if ($consult['public_flag'] == 1)
		{
			if ($userInfo && ($userInfo['id'] == $consult['advice_user_id'] || $userInfo['id'] == $consult['consult_user_id']))
			{
				$this->form->set('htitle', $user_set[$consult['consult_user_id']]['nickname'].' さんの相談');
			}
			else
			{
				$this->form->set('htitle', '非公開の相談');
			}
			$this->setTitle($this->form->get('htitle'), $advice['advice_title']);
			$this->setDescription('非公開の相談スレッド。'.$advice['advice_body']);
		}
		else
		{
			if ($consult['latest_reply_id']>0)
			{
				$this->form->set('htitle', $user_set[$consult['consult_user_id']]['nickname'].' さんの相談');
				$this->setTitle($this->getBodyToTitle($consult['consult_body']));
				$this->setDescription($consult['consult_body']);
			}
			else if ($consult['consult_status'] == ConsultsDao::CONSULT_STATUS_DURING)
			{
				if ($userInfo && ($userInfo['id'] == $consult['advice_user_id'] || $userInfo['id'] == $consult['consult_user_id']))
				{
					$this->form->set('htitle', $user_set[$consult['consult_user_id']]['nickname'].' さんの相談');
				}
				else
				{
					$this->form->set('htitle', '相談中');
				}
				$this->setTitle($this->form->get('htitle'), $advice['advice_title']);
				$this->setDescription('この相談スレッドは相談中です。'.$advice['advice_body']);
			}
			else
			{
				$this->form->set('htitle', '相談終了');
				$this->setTitle($this->form->get('htitle'), $advice['advice_title']);
				$this->setDescription('この相談スレッドは終了しました。'.$advice['advice_body']);
			}
		}

		$this->setGoodButton($advice_id);

		$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');
		$this->form->setScript('https://www.paypalobjects.com/js/external/dg.js');

		$this->createSecurityCode();

		return $this->forward('advice/consult/advice_consult_thread', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 返信詳細
	 */
	public function reply()
	{
		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		$consult_reply_id = $this->form->getInt('consult_reply_id');
		if (empty($advice_id) || empty($consult_id) || empty($consult_reply_id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$ConsultsDao = new ConsultsDao($this->db);
		$consult = $ConsultsDao->getItemByAdviceId($consult_id, $advice_id);
		if (empty($consult)) return $this->notfound();
		if ($consult['public_flag'] == ConsultsDao::PUBLIC_FLAG_PRIVATE)
		{
			return $this->errorPage('この相談スレッドは非公開です。');
		}
		$this->form->set('consult', $consult);

		$AdvicesDao = new AdvicesDao($this->db);
		$advice = $AdvicesDao->getItemByUserId($consult['advice_id'], $consult['advice_user_id']);
		if (empty($advice)) return $this->notfound();
		$this->form->set('advice', $advice);

		$ConsultReplysDao = new ConsultReplysDao($this->db);
		$reply = $ConsultReplysDao->getItemOfMypageFromUser($consult_reply_id);
		$this->form->set('reply', $reply);

		$UsersDao = new UsersDao($this->db);
		$user_list = $UsersDao->getUserListLarge(array($consult['advice_user_id'], $consult['consult_user_id']));
		$users = Util::arrayKeyData('user_id', $user_list);

		$this->form->set('advice_user', $users[$consult['advice_user_id']]);
		$this->form->set('consult_user', $users[$consult['consult_user_id']]);
		$this->form->set('user', $users[$reply['from_user_id']]);

		$UserRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $UserRanksDao->getItem($reply['from_user_id']));

		if ($reply['from_user_id'] == $consult['advice_user_id'])
		{
			$this->form->set('htitle', $reply['nickname'].' さんのアドバイス');
			$this->setDescription($reply['nickname'].'のアドバイスです。' . $reply['reply_body']);
			$this->setTitle('[アドバイス]' . $this->getBodyToTitle($reply['reply_body']));
		}
		else
		{
			$this->form->set('htitle', $reply['nickname'].' さんの相談');
			$this->setDescription($reply['nickname'].'の相談です。' . $reply['reply_body']);
			$this->setTitle('[相談]' . $this->getBodyToTitle($reply['reply_body']));
		}

		$this->setGoodButton($advice_id, $consult_id);

		return $this->forward('advice/consult/advice_consult_reply', APP_CONST_MAIN_FRAME);
	}
}
?>
