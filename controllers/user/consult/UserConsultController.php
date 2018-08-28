<?php
Sp::import('UsersDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
/**
 * ユーザー／相談したこと(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserConsultController extends BaseController
{
	/**
	 * 相談窓口一覧
	 */
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$total = 0;
		$limit = 20;
		$offset = $this->form->getInt('offset');

		$userInfo = $this->getUserInfo();

		// 相談したこと
		$consultsDao = new ConsultsDao($this->db);
		$this->form->set('consult_list', $consultsDao->getListOfMypage($total, $offset, $limit, $userInfo['id']));
		$this->form->set('consult_total', $total);
		$this->form->set('consult_limit', $limit);

		$this->form->set('htitle', '相談したこと');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		//$this->setSocialButton(array('fb_share', 'twitter'));
		//$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');

		return $this->forward('user/consult/user_consult_index', APP_CONST_MAIN_FRAME);
	}
}
?>
