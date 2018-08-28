<?php
/**
 * 管理(Controller)
 */
class AdminController extends AdminBaseController
{
	/**
	 * INDEX
	 */
	public function index()
	{
		return $this->resp->sendRedirect('/'.APP_ADMIN_DIR.'/data/user/');
	}

	public function login()
	{
		if ($this->form->validate($this->form->getValidates(0))) {

			$authdata = file_get_contents(APP_CONST_ADMIN_AUTH_FILE);

			list($login, $password) = explode("\t\t", $authdata);
			$login = trim($login);
			$password = trim($password);

			if ($this->form->get('adminlogin') == $login && $this->form->get('adminpassword') == $password)
			{
				$this->setAdminInfo($login);
				$loc = $this->form->get('loc');
				if ($loc == '' || $loc == '/'.APP_ADMIN_DIR.'/login') $loc = '/'.APP_ADMIN_DIR.'/';
				return $this->resp->sendRedirect($loc);
			}
			else
			{
				$this->form->setValidateErrors('adminpassword', 'ログインID、パスワードを確認してください。');
			}
		}

		$this->form->set('errors', $this->form->getValidateErrors());

		return $this->loginPage();
	}

	public function logout()
	{
		$this->deleteAdminInfo();
		$this->resp->sendRedirect('/admin/');
	}
}
?>