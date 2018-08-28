<?php
Sp::import('UsersDao', 'dao');
Sp::import('AdvicesDao', 'dao');
Sp::import('CategorysDao', 'dao');
Sp::import('NoticesDao', 'dao');
/**
 * 設定／退会
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSettingResignController extends BaseController
{
	/**
	 * 退会画面
	 */
	public function index()
	{
		if ($this->isForceHTTPS()) return $this->forceHTTPS();
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$this->createSecurityCode();

		$this->form->set('htitle', '退会する');
		$this->setTitle($this->form->get('htitle'), '設定');

		$this->resp->noCache();

		return $this->forward('user/setting/resign/user_setting_resign_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 退会処理
	 */
	public function user_resign()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(parent::ERROR_PAGE_MESSAGE5);
		}

		try
		{
			$this->db->beginTransaction();

			// 相談窓口
			$AdvicesDao = new AdvicesDao($this->db);
			$AdvicesDao->addSelect(AdvicesDao::COL_ADVICE_ID);
			$AdvicesDao->addSelect(AdvicesDao::COL_CATEGORY_ID);
			$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_USER_ID, $userInfo['id']);
			$AdvicesDao->addWhere(AdvicesDao::COL_DELETE_FLAG, AdvicesDao::DELETE_FLAG_ON);
			$advices = $AdvicesDao->select();

			if (count($advices) > 0)
			{
				$CategorysDao = new CategorysDao($this->db);

				foreach ($advices as $d) {
					$AdvicesDao->delete($d['advice_id'], $userInfo['id']);
					// カテゴリの数をカウント変更
					$CategorysDao->updateCountDownTotal($d['category_id']);
				}
			}

			$NoticesDao = new NoticesDao($this->db);
			$NoticesDao->delete($userInfo['id']);

			$UsersDao = new UsersDao($this->db);
			$UsersDao->delete($userInfo['id']);

			$this->deleteUserInfo();

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		$this->form->set('htitle', '退会処理が完了しました。');
		$this->setTitle($this->form->get('htitle'));

		$this->resp->noCache();

		return $this->forward('user/setting/resign/user_setting_resign_ok', APP_CONST_MAIN_FRAME);
	}
}
?>
