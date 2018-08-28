<?php
Sp::import('FaqsDao', 'dao');
Sp::import('FaqCategorysDao', 'dao');
/**
 * よくある質問(Controller)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class HelpController extends BaseController
{
	/**
	 * 一覧
	 */
	public function index()
	{
		$FaqCategorysDao = new FaqCategorysDao($this->db);
		$FaqCategorysDao->addWhereIn(FaqCategorysDao::COL_FAQ_CATEGORY_ID, array(3,4));
		$this->form->set('faq_category_list', $FaqCategorysDao->getList());

		$FaqsDao = new FaqsDao($this->db);
		$list = $FaqsDao->getList();
		$faq_data = Util::arrayKeyData('faq_category_id,faq_id', $list);
		$this->form->set('faq_data', $faq_data);

		$this->form->set('htitle', 'よくある質問');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('help/help_index', APP_CONST_MAIN_FRAME);
	}

	public function service()
	{
		return $this->_detail(1);
	}

	private function _detail($faq_category_id)
	{
		$FaqCategorysDao = new FaqCategorysDao($this->db);
		$category = $FaqCategorysDao->getItem($faq_category_id);

		$FaqsDao = new FaqsDao($this->db);
		$faq_list = $FaqsDao->getListByCategoryId($faq_category_id);

		$this->form->set('category', $category);
		$this->form->set('faq_list', $faq_list);

		$FaqCategorysDao = new FaqCategorysDao($this->db);
		$this->form->set('faq_category_list', $FaqCategorysDao->getList());

		$this->form->set('htitle', $category['title']);
		$this->setTitle($this->form->get('htitle'), 'よくある質問');

		$this->setGNavi('よくある質問', '/help/');
		$this->setGNavi($category['title'], $this->form->getSp('base_url'));

		return $this->forward('help/help_detail', APP_CONST_MAIN_FRAME);
	}
}
?>
