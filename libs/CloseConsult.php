<?php
Sp::import('ConsultsDao', 'dao');
/**
 * 相談スレッドのクローズ処理
 * 開始から7日間経過したもの
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class CloseConsult
{
	/**
	 * @var DbManager
	 */
	private $db;

	/**
	 * @var SpLogger
	 */
	private $logger;

	public function __construct(&$db, &$logger, &$argv)
	{
		$this->db =& $db;
		$this->logger =& $logger;
	}

	public function execute()
	{
		$this->logger->info('CloseConsult start.');

		$total = 0;
		$total2 = 0;

		try
		{
			$this->db->beginTransaction();

			$ts = time() - (86400 * 7);

			/**
			 * - 有料相談
			 * - 評価待ち
			 * - アドバイスください
			 * 以外の相談を終了
			 */
			$ConsultsDao = new ConsultsDao($this->db);
			$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_FINISH);
			$ConsultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_NOWRITE);
			$ConsultsDao->addValue(ConsultsDao::COL_FINISHDATE, Dao::DATE_NOW);
			$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
			$ConsultsDao->addWhere(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_WAIT, '!=');
			$ConsultsDao->addWhere(ConsultsDao::COL_PLEASE_FLAG, ConsultsDao::PLEASE_FLAG_OFF);
			$ConsultsDao->addWhere(ConsultsDao::COL_ADVICE_CHARGE_FLAG, ConsultsDao::CHARGE_FLAG_FREE);
			$ConsultsDao->addWhereStr(ConsultsDao::COL_CREATEDATE, date("Y-m-d 00:00:00", $ts), '<');
			$total = $ConsultsDao->doUpdate();

			/**
			 * 有料相談のうちアドバイスがまだの相談は終了
			 */
			$ConsultsDao = new ConsultsDao($this->db);
			$ConsultsDao->addValue(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_FINISH);
			$ConsultsDao->addValue(ConsultsDao::COL_REVIEW_STATE, ConsultsDao::REVIEW_STATE_NOWRITE);
			$ConsultsDao->addValue(ConsultsDao::COL_FINISHDATE, Dao::DATE_NOW);
			$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_STATUS, ConsultsDao::CONSULT_STATUS_DURING);
			$ConsultsDao->addWhere(ConsultsDao::COL_REVIEW_STATE, 0);
			$ConsultsDao->addWhere(ConsultsDao::COL_ADVICE_CHARGE_FLAG, ConsultsDao::CHARGE_FLAG_CHARGE);
			$ConsultsDao->addWhereStr(ConsultsDao::COL_CREATEDATE, date("Y-m-d 00:00:00", $ts), '<');
			$total2 = $ConsultsDao->doUpdate();

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		$this->logger->info('CloseConsult [Total '.$total.', Charge Total '.$total2.'] end.');

		return true;
	}
}
?>
