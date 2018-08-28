<?php
Sp::import('AdminBaseController', 'controllers');
Sp::import('publisher/regist/PublisherRegistController', 'controllers');
Sp::import('PublishersDao', 'dao');
/**
 * 管理画面 - 出版社(Controller)
 */
class AdminPublisherController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$total = 0;
		$offset = $this->form->getInt('offset', 0);

		$dao = new PublishersDao($this->db);
		$list = $dao->getPageList(&$total, $offset, APP_CONST_ADMIN_PAGE_LIMIT, PublishersDao::COL_PUBLISHER_ID, '', -1);
		$this->form->set('list', $list);
		$this->form->set('total', $total);

		$this->form->set('htitle', '出版社一覧');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/publisher/admin_publisher_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 編集
	 */
	public function edit()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		if ($this->form->isGetMethod()) {
			$dao = new PublishersDao($this->db);
			$dao->addWhere(PublishersDao::COL_DELETE_FLAG, 0);
			$dao->addWhere(PublishersDao::COL_PUBLISHER_ID, $id);
			$user = $dao->selectRow();
			if (count($user)==0) return $this->errorPage('データがありません');
			unset($user['password']);
			$this->form->setDefaultAll($user);
		}

		$ctr = new PublisherRegistController($this->logger, $this->db, $this->form, $this->resp);
		$ctr->index();

		$this->form->setParameterForm('id');

		$this->form->set('htitle', '出版社情報の変更');
		$this->setTitle($this->form->get('htitle'), '出版社');

		return $this->forward('admin/publisher/admin_publisher_edit', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 確認
	 */
	public function confirm()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		$ctr = new PublisherRegistController($this->logger, $this->db, $this->form, $this->resp);
		$ctr->validate_user_id = $id;
		$ctr->confirm();

		if ($this->form->getTemplateNmae() == 'publisher/regist/publisher_regist_index') {
			return $this->edit();
		}

		$this->form->setParameterForm('id');
		$this->form->setParameterForm('status');

		$this->form->set('htitle', '出版社情報の変更（確認画面）');
		$this->setTitle($this->form->get('htitle'), '出版社');

		return $this->forward('admin/publisher/admin_publisher_confirm', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 登録
	 */
	public function save()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		$ctr = new PublisherRegistController($this->logger, $this->db, $this->form, $this->resp);

		$this->form->set('name', $this->form->get('name_sei').'　'.$this->form->get('name_mei'));
		$this->form->set('kana', $this->form->get('kana_sei').'　'.$this->form->get('kana_mei'));

		try {

			$this->db->beginTransaction();

			$dao = new PublishersDao($this->db);
			$dao->addValue(PublishersDao::COL_STATUS, $this->form->getInt('status'));
			$dao->addValueStr(PublishersDao::COL_NICKNAME, $this->form->get('nickname'));
			$dao->addValueStr(PublishersDao::COL_EMAIL, $this->form->get('email'));
			if ($this->form->get('password')!='') {
				$dao->addValueStr(PublishersDao::COL_PASSWORD, Util::password($this->form->get('password')));
			}
			$dao->addValueStr(PublishersDao::COL_CORPORATE_NAME, $this->form->get('corporate_name'));
			$dao->addValueStr(PublishersDao::COL_DIVISION, $this->form->get('division'));
			$dao->addValueStr(PublishersDao::COL_POST, $this->form->get('post'));
			$dao->addValueStr(PublishersDao::COL_NAME, $this->form->get('name'));
			$dao->addValueStr(PublishersDao::COL_NAME_SEI, $this->form->get('name_sei'));
			$dao->addValueStr(PublishersDao::COL_NAME_MEI, $this->form->get('name_mei'));
			$dao->addValueStr(PublishersDao::COL_KANA, $this->form->get('kana'));
			$dao->addValueStr(PublishersDao::COL_KANA_SEI, $this->form->get('kana_sei'));
			$dao->addValueStr(PublishersDao::COL_KANA_MEI, $this->form->get('kana_mei'));
			$dao->addValueStr(PublishersDao::COL_ZIP, $this->form->get('zip'));
			$dao->addValueStr(PublishersDao::COL_AREA, $this->form->get('area'));
			$dao->addValueStr(PublishersDao::COL_ADDR1, $this->form->get('addr1'));
			$dao->addValueStr(PublishersDao::COL_ADDR2, $this->form->get('addr2'));
			$dao->addValueStr(PublishersDao::COL_TEL, $this->form->get('tel'));
			$dao->addValueStr(PublishersDao::COL_FAX, $this->form->get('fax'));
			$dao->addValueStr(PublishersDao::COL_MOBILE_TEL, $this->form->get('mobile_tel'));
			$dao->addValueStr(PublishersDao::COL_URL, $this->form->get('url'));
			if ($this->form->getInt('filedelete')==1) {
				$dao->addValueStr(PublishersDao::COL_IMAGE_FILE, '');
				$dao->addValueStr(PublishersDao::COL_IMAGE_PATH, '');
				$dao->addValue(PublishersDao::COL_IMAGE_SIZE, 0);
			} else if ($this->form->get('image_file')!='') {
				$dao->addValueStr(PublishersDao::COL_IMAGE_FILE, $this->form->get('image_file'));
				$dao->addValueStr(PublishersDao::COL_IMAGE_PATH, $this->form->get('image_path'));
				$dao->addValue(PublishersDao::COL_IMAGE_SIZE, $this->form->get('image_size'));
			}
			$dao->addValueStr(PublishersDao::COL_MAILMAGA_FLAG, $this->form->get('mailmaga_flag'));
//			$dao->addValue(PublishersDao::COL_LASTUPDATE, Dao::DATE_NOW);
			$dao->addWhere(PublishersDao::COL_PUBLISHER_ID, $id);
			$dao->doUpdate();

			// イメージのコピー
			if ($ctr->copyFile('publisher', $id) === false) {
				throw new Exception('ファイルのコピーに失敗');
			}

			$this->db->commit();

		} catch (SpException $e) {
			$this->db->rollback();
			$this->logger->exception($e);
			return $this->edit();
		}

		return $this->resp->sendRedirect('/admin/publisher/');
	}

	/**
	 * 削除
	 */
	public function delete()
	{
		$id = $this->form->getInt('id', 0);
		if ($id > 0) {
			$dao = new PublishersDao($this->db);
			$dao->delete($id);
		}
		return $this->resp->sendRedirect('/admin/publisher/');
	}

	/**
	 * ユーザーとしてログイン
	 */
	public function userauth()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');
		$dao = new PublishersDao($this->db);
		$dao->addWhere(PublishersDao::COL_DELETE_FLAG, 0);
		$dao->addWhere(PublishersDao::COL_PUBLISHER_ID, $id);
		$user = $dao->selectRow();
		$this->setUserInfo($user, APP_CONST_USER_TYPE_P);
		return $this->resp->sendRedirect('/'.APP_CONST_USER_TYPE_P.'/mypage/');
	}
}
?>
