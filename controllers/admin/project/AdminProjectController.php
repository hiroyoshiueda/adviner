<?php
Sp::import('AdminBaseController');
Sp::import('ProjectsDao', 'dao');
Sp::import('ProposesDao', 'dao');
Sp::import('PublishersDao', 'dao');
Sp::import('UsersDao', 'dao');
/**
 * 管理画面 - よくある質問カテゴリ(Controller)
 */
class AdminProjectController extends AdminBaseController
{
	/**
	 * 一覧
	 */
	public function payment()
	{
		$total = 0;
		$offset = $this->form->getInt('offset', 0);
		$admin_pay_flag = $this->form->getInt('admin_pay_flag', 0);

		$projectsDao = new ProjectsDao($this->db);
		$projectsDao->addSelect('p.*');
		$projectsDao->addSelect('pr.'.ProposesDao::COL_PROPOSE_ID);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_USER_ID);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_PUBLISHER_PAY_FLAG);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_PUBLISHER_PAY_DATE);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_USER_DELIVERY_FLAG);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_USER_DELIVERY_DATE);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_ADMIN_PAY_FLAG);
		$projectsDao->addSelect('pr.'.ProposesDao::COL_ADMIN_PAY_DATE);
		$projectsDao->setTable(ProjectsDao::TABLE_NAME, 'p');
		$projectsDao->addTableJoin(ProposesDao::TABLE_NAME, 'pr', 'p.project_id=pr.project_id');
		$projectsDao->addWhere('pr.'.ProposesDao::COL_ACCEPT_FLAG, 1);
		$projectsDao->addWhere('pr.'.ProposesDao::COL_ADMIN_PAY_FLAG, $admin_pay_flag);
		$projectsDao->addWhere('p.'.ProjectsDao::COL_DELETE_FLAG, 0);
		$projectsDao->addOrder(ProjectsDao::COL_DELIVERY_DEADLINE);
		$list = $projectsDao->selectPage($offset, APP_CONST_ADMIN_PAGE_LIMIT, &$total);
		$this->form->set('list', $list);
		$this->form->set('total', $total);

		if (count($list)>0) {
			// 出版社
			$publisher_ids = Util::arraySelectKey('publisher_id', &$list);
			$publishersDao = new PublishersDao($this->db);
			$publisher = $publishersDao->getListByIds($publisher_ids);
			$this->form->set('publisher', Util::arrayKeyData('publisher_id', $publisher));
			// デザイナー
			$user_ids = Util::arraySelectKey('user_id', &$list);
			$usersDao = new UsersDao($this->db);
			$user = $usersDao->getListByIds($user_ids);
			$this->form->set('user', Util::arrayKeyData('user_id', $user));
		}

		$this->form->set('htitle', '成立案件');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/project/admin_project_payment', APP_CONST_ADMIN_FRAME);
	}

	public function paid()
	{
		$id = $this->form->getInt('id', 0);
		if ($id > 0) {
			$proposesDao = new ProposesDao($this->db);
			$proposesDao->addValue(ProposesDao::COL_ADMIN_PAY_FLAG, 1);
			$proposesDao->addValue(ProposesDao::COL_ADMIN_PAY_DATE, Dao::DATE_NOW);
			$proposesDao->addWhere(ProposesDao::COL_PROPOSE_ID, $id);
			$proposesDao->doUpdate();
		}
		return $this->resp->sendRedirect('/admin/project/payment');
	}

	public function unpublic()
	{
		$total = 0;
		$offset = $this->form->getInt('offset', 0);

		$projectsDao = new ProjectsDao($this->db);
		$projectsDao->addSelect('p.*');
		$projectsDao->addSelectCount('pr.propose_id', 'cnt');
		$projectsDao->setTable(ProjectsDao::TABLE_NAME, 'p');
		$projectsDao->addTableJoin(ProposesDao::TABLE_NAME, 'pr', 'p.project_id=pr.project_id');
		$projectsDao->addWhere('p.'.ProjectsDao::COL_CLOSE_FLAG, 0);
		$projectsDao->addWhereStr('p.'.ProjectsDao::COL_PUBLIC_DATE, date('Y-m-d'), '<');
		$projectsDao->addHaving('cnt > 0');
		$projectsDao->addWhere('p.'.ProjectsDao::COL_DELETE_FLAG, 0);
		$projectsDao->addOrder('p.'.ProjectsDao::COL_DELIVERY_DEADLINE);
		$list = $projectsDao->selectPage($offset, APP_CONST_ADMIN_PAGE_LIMIT, &$total);
		$this->form->set('list', $list);
		$this->form->set('total', $total);

		if (count($list)>0) {
			// 出版社
			$publisher_ids = Util::arraySelectKey('publisher_id', &$list);
			$publishersDao = new PublishersDao($this->db);
			$publisher = $publishersDao->getListByIds($publisher_ids);
			$this->form->set('publisher', Util::arrayKeyData('publisher_id', $publisher));
		}

		$this->form->set('htitle', '未公表案件');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('admin/project/admin_project_unpublic', APP_CONST_ADMIN_FRAME);
	}
}
?>
