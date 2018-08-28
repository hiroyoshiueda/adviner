<?php
Sp::import('OrderCalcMonthly', 'libs');
/**
 * 月次処理 00:08
 * @author Hiroyoshi
 */
class BatchMonthly extends BaseBatch
{
	/**
	 * 実行
	 */
	public function run()
	{
		// 報酬計算
		$OrderCalcMonthly = new OrderCalcMonthly($this->db, $this->logger, $this->argv);
		$OrderCalcMonthly->execute();

		return true;
	}
}
?>
