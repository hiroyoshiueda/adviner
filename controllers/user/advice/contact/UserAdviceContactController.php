<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('AdviceRanksDao', 'dao');
Sp::import('CategorysDao', 'dao');
/**
 * 相談窓口登録フォーム(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserAdviceContactController extends BaseController
{
	/**
	 * 新規登録
	 */
	public function index()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$userInfo = $this->getUserInfo();

		$CategorysDao = new CategorysDao($this->db);
		$this->form->setSp('categoryOptions', $CategorysDao->getSelectOptions());

		if ($this->form->isGetMethod()) {
			//$this->form->set('public_type', '1');
			$this->form->set('comment_status', '1');
			$this->form->set('charge_flag', '0');
			$this->form->set('charge_count', '1');
		}

		$this->form->setParameterForm('id');

		$this->form->set('htitle', '相談窓口の登録');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/advice/contact/user_advice_contact_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 編集
	 */
	public function edit()
	{
		if ($this->checkUserAuth() === false) return $this->loginPage();

		$id = $this->form->getInt('id');
		if (empty($id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$AdvicesDao = new AdvicesDao($this->db);
		$item = $AdvicesDao->getItemByUserId($id, $userInfo['id']);
		if (empty($item)) return $this->notfound();

		$item['conditions'] = app_split_to_array($item['conditions']);
		if ($item['charge_price'] != '') $item['charge_price'] = preg_replace("/\.[0-9]+$/", '', $item['charge_price']);

		$this->index();

		if ($this->form->isGetMethod()) {
			$this->form->setAll($item);
		}

		$this->form->setParameter('default_category_id', $item['category_id']);
		$this->form->setParameter('advice_status', $item['advice_status']);

		if ($item['advice_status'] == AdvicesDao::ADVICE_STATUS_REFUSE) {
			$this->form->set('htitle', '相談窓口の再申込み');
		} else {
			$this->form->set('htitle', '相談窓口の変更');
		}
		$this->setTitle($this->form->get('htitle'), 'マイページ');

		return true;
	}

	/**
	 * 確認画面
	 */
	public function confirm()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->index();
		}

		$id = $this->form->getInt('id');

		$userInfo = $this->getUserInfo();

		if ($this->form->get('charge_flag') == 1) {
			//$this->form->set('public_type', 1);
			// 金額計算
			$charge_rate = $this->getUserChargeRate();
			$your_reward = floor($this->form->get('charge_price') * ($charge_rate / 100));
			$system_fee = $this->form->get('charge_price') - $your_reward;
			$this->form->set('your_reward', $your_reward);
			$this->form->set('system_fee', $system_fee);
		}

		$this->form->setParameterForm('id');
		$this->form->setParameterForm('category_id');
		$this->form->setParameterForm('advice_status');
		$this->form->setParameterForm('advice_title');
		$this->form->setParameterForm('advice_body');
		$this->form->setParameterForm('advice_tag');
		//$this->form->setParameterForm('public_type');
		//$this->form->setParameterForm('comment_status');
		$this->form->setParameterForm('charge_flag');
		$this->form->setParameterForm('charge_price');
		//$this->form->setParameterForm('charge_count');
		$this->form->setParameterForm('charge_body');
		$this->form->setParameterForm('agree');
		$this->form->setParameterForm('agree2');
		$this->form->setParameterForm('default_category_id');

		$this->createSecurityCode();

		if ($id > 0) {
			if ($this->form->get('advice_status') == AdvicesDao::ADVICE_STATUS_REFUSE) {
				$this->form->set('htitle', '相談窓口の再申込み（確認）');
			} else {
				$this->form->set('htitle', '相談窓口の変更（確認）');
			}
		} else {
			$this->form->set('htitle', '相談窓口の登録（確認）');
		}
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('user/advice/contact/user_advice_contact_confirm', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 保存処理
	 */
	public function save()
	{
//		// facebook callback
//		if ($this->form->get('state') != '' && $this->form->get('code') != '')
//		{
//			return $this->_facebook_share();
//		}

		if ($this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(self::ERROR_PAGE_MESSAGE5);

		} else if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
		}
		else
		{
			$id = $this->form->getInt('id');

			if ($this->form->get('advice_tag')!='')
			{
				$this->form->set('advice_tag', mb_ereg_replace('[/,、　]+', ' ', $this->form->get('advice_tag')));
				$this->form->set('advice_tag_search', mb_split('[ ]+', $this->form->get('advice_tag')));
			}

			$userInfo = $this->getUserInfo();

			try
			{
				$this->db->beginTransaction();

				$AdvicesDao = new AdvicesDao($this->db);
				$AdvicesDao->addValue(AdvicesDao::COL_CATEGORY_ID, $this->form->get('category_id'));
				$AdvicesDao->addValueStr(AdvicesDao::COL_ADVICE_TITLE, $this->form->get('advice_title'));
				$AdvicesDao->addValueStr(AdvicesDao::COL_ADVICE_BODY, $this->form->get('advice_body'));
				$AdvicesDao->addValueStr(AdvicesDao::COL_ADVICE_TAG, $this->form->get('advice_tag'));
				$AdvicesDao->addValueStr(AdvicesDao::COL_ADVICE_TAG_SEARCH, $this->form->getToStringEnclose('advice_tag_search', '[', ']'));
				// 有料
				if ($this->form->get('charge_flag') == 1)
				{
					$AdvicesDao->addValue(AdvicesDao::COL_PUBLIC_TYPE, AdvicesDao::PUBLIC_TYPE_PRIVATE);
					$AdvicesDao->addValue(AdvicesDao::COL_CHARGE_FLAG, AdvicesDao::CHARGE_FLAG_CHARGE);
					$AdvicesDao->addValue(AdvicesDao::COL_CHARGE_PRICE, $this->form->get('charge_price'));
					$AdvicesDao->addValue(AdvicesDao::COL_CHARGE_COUNT, $this->form->get('charge_count'));
					$AdvicesDao->addValueStr(AdvicesDao::COL_CHARGE_BODY, $this->form->get('charge_body'));
				}
				else
				{
					$AdvicesDao->addValue(AdvicesDao::COL_PUBLIC_TYPE, AdvicesDao::PUBLIC_TYPE_PUBLIC);
					//$AdvicesDao->addValue(AdvicesDao::COL_PUBLIC_TYPE, ($this->form->get('public_type')==1 ? AdvicesDao::PUBLIC_TYPE_PRIVATE : AdvicesDao::PUBLIC_TYPE_PUBLIC));
				}

				if ($id > 0)
				{
					// 再申込み
					if ($this->form->get('advice_status') == AdvicesDao::ADVICE_STATUS_REFUSE) {
						$AdvicesDao->addValue(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_EXAMINE);
					}
					$AdvicesDao->addValue(AdvicesDao::COL_LASTUPDATE, Dao::DATE_NOW);
					$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $id);
					$AdvicesDao->addWhere(AdvicesDao::COL_ADVICE_USER_ID, $userInfo['id']);
					$AdvicesDao->doUpdate();

					// カテゴリの数を変更
					if ($this->form->get('advice_status') != AdvicesDao::ADVICE_STATUS_REFUSE)
					{
						$default_category_id = $this->form->get('default_category_id');
						if ($default_category_id>0 && $default_category_id != $this->form->get('category_id'))
						{
							// カウントダウン
							$CategorysDao = new CategorysDao($this->db);
							$CategorysDao->updateCountDownTotal($default_category_id);
							// カウントアップ
							$CategorysDao->reset();
							$CategorysDao->updateCountUpTotal($this->form->get('category_id'));
						}
					}
				}
				else
				{
					$AdvicesDao->addValue(AdvicesDao::COL_ADVICE_USER_ID, $userInfo['id']);
					$AdvicesDao->addValue(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_EXAMINE);
					$AdvicesDao->addValue(AdvicesDao::COL_CREATEDATE, Dao::DATE_NOW);
					$AdvicesDao->doInsert();

					$new_advice_id = $AdvicesDao->getLastInsertId();

					$AdviceRanksDao = new AdviceRanksDao($this->db);
					$AdviceRanksDao->addValue(AdviceRanksDao::COL_ADVICE_ID, $new_advice_id);
					$AdviceRanksDao->addValue(AdviceRanksDao::COL_ADVICE_USER_ID, $userInfo['id']);
					$AdviceRanksDao->doInsert();
				}

				$this->db->commit();

				$qstr = ($id>0) ? 'edit_advice=true' : 'new_advice=true';
				$redirect = '/user/advice/?' . $qstr;

//				// Facebook共有
//				if ($this->form->get('advice_fb_share') == 1)
//				{
//					$this->form->setSession('rd_url', $rd_url);
//					$this->form->setSession('ses_advice_id', ($id>0 ? $id : $new_advice_id));
//					$this->form->setSession('ses_advice_title', $this->form->get('advice_title').' - '.$userInfo['nickname'].APP_CONST_SITE_TITLE3);
//					$description = preg_replace('/[　\s\r\n\t]+/u', '', $this->form->get('advice_body'));
//					$description = mb_substr($description, 0, 200);
//					$this->form->setSession('ses_advice_description', $description);
//					$facebook = new Facebook(array(
//						'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
//						'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
//					));
//					$oauth_url = $this->getFbOAuth($facebook);
//					return $this->resp->sendRedirect($oauth_url);
//				}

				$this->resp->sendRedirect($redirect);
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$this->db->rollback();
			}
		}

		return $this->index();
	}

//	/**
//	 * Facebook共有
//	 */
//	private function _facebook_share()
//	{
//		if ($this->checkUserAuth() === false) return $this->notfound();
//
//		$rd_url = $this->form->get('rd_url', '/user/mypage/');
//		$ses_advice_id = $this->form->getInt('ses_advice_id');
//		$ses_advice_title = $this->form->get('ses_advice_title');
//		$ses_advice_description = $this->form->get('ses_advice_description');
//
//		$this->form->clearSession('rd_url');
//		$this->form->clearSession('ses_advice_id');
//		$this->form->clearSession('ses_advice_title');
//		$this->form->clearSession('ses_advice_description');
//
//		$facebook = new Facebook(array(
//			'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
//			'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
//		));
//
//		try
//		{
//			if ($facebook->getUser())
//			{
//				/**
//				 * https://developers.facebook.com/docs/reference/api/post/
//				 */
//				$response = $facebook->api('/me/feed', 'POST', array(
//						'message' => "Adviner [アドバイナー] の相談窓口を登録しました。\nお気軽にご相談ください！",
//						'link' => constant('app_site_url').'advice/'.$ses_advice_id.'/',
//						'name' => $ses_advice_title,
//						'description' => $ses_advice_description,
//						'picture' => constant('app_site_url').'img/fb_page.png'
//					)
//				);
//				//$this->logger->debug($response);
//			}
//		}
//		catch (FacebookApiException $e)
//		{
//			$this->logger->exception($e);
//		}
//		catch (SpException $e)
//		{
//			$this->logger->exception($e);
//		}
//
//		return $this->resp->sendRedirect($rd_url);
//	}

	private function _validate()
	{
		$ret = $this->form->validate($this->form->getValidates(0));

		$charge_flag = $this->form->get('charge_flag');

		if ($charge_flag == 1)
		{
			$v = array(
				array('charge_price', '有料相談一回の価格を入力してください。', 'required'),
				array('charge_price', '有料相談一回の価格は半角数字を入力してください。', 'number'),
				array('charge_price', '有料相談一回の価格は 100円 ～ 3,000円の範囲内で設定してください。', 'num_range', array(100, 3000)),
				//array('charge_count', '有料相談一回のアドバイス回数を選択してください。', 'required'),
				//array('charge_count', '有料相談一回のアドバイス回数は半角数字を入力してください。', 'number'),
				//array('charge_count', '有料相談一回のアドバイス回数は 10以下の数字を入力してください。', 'num_range', array(0, 10)),
				array('charge_body', '有料相談に関する詳細を入力してください。', 'required'),
				array('charge_body', '有料相談に関する詳細は全角800文字以下で入力してください。', 'maxlengthZen', array(800)),
				array('agree2', '有料でアドバイスをする場合は、有料アドバイス業務規約の内容に同意いただきチェックを入れてください。', 'required')
			);
			if ($this->form->validate($v) === false) {
				$ret = false;
			}
		}

		return $ret;
	}

	/**
	 * 相談窓口の削除
	 */
	public function delete()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$id = $this->form->getInt('id');
		if (empty($id)) return $this->notfound();

		$userInfo = $this->getUserInfo();

		try
		{
			$advicesDao = new AdvicesDao($this->db);
			$advice = $advicesDao->getItemByUserId($id, $userInfo['id']);
			if (empty($advice)) return $this->notfound();

			$this->db->beginTransaction();

			$advicesDao->delete($advice['advice_id'], $advice['advice_user_id']);

			// カテゴリの数をカウント変更
			$categorysDao = new CategorysDao($this->db);
			$categorysDao->updateCountDownTotal($advice['category_id']);

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		return $this->resp->sendRedirect('/user/advice/?delete_advice=true');
	}

	/**
	 * 相談窓口の受付・停止変更
	 */
	public function change_status()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		$id = $this->form->getInt('id');
		if (empty($id)) return $this->notfound();

		$advice_status = $this->form->get('advice_status')=='1' ? 1 : 0;

		$userInfo = $this->getUserInfo();

		try
		{
			$advicesDao = new AdvicesDao($this->db);
			$advice = $advicesDao->getItemByUserId($id, $userInfo['id']);
			if (empty($advice)) return $this->notfound();

			$this->db->beginTransaction();

			$advicesDao->reset();
			$advicesDao->addValue(AdvicesDao::COL_ADVICE_STATUS, $advice_status);
			$advicesDao->addValue(AdvicesDao::COL_LASTUPDATE, Dao::DATE_NOW);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice['advice_id']);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
			$advicesDao->doUpdate();

			// カテゴリの数をカウント変更
			$categorysDao = new CategorysDao($this->db);
			if ($advice_status == 1)
			{
				$categorysDao->updateCountUpTotal($advice['category_id']);
			}
			else
			{
				$categorysDao->updateCountDownTotal($advice['category_id']);
			}

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		return $this->resp->sendRedirect('/user/advice/?chage_advice_status='.$advice_status);
	}
}
?>
