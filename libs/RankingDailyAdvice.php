<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('AdviceRanksDao', 'dao');
/**
 * アドバイス窓口ランキング集計（日次）
 * @author Hiroyoshi
 */
class RankingDailyAdvice
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

	/**
	 * 実行
	 */
	public function execute()
	{
		$this->logger->info('RankingDailyAdvice start.');

		$adviceRanksDao = new AdviceRanksDao($this->db);
		$rank_list = $adviceRanksDao->select();

		$total = count($rank_list);

		if ($total>0)
		{
			foreach ($rank_list as $d)
			{
				try
				{
					// ポイントの集計
					$this->_shiftCount($d);

					// 集計の更新
					$this->db->beginTransaction();

					$adviceRanksDao->reset();
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_TODAY, $d[AdviceRanksDao::COL_PV_TODAY]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_1, $d[AdviceRanksDao::COL_PV_1]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_2, $d[AdviceRanksDao::COL_PV_2]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_3, $d[AdviceRanksDao::COL_PV_3]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_4, $d[AdviceRanksDao::COL_PV_4]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_5, $d[AdviceRanksDao::COL_PV_5]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_6, $d[AdviceRanksDao::COL_PV_6]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_PV_7, $d[AdviceRanksDao::COL_PV_7]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_TODAY, $d[AdviceRanksDao::COL_CONSULT_TODAY]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_1, $d[AdviceRanksDao::COL_CONSULT_1]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_2, $d[AdviceRanksDao::COL_CONSULT_2]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_3, $d[AdviceRanksDao::COL_CONSULT_3]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_4, $d[AdviceRanksDao::COL_CONSULT_4]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_5, $d[AdviceRanksDao::COL_CONSULT_5]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_6, $d[AdviceRanksDao::COL_CONSULT_6]);
					$adviceRanksDao->addValue(AdviceRanksDao::COL_CONSULT_7, $d[AdviceRanksDao::COL_CONSULT_7]);
					$adviceRanksDao->addWhere(AdviceRanksDao::COL_ADVICE_ID, $d['advice_id']);
					$adviceRanksDao->doUpdate();

					$advicesDao = new AdvicesDao($this->db);
					$advicesDao->addValue(AdvicesDao::COL_ADVICE_POINT, $d[AdvicesDao::COL_ADVICE_POINT]);
					$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $d['advice_id']);
					$advicesDao->doUpdate();

					$this->db->commit();
				}
				catch (SpException $e)
				{
					$this->logger->error($d);
					$this->logger->exception($e);
					$this->db->rollback();
				}
			}
		}

		$this->logger->info('RankingDailyAdvice end: '.$total.' total');

		return true;
	}

	/**
	 * ランキングポイントの集計
	 * @param array $d
	 */
	private function _shiftCount(&$d)
	{
		$pv_total = 0;
		$consult_total = 0;
		// ポイント対象は5日間
		$point_days = 6;
		// PVと相談数
		for ($i=7; $i>=0; $i--) {
			// PV
			$pv = ($i==0) ? $d['pv_today'] : $d['pv_'.$i];
			$d['pv_'.($i+1)] = $pv;
			if ($i<$point_days) $pv_total += $pv;
			// 相談数
			$consult = ($i==0) ? $d['consult_today'] : $d['consult_'.$i];
			$d['consult_'.($i+1)] = $consult;
			if ($i<$point_days) $consult_total += $consult;
		}
		// 今日分はリセット
		$d['pv_today'] = 0;
		$d['consult_today'] = 0;
		// ポイント
		$d['advice_point'] = 0;
		// 相談数は3倍
		$d['advice_point'] = $pv_total + ($consult_total * 3);

		return;
	}
}
?>
