<?php
//Sp::import('user/regist/UserRegistController', 'controllers');
Sp::import('UsersDao', 'dao');
/**
 * 管理画面／ユーザー(Controller)
 */
class AdminDataUserController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$total = 0;
		$limit = APP_CONST_ADMIN_PAGE_LIMIT;
		$offset = $this->form->getInt('offset');
		// pagenumは1～
		$pagenum = $this->form->getInt('pagenum');
		if ($pagenum>0) {
			$offset = ($pagenum - 1) * $limit;
			$this->form->set('offset', $offset);
		}

		$UsersDao = new UsersDao($this->db);
		$this->form->set('list', $UsersDao->getPageListOnAdmin($total, $limit, $offset, array('user_id'=>'DESC')));
		$this->form->set('total', $total);
		$this->form->set('limit', $limit);

		$this->form->set('htitle', 'ユーザー管理');
		$this->setTitle($this->form->get('htitle'));

		$this->setPageData('data', 'user');

		return $this->forward('admin/data/user/admin_data_user_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 編集
	 */
	public function edit()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		if ($this->form->isGetMethod()) {
			$dao = new UsersDao($this->db);
			$dao->addWhere(UsersDao::COL_DELETE_FLAG, 0);
			$dao->addWhere(UsersDao::COL_USER_ID, $id);
			$user = $dao->selectRow();
			if (count($user)==0) return $this->errorPage('データがありません');
			if ($user['coverage']!='') $user['coverage'] = explode("\t", $user['coverage']);
			if ($user['meeting_method']!='') $user['meeting_method'] = explode("\t", $user['meeting_method']);
			unset($user['password']);
			$this->form->setDefaultAll($user);
		}

		$ctr = new UserRegistController($this->logger, $this->db, $this->form, $this->resp);
		$ctr->index();

		$this->form->setParameterForm('id');

		$this->form->set('htitle', 'デザイナー情報の変更');
		$this->setTitle($this->form->get('htitle'), 'デザイナー');

		return $this->forward('admin/user/admin_user_edit', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 確認
	 */
	public function confirm()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		$ctr = new UserRegistController($this->logger, $this->db, $this->form, $this->resp);
		$ctr->validate_user_id = $id;
		$ctr->confirm();

		if ($this->form->getTemplateNmae() == 'user/regist/user_regist_index') {
			return $this->edit();
		}

		$this->form->setParameterForm('id');
		$this->form->setParameterForm('status');

		$this->form->set('htitle', 'デザイナー情報の変更（確認画面）');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/user/admin_user_confirm', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 登録
	 */
	public function save()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');

		$ctr = new UserRegistController($this->logger, $this->db, $this->form, $this->resp);

		$this->form->set('name', $this->form->get('name_sei').'　'.$this->form->get('name_mei'));
		$this->form->set('kana', $this->form->get('kana_sei').'　'.$this->form->get('kana_mei'));

		try {

			$this->db->beginTransaction();

			$dao = new UsersDao($this->db);
			$dao->addValue(UsersDao::COL_STATUS, $this->form->getInt('status'));
			$dao->addValueStr(UsersDao::COL_NICKNAME, $this->form->get('nickname'));
			$dao->addValueStr(UsersDao::COL_EMAIL, $this->form->get('email'));
			if ($this->form->get('password')!='') {
				$dao->addValueStr(UsersDao::COL_PASSWORD, Util::password($this->form->get('password')));
			}
			$dao->addValueStr(UsersDao::COL_NAME, $this->form->get('name'));
			$dao->addValueStr(UsersDao::COL_NAME_SEI, $this->form->get('name_sei'));
			$dao->addValueStr(UsersDao::COL_NAME_MEI, $this->form->get('name_mei'));
			$dao->addValueStr(UsersDao::COL_KANA, $this->form->get('kana'));
			$dao->addValueStr(UsersDao::COL_KANA_SEI, $this->form->get('kana_sei'));
			$dao->addValueStr(UsersDao::COL_KANA_MEI, $this->form->get('kana_mei'));
			$dao->addValueStr(UsersDao::COL_ZIP, $this->form->get('zip'));
			$dao->addValueStr(UsersDao::COL_AREA, $this->form->get('area'));
			$dao->addValueStr(UsersDao::COL_ADDR1, $this->form->get('addr1'));
			$dao->addValueStr(UsersDao::COL_ADDR2, $this->form->get('addr2'));
			$dao->addValueStr(UsersDao::COL_TEL, $this->form->get('tel'));
			$dao->addValueStr(UsersDao::COL_FAX, $this->form->get('fax'));
			$dao->addValueStr(UsersDao::COL_MOBILE_TEL, $this->form->get('mobile_tel'));
			$dao->addValueStr(UsersDao::COL_URL, $this->form->get('url'));
			if ($this->form->getInt('filedelete')==1) {
				$dao->addValueStr(UsersDao::COL_IMAGE_FILE, '');
				$dao->addValueStr(UsersDao::COL_IMAGE_PATH, '');
				$dao->addValue(UsersDao::COL_IMAGE_SIZE, 0);
			} else if ($this->form->get('image_file')!='') {
				$dao->addValueStr(UsersDao::COL_IMAGE_FILE, $this->form->get('image_file'));
				$dao->addValueStr(UsersDao::COL_IMAGE_PATH, $this->form->get('image_path'));
				$dao->addValue(UsersDao::COL_IMAGE_SIZE, $this->form->get('image_size'));
			}
			$dao->addValueStr(UsersDao::COL_CAREER, $this->form->get('career'));
			$dao->addValueStr(UsersDao::COL_FORTE, $this->form->get('forte'));
			$dao->addValueStr(UsersDao::COL_PRODUCTION_ENV, $this->form->get('production_env'));
			$dao->addValueStr(UsersDao::COL_BUSINESS_HOURS, $this->form->get('business_hours'));
			$dao->addValueStr(UsersDao::COL_MEETING_METHOD, $this->form->getToString('meeting_method', "\t"));
			$dao->addValueStr(UsersDao::COL_MEETING_OTHER1, $this->form->get('meeting_other1'));
			$dao->addValueStr(UsersDao::COL_MEETING_OTHER2, $this->form->get('meeting_other2'));
			$dao->addValueStr(UsersDao::COL_MAILMAGA_FLAG, $this->form->get('mailmaga_flag'));
			$dao->addValueStr(UsersDao::COL_COVERAGE, $this->form->getToString('coverage', "\t"));
//			$dao->addValue(UsersDao::COL_LASTUPDATE, Dao::DATE_NOW);
			$dao->addWhere(UsersDao::COL_USER_ID, $id);
			$dao->doUpdate();

			// イメージのコピー
			if ($ctr->copyFile('user', $id) === false) {
				throw new Exception('ファイルのコピーに失敗');
			}

			$this->db->commit();

		} catch (SpException $e) {
			$this->db->rollback();
			$this->logger->exception($e);
			return $this->edit();
		}

		return $this->resp->sendRedirect('/admin/user/');
	}

	/**
	 * 削除
	 */
	public function delete()
	{
		$id = $this->form->getInt('id', 0);
		if ($id > 0) {
			$dao = new UsersDao($this->db);
			$dao->delete($id);
		}

		return $this->resp->sendRedirect('/admin/user/');
	}

	/**
	 * ユーザーとしてログイン
	 */
	public function userauth()
	{
		$id = $this->form->getInt('id', 0);
		if (empty($id)) return $this->errorPage('URLが違います');
		$dao = new UsersDao($this->db);
		$dao->addWhere(UsersDao::COL_USER_ID, $id);
		$user = $dao->selectRow();
		$this->setUserInfo($user);
		return $this->resp->sendRedirect('/');
	}
}
?>
