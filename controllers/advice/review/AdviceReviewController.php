<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('AdviceRanksDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('ConsultReplysDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
/**
 * 評価の詳細
 * @author Hiroyoshi
 */
class AdviceReviewController extends BaseController
{
	public function index()
	{
		$consult_review_id = $this->form->getInt('consult_review_id');
		if (empty($consult_review_id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$ConsultReviewsDao = new ConsultReviewsDao($this->db);
		$review = $ConsultReviewsDao->getItem($consult_review_id);
		if (empty($review)) return $this->notfound();
		if ($review['review_public_flag'] == ConsultReviewsDao::REVIEW_PUBLIC_FLAG_PRIVATE)
		{
			return $this->errorPage('この評価コメントは非公開です。');
		}

		$ConsultsDao = new ConsultsDao($this->db);
		$consult = $ConsultsDao->getItemByAdviceId($review['consult_id'], $review['advice_id']);
		if (empty($consult)) return $this->notfound();

		$AdvicesDao = new AdvicesDao($this->db);
		$advice = $AdvicesDao->getItemByUserId($consult['advice_id'], $consult['advice_user_id']);
		if (empty($advice)) return $this->notfound();

		$this->form->set('review', $review);
		$this->form->set('consult', $consult);
		$this->form->set('advice', $advice);

		$UsersDao = new UsersDao($this->db);
		$user_list = $UsersDao->getUserListLarge(array($consult['advice_user_id'], $consult['consult_user_id']));
		$users = Util::arrayKeyData('user_id', $user_list);

		$this->form->set('advice_user', $users[$consult['advice_user_id']]);
		$this->form->set('consult_user', $users[$consult['consult_user_id']]);
		$this->form->set('user', $users[$review['consult_review_user_id']]);

		$user = $this->form->get('user');

		$UserRanksDao = new UserRanksDao($this->db);
		$this->form->set('user_rank', $UserRanksDao->getItem($review['consult_review_user_id']));

		if ($user['nickname'] == '')
		{
			$this->form->set('htitle', '評価コメント');
			$this->setTitle('[評価]' . $this->getBodyToTitle($review['review_body']));
			$this->setDescription('評価コメントです。' . $review['review_body']);
		}
		else
		{
			$this->form->set('htitle', $user['nickname'].' さんの評価コメント');
			$this->setTitle('[評価]' . $this->getBodyToTitle($review['review_body']));
			$this->setDescription($user['nickname'].'の評価コメントです。' . $review['review_body']);
		}

		$this->setGoodButton($consult['advice_id'], $consult['consult_id']);

		return $this->forward('advice/review/advice_review_index', APP_CONST_MAIN_FRAME);
	}
}
?>
