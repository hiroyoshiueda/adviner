<?php
/**
 * もろもろの掃除
 * @author Hiroyoshi
 */
class Cleaner
{
	/**
	 * tmpディレクトリの削除
	 * @param SpLogger $logger
	 * @param int $days $days日以前のファイルを削除
	 */
	public static function tempDir(&$logger, $days)
	{
		$files = array();
		UtilFile::readDirFile(&$files, APP_WWW_DIR . '/tmp');
		UtilFile::readDirFile(&$files, APP_DIR . '/tmp');
		if (count($files)>0) {
			clearstatcache();
			$remove_ts = time() - ($days * 86400);
			foreach ($files as $f) {
				if (file_exists($f) && is_file($f)) {
					if (filemtime($f) < $remove_ts) {
						@unlink($f);
						$logger->debug('Delete tmp file --> '.$f);
					}
				}
			}
		}
		return;
	}

	/**
	 * logディレクトリの削除
	 * @param SpLogger $logger
	 * @param string $date
	 */
	public static function logDir(&$logger, $date)
	{
		$logger->deleteLogDir($date);
		return;
	}

//	/**
//	 * tmpユーザーの削除
//	 * @param DbManager $db
//	 * @param int $days 登録日が$days日以前のユーザーを削除
//	 */
//	public static function tempUser(&$db, $days)
//	{
//		Sp::import('UsersDao', 'dao', true);
//
//		$remove_date = date('Y-m-d', time() - ($days * 86400));
//
//		$usersDao = new UsersDao($db);
//		$usersDao->addWhere(UsersDao::COL_STATUS, UsersDao::STATUS_TEMP);
//		$usersDao->addWhereStr(UsersDao::COL_CREATEDATE, $remove_date, '<');
//		return $usersDao->doDelete();
//	}
//
//
//	/**
//	 * tmpページデータの削除
//	 * @param DbManager $db
//	 * @param int $days 更新日が$days日以前のデータを削除
//	 */
//	public static function tempPublicationPage(&$db, $days)
//	{
//		Sp::import('PublicationPageTempsDao', 'dao', true);
//
//		$remove_date = date('Y-m-d', time() - ($days * 86400));
//
//		$tempsDao = new PublicationPageTempsDao($db);
//		$tempsDao->addWhereStr(PublicationPageTempsDao::COL_LASTUPDATE, $remove_date, '<');
//		return $tempsDao->doDelete();
//	}
}
?>
