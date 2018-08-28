<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultReviewsDao', 'dao');
Sp::import('GoodsDao', 'dao');
Sp::import('smarty/plugins/modifier.good_btn.php', 'libs');
/**
 * GOOD API(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiGoodController extends BaseController
{
	/**
	 * GOODボタンの表示
	 * ボタンHTMLをそのまま返す
	 */
//	public function button()
//	{
//		if ($this->checkXHR() === false || $this->form->isGetMethod() === false) return $this->notfound();
//
//		$good_count = 0;
//		$user_count = 0;
//
//		$href = $this->form->get('href');
//
//		list($id_params, $permalink) = $this->_parse_url($href);
//
//		if (count($id_params) > 0 && $permalink != '')
//		{
//			$GoodsDao = new GoodsDao($this->db);
//			$good_count = $GoodsDao->getCount($permalink);
//
//			if ($this->checkUserAuth())
//			{
//				$userInfo = $this->getUserInfo();
//				$GoodsDao->reset();
//				$user_count = $GoodsDao->getUserCount($userInfo['id'], $permalink);
//			}
//		}
//
//		$btn = '<span class="ad_good_button_count">'.$good_count.'</span>';
//		if ($user_count > 0)
//		{
//			$btn .= '<span class="ad_good_button_text">GOOD!</span>';
//			$evt_class = 'ad_good_cancel';
//		}
//		else
//		{
//			$btn .= '<span class="ad_good_button_text">GOOD!</span>';
//			if ($this->checkUserAuth()) {
//				$evt_class = 'ad_good_send';
//			} else {
//				$evt_class = 'ad_good_login';
//			}
//		}
//
//		$this->form->set('data', '<a class="ad_good_button_face '.$evt_class.'"><span class="ad_good_button_context">'.$btn.'</span></a>');
//
//		$this->resp->setContentType(SpResponse::CTYPE_JSON);
//		$this->resp->setHeader('X-Content-Type-Options', 'nosniff');
//
//		return $this->forward('json', APP_CONST_EMPTY_FRAME);
//	}
	public function button()
	{
		if ($this->form->isGetMethod() === false) return $this->notfound();

		$good_count = 0;
		$user_count = 0;

		$href = $this->form->get('href');

		list($id_params, $permalink) = $this->_parse_url($href);

		if (count($id_params) > 0 && $permalink != '')
		{
			$GoodsDao = new GoodsDao($this->db);
			$good_count = $GoodsDao->getCount($permalink);

			if ($this->checkUserAuth())
			{
				$userInfo = $this->getUserInfo();
				$GoodsDao->reset();
				$user_count = $GoodsDao->getUserCount($userInfo['id'], $permalink);
			}
		}

		$this->form->set('html', smarty_modifier_good_btn($permalink, $good_count, $user_count, $this->checkUserAuth()));

		return $this->forward('empty', '_frame/good_button_frame');
	}

	/**
	 * GOOD処理
	 */
	public function send($is_good=true)
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		$href = $this->form->get('href');

		list($id_params, $permalink) = $this->_parse_url($href);

		if (count($id_params) > 0 && $permalink != '')
		{
			try
			{
				$this->db->beginTransaction();

				$GoodsDao = new GoodsDao($this->db);
				$user_count = $GoodsDao->getUserCount($userInfo['id'], $permalink);

				if ($is_good)
				{
					if ($user_count == 0)
					{
						$GoodsDao->addValue(GoodsDao::COL_USER_ID, $userInfo['id']);
						foreach ($id_params as $col => $val)
						{
							$GoodsDao->addValue($col, $val);
						}
						$GoodsDao->addValueStr(GoodsDao::COL_PERMALINK, $permalink);
						$GoodsDao->doInsert();

						$json_data['text'] = 'GOOD!';
						$json_data['result'] = 1;
					}
				}
				else
				{
					if ($user_count == 1)
					{
						$GoodsDao->cancel($userInfo['id'], $permalink);

						$json_data['text'] = 'GOOD!';
						$json_data['result'] = 1;
					}
				}
				$this->db->commit();
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
			}
		}
		else
		{
			$json_data['errmsg'] = 'The call method is different.';
		}

		return $this->jsonPage($json_data, false);
	}

	/**
	 * GOOD取消し処理
	 */
	public function cancel()
	{
		return $this->send(false);
	}

	private function _parse_url($url)
	{
		$id_params = array();
		$permalink = '';

		if (preg_match("|/advice/([0-9]+)/$|i", $url, $m))
		{
			$id_params['advice_id'] = $m[1];
			$permalink = '/advice/'.$m[1].'/';
		}
		else if (preg_match("|/advice/([0-9]+)/([0-9]+)/$|i", $url, $m))
		{
			$id_params['advice_id'] = $m[1];
			$id_params['consult_id'] = $m[2];
			$permalink = '/advice/'.$m[1].'/'.$m[2].'/';
		}
		else if (preg_match("|/advice/([0-9]+)/([0-9]+)/([0-9]+)/$|i", $url, $m))
		{
			$id_params['advice_id'] = $m[1];
			$id_params['consult_id'] = $m[2];
			$id_params['consult_reply_id'] = $m[3];
			$permalink = '/advice/'.$m[1].'/'.$m[2].'/'.$m[3].'/';
		}
		else if (preg_match("|/advice/consult/([0-9]+)/$|i", $url, $m))
		{
			$id_params['consult_id'] = $m[1];
			$permalink = '/advice/consult/'.$m[1].'/';
		}
		else if (preg_match("|/advice/review/([0-9]+)/$|i", $url, $m))
		{
			$id_params['consult_review_id'] = $m[1];

			$ConsultReviewsDao = new ConsultReviewsDao($this->db);
			$review = $ConsultReviewsDao->getItem($id_params['consult_review_id']);
			if (empty($review)) return $this->notfound();

			$id_params['advice_id'] = $review['advice_id'];
			$id_params['consult_id'] = $review['consult_id'];
			$permalink = '/advice/review/'.$m[1].'/';
		}

		return array($id_params, $permalink);
	}
}
?>
