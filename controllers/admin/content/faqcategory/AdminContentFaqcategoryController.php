<?php
Sp::import('FaqCategorysDao', 'dao');
/**
 * 管理画面／よくある質問カテゴリ(Controller)
 */
class AdminContentFaqcategoryController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$FaqCategorysDao = new FaqCategorysDao($this->db);
		$FaqCategorysDao->addWhere(FaqCategorysDao::COL_DELETE_FLAG, FaqCategorysDao::DELETE_FLAG_ON);
		$FaqCategorysDao->addOrder(FaqCategorysDao::COL_ORDER_NUM);
		$this->form->set('list', $FaqCategorysDao->select());

		$this->form->set('htitle', 'よくある質問カテゴリー');
		$this->setTitle($this->form->get('htitle'));

		$this->setPageData('content', 'faqcategory');

		return $this->forward('admin/content/faqcategory/admin_content_faqcategory_index');
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
				$dao = new FaqCategorysDao($this->db);
				foreach ($order_ids as $i => $id) {
					$id = (int)$id;
					if ($id>0 && isset($order_nums[$i])) {
						$order_nums[$i] = (int)$order_nums[$i];
						$dao->reset();
						$dao->addValue(FaqCategorysDao::COL_ORDER_NUM, $order_nums[$i]);
						$dao->addWhere(FaqCategorysDao::COL_FAQ_CATEGORY_ID, $id);
						$dao->doUpdate();
					}
				}
			}
		}

		return $this->resp->sendRedirect('');
	}

	/**
	 * 登録フォーム
	 */
	public function form()
	{
		$id = $this->form->get('id');

		if ($id > 0) {
			$dao = new FaqCategorysDao($this->db);
			$this->form->setDefaultAll($dao->getItem($id));
			$this->form->set('htitle', 'よくある質問カテゴリーの変更');
		} else {
			$dao = new FaqCategorysDao($this->db);
			$max = $dao->getMaxOrderNum();
			$this->form->setDefault('order_num', ($max + 1));
			$this->form->set('htitle', 'よくある質問カテゴリーの追加');
		}

		$this->form->setParameterForm('id');

		$this->setTitle($this->form->get('htitle'));

		$this->createSecurityCode();
		$this->setPageData('content', 'faqcategory');

		return $this->forward('admin/content/faqcategory/admin_content_faqcategory_form');
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

				$dao = new FaqCategorysDao($this->db);
				$dao->addValue(FaqCategorysDao::COL_ORDER_NUM, $this->form->get('order_num'));
				$dao->addValueStr(FaqCategorysDao::COL_TITLE, $this->form->get('title'));
				$dao->addValue(FaqCategorysDao::COL_LASTUPDATE, Dao::DATE_NOW);

				if ($id > 0) {
					$dao->addValue(FaqCategorysDao::COL_DISPLAY_FLAG, $this->form->getInt('display_flag', 0));
					$dao->addWhere(FaqCategorysDao::COL_FAQ_CATEGORY_ID, $id);
					$dao->doUpdate();
				} else {
					$dao->addValue(FaqCategorysDao::COL_CREATEDATE, Dao::DATE_NOW);
					$dao->doInsert();
				}

				$this->db->commit();

				$json_data['result'] = 1;
				$json_data['redirect'] = APP_CONST_ADMIN_PATH.'/content/faqcategory/';
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
		$id = $this->form->get('id');

		if ($id > 0) {
			$dao = new FaqCategorysDao($this->db);
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
