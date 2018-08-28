<?php
Sp::import('AdminBaseController');
Sp::import('QaCategorysDao', 'dao');
/**
 * 管理画面 - Q&Aカテゴリ(Controller)
 */
class AdminQacategoryController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$dao = new QaCategorysDao($this->db);
		$list = $dao->getList();
		$this->form->set('list', $list);

		$this->form->set('htitle', 'Ｑ＆Ａカテゴリー');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/qacategory/admin_qacategory_index', APP_CONST_ADMIN_FRAME);
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
				$dao = new QaCategorysDao($this->db);
				foreach ($order_id as $i => $id) {
					$id = (int)$id;
					if ($id>0 && isset($order_num[$i])) {
						$order_num[$i] = (int)$order_num[$i];
						$dao->reset();
						$dao->addValue(QaCategorysDao::COL_ORDER_NUM, $order_num[$i]);
						$dao->addWhere(QaCategorysDao::COL_QA_CATEGORY_ID, $id);
						$dao->doUpdate();
					}
				}
			}
		}
		return $this->resp->sendRedirect('/admin/qacategory/');
	}

	public function delete()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new QaCategorysDao($this->db);
			$dao->delete($id);
		}
		return $this->resp->sendRedirect('/admin/qacategory/');
	}

	public function regist()
	{
		$id = $this->form->getInt('id');
		if ($id == 0) {
			$dao = new QaCategorysDao($this->db);
			$max = $dao->getMaxOrderNum();
			$this->form->setDefault('order_num', ($max + 1));
		}

		$this->form->set('htitle', 'Ｑ＆Ａカテゴリーの登録');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/qacategory/admin_qacategory_regist');
	}

	public function edit()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new QaCategorysDao($this->db);
			$this->form->setDefaultAll($dao->getItem($id));
		}

		$this->form->setParameterForm('id');

		$this->regist();

		$this->form->set('htitle', 'Ｑ＆Ａカテゴリーの変更');
		$this->setTitle($this->form->get('htitle'));

		return;
	}

	/**
	 * 登録
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

			$dao = new QaCategorysDao($this->db);
			$dao->addValue(QaCategorysDao::COL_ORDER_NUM, $this->form->get('order_num'));
			$dao->addValueStr(QaCategorysDao::COL_TITLE, $this->form->get('title'));
			$dao->addValue(QaCategorysDao::COL_LASTUPDATE, Dao::DATE_NOW);

			if ($id > 0) {
				$dao->addValue(QaCategorysDao::COL_DISPLAY_FLAG, $this->form->get('display_flag'));
				$dao->addWhere(QaCategorysDao::COL_QA_CATEGORY_ID, $id);
				$dao->doUpdate();
			} else {
				$dao->addValue(QaCategorysDao::COL_CREATEDATE, Dao::DATE_NOW);
				$dao->doInsert();
			}

			$this->db->commit();

		} catch (Exception $e) {
			$this->db->rollback();
			$this->logger->exception($e);
			return $this->edit();
		}

		return $this->resp->sendRedirect('/admin/qacategory/');
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
