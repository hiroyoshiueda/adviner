<?php
Sp::import('UsersDao', 'dao');
Sp::import('FollowsDao', 'dao');
Sp::import('FeedsDao', 'dao');
Sp::import('FollowDataUtil', 'libs');
Sp::import('smarty/plugins/modifier.follow_btn.php', 'libs');
/**
 * ユーザーAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiUsersController extends BaseController
{
//	/**
//	 * フォロー処理
//	 */
//	public function follow($is_follow=true)
//	{
//		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();
//
//		$json_data = array(
//			'html' => '',
//			'result' => 0,
//			'errmsg' => ''
//		);
//
//		$userInfo = $this->getUserInfo();
//
//		$follow_user_id = $this->form->getInt('follow_user_id');
//
//		if ($follow_user_id > 0)
//		{
//			try
//			{
//				$FollowsDao = new FollowsDao($this->db);
//				$FeedsDao = new FeedsDao($this->db);
//
//				if ($is_follow)
//				{
//					FollowDataUtil::addFollowUser($FollowsDao, $FeedsDao, $userInfo['id'], $follow_user_id);
//					$json_data['html'] = smarty_modifier_follow_btn(1, $follow_user_id, 'u', '', false);
//				}
//				else
//				{
//					FollowDataUtil::delFollowUser($FollowsDao, $FeedsDao, $userInfo['id'], $follow_user_id);
//					$json_data['html'] = smarty_modifier_follow_btn(0, $follow_user_id, 'u', '', false);
//				}
//				$json_data['result'] = 1;
//				// キャッシュをクリア
//				$this->form->clearSession(self::CACHE_FRIENDS_ACTIVE_LIST);
//			}
//			catch (SpException $e)
//			{
//				$this->logger->exception($e);
//				$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
//			}
//		}
//
//		return $this->jsonPage($json_data, false);
//	}
//
//	/**
//	 * フォロー解除処理
//	 */
//	public function unfollow()
//	{
//		return $this->follow(false);
//	}
}
?>
