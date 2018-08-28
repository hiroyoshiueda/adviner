<?php
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
/**
 * ユーザーランキング集計（日次）
 * @author Hiroyoshi
 */
class RankingDailyUser
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
		$this->logger->info('RankingDailyUser start.');

		$userRanksDao = new UserRanksDao($this->db);
		$rank_list = $userRanksDao->select();

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

					$userRanksDao->reset();
					$userRanksDao->addValue(UserRanksDao::COL_LEVEL, $d[UserRanksDao::COL_LEVEL]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_TODAY, $d[UserRanksDao::COL_CONSULT_TODAY]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_1, $d[UserRanksDao::COL_CONSULT_1]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_2, $d[UserRanksDao::COL_CONSULT_2]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_3, $d[UserRanksDao::COL_CONSULT_3]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_4, $d[UserRanksDao::COL_CONSULT_4]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_5, $d[UserRanksDao::COL_CONSULT_5]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_6, $d[UserRanksDao::COL_CONSULT_6]);
					$userRanksDao->addValue(UserRanksDao::COL_CONSULT_7, $d[UserRanksDao::COL_CONSULT_7]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_TODAY, $d[UserRanksDao::COL_FAVORITE_TODAY]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_1, $d[UserRanksDao::COL_FAVORITE_1]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_2, $d[UserRanksDao::COL_FAVORITE_2]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_3, $d[UserRanksDao::COL_FAVORITE_3]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_4, $d[UserRanksDao::COL_FAVORITE_4]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_5, $d[UserRanksDao::COL_FAVORITE_5]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_6, $d[UserRanksDao::COL_FAVORITE_6]);
					$userRanksDao->addValue(UserRanksDao::COL_FAVORITE_7, $d[UserRanksDao::COL_FAVORITE_7]);
					$userRanksDao->addWhere(UserRanksDao::COL_USER_ID, $d['user_id']);
					$userRanksDao->doUpdate();

					$usersDao = new UsersDao($this->db);
					$usersDao->addValue(UsersDao::COL_USER_POINT, $d[UsersDao::COL_USER_POINT]);
					$usersDao->addWhere(UsersDao::COL_USER_ID, $d['user_id']);
					$usersDao->doUpdate();

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

		$this->logger->info('RankingDailyUser end: '.$total.' total');

		return true;
	}

	/**
	 * ランキングポイントの集計
	 * @param array $d
	 */
	private function _shiftCount(&$d)
	{
		$consult_total = 0;
		$favorite_total = 0;
		// ポイント対象は5日間
		$point_days = 5;
		// 相談数とお気に入り数
		for ($i=7; $i>=0; $i--) {
			// 相談数
			$consult = ($i==0) ? $d['consult_today'] : $d['consult_'.$i];
			$d['consult_'.($i+1)] = $consult;
			if ($i<$point_days) $consult_total += $consult;
			// お気に入り数
			$favorite = ($i==0) ? $d['favorite_today'] : $d['favorite_'.$i];
			$d['favorite_'.($i+1)] = $favorite;
			if ($i<$point_days) $favorite_total += $favorite;
		}
		// 今日分はリセット
		$d['consult_today'] = 0;
		$d['favorite_today'] = 0;
		/**
		 * 相談数：3倍
		 * お気に入り数：2倍
		 * 評価平均：10倍して切り上げ
		 */
		$d['user_point'] = ($consult_total * 3) + ($favorite_total * 2);
		$d['user_point'] = $d['user_point'] + ceil($d['evaluate_ave'] * 10);
		// levelの算出
		$all_total = $d['evaluate_total'] + $d['consult_total'] + $d['favorite_total'];
		if ($all_total<=25)
		{
			$d['level'] = 1;
		}
		else if ($all_total<=250)
		{
			// level 10まで
			$d['level'] = floor($all_total / 25);
		}
		else if ($all_total<=650)
		{
			// level 20まで
			$d['level'] = floor($all_total / 40);
		}
		else
		{
			$d['level'] = floor($all_total / 80);
		}
		return;
	}
}
?>
