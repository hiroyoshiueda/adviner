<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('CategorysDao', 'dao');
/**
 * 管理画面／相談窓口(Controller)
 */
class AdminDataAdviceController extends AdminBaseController
{
	/**
	 * 相談窓口一覧
	 */
	public function index()
	{
		return $this->_list('index');
	}

	/**
	 * 承認待ちリスト
	 */
	public function examine()
	{
		return $this->_list('examine');
	}

	private function _list($page_type)
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

		if ($page_type == 'examine')
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$this->form->set('list', $AdvicesDao->getExamineList($total, $offset, $limit));

			$this->form->set('htitle', '承認待ち相談窓口');
			$this->setTitle($this->form->get('htitle'));
			$this->setPageData('data', 'examine');
		}
		else
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$this->form->set('list', $AdvicesDao->getPageList($total, $offset, $limit));

			$this->form->set('htitle', 'すべての相談窓口');
			$this->setTitle($this->form->get('htitle'));
			$this->setPageData('data', 'advice');
		}

		$this->form->set('total', $total);
		$this->form->set('limit', $limit);

		return $this->forward('admin/data/advice/admin_data_advice_index', APP_CONST_ADMIN_FRAME);
	}

	/**
	 * 承認
	 */
	public function accept()
	{
		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		try
		{
			$this->db->beginTransaction();

			$advicesDao = new AdvicesDao($this->db);
			$advicesDao->addSelect(AdvicesDao::COL_CATEGORY_ID);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
			$category_id = $advicesDao->selectId();

			$advicesDao->reset();
			$advicesDao->addValue(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_OK);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_EXAMINE);
			$advicesDao->doUpdate();

			// カテゴリの数をカウントアップ
			$categorysDao = new CategorysDao($this->db);
			$categorysDao->updateCountUpTotal($category_id);

			// 通知する

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		return $this->resp->sendRedirect('examine');
	}

	/**
	 * 断る
	 */
	public function refuse()
	{
		$advice_id = $this->form->getInt('advice_id');
		if (empty($advice_id)) return $this->notfound();

		try
		{
			$this->db->beginTransaction();

			$advicesDao = new AdvicesDao($this->db);
//			$advicesDao->addSelect(AdvicesDao::COL_CATEGORY_ID);
//			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
//			$category_id = $advicesDao->selectId();

//			$advicesDao->reset();
			$advicesDao->addValue(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_REFUSE);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_ID, $advice_id);
			$advicesDao->addWhere(AdvicesDao::COL_ADVICE_STATUS, AdvicesDao::ADVICE_STATUS_EXAMINE);
			$advicesDao->doUpdate();

//			// カテゴリの数をカウントアップ
//			$categorysDao = new CategorysDao($this->db);
//			$categorysDao->updateCountUpTotal($category_id);

			// 通知する

			$this->db->commit();
		}
		catch (SpException $e)
		{
			$this->logger->exception($e);
			$this->db->rollback();
		}

		return $this->resp->sendRedirect('examine');
	}
}
?>
