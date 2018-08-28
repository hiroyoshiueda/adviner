<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('FollowsDao', 'dao');
Sp::import('FeedsDao', 'dao');
Sp::import('FollowDataUtil', 'libs');
Sp::import('smarty/plugins/modifier.follow_btn.php', 'libs');
/**
 * 相談窓口API(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiAdviceController extends BaseController
{
	/**
	 * フォロー処理
	 */
	public function follow($is_follow=true)
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		$follow_advice_id = $this->form->getInt('follow_advice_id');

		if ($follow_advice_id > 0)
		{
			try
			{
				$FollowsDao = new FollowsDao($this->db);
				$FeedsDao = new FeedsDao($this->db);

				if ($is_follow)
				{
					$advicesDao = new AdvicesDao($this->db);
					$advicesDao->addSelect(AdvicesDao::COL_ADVICE_USER_ID);
					$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $follow_advice_id);
					$advicesDao->addWhere(AdvicesDao::COL_DELETE_FLAG, AdvicesDao::DELETE_FLAG_ON);
					$advicesDao->addWhere(AdvicesDao::COL_DISPLAY_FLAG, AdvicesDao::DISPLAY_FLAG_ON);
					$advice_user_id = $advicesDao->selectId();
					if ($advice_user_id > 0)
					{
						FollowDataUtil::addFollowAdvice($FollowsDao, $FeedsDao, $userInfo['id'], $follow_advice_id, $advice_user_id);
						$json_data['html'] = smarty_modifier_follow_btn(1, $follow_advice_id, 'a', '', false);
					}
				}
				else
				{
					FollowDataUtil::delFollowAdvice($FollowsDao, $FeedsDao, $userInfo['id'], $follow_advice_id);
					$json_data['html'] = smarty_modifier_follow_btn(0, $follow_advice_id, 'a', '', false);
				}
				$json_data['result'] = 1;
				// キャッシュをクリア
				$this->form->clearSession(self::CACHE_FRIENDS_ACTIVE_LIST);
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
			}
		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * フォロー解除処理
	 */
	public function unfollow()
	{
		return $this->follow(false);
	}
}
?>
