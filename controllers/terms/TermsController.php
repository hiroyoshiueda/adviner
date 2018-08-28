<?php
/**
 * サービス(Controller)
 * @author Hiroyoshi
 */
class TermsController extends BaseController
{
	/**
	 * 利用規約
	 */
	public function rule()
	{
		$this->form->set('page_tpl', '_parts/terms_rule_html.tpl');

		$this->form->set('htitle', 'Adviner 利用規約');
		$this->setTitle($this->form->get('htitle'));

		$this->setDescription('Adviner の利用に際して適用される利用規約です。');

		return $this->forward('terms/terms_common', APP_CONST_MAIN_FRAME);
	}

	/**
	 * 有料アドバイス利用規約
	 */
	public function charge_advice()
	{
		$this->form->set('page_tpl', '_parts/terms_charge_advice_html.tpl');

		$this->form->set('htitle', '有料アドバイス業務規約');
		$this->setTitle($this->form->get('htitle'));

		$this->setDescription('有料でアドバイスする時に適用される利用規約です。');

		return $this->forward('terms/terms_common', APP_CONST_MAIN_FRAME);
	}

	/**
	 * プライバシーポリシー
	 */
	public function privacy()
	{
		$this->form->set('page_tpl', '_parts/terms_privacy_html.tpl');

		$this->form->set('htitle', 'プライバシーポリシー');
		$this->setTitle($this->form->get('htitle'));

		$this->setDescription('Adviner で適用されるプライバシーポリシーです。');

		return $this->forward('terms/terms_common', APP_CONST_MAIN_FRAME);
	}

	/**
	 * ガイドライン
	 */
	public function guide()
	{
		$this->form->set('page_tpl', '_parts/terms_guide_html.tpl');

		$this->form->set('htitle', '相談・アドバイスする時のガイドライン');
		$this->setTitle($this->form->get('htitle'));

		$this->setDescription('Adviner で相談したり、アドバイスする時のガイドラインです。');

		return $this->forward('terms/terms_common', APP_CONST_MAIN_FRAME);
	}

	public function info()
	{
		$this->form->set('page_tpl', 'terms/terms_info.tpl');

		$this->form->set('htitle', '特定商取引法に基づく表記');
		$this->setTitle($this->form->get('htitle'));

		return $this->forward('terms/terms_common', APP_CONST_MAIN_FRAME);
	}
}
?>
