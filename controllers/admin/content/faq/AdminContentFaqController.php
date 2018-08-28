<?php
Sp::import('FaqCategorysDao', 'dao');
Sp::import('FaqsDao', 'dao');
/**
 * 管理画面／よくある質問(Controller)
 */
class AdminContentFaqController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$faqCategorysDao = new FaqCategorysDao($this->db);
		$category_list = $faqCategorysDao->getList();

		$faq_category_id = $this->form->get('faq_category_id');

		$dao = new FaqsDao($this->db);
		if ($faq_category_id>0) $dao->addWhere(FaqsDao::COL_FAQ_CATEGORY_ID, $faq_category_id);
		$list = $dao->getList();
		$this->form->set('list', $list);

		$this->form->set('category_map', Util::arrayKeyData('faq_category_id', $category_list));

		$faqCategorysDao->reset();
		$key_name_list = $faqCategorysDao->getKeyNameList();
		$this->form->setSp('categoryOptions', Util::arrayToTextValue($key_name_list, 0));

		$this->form->setParameterForm('faq_category_id');
		$this->form->set('qstr', $this->form->getParameterQueryString());

		$this->form->set('htitle', 'よくある質問');
		$this->setTitle($this->form->get('htitle'));

		$this->setPageData('content', 'faq');

		return $this->forward('admin/content/faq/admin_content_faq_index');
	}

	/**
	 * 並び順保存
	 */
	public function saveorder()
	{
		if ($this->form->isPostMethod()) {
			$order_nums = $this->form->get('order_nums');
			$order_ids = $this->form->get('order_ids');
			if (is_array($order_ids) && count($order_ids)>0) {
				$dao = new FaqsDao($this->db);
				foreach ($order_ids as $i => $id) {
					$id = (int)$id;
					if ($id>0 && isset($order_nums[$i])) {
						$order_num[$i] = (int)$order_nums[$i];
						$dao->reset();
						$dao->addValue(FaqsDao::COL_ORDER_NUM, $order_nums[$i]);
						$dao->addWhere(FaqsDao::COL_FAQ_ID, $id);
						$dao->doUpdate();
					}
				}
			}
		}

		$this->form->setParameterForm('faq_category_id');
		$qstr = $this->form->getParameterQueryString();

		if ($qstr!='') $qstr = '?'.$qstr;
		return $this->resp->sendRedirect($qstr);
	}

	/**
	 * 登録フォーム
	 */
	public function form()
	{
		$id = $this->form->getInt('id');

		if ($id > 0) {
			$dao = new FaqsDao($this->db);
			$this->form->setAll($dao->getItem($id));
			$this->form->set('htitle', 'よくある質問の変更');
		} else {
			$dao = new FaqsDao($this->db);
			$max = $dao->getMaxOrderNum();
			$this->form->setDefault('order_num', ($max + 1));
			$this->form->set('htitle', 'よくある質問の追加');
		}

		$dao = new FaqCategorysDao($this->db);
		$category_list = $dao->getKeyNameList();
		$this->form->setSp('categoryOptions', Util::arrayToTextValue($category_list));

		$this->form->setParameterForm('id');

		$this->setTitle($this->form->get('htitle'));

		$this->createSecurityCode();
		$this->setPageData('content', 'faq');

//		$this->form->setScript(APP_CONST_JS_PATH . 'ckeditor/ckeditor.js');
//		$this->form->setScript(APP_CONST_JS_PATH . 'editor.js');

		return $this->forward('admin/content/faq/admin_content_faq_form');
	}

	/**
	 * 保存処理
	 */
	public function form_save_api()
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkAdminAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'errors' => array(),
			'security_token' => ''
		);

		$id = $this->form->get('id');

		if ($this->_validate() === false)
		{
			$json_data['errmsg'] = parent::ERROR_AJAX_INPUT_MESSAGE;
			$json_data['errors'] = $this->form->getValidateErrors();
		}
//		else if ($this->checkSecurityCode() === false)
//		{
//			$json_data['errmsg'] = parent::ERROR_PAGE_MESSAGE5;
//		}
		else
		{
			try
			{
				$this->db->beginTransaction();

				$dao = new FaqsDao($this->db);
				$dao->addValue(FaqsDao::COL_ORDER_NUM, $this->form->get('order_num'));
				$dao->addValue(FaqsDao::COL_FAQ_CATEGORY_ID, $this->form->get('faq_category_id'));
				$dao->addValueStr(FaqsDao::COL_QUESTION, $this->form->get('question'));
				$dao->addValueStr(FaqsDao::COL_ANSWER, $this->form->get('answer'));
				$dao->addValue(FaqsDao::COL_LASTUPDATE, Dao::DATE_NOW);

				if ($id > 0) {
					$dao->addValue(FaqsDao::COL_DISPLAY_FLAG, $this->form->getInt('display_flag', 0));
					$dao->addWhere(FaqsDao::COL_FAQ_ID, $id);
					$dao->doUpdate();
				} else {
					$dao->addValue(FaqsDao::COL_CREATEDATE, Dao::DATE_NOW);
					$dao->doInsert();
				}

				$this->db->commit();

				$json_data['result'] = 1;
				$qstr = $this->form->getParameterQueryString();
				if ($qstr!='') $qstr = '?'.$qstr;
				$json_data['redirect'] = APP_CONST_ADMIN_PATH.'/content/faq/'.$qstr;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$this->db->rollback();
				$json_data['errmsg'] = parent::ERROR_AJAX_MESSAGE1;
			}
		}

		return $this->jsonPage($json_data, true);
	}

	/**
	 * 削除
	 */
	public function form_delete()
	{
		$id = $this->form->getInt('id');

		if ($id > 0) {
			$dao = new FaqsDao($this->db);
			$dao->delete($id);
		}

		return $this->resp->sendRedirect('');
	}

	/**
	 * 入力値チェック
	 */
	private function _validate()
	{
		$ret = $this->form->validate($this->form->getValidates(0));

		return $ret;
	}
}
?>
