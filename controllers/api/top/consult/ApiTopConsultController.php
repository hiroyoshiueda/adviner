<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * トップ・公開された相談リストAPI(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiTopConsultController extends BaseController
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
			$ConsultsDao = new ConsultsDao($this->db);
			$list = $ConsultsDao->getPageListAndAdviceOfPublic($total, $offset, $limit);

			$user_set = array();

			if ($total > 0)
			{
				$advice_user_ids = Util::arraySelectKey('advice_user_id', $list, true);
				$consult_user_ids = Util::arraySelectKey('consult_user_id', $list, true);
				$user_ids = array_merge($advice_user_ids, $consult_user_ids);

				$UsersDao = new UsersDao($this->db);
				$user_list = $UsersDao->getUserList($user_ids);
				$user_set = Util::arrayKeyData('user_id', $user_list);

				$this->setGoodButton(0, $consult_ids);
			}

			$tpl_vars = array(
				'consult_list' => $list,
				'form' => array(
					'user_set' => $user_set
				),
				'good' => $this->form->getSp('good'),
				'userInfo' => $userInfo,
				'AppConst' => array('mainCategorys' => AppConst::$mainCategorys),
				'REAL_URL' => $this->form->getSp('REAL_URL'),
				'is_top' => true
			);

			if ($offset > 0) $tpl_vars['is_top'] = false;

			$json_data['html'] = $this->form->getTemplateContents($tpl_vars, 'api/top/consult/api_top_consult_get_list');
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
