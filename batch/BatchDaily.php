<?php
Sp::import('Cleaner', 'libs');
Sp::import('CloseConsult', 'libs');
Sp::import('RankingDailyAdvice', 'libs');
Sp::import('RankingDailyUser', 'libs');
//Sp::import('OrderCalcDaily', 'libs');
/**
 * 日次処理 00:03
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class BatchDaily extends BaseBatch
{
	/**
	 * 実行
	 */
	public function run()
	{
//		// 1日から前日までの相談料集計
//		$OrderCalcDaily = new OrderCalcDaily($this->db, $this->logger, $this->argv);
//		$OrderCalcDaily->execute();

		// 相談スレッドのクローズ処理
		$CloseConsult = new CloseConsult($this->db, $this->logger, $this->argv);
		$CloseConsult->execute();

		// 相談窓口のポイント集計
		$RankingDailyAdvice = new RankingDailyAdvice($this->db, $this->logger, $this->argv);
		$RankingDailyAdvice->execute();

		// ユーザーのポイント集計
		$RankingDailyUser = new RankingDailyUser($this->db, $this->logger, $this->argv);
		$RankingDailyUser->execute();

		// tmp掃除
		Cleaner::tempDir($this->logger, APP_CONST_TMP_DIR_REMOVE_DAYS);

		// log掃除
		Cleaner::logDir($this->logger, date("Y-m-d"));

		return true;
	}
}
?>
