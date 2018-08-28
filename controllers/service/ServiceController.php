<?php
/**
 * サービス(Controller)
 * @author Hiroyoshi
 */
class ServiceController extends BaseController
{
	/**
	 * Advinerとは
	 */
	public function index()
	{
		$this->form->set('htitle', '相談とアドバイスの流れ');
		$this->setTitle($this->form->get('htitle'));

		//$this->setDescription('初めての方へAdviner(アドバイナー)について紹介しています。');

		return $this->forward('service/service_index', APP_CONST_MAIN_FRAME);
	}

	public function charge()
	{
		$this->form->set('htitle', '有料アドバイスについて');
		$this->setTitle($this->form->get('htitle'));

		//$this->setDescription('初めての方へAdviner(アドバイナー)について紹介しています。');

		return $this->forward('service/service_charge', APP_CONST_MAIN_FRAME);
	}

	public function pay()
	{
		$this->form->set('htitle', '有料相談する方法');
		$this->setTitle($this->form->get('htitle'));

		//$this->setDescription('初めての方へAdviner(アドバイナー)について紹介しています。');

		return $this->forward('service/service_pay', APP_CONST_MAIN_FRAME);
	}
}
?>
