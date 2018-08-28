<?php
Sp::import('OrdersDao', 'dao');
Sp::import('OrderPaymentsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('UserAccountsDao', 'dao');
Sp::import('AdvicesDao', 'dao');
/**
 * ユーザー／報酬管理(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserRewardController extends BaseController
{
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$year = $this->form->getInt('year');
		$month = $this->form->getInt('month');

		$userInfo = $this->getUserInfo();

		// 有料相談窓口の存在確認
		$AdvicesDao = new AdvicesDao($this->db);
		$is_charge_advice = $AdvicesDao->isChargeAdvice($userInfo['id']);

		$payment = array();
		$order_list = array();
		$order_user = array();
		$order_advice = array();
		$is_account_empty = false;
		$ym_list = array();
		$sdate = null;
		$edate = null;

		if ($is_charge_advice)
		{
			// 常に今月の情報
			$OrderPaymentsDao = new OrderPaymentsDao($this->db);
			$payment = $OrderPaymentsDao->getItemByYearMonthly($userInfo['id'], date('Y'), date('n'));

			$OrderPaymentsDao->reset();
			$ym_list = $OrderPaymentsDao->getYearMonthly($userInfo['id']);
			if (count($ym_list)>0)
			{
				foreach ($ym_list as $i => $d) {
					$ym_list[$i]['year'] = substr($d['year_monthly'], 0, 4);
					$ym_list[$i]['month'] = (int)substr($d['year_monthly'], 4, 2);
				}
			}

			if ($year > 1999 && $year <= 2050 && $month > 0 && $month <= 12)
			{
				$sdate = sprintf("%04d-%02d-%02d 00:00:00", $year, $month, 1);
				$edate = sprintf("%04d-%02d-%02d 23:59:59", $year, $month, date("t", mktime(0,0,0,$month,1,$year)));
			}
			else
			{
				$this->form->set('year', null);
				$this->form->set('month', null);
			}

			$total = 0;
			$OrdersDao = new OrdersDao($this->db);
			$order_list = $OrdersDao->getOwnerPageList($total, 0, 20, $userInfo['id'], $sdate, $edate);

			if (count($order_list) > 0)
			{
				$user_ids = Util::arraySelectKey('consult_user_id', $order_list, true);
				$UsersDao = new UsersDao($this->db);
				$user_list = $UsersDao->getUserList($user_ids);
				$order_user = Util::arrayKeyData('user_id', $user_list);

				$advice_ids = Util::arraySelectKey('advice_id', $order_list, true);
				$AdvicesDao = new AdvicesDao($this->db);
				$advice_list = $AdvicesDao->getAdviceList($advice_ids, -1, -1);
				$order_advice = Util::arrayKeyData('advice_id', $advice_list);

				$UserAccountsDao = new UserAccountsDao($this->db);
				$account = $UserAccountsDao->getItemByUserId($userInfo['id']);
				if (empty($account) || $account['bank_number'] == '') {
					$is_account_empty = true;
				}
			}
		}

		$this->form->set('payment', $payment);
		$this->form->set('ym_list', $ym_list);
		$this->form->set('order_list', $order_list);
		$this->form->set('order_user', $order_user);
		$this->form->set('order_advice', $order_advice);
		$this->form->set('is_charge_advice', $is_charge_advice);
		$this->form->set('is_account_empty', $is_account_empty);

		$this->form->setParameterForm('year');
		$this->form->setParameterForm('month');

		$this->form->set('htitle', '報酬管理');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/reward/user_reward_index', APP_CONST_MAIN_FRAME);
	}
}
?>
