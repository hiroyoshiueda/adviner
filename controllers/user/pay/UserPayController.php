<?php
Sp::import('OrdersDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('AdvicesDao', 'dao');
/**
 * ユーザー・支払い管理(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserPayController extends BaseController
{
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$total = 0;
		$offset = $this->form->getInt('offset');
		$limit = 20;

		$userInfo = $this->getUserInfo();

		$order_user = array();
		$order_advice = array();

		$OrdersDao = new OrdersDao($this->db);
		$order_list = $OrdersDao->getPurchaserPageList($total, $offset, $limit, $userInfo['id']);

		if (count($order_list) > 0)
		{
			$user_ids = Util::arraySelectKey('advice_user_id', $order_list, true);
			$UsersDao = new UsersDao($this->db);
			$user_list = $UsersDao->getUserList($user_ids);
			$order_user = Util::arrayKeyData('user_id', $user_list);

			$advice_ids = Util::arraySelectKey('advice_id', $order_list, true);
			$AdvicesDao = new AdvicesDao($this->db);
			$advice_list = $AdvicesDao->getAdviceList($advice_ids);
			$order_advice = Util::arrayKeyData('advice_id', $advice_list);
		}

		$this->form->set('order_total', $total);
		$this->form->set('order_list', $order_list);
		$this->form->set('order_user', $order_user);
		$this->form->set('order_advice', $order_advice);

		$this->form->set('htitle', '支払い管理');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/pay/user_pay_index', APP_CONST_MAIN_FRAME);
	}
}
?>
