<?php
Sp::import('BooksDao', 'dao');
Sp::import('BookRanksDao', 'dao');
Sp::import('CategoryRanksDao', 'dao');
/**
 * 毎日実行するジャンル別ランキング集計バッチ
 * @author Hiroyoshi
 */
class RankingCategoryDaily
{
	/**
	 * @var DbManager
	 */
	private $db;

	/**
	 * @var SpLogger
	 */
	private $logger;

	public function __construct(&$db, &$logger)
	{
		$this->db =& $db;
		$this->logger =& $logger;
	}

	public function execute()
	{
		$this->logger->info('RankingCategoryDaily start.');

		$categorys = AppConst::$book_category;

		$bookRanksDao = new BookRanksDao($this->db);
		$categoryRanksDao = new CategoryRanksDao($this->db);
		$categoryRanksDao->deleteAll();
		$categoryRanksDao->reset();
		foreach ($categorys as $category_id => $category_name) {
			$bookRanksDao->addWhere('b.'.BooksDao::COL_CATEGORY_ID, $category_id);
			$bookRanksDao->setPopularBooks();
			$bookRanksDao->addLimit(5);
			$book = $bookRanksDao->select();
			$bookRanksDao->reset();

			$book_id = (isset($book[0]['book_id']) && $book[0]['book_id']>0) ? $book[0]['book_id'] : 0;
			$categoryRanksDao->addValue(CategoryRanksDao::COL_CATEGORY_ID, $category_id);
			$categoryRanksDao->addValueStr(CategoryRanksDao::COL_CATEGORY_NAME, $category_name);
			$categoryRanksDao->addValue(CategoryRanksDao::COL_BOOK_ID, $book_id);
			$book_data = '';
			if (count($book)>0) {
				foreach ($book as $d) {
					if ($book_data!='') $book_data .= "\n";
					$book_data .= $d['book_id']."\t".$d['title'];
				}
			}
			$categoryRanksDao->addValueStr(CategoryRanksDao::COL_BOOK_DATA, $book_data);
			$categoryRanksDao->doInsert();
			$categoryRanksDao->reset();
		}

		$this->logger->info('RankingCategoryDaily end.');

		return true;
	}
}
?>
