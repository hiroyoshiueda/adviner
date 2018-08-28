<?php
Sp::import('UsersDao', 'dao');
Sp::import('QuestionsDao', 'dao');
/**
 * Q&A質問登録(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class QaQuestionController extends BaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$this->form->set('htitle', 'お問い合わせ');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('contact/contact_index', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 投稿フォーム
	 */
	public function form()
	{
		$question_id = $this->form->getInt('question_id');

		if ($question_id > 0)
		{

		}
		else
		{
			if ($this->form->isGetMethod()) {
				$this->form->setDefault('limit_type', '0');
			}
			$this->form->set('htitle', '質問する');
		}

		$CategorysDao = new CategorysDao($this->db);
		$this->form->setSp('categoryOptions', $CategorysDao->getSelectOptions());

		$this->setTitle($this->form->get('htitle'));

		$this->createSecurityCode();

		return $this->forward('qa/question/qa_question_form', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 確認
	 */
	public function confirm()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(parent::ERROR_PAGE_MESSAGE5);
		}
		else if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->form();
		}

		$this->form->setParameterForm('question_id');
		$this->form->setParameterForm('question_title');
		$this->form->setParameterForm('question_body');
		$this->form->setParameterForm('category_id');
		$this->form->setParameterForm('limit_type');

		$this->form->set('htitle', '質問内容の確認');
		$this->setTitle($this->form->get('htitle'));

		$this->createSecurityCode();

		$this->resp->noCache();

		return $this->forward('qa/question/qa_question_confirm', APP_CONST_MAIN_FRAME);
	}

	public function finished()
	{
		if ($this->checkUserAuth() === false) return $this->notfound();

		if ($this->checkSecurityCode() === false)
		{
			return $this->errorPage(parent::ERROR_PAGE_MESSAGE5);
		}
		else if ($this->_validate() === false)
		{
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->form();
		}

		$userInfo = $this->getUserInfo();

		try
		{
			$this->db->beginTransaction();

			$QuestionsDao = new QuestionsDao($this->db);
			$QuestionsDao->addValue(QuestionsDao::COL_QUESTION_USER_ID, $userInfo['id']);
			$QuestionsDao->addValue(QuestionsDao::COL_CATEGORY_ID, $this->form->get('category_id'));
			$QuestionsDao->addValueStr(QuestionsDao::COL_QUESTION_TITLE, $this->form->get('question_title'));
			$QuestionsDao->addValueStr(QuestionsDao::COL_QUESTION_BODY, $this->form->get('question_body'));
			$QuestionsDao->addValue(QuestionsDao::COL_LIMIT_TYPE, QuestionsDao::LIMIT_TYPE_ANYONE);
			$QuestionsDao->addValue(QuestionsDao::COL_ANSWERDATE, Dao::DATE_NOW);
			$QuestionsDao->addValue(QuestionsDao::COL_CREATEDATE, Dao::DATE_NOW);
			$QuestionsDao->addValue(QuestionsDao::COL_LASTUPDATE, Dao::DATE_NOW);
			$QuestionsDao->doInsert();

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
			$this->form->setValidateErrors('system', parent::ERROR_PAGE_MESSAGE6);
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->form();
		}

		return $this->resp->sendRedirect("/");
	}

	private function _validate()
	{
		$ret = $this->form->validate($this->form->getValidates(0));

		return $ret;
	}
}
?>
