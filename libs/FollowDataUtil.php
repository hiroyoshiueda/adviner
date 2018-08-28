<?php
/**
 *
 */
class FollowDataUtil
{
	/**
	 * followDataの取得
	 * @param FollowsDao $followsDao
	 * @param AdvicesDao $advicesDao
	 * @param int $user_id
	 */
	public static function getFollowData(&$followsDao, &$advicesDao, $user_id)
	{
		if ($user_id > 0)
		{
			$followData = array('user_id'=>array(), 'advice_id'=>array());

			$follow_list = $followsDao->getList($user_id);
			if (count($follow_list) > 0)
			{
				foreach ($follow_list as $d)
				{
					if ($d['follow_user_id'] > 0)
					{
						$followData['user_id'][] = $d['follow_user_id'];
					}
					else if ($d['follow_advice_id'] > 0)
					{
						$followData['advice_id'][] = $d['follow_advice_id'];
					}
//					else if ($d['follow_category_id'] > 0)
//					{
//						$advicesDao->reset();
//						$list = $advicesDao->getIdListByCategoryId($d['follow_category_id']);
//						if (count($list) > 0)
//						{
//							foreach ($list as $dd) {
//								if ($dd['advice_id'] > 0) $followData['advice_id'][] = $dd['advice_id'];
//							}
//						}
//					}
				}
				$followData['user_id'] = array_unique($followData['user_id']);
				$followData['advice_id'] = array_unique($followData['advice_id']);
			}
			return $followData;
		}
		return null;
	}

	/**
	 * ユーザー追加
	 * @param FollowsDao $followsDao
	 * @param FeedsDao $feedsDao
	 * @param int $user_id
	 * @param int $follow_user_id
	 */
	public static function addFollowUser(&$followsDao, &$feedsDao, $user_id, $follow_user_id)
	{
		$followsDao->reset();
		if ($followsDao->addUser($user_id, $follow_user_id) > 0)
		{
			$feedsDao->reset();
			$feedsDao->delete($user_id);
		}
	}

	/**
	 * ユーザー解除
	 * @param FollowsDao $followsDao
	 * @param FeedsDao $feedsDao
	 * @param int $user_id
	 * @param int $follow_user_id
	 */
	public static function delFollowUser(&$followsDao, &$feedsDao, $user_id, $follow_user_id)
	{
		$followsDao->reset();
		if ($followsDao->delUser($user_id, $follow_user_id))
		{
			$feedsDao->reset();
			$feedsDao->delete($user_id);
		}
	}

	/**
	 * ユーザー追加
	 * @param FollowsDao $followsDao
	 * @param FeedsDao $feedsDao
	 * @param int $user_id
	 * @param int $follow_user_id
	 */
	public static function addFollowAdvice(&$followsDao, &$feedsDao, $user_id, $follow_advice_id, $advice_user_id)
	{
		$followsDao->reset();
		if ($followsDao->addAdvice($user_id, $follow_advice_id, $advice_user_id) > 0)
		{
			$feedsDao->reset();
			$feedsDao->delete($user_id);
		}
	}

	/**
	 * ユーザー解除
	 * @param FollowsDao $followsDao
	 * @param FeedsDao $feedsDao
	 * @param int $user_id
	 * @param int $follow_user_id
	 */
	public static function delFollowAdvice(&$followsDao, &$feedsDao, $user_id, $follow_advice_id)
	{
		$followsDao->reset();
		if ($followsDao->delAdvice($user_id, $follow_advice_id))
		{
			$feedsDao->reset();
			$feedsDao->delete($user_id);
		}
	}
}
?>