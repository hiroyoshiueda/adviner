<?php
Sp::import('OrdersDao', 'dao');
Sp::import('OrderPaymentsDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * 報酬計算（日次）
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class OrderCalcDaily
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

	/**
	 *
	 * 引数無し：1日から前日23:59:59まで集計
	 * 引数有り：1日から引数日23:59:59まで集計
	 *
	 * @param unknown_type $db
	 * @param unknown_type $logger
	 * @param unknown_type $argv
	 */
	public function __construct(&$db, &$logger, &$argv)
	{
		$this->db =& $db;
		$this->logger =& $logger;

		/**
		 * 手動実行で実行する場合は引数指定
		 */
		if (isset($argv[2]) && $argv[2] != '')
		{
			$this->exec_date = $argv[2];
			$d = preg_split('/[\/\- :]+/', $this->exec_date);
			if ($d[0] && $d[1] && $d[2]) {
				$curr_ts = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
			}
		}
		else
		{
			// デフォルトは前日
			$curr_ts = time() - 86400;
			$this->exec_date = date("Y-m-d", $curr_ts);
		}

		$this->exec_ym = str_replace('-', '', substr($this->exec_date, 0, 7));

		$this->calc_sdate = date("Y-m-01", $curr_ts);
		$this->calc_edate = $this->exec_date;
	}

	/**
	 * 毎日00:03頃に実行
	 */
	public function execute()
	{
		$this->logger->info('OrderCalcDaily start.');
		$this->logger->info('['.$this->calc_sdate.' - '.$this->calc_edate.'] ExecDate: '.$this->exec_date.', ExecYm: '.$this->exec_ym);

		// １日から指定日までの集計
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

					$sales_total = $d['sales_total'];
					$reward_total = $d['reward_total'];

					// 集計の更新
					$this->db->beginTransaction();

					$OrderPaymentsDao->reset();
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_SALES_TOTAL, $sales_total);
					$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_REWARD_TOTAL, $reward_total);

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
						$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_STATUS, OrderPaymentsDao::STATUS_BEFORE);
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

		$this->logger->info('OrderCalcDaily end: '.$total.' total');

		return true;
	}
}
?>
