<?php
class UtilMobile
{
	const TYPE_DOCOMO = 1;
	const TYPE_EZWEB = 2;
	const TYPE_SOFTBANK = 3;
	const TYPE_EMOBILE = 4;
	const TYPE_WILLCOM = 5;
	const TYPE_OTHER = 99;

	/**
	 * メールアドレスからキャリア判定
	 * @param string $email
	 */
	public static function getType($email)
	{
		if (preg_match("/@docomo\.ne\.jp$/", $email)) {

			return self::TYPE_DOCOMO;

		} else if (preg_match("/@ezweb\.ne\.jp$/", $email)
				|| preg_match("/@[a-z]+\.ezweb\.ne\.jp$/", $email)) {

			return self::TYPE_EZWEB;

		} else if (preg_match("/@softbank\.ne\.jp$/", $email)
				|| preg_match("/@i\.softbank\.jp$/", $email)
				|| preg_match("/@disney\.ne\.jp$/", $email)) {

			return self::TYPE_SOFTBANK;

		} else if (preg_match("/@[a-z]{1}\.vodafone\.ne\.jp$/", $email)
				|| preg_match("/@jp\-[a-z]{1}\.ne\.jp$/", $email)) {

			return self::TYPE_SOFTBANK;

		} else if (preg_match("/@emnet\.ne\.jp$/", $email)) {

			return self::TYPE_EMOBILE;

		} else if (preg_match("/@pdx\.ne\.jp$/", $email)
				|| preg_match("/@[a-z]+\.pdx\.ne\.jp$/", $email)
				|| preg_match("/@willcom\.com$/", $email)) {

			return self::TYPE_WILLCOM;
		}
		return self::TYPE_OTHER;
	}
}
?>
