<?php
Sp::import('AdminBaseController', 'controllers');
/**
 * 管理画面 - 各種設定(Controller)
 */
class AdminSettingController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$authdata= file_get_contents(APP_CONST_ADMIN_AUTH_FILE);

		list($login, $password) = explode("\t\t", $authdata);
		$login = trim($login);
		$password = trim($password);

		$this->form->setDefault('login', $login);
		$this->form->setDefault('password', $password);

		$this->form->set('htitle', '管理ツールログイン情報');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/setting/admin_setting_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 保存
	 */
	public function save()
	{
		if ($this->_validate() === false) {
			$this->form->set('errors', $this->form->getValidateErrors());
			return $this->index();
		}

		$login = $this->form->get('login');
		$password = $this->form->get('password');

		$data = $login."\t\t".$password;

		file_put_contents(APP_CONST_ADMIN_AUTH_FILE, $data);

		return $this->resp->sendRedirect('/admin/setting/?success=true');
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
