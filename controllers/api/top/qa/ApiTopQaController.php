<?php
Sp::import('QuestionsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('CategorysDao', 'dao');
/**
 * トップQ&AリストAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiTopQaController extends BaseController
{
	const PAGE_LIMIT = 10;

	/**
	 * 相談窓口
	 */
	public function get_list()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkUserAuth() === false) return $this->notfound();

		$total = 0;
		$limit = self::PAGE_LIMIT;
		$pagenum = $this->form->getInt('pagenum');
		$offset = $pagenum * $limit;

		$json_data = array(
			'html' => '',
			'more' => 0,
			'result' => 0,
			'errmsg' => ''
		);

		$userInfo = $this->getUserInfo();

		try
		{
			$user_set = array();
			$category_set = array();

			$QuestionsDao = new QuestionsDao($this->db);
			$list = $QuestionsDao->getPageList($total, $offset, $limit, array(QuestionsDao::COL_ANSWERDATE => 'DESC'));

			if (empty($list) === false)
			{
				$user_ids = Util::arraySelectKey('question_user_id', $list, true);
				$user_set = UsersDao::getUserSet($this->db, $user_ids);

				$category_set = $this->getLoadCategorySet(new CategorysDao($this->db));

//				$this->setGoodButton($advice_ids);
			}

			$tpl_vars = array(
				'list' => $list,
				'form' => array(
					'user_set' => $user_set,
					'category_set' => $category_set
				),
//				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => true
			);

			if ($offset > 0) $tpl_vars['is_top'] = false;

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/top/qa/api_top_qa_get_list');
			$json_data['result'] = 1;

			// 続きの有無
			if ($total > $offset + $limit) {
				$json_data['more'] = 1;
			}
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$json_data['errmsg'] = self::ERROR_AJAX_MESSAGE1;
		}

		return $this->jsonPage($json_data, false);
	}
}
?>
