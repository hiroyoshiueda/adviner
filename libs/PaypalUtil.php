<?php
/**
 * PAYPAL API(nvp) 操作
 */
class PaypalUtil
{
	private $api_username = '';
	private $api_password = '';
	private $api_signature = '';
	private $api_endpoint = '';
	private $api_version = '65.1';
	private $nvp_postfields = '';
	private $errormsg = '';

	public function __construct($username, $password, $signature, $env='')
	{
		$this->api_username = $username;
		$this->api_password = $password;
		$this->api_signature = $signature;

		if ($env == 'sandbox') {
			$this->api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
		} else {
			$this->api_endpoint = 'https://api-3t.paypal.com/nvp';
		}
	}

	/**
	 * @param string $env
	 * @return PaypalUtil
	 */
	static public function getPaypalUtil($env)
	{
		if ($env == 'sandbox')
		{
			$api_user = 'hiro02_1314342073_biz_api1.live.jp';
			$api_pass = '1314342104';
			$api_sign = 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AHnmblZE6-xMcmcwrhhvZ6isMnyJ';
		}
		else
		{
			$api_user = 'e-sora_api1.live.jp';
			$api_pass = 'E6PSQ5FRJLKW99LF';
			$api_sign = 'Aq-PAybj4N0tlMFl2.9SbJ6wdZh6AhleJ2MOZKCEoAgjlOZC93mxUA3i';
		}

		return new PaypalUtil($api_user, $api_pass, $api_sign, $env);
	}

	public function getNVP($method_name, $q_str)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		//if (USE_PROXY) curl_setopt($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);

		$req_arr   = array();
		$req_arr[] = 'METHOD='.urlencode($method_name);
		$req_arr[] = 'VERSION='.urlencode($this->api_version);
		$req_arr[] = 'PWD='.urlencode($this->api_password);
		$req_arr[] = 'USER='.urlencode($this->api_username);
		$req_arr[] = 'SIGNATURE='.urlencode($this->api_signature);
		if ($q_str != '') $req_arr[] = $q_str;

		$this->nvp_postfields = implode('&', $req_arr);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->nvp_postfields);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			$this->errormsg = curl_error($ch).'('.curl_errno($ch).')';
			return false;
		}

		$res_arr = $this->_parsedQueryString($response);

		@curl_close($ch);

		return $res_arr;
	}

	public function getEndPoint()
	{
		return $this->api_endpoint;
	}

	public function getPostFields()
	{
		return $this->nvp_postfields;
	}

	public function getErrorMessage()
	{
		return $this->errormsg;
	}

	private function _parsedQueryString($q_str)
	{
		$parsed_arr = array();
		$q_arr = explode('&', $q_str);
		if (count($q_arr) > 0)
		{
			foreach ($q_arr as $value) {
				$arr = explode('=', $value);
				if($arr[0] && isset($arr[1])) {
					$parsed_arr[urldecode($arr[0])] = urldecode($arr[1]);
				}
			}
		}
		return $parsed_arr;
	}
}