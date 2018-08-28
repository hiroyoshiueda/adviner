<?php
Sp::import('facebook.php', 'libs/facebook-php-sdk/src');
/**
 * Facebookツール(Util)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class FacebookUtil
{
	const FRIENDS_LIST_CACHE = '__fb_friends_list_cache__';
	const FRIENDS_LIST_CACHE_TS = '__fb_friends_list_cache_ts__';

	/**
	 * @param SpLogger $logger
	 * @param SpForm $form
	 * @param boolean $is_shuffle
	 */
	public static function getFriendsList(&$logger, &$form, $is_shuffle=true)
	{
		$ts = $form->get(self::FRIENDS_LIST_CACHE_TS);
		$friends_list = $form->get(self::FRIENDS_LIST_CACHE);

		if (empty($ts) || $ts < time())
		{
			$friends_list = self::_getApiFriendsList($logger);
			if ($is_shuffle) shuffle($friends_list);

			$ts = time() + 300;
			$form->setSession(self::FRIENDS_LIST_CACHE_TS, $ts);
			$form->setSession(self::FRIENDS_LIST_CACHE, $friends_list);
		}

		return $friends_list;
	}

	/**
	 *
	 * @param SpLogger $logger
	 */
	private static function _getApiFriendsList(&$logger)
	{
		$Facebook = new Facebook(array(
			'appId'  => APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY,
			'secret' => APP_CONST_FACEBOOK_OAUTH_CONSUMER_SECRET
		));

		$friends_list = array();

		try
		{
			if ($Facebook->getUser())
			{
				$response = $Facebook->api('/me/friends', 'GET');
//$logger->debug($response['data']);
				if (is_array($response['data']))
				{
					$friends_list = $response['data'];
				}
			}
		}
		catch (FacebookApiException $e)
		{
			$logger->exception($e);
		}

		return $friends_list;
	}
}
?>