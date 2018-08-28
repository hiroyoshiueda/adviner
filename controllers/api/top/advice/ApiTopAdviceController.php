<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('UserRanksDao', 'dao');
Sp::import('CategorysDao', 'dao');
/**
 * トップ相談窓口リストAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiTopAdviceController extends BaseController
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
			$AdvicesDao = new AdvicesDao($this->db);
			$list = $AdvicesDao->getPopularPageList($total, $offset, $limit);

			$advice_ids = Util::arraySelectKey('advice_id', $list, true);

			if ($total > 0)
			{
				$this->setGoodButton($advice_ids);
			}

			$category_set = $this->getLoadCategorySet(new CategorysDao($this->db));

			$tpl_vars = array(
				'advice_list' => $list,
				'form' => array(
					'category_set' => $category_set,
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => true
			);

			if ($offset > 0) $tpl_vars['is_top'] = false;

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, '_parts/advice_list');
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
