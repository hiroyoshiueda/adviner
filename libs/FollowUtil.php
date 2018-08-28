<?php
Sp::import('FollowsDao', 'dao');
/**
 * フォロー関連
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class FollowUtil
{
	private $followsDao = null;

	function __construct(&$db)
	{
		$this->followsDao = new FollowsDao($db);
	}

	/**
	 * フォローしているデータ
	 * @param int $user_id
	 */
	public function getFollowing($user_id)
	{
		$following = array('user' => array(), 'advice' => array());

		$follow_list = $this->followsDao->getList($user_id);

		if (count($follow_list) > 0)
		{
			foreach ($follow_list as $d)
			{
				if ($d['follow_user_id'] > 0) {
					$following['user'][] = $d['follow_user_id'];
				}
				if ($d['follow_advice_id'] > 0) {
					$following['advice'][] = $d['follow_advice_id'];
				}
			}
		}
		return $following;
	}
}
?>
