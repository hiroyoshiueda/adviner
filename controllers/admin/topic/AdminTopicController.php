<?php
Sp::import('AdminBaseController');
Sp::import('TopicsDao', 'dao');
/**
 * 管理画面 - お知らせ(Controller)
 */
class AdminTopicController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$total = 0;
		$offset = $this->form->getInt('offset', 0);
		$type = $this->form->getInt('type', 0);

		$dao = new TopicsDao($this->db);
		$list = $dao->getPageList(&$total, $offset, APP_CONST_ADMIN_PAGE_LIMIT, TopicsDao::COL_DATE, 'DESC');
		$this->form->set('list', $list);
		$this->form->set('total', $total);

		$this->form->set('htitle', 'お知らせ');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/topic/admin_topic_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 削除
	 */
	public function delete()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new TopicsDao($this->db);
			$dao->delete($id);
		}
		return $this->resp->sendRedirect('/admin/topic/');
	}

	/**
	 * 新規登録
	 */
	public function regist()
	{
		$this->form->set('date', $this->form->get('date', date('Y-m-d')));

		$this->form->setStyleSheet(APP_CONST_CSS_PATH . 'ui.datepicker.css');
		$this->form->setScript(APP_CONST_JS_PATH . 'ui.datepicker.js');
		$this->form->setScript(APP_CONST_JS_PATH . 'ui.datepicker-setting.js');
		$this->form->setScript(APP_CONST_JS_PATH . 'ckeditor/ckeditor.js');
		$this->form->setScript(APP_CONST_JS_PATH . 'editor.js');
//		$this->form->setScript(APP_CONST_JS_PATH . 'jquery.DOMWindow.js');

		$this->form->set('htitle', 'お知らせの登録');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/topic/admin_topic_regist');
	}

	/**
	 * 編集
	 */
	public function edit()
	{
		$id = $this->form->getInt('id');
		if ($id > 0) {
			$dao = new TopicsDao($this->db);
			$this->form->setDefaultAll($dao->getItem($id));
		}

		$this->form->setParameterForm('id');

		$this->regist();

		$this->form->set('htitle', 'お知らせの変更');
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

			$dao = new TopicsDao($this->db);
			$dao->addValueStr(TopicsDao::COL_DATE, $this->form->get('date'));
			$dao->addValueStr(TopicsDao::COL_TITLE, $this->form->get('title'));
			$dao->addValueStr(TopicsDao::COL_BODY, $this->form->get('body'));
			$dao->addValue(TopicsDao::COL_LASTUPDATE, Dao::DATE_NOW);

			if ($id > 0) {
				$dao->addValue(TopicsDao::COL_DISPLAY_FLAG, $this->form->getInt('display_flag', 0));
				$dao->addWhere(TopicsDao::COL_TOPIC_ID, $id);
				$dao->doUpdate();
			} else {
				$dao->addValue(TopicsDao::COL_CREATEDATE, Dao::DATE_NOW);
				$dao->doInsert();
			}

			$this->db->commit();

		} catch (SpException $e) {
			$this->db->rollback();
			$this->logger->exception($e);
			return $this->index();
		}

		return $this->resp->sendRedirect('/admin/topic/');
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
