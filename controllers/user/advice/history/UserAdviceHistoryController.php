<?php
Sp::import('UsersDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
/**
 * ユーザー／相談されたこと(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserAdviceHistoryController extends BaseController
{
	/**
	 * 相談された履歴
	 */
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$total = 0;
		$limit = 20;
		$offset = $this->form->getInt('offset');

		$userInfo = $this->getUserInfo();

		$consultsDao = new ConsultsDao($this->db);
		$this->form->set('consult_list', $consultsDao->getAdviceHistoryOfMypage($total, $offset, $limit, $userInfo['id']));
		$this->form->set('consult_total', $total);
		$this->form->set('consult_limit', $limit);

		$this->form->set('htitle', '相談されたこと');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/advice/history/user_advice_history_index', APP_CONST_MAIN_FRAME);
	}
}
?>
