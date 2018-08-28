<?php
Sp::import('UsersDao', 'dao');
Sp::import('AdvicesDao', 'dao');
/**
 * ユーザー／相談窓口(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserAdviceController extends BaseController
{
	/**
	 * 相談窓口一覧
	 */
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$userInfo = $this->getUserInfo();

		// 相談窓口
		$advicesDao = new AdvicesDao($this->db);
		$this->form->set('advice_list', $advicesDao->getListOfMypage($userInfo['id']));

		$this->form->set('htitle', '相談窓口');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		$this->setSocialButton(array('fb_share', 'twitter'));
		$this->form->setScript($this->form->get('JS_URL').'/js/adviner.onload.js');

		return $this->forward('user/advice/user_advice_index', APP_CONST_MAIN_FRAME);
	}
}
?>
