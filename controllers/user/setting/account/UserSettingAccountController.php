<?php
Sp::import('UserAccountsDao', 'dao');
Sp::import('UserRanksDao', 'dao');
/**
 * 設定 - 銀口座設定
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSettingAccountController extends BaseController
{
	/**
	 * 支払い銀行口座設定
	 */
	public function index()
	{
		if ($this->isForceHTTPS()) return $this->forceHTTPS();
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$userInfo = $this->getUserInfo();

		$UserAccountsDao = new UserAccountsDao($this->db);
		$account = $UserAccountsDao->getItemByUserId($userInfo['id']);

		if ($this->form->isGetMethod())
		{
			$this->form->setDefaultAll($account);
		}

		$this->setLoadUserRank(new UserRanksDao($this->db));

		$this->createSecurityCode();

		$this->form->set('htitle', '銀行口座設定');
		$this->setTitle($this->form->get('htitle'), '設定');

		return $this->forward('user/setting/account/user_setting_account_index', APP_CONST_MAIN_FRAME);
	}

	public function save()
	{
		if ($this->isForceHTTPS() || $this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(parent::ERROR_PAGE_MESSAGE5);
		}
		else if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
		}
		else
		{
			try
			{
				$UserAccountsDao = new UserAccountsDao($this->db);
				$account = $UserAccountsDao->getItemByUserId($userInfo['id']);

				$this->db->beginTransaction();

				$UserAccountsDao = new UserAccountsDao($this->db);
				$UserAccountsDao->addValue(UserAccountsDao::COL_PAYMENT_TYPE, 1);
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BANK_NAME, $this->form->get('bank_name'));
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BANK_CODE, $this->form->get('bank_code'));
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BRANCH_NAME, $this->form->get('branch_name'));
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BRANCH_CODE, $this->form->get('branch_code'));
				$UserAccountsDao->addValue(UserAccountsDao::COL_DEPOSIT_TYPE, $this->form->get('deposit_type'));
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BANK_NUMBER, $this->form->get('bank_number'));
				$UserAccountsDao->addValueStr(UserAccountsDao::COL_BANK_HOLDER, $this->form->get('bank_holder'));
				$UserAccountsDao->addValue(UserAccountsDao::COL_LASTUPDATE, Dao::DATE_NOW);

				if (empty($account))
				{
					$UserAccountsDao->addValue(UserAccountsDao::COL_USER_ID, $userInfo['id']);
					$UserAccountsDao->addValue(UserAccountsDao::COL_CREATEDATE, Dao::DATE_NOW);
					$UserAccountsDao->doInsert();
				}
				else
				{
					$UserAccountsDao->addWhere(UserAccountsDao::COL_USER_ID, $userInfo['id']);
					$UserAccountsDao->doUpdate();
				}

				$this->db->commit();

				return $this->resp->sendRedirect('/user/setting/account/?save=true');
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$this->db->rollback();
			}
		}
		return $this->index();
	}

	public function delete()
	{
		if ($this->isForceHTTPS() || $this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$UserAccountsDao = new UserAccountsDao($this->db);
		$account = $UserAccountsDao->getItemByUserId($userInfo['id']);

		if ($account['user_account_id'] > 0)
		{
			$UserAccountsDao = new UserAccountsDao($this->db);
			$UserAccountsDao->delete($account['user_account_id']);
		}

		return $this->resp->sendRedirect('/user/setting/account/?delete=true');
	}

	/**
	 * 入力チェック
	 */
	private function _validate()
	{
		return $this->form->validate($this->form->getValidates(0));
	}
}
?>
