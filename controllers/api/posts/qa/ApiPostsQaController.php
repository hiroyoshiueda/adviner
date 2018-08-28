<?php
Sp::import('QuestionsDao', 'dao');
Sp::import('AnswersDao', 'dao');
/**
 * Q&A投稿API(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiPostsQaController extends BaseController
{
	/**
	 * 質問（アドバイスください）投稿
	 */
	public function post_question()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'security_token' => '',
			'question_id' => 0,
			'fb_share' => array()
		);

		$userInfo = $this->getUserInfo();

		if ($this->checkSecurityCode() === false)
		{
			$json_data['errmsg'] = self::ERROR_PAGE_MESSAGE5;
		}
		else if ($this->_validateQuestion() === false)
		{
			$json_data['errmsg'] = '入力エラーがあります。';
			$json_data['errors'] = $this->form->getValidateErrors();
		}
		else
		{
			try
			{
				$this->db->beginTransaction();

				$QuestionsDao = new QuestionsDao($this->db);
				$QuestionsDao->addValue(QuestionsDao::COL_QUESTION_USER_ID, $userInfo['id']);
				$QuestionsDao->addValue(QuestionsDao::COL_CATEGORY_ID, $this->form->get('category_id'));
				//$QuestionsDao->addValueStr(QuestionsDao::COL_QUESTION_TITLE, $this->form->get('question_title'));
				$QuestionsDao->addValueStr(QuestionsDao::COL_QUESTION_BODY, $this->form->get('question_body'));
				$QuestionsDao->addValue(QuestionsDao::COL_LIMIT_TYPE, QuestionsDao::LIMIT_TYPE_ANYONE);
				$QuestionsDao->addValue(QuestionsDao::COL_ANSWERDATE, Dao::DATE_NOW);
				$QuestionsDao->addValue(QuestionsDao::COL_CREATEDATE, Dao::DATE_NOW);
				$QuestionsDao->addValue(QuestionsDao::COL_LASTUPDATE, Dao::DATE_NOW);
				$QuestionsDao->doInsert();

				$question_id = $QuestionsDao->getLastInsertId();

				$this->db->commit();

				$json_data['question_id'] = $question_id;
				$json_data['result'] = 1;

				// Facebookシェア
				if ($this->form->getInt('is_fb_share') == 1)
				{
					$json_data['fb_share'] = array(
						'message' => '[質問]'.$this->form->get('question_body'),
						'link' => constant('app_site_real_url').'qa/question/'.$question_id.'/',
						//'name' => APP_CONST_SITE_TITLE_TOP,
						'description' => $userInfo['nickname'].'さんから質問です。回答をお待ちしております。',
						'picture' => constant('app_site_real_url').'img/fb_page.png'
					);
				}
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = parent::ERROR_AJAX_MESSAGE1;
			}
		}

		return $this->jsonPage($json_data, true);
	}

	/**
	 * 入力チェック
	 */
	private function _validateQuestion()
	{
		$ret = $this->form->validate($this->form->getValidates(0));
		return $ret;
	}

	/**
	 * 回答フォーム
	 */
	public function get_answer_form()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$question_id = $this->form->getInt('question_id');
		if (empty($question_id)) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$QuestionsDao = new QuestionsDao($this->db);
			$question = $QuestionsDao->getItem($question_id);
			if (empty($question)) {
				$json_data['errmsg'] = '不正な呼出しです。';
				return $this->jsonPage($json_data, false);
			}

			$tpl_vars = array(
				'question' => $question,
				'userInfo' => $userInfo
			);

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/posts/qa/api_posts_qa_get_answer_form');
			$json_data['result'] = 1;
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}

	public function post_answer()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$question_id = $this->form->getInt('question_id');
		if (empty($question_id)) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'security_token' => '',
			'fb_share' => array()
		);

		$userInfo = $this->getUserInfo();

		if ($this->checkSecurityCode() === false)
		{
			$json_data['errmsg'] = self::ERROR_PAGE_MESSAGE5;
		}
		else if ($this->_validateAnswer() === false)
		{
			$json_data['errmsg'] = '入力エラーがあります。';
			$json_data['errors'] = $this->form->getValidateErrors();
		}
		else
		{
			try
			{
				$QuestionsDao = new QuestionsDao($this->db);
				$question = $QuestionsDao->getItem($question_id);
				if (empty($question)) {
					$json_data['errmsg'] = '不正な呼出しです。';
					return $this->jsonPage($json_data, false);
				}

				$this->db->beginTransaction();

				$AnswersDao = new AnswersDao($this->db);
				$AnswersDao->addValue(AnswersDao::COL_QUESTION_ID, $question_id);
				$AnswersDao->addValue(AnswersDao::COL_QUESTION_USER_ID, $question['question_user_id']);
				$AnswersDao->addValue(AnswersDao::COL_ANSWER_USER_ID, $userInfo['id']);
				//$AnswersDao->addValueStr(AnswersDao::COL_ANSWER_TITLE, $this->form->get('answer_title'));
				$AnswersDao->addValueStr(AnswersDao::COL_ANSWER_BODY, $this->form->get('answer_body'));
				$AnswersDao->addValue(AnswersDao::COL_CREATEDATE, Dao::DATE_NOW);
				$AnswersDao->addValue(AnswersDao::COL_LASTUPDATE, Dao::DATE_NOW);
				$AnswersDao->doInsert();

				$answer_id = $AnswersDao->getLastInsertId();

				$QuestionsDao = new QuestionsDao($this->db);
				$QuestionsDao->addValue(QuestionsDao::COL_ANSWER_TOTAL, QuestionsDao::COL_ANSWER_TOTAL.'+1');
				$QuestionsDao->addValue(QuestionsDao::COL_ANSWERDATE, Dao::DATE_NOW);
				$QuestionsDao->addWhere(QuestionsDao::COL_QUESTION_ID, $question_id);
				$QuestionsDao->doUpdate();

				$this->db->commit();

				$json_data['answer_id'] = $answer_id;

				// Facebookシェア
				if ($this->form->getInt('is_fb_share') == 1)
				{
					$json_data['fb_share'] = array(
						'message' => '[回答]'.$this->form->get('answer_body'),
						'link' => constant('app_site_real_url').'qa/answer/'.$answer_id.'/',
						//'name' => APP_CONST_SITE_TITLE_TOP,
						'description' => $userInfo['nickname'].'さんの質問に対する回答です。',
						'picture' => constant('app_site_real_url').'img/fb_page.png'
					);
				}
				
				$AnswersDao = new AnswersDao($this->db);

				$tpl_vars = array(
					'answer_list' => array(),
					'question' => $question,
					'userInfo' => $userInfo
				);

				$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/posts/qa/api_posts_qa_post_answer_list');
				$json_data['result'] = 1;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$json_data['errmsg'] = parent::ERROR_AJAX_MESSAGE1;
			}
		}

		return $this->jsonPage($json_data, true);
	}


	/**
	 * 入力チェック
	 */
	private function _validateAnswer()
	{
		$ret = $this->form->validate($this->form->getValidates(1));
		return $ret;
	}
}
?>
