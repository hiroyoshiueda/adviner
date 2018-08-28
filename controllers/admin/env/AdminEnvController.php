<?php
Sp::import('EnvsDao', 'dao');
/**
 * 管理画面 - 環境設定(Controller)
 */
class AdminEnvController extends AdminBaseController
{
	public function index()
	{
		return $this->resp->sendRedirect('ssl');
	}

	/**
	 * 外部への公開
	 */
	public function public_site()
	{
		if ($this->form->isGetMethod())
		{
			$this->_defaultEnv(array('ENV_PUBLIC_SITE'));
		}

		$this->form->set('htitle', '外部への公開');
		$this->setTitle($this->form->get('htitle'), '環境設定');

		$this->createSecurityCode();
		$this->setPageData('env', 'public_site');

		return $this->forward('admin/env/admin_env_public_site');
	}

	public function public_site_save_api()
	{
		return $this->_save_api(array('ENV_PUBLIC_SITE'), 0);
	}

	public function public_site_delete_api()
	{
		return $this->_delete_api(array('ENV_PUBLIC_SITE'));
	}

	/**
	 * アクセス解析タグ
	 */
	public function analyze()
	{
		if ($this->form->isGetMethod())
		{
			$this->_defaultEnv(array('ENV_ANALYZE_TAG'));
		}

		$this->form->set('htitle', 'アクセス解析タグ');
		$this->setTitle($this->form->get('htitle'), '環境設定');

		$this->createSecurityCode();
		$this->setPageData('env', 'analyze');

		return $this->forward('admin/env/admin_env_analyze');
	}

	public function analyze_save_api()
	{
		return $this->_save_api(array('ENV_ANALYZE_TAG'), 1);
	}

	public function analyze_delete_api()
	{
		return $this->_delete_api(array('ENV_ANALYZE_TAG'));
	}

	/**
	 * SSL接続
	 */
	public function ssl()
	{
		if ($this->form->isGetMethod())
		{
			$this->_defaultEnv(array('ENV_FORCE_HTTPS', 'ENV_ADMIN_HTTPS'));
		}

		$this->form->set('htitle', 'SSL接続');
		$this->setTitle($this->form->get('htitle'), '環境設定');

		$this->createSecurityCode();
		$this->setPageData('env', 'ssl');

		return $this->forward('admin/env/admin_env_ssl');
	}

	public function ssl_save_api()
	{
		return $this->_save_api(array('ENV_FORCE_HTTPS', 'ENV_ADMIN_HTTPS'), 0);
	}

	private function _save_api($save_keys, $validate_num=0)
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkAdminAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'errors' => array(),
			'security_token' => ''
		);

		if ($validate_num > 0 && $this->form->validate($this->form->getValidates($validate_num)) === false)
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

				$EnvsDao = new EnvsDao($this->db);
				foreach ($save_keys as $key) {
					$EnvsDao->save($key, $this->form->get($key));
				}

				$this->db->commit();

				$json_data['result'] = 1;
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

	private function _delete_api($save_keys)
	{
		if ($this->checkXHR() === false || $this->form->isPostMethod() === false || $this->checkAdminAuth() === false) return $this->notfound();

		$json_data = array(
			'html' => '',
			'result' => 0,
			'errmsg' => '',
			'errors' => array(),
			'security_token' => '',
			'values' => array()
		);

		$values = array();

//		if ($this->checkSecurityCode() === false)
//		{
//			$json_data['errmsg'] = parent::ERROR_PAGE_MESSAGE5;
//		}
//		else
//		{
			try
			{
				$this->db->beginTransaction();

				$EnvsDao = new EnvsDao($this->db);
				foreach ($save_keys as $key) {
					if ($key == 'ENV_ANALYZE_TAG') {
						$EnvsDao->save($key, '');
						$values[$key] = '';
					} else {
//						$EnvsDao->save($key, '0');
//						$values[$key] = '0';
					}
				}

				$this->db->commit();

				$json_data['values'] = $values;
				$json_data['result'] = 1;
			}
			catch (SpException $e)
			{
				$this->logger->exception($e);
				$this->db->rollback();
				$json_data['errmsg'] = parent::ERROR_AJAX_MESSAGE1;
			}
//		}

		return $this->jsonPage($json_data, true);
	}

	private function _defaultEnv($keys)
	{
		$EnvsDao = new EnvsDao($this->db);
		$env_set = $EnvsDao->getKeyValue();

		foreach ($keys as $key) {
			$this->form->set($key, $env_set[$key]);
		}
	}
}
?>
