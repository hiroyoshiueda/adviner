<?php
Sp::import('OrdersDao', 'dao');
Sp::import('OrderPaymentsDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * 報酬計算（月次）
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class OrderCalcMonthly
{
	/**
	 * @var DbManager
	 */
	private $db;

	/**
	 * @var SpLogger
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $exec_date = '';

	/**
	 * @var string
	 */
	private $exec_ym = '';

	/**
	 * @var string
	 */
	private $calc_sdate;

	/**
	 * @var string
	 */
	private $calc_edate;

	public function __construct(&$db, &$logger, &$argv)
	{
		$this->db =& $db;
		$this->logger =& $logger;

		/**
		 * 手動実行（当月1日以外）で実行する場合は引数指定
		 */
		if (isset($argv[2]) && $argv[2] != '')
		{
			$this->exec_date = $argv[2];
			$d = preg_split('/[\/\- :]+/', $this->exec_date);
			if ($d[0] && $d[1]) {
				$curr_ts = mktime(0, 0, 0, $d[1], 1, $d[0]);
			}
		}
		else
		{
			$curr_ts = time();
			$this->exec_date = date("Y-m-d", $curr_ts);
		}

		$this->exec_ym = str_replace('-', '', substr($this->exec_date, 0, 7));

		$prev_ts = $curr_ts - 86400;
		$prev_ym = date('Y-m', $prev_ts);
		$this->calc_sdate = $prev_ym.'-01';
		$this->calc_edate = $prev_ym.'-'.date('t', $prev_ts);
	}

	/**
	 * 毎月1日00:08頃に実行
	 */
	public function execute()
	{
		$this->logger->info('OrderCalcMonthly start.');
		$this->logger->info('['.$this->calc_sdate.' - '.$this->calc_edate.'] ExecDate: '.$this->exec_date.', ExecYm: '.$this->exec_ym);

		// 先月分の集計
		$OrdersDao = new OrdersDao($this->db);
		$OrdersDao->addSelect(OrdersDao::COL_ADVICE_USER_ID);
		$OrdersDao->addSelectSum(OrdersDao::COL_AMOUNT, 'sales_total');
		$OrdersDao->addSelectSum(OrdersDao::COL_REWARD, 'reward_total');
		$OrdersDao->addWhere(OrdersDao::COL_STATUS, OrdersDao::STATUS_RECEIVE);
		$OrdersDao->addWhereStr(OrdersDao::COL_CREATEDATE, $this->calc_sdate.' 00:00:00', '>=');
		$OrdersDao->addWhereStr(OrdersDao::COL_CREATEDATE, $this->calc_edate.' 23:59:59', '<=');
		$OrdersDao->addWhere(OrdersDao::COL_DELETE_FLAG, OrdersDao::DELETE_FLAG_ON);
		$OrdersDao->addWhere(OrdersDao::COL_DISPLAY_FLAG, OrdersDao::DISPLAY_FLAG_ON);
		$OrdersDao->addGroupBy(OrdersDao::COL_ADVICE_USER_ID);

		$order_list = $OrdersDao->select();

		$total = count($order_list);

		if ($total>0)
		{
			foreach ($order_list as $d)
			{
				try
				{
					$OrderPaymentsDao = new OrderPaymentsDao($this->db);
					$OrderPaymentsDao->addSelect(OrderPaymentsDao::COL_ORDER_PAYMENT_ID);
					$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_USER_ID, $d['advice_user_id']);
					$OrderPaymentsDao->addWhereStr(OrderPaymentsDao::COL_YEAR_MONTHLY, $this->exec_ym);
					$order_payment_id = $OrderPaymentsDao->selectId();

					// 先々月までの繰り越し分
					$OrderPaymentsDao->reset();
					$OrderPaymentsDao->addSelect(OrderPaymentsDao::COL_CARRY_TOTAL);
					$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_USER_ID, $d['advice_user_id']);
					if ($order_payment_id > 0) {
						$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_ORDER_PAYMENT_ID, $order_payment_id, '!=');
					}
					$OrderPaymentsDao->addOrder(OrderPaymentsDao::COL_YEAR_MONTHLY, 'DESC');
					$OrderPaymentsDao->addLimit(1);
					$carry_total = $OrderPaymentsDao->selectId();

//					// 報酬率
//					$UsersDao = new UsersDao($this->db);
//					$UsersDao->addSelect(UsersDao::COL_USER_ID);
//					$UsersDao->addSelect(UsersDao::COL_CHARGE_RATE);
//					$UsersDao->addWhere(UsersDao::COL_USER_ID, $d['advice_user_id']);
//					$user = $UsersDao->selectRow();
//					$charge_rate = app_get_user_charge_rate($user);

					$status = 0;
					$sales_total = $d['sales_total'];
					$reward_total = $d['reward_total'];
					//$reward_total = floor($sales_total * ($charge_rate / 100));

					// 先月報酬と過去の繰り越し分で最低支払額をチェック
					$reward_payment = $reward_total + $carry_total;
					if ($reward_payment >= APP_CONST_USER_DEFAULT_PAYMENT_MIN) {
						$bank_total = $reward_payment;
						$stock_total = 0;
						$carry_total = 0;
						$status = OrderPaymentsDao::STATUS_BEFORE;
					} else {
						$bank_total = 0;
						$stock_total = $reward_total;
						$carry_total = $reward_payment;
					}

					// 集計の更新
					$this->db->beginTransaction();

					$OrderPaymentsDao->reset();
					if ($status > 0) $OrderPaymentsDao->addValue(OrderPaymentsDao::COL_STATUS, $status);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_PREV_SALES_TOTAL, $sales_total);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_PREV_REWARD_TOTAL, $reward_total);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_BANK_TOTAL, $bank_total);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_STOCK_TOTAL, $stock_total);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_CARRY_TOTAL, $carry_total);

					if ($order_payment_id > 0)
					{
						$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_LASTUPDATE, Dao::DATE_NOW);
						$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_ORDER_PAYMENT_ID, $order_payment_id);
						$OrderPaymentsDao->doUpdate();
					}
					else
					{
						$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_USER_ID, $d['advice_user_id']);
						$OrderPaymentsDao->addValueStr(OrderPaymentsDao::COL_YEAR_MONTHLY, $this->exec_ym);
						$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_CREATEDATE, Dao::DATE_NOW);
						$OrderPaymentsDao->doInsert();
					}

					$this->db->commit();
				}
				catch (SpException $e)
				{
					$this->logger->exception($e);
					$this->db->rollback();
				}
			}
		}

		$this->logger->info('OrderCalcMonthly end: '.$total.' total');

		return true;
	}
}
?>
