<?php
Sp::import('AdvicesDao', 'dao');
Sp::import('ConsultsDao', 'dao');
Sp::import('OrdersDao', 'dao');
Sp::import('OrderPaymentsDao', 'dao');
Sp::import('UsersDao', 'dao');
Sp::import('PaypalUtil.php', 'libs');
/**
 * (POPUP)支払い画面
 * @author Hiroyoshi
 */
class PopupPaymentController extends BaseController
{
//	const PAYPAL_ENV = 'sandbox';
	const PAYPAL_ENV = '';

//	/**
//	 * お支払い
//	 */
//	public function index()
//	{
//		$advice_id = $this->form->getInt('advice_id');
//		$consult_id = $this->form->getInt('consult_id');
//		if (empty($advice_id) || empty($consult_id) || $this->checkUserAuth() === false) return $this->notfound();
//
//		$userInfo = $this->getUserInfo();
//
//		$AdvicesDao = new AdvicesDao($this->db);
//		$advice = $AdvicesDao->getItem($advice_id);
//		if (empty($advice)) return $this->notfound();
//
//		$ConsultsDao = new ConsultsDao($this->db);
//		$consult = $ConsultsDao->getItem($consult_id);
//		if (empty($consult)) return $this->notfound();
//
//		// 支払い前
//		if ($consult['order_id'] == 0 && $consult['order_status'] != ConsultsDao::ORDER_STATUS_RECEIVE)
//		{
//
//		}
//		// 支払い済
//		else
//		{
//
//		}
//
//		$this->form->setParameterForm('advice_id');
//		$this->form->setParameterForm('consult_id');
//
//		$this->form->set('htitle', 'お支払い');
//		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);
//
//		$this->form->setScript('https://www.paypalobjects.com/js/external/dg.js');
//
//		return $this->forward('popup/payment/popup_payment_index', APP_CONST_POPUP_FRAME);
//	}

	/**
	 * Palpalリダイレクト
	 */
	public function paypal()
	{
		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		if (empty($advice_id) || empty($consult_id) || $this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		$AdvicesDao = new AdvicesDao($this->db);
		$advice = $AdvicesDao->getItem($advice_id);
		if (empty($advice)) return $this->notfound();

		$ConsultsDao = new ConsultsDao($this->db);
		$consult = $ConsultsDao->getItem($consult_id);
		if (empty($consult)) return $this->notfound();

		$is_error = false;

		// 支払い前
		if ($consult['order_id'] == 0 && $consult['order_status'] != ConsultsDao::ORDER_STATUS_RECEIVE)
		{
			// 注文処理
			$OrdersDao = new OrdersDao($this->db);
			$OrdersDao->addValue(OrdersDao::COL_STATUS, OrdersDao::STATUS_PROG);
			$OrdersDao->addValue(OrdersDao::COL_USER_ID, $userInfo['id']);
			$OrdersDao->addValue(OrdersDao::COL_ADVICE_ID, $advice_id);
			$OrdersDao->addValue(OrdersDao::COL_ADVICE_USER_ID, $advice['advice_user_id']);
			$OrdersDao->addValue(OrdersDao::COL_CONSULT_ID, $consult_id);
			$OrdersDao->addValue(OrdersDao::COL_CONSULT_USER_ID, $consult['consult_user_id']);
			$OrdersDao->addValue(OrdersDao::COL_PAYMENT_METHOD, OrdersDao::PAYMENT_METHOD_CARD);
			$OrdersDao->addValue(OrdersDao::COL_AMOUNT, $advice['charge_price']);
			$OrdersDao->addValue(OrdersDao::COL_CREATEDATE, Dao::DATE_NOW);
			$OrdersDao->doInsert();

			$order_id = $OrdersDao->getLastInsertId();

			$q_str = 'advice_id='.$advice_id.'&consult_id='.$consult_id.'&order_id='.$order_id;
			$redirect = $this->callSetExpressCheckout($advice['charge_price'], $q_str);
			if ($redirect !== false) {
				return $this->resp->sendRedirect($redirect);
			}
			$is_error = true;
		}

		$this->form->set('is_error', $is_error);

		$this->form->set('htitle', 'お支払い');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('popup/payment/popup_payment_paypal', APP_CONST_POPUP_FRAME);
	}

	/**
	 * 決済完了後画面
	 */
	public function paypal_success()
	{
		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		$order_id = $this->form->getInt('order_id');
		if (empty($advice_id) || empty($consult_id) || empty($order_id) || $this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		// EC-7DY57136NU257830X
		$token = $this->form->get('token');
		$payerid = $this->form->get('PayerID');

		$is_error = true;

		if (strlen($token) == 20 && $payerid != '')
		{
			$AdvicesDao = new AdvicesDao($this->db);
			$advice = $AdvicesDao->getItem($advice_id);
			if (empty($advice)) return $this->notfound();

			$ConsultsDao = new ConsultsDao($this->db);
			$consult = $ConsultsDao->getItem($consult_id);
			if (empty($consult)) return $this->notfound();

			// 処理中の注文データ
			$OrdersDao = new OrdersDao($this->db);
			$order = $OrdersDao->getItemByAdviceIdAndConsultId($order_id, $advice_id, $consult_id);
			if (empty($order) || $order['status'] != OrdersDao::STATUS_PROG) return $this->notfound();

			$tmp_arr = array();
			$payment_data = '';

			// 決済詳細情報の取得
			$detail_arr = $this->callGetExpressCheckoutDetails($token);
			if ($detail_arr !== false)
			{
				$tmp_arr['GetExpressCheckoutDetails'] = $detail_arr;
				$payment_data = var_export($tmp_arr, true);

				$OrdersDao = new OrdersDao($this->db);
				$OrdersDao->addValueStr(OrdersDao::COL_PAYMENT_DATA, $payment_data);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_NAME, $detail_arr['FIRSTNAME'].' '.$detail_arr['LASTNAME']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_FIRST_NAME, $detail_arr['FIRSTNAME']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_LAST_NAME, $detail_arr['LASTNAME']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_EMAIL, $detail_arr['EMAIL']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_ZIP, $detail_arr['SHIPTOZIP']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_ADDRESS1, $detail_arr['SHIPTOSTATE']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_ADDRESS2, $detail_arr['SHIPTOCITY']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_ADDRESS3, $detail_arr['SHIPTOSTREET']);
				$OrdersDao->addValueStr(OrdersDao::COL_ORDER_ADDRESS4, $detail_arr['SHIPTOSTREET2']);
				$OrdersDao->addWhere(OrdersDao::COL_ORDER_ID, $order_id);
				$OrdersDao->doUpdate();

				// 決済確定処理
				$payment_arr = $this->callDoExpressCheckoutPayment($order['amount'], $token, $payerid);
				if ($payment_arr !== false)
				{
					try
					{
						$tmp_arr['DoExpressCheckoutPayment'] = $payment_arr;
						$payment_data = var_export($tmp_arr, true);

						$advice_user_id = $order['advice_user_id'];
						$amount = $order['amount'];

						$this->db->beginTransaction();

						$UsersDao = new UsersDao($this->db);
						$UsersDao->addSelect(UsersDao::COL_CHARGE_RATE);
						$UsersDao->addWhere(UsersDao::COL_USER_ID, $advice_user_id);
						$charge_rate = $UsersDao->selectId();
						// 報酬額決定
						$reward = floor($order['amount'] * ($charge_rate / 100));

						$OrdersDao = new OrdersDao($this->db);
						$OrdersDao->addValue(OrdersDao::COL_STATUS, OrdersDao::STATUS_RECEIVE);
						$OrdersDao->addValueStr(OrdersDao::COL_PAYMENT_KEY, $payment_arr['TRANSACTIONID']);
						$OrdersDao->addValue(OrdersDao::COL_PAYMENT_FEE, $payment_arr['FEEAMT']);
						$OrdersDao->addValueStr(OrdersDao::COL_PAYMENT_DATA, $payment_data);
						$OrdersDao->addValue(OrdersDao::COL_REWARD, $reward);
						$OrdersDao->addValue(OrdersDao::COL_USER_CHARGE_RATE, $charge_rate);
						$OrdersDao->addValue(OrdersDao::COL_FINISHDATE, Dao::DATE_NOW);
						$OrdersDao->addWhere(OrdersDao::COL_ORDER_ID, $order_id);
						$OrdersDao->doUpdate();

						$ConsultsDao = new ConsultsDao($this->db);
						$ConsultsDao->addValue(ConsultsDao::COL_ORDER_ID, $order_id);
						$ConsultsDao->addValue(ConsultsDao::COL_ORDER_STATUS, ConsultsDao::ORDER_STATUS_RECEIVE);
						$ConsultsDao->addWhere(ConsultsDao::COL_CONSULT_ID, $consult_id);
						$ConsultsDao->doUpdate();

						$ym = date("Ym");

						$OrderPaymentsDao = new OrderPaymentsDao($this->db);
						$OrderPaymentsDao->addSelect(OrderPaymentsDao::COL_ORDER_PAYMENT_ID);
						$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_USER_ID, $advice_user_id);
						$OrderPaymentsDao->addWhereStr(OrderPaymentsDao::COL_YEAR_MONTHLY, $ym);
						$order_payment_id = $OrderPaymentsDao->selectId();

						$OrderPaymentsDao = new OrderPaymentsDao($this->db);
						if ($order_payment_id > 0)
						{
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_SALES_TOTAL, OrderPaymentsDao::COL_SALES_TOTAL.'+'.$amount);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_REWARD_TOTAL, OrderPaymentsDao::COL_REWARD_TOTAL.'+'.$reward);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_LASTUPDATE, Dao::DATE_NOW);
							$OrderPaymentsDao->addWhere(OrderPaymentsDao::COL_ORDER_PAYMENT_ID, $order_payment_id);
							$OrderPaymentsDao->doUpdate();
						}
						else
						{
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_USER_ID, $advice_user_id);
							$OrderPaymentsDao->addValueStr(OrderPaymentsDao::COL_YEAR_MONTHLY, $ym);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_SALES_TOTAL, $amount);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_REWARD_TOTAL, $reward);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_CREATEDATE, Dao::DATE_NOW);
							$OrderPaymentsDao->addValue(OrderPaymentsDao::COL_LASTUPDATE, Dao::DATE_NOW);
							$OrderPaymentsDao->doInsert();
						}

						$this->db->commit();

						$is_error = false;
					}
					catch (SpException $e)
					{
						$this->logger->exception($e);
						$this->db->rollback();
					}
				}
			}
		}

		// ここでエラーの場合はアラートメールを送信するレベル
		if ($is_error)
		{

		}

		$this->form->set('is_error', $is_error);

		$this->form->set('htitle', '支払い完了');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('popup/payment/popup_payment_paypal_success', APP_CONST_POPUP_FRAME);
	}

	/**
	 * キャンセル時画面
	 */
	public function paypal_cancel()
	{
		$advice_id = $this->form->getInt('advice_id');
		$consult_id = $this->form->getInt('consult_id');
		$order_id = $this->form->getInt('order_id');
		if (empty($advice_id) || empty($consult_id) || empty($order_id) || $this->checkUserAuth() === false) return $this->notfound();

		$userInfo = $this->getUserInfo();

		// 処理中の注文データ
		$OrdersDao = new OrdersDao($this->db);
		$order = $OrdersDao->getItemByAdviceIdAndConsultId($order_id, $advice_id, $consult_id);
		if ($order['status'] == OrdersDao::STATUS_PROG)
		{
			$OrdersDao->reset();
			$OrdersDao->statusCancel($order_id);
		}

		$this->form->set('htitle', 'キャンセル');
		$this->setTitle($this->form->get('htitle'), $userInfo['nickname']);

		return $this->forward('popup/payment/popup_payment_paypal_cancel', APP_CONST_POPUP_FRAME);
	}

	/**
	 * @param PaypalUtil $PaypalUtil
	 * @param array $nvp_arr
	 */
	private function parsedNVP(&$PaypalUtil, &$nvp_arr)
	{
//$this->logger->debug($nvp_arr);
//$this->logger->debug($PaypalUtil->getPostFields());
		if ($nvp_arr === false)
		{
			$this->logger->error('[Paypal API] failed: '.$PaypalUtil->getErrorMessage());
		}
		else
		{
			if (count($nvp_arr) == 0 || !array_key_exists('ACK', $nvp_arr))
			{
				$this->logger->error('[Paypal API] Invalid HTTP Response for POST request('.$PaypalUtil->getPostFields().') to '.$PaypalUtil->getEndPoint().'.');
			}
			else if ('SUCCESS' == strtoupper($nvp_arr['ACK']) || 'SUCCESSWITHWARNING' == strtoupper($nvp_arr['ACK']))
			{
				return true;
			}
			else
			{
				$this->logger->error('[Paypal API] Response error to '.$PaypalUtil->getEndPoint().'.');
				$this->logger->error($nvp_arr);
			}
		}
		return false;
	}

	/**
	 * 決済開始処理
	 * @param int $amount
	 * @param string $q_str
	 */
	private function callSetExpressCheckout($amount, $q_str)
	{
		if (empty($_SERVER['HTTPS']))
		{
			$return_url = urlencode(constant('app_site_url').'popup/payment/paypal_success?'.$q_str);
			$cancel_url = urlencode(constant('app_site_url').'popup/payment/paypal_cancel?'.$q_str);
		}
		else
		{
			$return_url = urlencode(constant('app_site_ssl_url').'popup/payment/paypal_success?'.$q_str);
			$cancel_url = urlencode(constant('app_site_ssl_url').'popup/payment/paypal_cancel?'.$q_str);
		}

		$payment_amount = urlencode($amount);

		$req_arr   = array();
		$req_arr[] = 'RETURNURL='.$return_url;
		$req_arr[] = 'CANCELURL='.$cancel_url;
		$req_arr[] = 'AMT='.$payment_amount;
		$req_arr[] = 'CURRENCYCODE=JPY';
		$req_arr[] = 'PAYMENTACTION=Sale';
		$req_arr[] = 'PAYMENTREQUEST_0_CURRENCYCODE=JPY';
		$req_arr[] = 'PAYMENTREQUEST_0_AMT='.$payment_amount;
		$req_arr[] = 'PAYMENTREQUEST_0_ITEMAMT='.$payment_amount;
		//$req_arr[] = 'PAYMENTREQUEST_0_DESC=Movies';
		$req_arr[] = 'PAYMENTREQUEST_0_PAYMENTACTION=Sale';
		$req_arr[] = 'L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital';
		$req_arr[] = 'L_PAYMENTREQUEST_0_NAME0='.urlencode('Adviner（有料相談）');
		$req_arr[] = 'L_PAYMENTREQUEST_0_QTY0=1';
		$req_arr[] = 'L_PAYMENTREQUEST_0_AMT0='.$payment_amount;
		//$req_arr[] = 'L_PAYMENTREQUEST_0_DESC0='.urlencode('Movie time: 1 hours 12 minutes');
		$req_arr[] = 'REQCONFIRMSHIPPING=0';
		$req_arr[] = 'NOSHIPPING=1';
		$req_arr[] = 'LOCALECODE=JP';

		$req_str = implode('&', $req_arr);

		$PaypalUtil = PaypalUtil::getPaypalUtil(self::PAYPAL_ENV);
		$nvp_arr = $PaypalUtil->getNVP('SetExpressCheckout', $req_str);

		if ($this->parsedNVP($PaypalUtil, $nvp_arr) !== false)
		{
			$redirect = '';
			$token = urldecode($nvp_arr['TOKEN']);
			if (self::PAYPAL_ENV == 'sandbox')
			{
				$redirect = 'https://www.sandbox.paypal.com/incontext?token='.$token;
			}
			else
			{
				$redirect = 'https://www.paypal.com/incontext?token='.$token;
			}
			//$this->logger->debug($nvp_arr);
			//$this->logger->debug('paypal url: '.$redirect);
			return $redirect;
		}

		return false;
	}

	/**
	 * 決済詳細情報の取得
	 * @param string $token
	 */
	private function callGetExpressCheckoutDetails($token)
	{
		$req_arr   = array();
		$req_arr[] = 'TOKEN='.urlencode($token);

		$req_str = implode('&', $req_arr);

		$PaypalUtil = PaypalUtil::getPaypalUtil(self::PAYPAL_ENV);
		$nvp_arr = $PaypalUtil->getNVP('GetExpressCheckoutDetails', $req_str);

		if ($this->parsedNVP($PaypalUtil, $nvp_arr) !== false)
		{
			$this->logger->debug($nvp_arr);
			return $nvp_arr;
		}

		return false;
	}

	/**
	 * 決済確定処理
	 * @param int $amount
	 * @param string $token
	 * @param string $payerid
	 * @return array or false
	 */
	private function callDoExpressCheckoutPayment($amount, $token, $payerid)
	{
		$req_arr   = array();
		$req_arr[] = 'TOKEN='.urlencode($token);
		$req_arr[] = 'PAYERID='.urlencode($payerid);
		$req_arr[] = 'AMT='.urlencode($amount);
		$req_arr[] = 'CURRENCYCODE=JPY';
		$req_arr[] = 'PAYMENTACTION=Sale';

		$req_str = implode('&', $req_arr);

		$PaypalUtil = PaypalUtil::getPaypalUtil(self::PAYPAL_ENV);
		$nvp_arr = $PaypalUtil->getNVP('DoExpressCheckoutPayment', $req_str);

		if ($this->parsedNVP($PaypalUtil, $nvp_arr) !== false)
		{
			return $nvp_arr;
		}

		return false;
	}
}
?>
