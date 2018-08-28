<?php
Sp::import('AdminBaseController');
Sp::import('FaqCategorysDao', 'dao');
Sp::import('FaqsDao', 'dao');
/**
 * 管理ツール／よくある質問(Controller)
 */
class AdminFaqController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$faqCategorysDao = new FaqCategorysDao($this->db);
		$category_list = $faqCategorysDao->getList();

//		$this->form->setDefault('faq_category_id', (int)$category_list[0]['faq_category_id']);
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

		return $this->forward('admin/faq/admin_faq_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 並び順保存
	 */
	public function saveorder()
	{
		if ($this->form->isPostMethod()) {
			$order_num = $this->form->get('order_num');
			$order_id = $this->form->get('order_id');
			if (is_array($order_id) && count($order_id)>0) {
				$dao = new FaqsDao($this->db);
				foreach ($order_id as $i => $id) {
					$id = (int)$id;
					if ($id>0 && isset($order_num[$i])) {
						$order_num[$i] = (int)$order_num[$i];
						$dao->reset();
						$dao->addValue(FaqsDao::COL_ORDER_NUM, $order_num[$i]);
						$dao->addWhere(FaqsDao::COL_FAQ_ID, $id);
						$dao->doUpdate();
					}
				}
			}
		}

		$this->form->setParameterForm('faq_category_id');
		$qstr = $this->form->getParameterQueryString();

		if ($qstr!='') $qstr = '?'.$qstr;
		return $this->resp->sendRedirect('/admin/faq/'.$qstr);
	}

	/**
	 * 削除
	 */
	public function delete()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new FaqsDao($this->db);
			$dao->delete($id);
		}
		return $this->resp->sendRedirect('/admin/faq/');
	}

	/**
	 * 新規登録
	 */
	public function regist()
	{
		$id = $this->form->getInt('id');
		if ($id == 0) {
			$dao = new FaqsDao($this->db);
			$max = $dao->getMaxOrderNum();
			$this->form->setDefault('order_num', ($max + 1));
		}

		$dao = new FaqCategorysDao($this->db);
		$category_list = $dao->getKeyNameList();
		$this->form->setSp('categoryOptions', Util::arrayToTextValue($category_list));

		$this->form->set('htitle', 'よくある質問の登録');
		$this->setTitle($this->form->get('htitle'));

		$this->form->setScript(APP_CONST_JS_PATH . 'ckeditor/ckeditor.js');
		$this->form->setScript(APP_CONST_JS_PATH . 'editor.js');

		return $this->forward('admin/faq/admin_faq_regist');
	}

	/**
	 * 編集
	 */
	public function edit()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new FaqsDao($this->db);
			$this->form->setAll($dao->getItem($id));
		}

		$this->form->setParameterForm('id');

		$this->regist();

		$this->form->set('htitle', 'よくある質問の変更');
		$this->setTitle($this->form->get('htitle'));

		return;
	}

	/**
	 * 保存処理
	 */
	public function save()
	{
		if ($this->_validate() === false) {
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->edit();
		}

		$id = $this->form->getInt('id');

		try {

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

		} catch (Exception $e) {
			$this->db->rollback();
			$this->logger->exception($e);
			return $this->edit();
		}

		$this->form->setParameterForm('faq_category_id');
		$qstr = $this->form->getParameterQueryString();

		if ($qstr!='') $qstr = '?'.$qstr;
		return $this->resp->sendRedirect('/admin/faq/'.$qstr);
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
