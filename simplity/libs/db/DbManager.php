<?php
/**
 * DbManager
 */
class DbManager {

	/**
	 * @var SpLogger
	 */
	private $_logger = null;
	private $_charset = 'utf8';
	private $_lastQuery = '';
	private $_updateLines = 0;
	private $_clientCharset = '';
	private $_slaveNum = -1;

	/**
	 * @var mysqli
	 */
	private $_cons = array();
	private $_servers = array();
	private $_schemas = array();
	private $_users = array();
	private $_passwords = array();

	public function __construct($logger=null) {
		$this->_logger = $logger;
	}
	public function initConnect($conf)
	{
		$this->setConnectConf($conf['db_server'], $conf['db_schema'], $conf['db_user'], $conf['db_password']);
		$slave_db = (int)$conf['slave_db'];
		if ($slave_db>0) {
			for ($i=1; $i<=$slave_db; $i++) {
				$this->setConnectConf($conf['slave'.$i.'_db_server'], $conf['slave'.$i.'_db_schema'], $conf['slave'.$i.'_db_user'], $conf['slave'.$i.'_db_password']);
			}
		}
	}
	public function setConnectConf($server, $schema, $user, $password)
	{
		$this->_servers[] = $server;
		$this->_schemas[] = $schema;
		$this->_users[] = $user;
		$this->_passwords[] = $password;
	}
	public function connect($n=0)
	{
		if (isset($this->_cons[$n]) && $this->_cons[$n] && $this->_cons[$n]->ping()) return;
		$this->_cons[$n] = @new mysqli($this->_servers[$n], $this->_users[$n], $this->_passwords[$n], $this->_schemas[$n]);
		if (mysqli_connect_errno()) throw new DbException('', mysqli_connect_error(), mysqli_connect_errno());
		$this->_cons[$n]->set_charset($this->_charset);
		$this->_logger->debug("'".$n.' > '.$this->_servers[$n].' / '.$this->_schemas[$n]."' is connection.");
	}
	public function close($n=0)
	{
		if ($this->_cons[$n]) {
			@$this->_cons[$n]->close();
			$this->_logger->debug("'".$n.' > '.$this->_servers[$n].' / '.$this->_schemas[$n]."' is close connection.");
		}
		$this->_cons[$n] = null;
	}
	public function closeConnection()
	{
		foreach ($this->_cons as $n => $con) {
			if ($con) {
				@$con->close();
				$this->_logger->debug("'".$n.' > '.$this->_servers[$n].' / '.$this->_schemas[$n]."' is close connection.");
			}
			$this->_cons[$n] = null;
		}
		$this->_cons = array();
	}
	public function executeQuery($query) {
		return $this->_query($query);
	}
	public function executeUpdate($query) {
//		$i=0;
//		while ($i<5) {
//			try {
//				$this->_query($query);
//				break;
//			} catch(Exception $e) {
//				if (preg_match("/Lost connection to MySQL/i", $e->getMessage())) {
//					$this->connect(0);
//				} else {
//					throw new DbException($query, $this->_cons[0]->error, $this->_cons[0]->errno);
//				}
//			}
//			$i++;
//		}
		$this->_query($query);
		$this->_updateLines = $this->_cons[0]->affected_rows;
		$this->_logger->query("affected --> ".$this->_updateLines." rows");
		if ($this->_updateLines == -1) throw new DbException($query, $this->_cons[0]->error, $this->_cons[0]->errno);
		return true;
	}
	public function executeToFile($filename)
	{
		$query = file_get_contents($filename);
		$qs = preg_split("/;\r?\n/", $query);
		foreach ($qs as $q) {
			$q = ltrim($q);
			if ($q!='') $this->_query($q);
		}
		return true;
	}
	public function getUpdateLines() {
		return $this->_updateLines;
	}
	public function getInt($query) {
		$result = $this->_query($query);
		$row = $result->fetch_row();
		$result->free();
		if ($row === null) return 0;
		return (int)$row[0];
	}
	public function getString($query) {
		$result = $this->_query($query);
		$row = $result->fetch_row();
		$result->free();
		if ($row === null) return null;
		return (string)$row[0];
	}
	public function getKeyValue($query) {
		$result = $this->_query($query);
		$ary = array();
		while ($row = $result->fetch_row()) {
			$ary[$row[0]] = $row[1];
		}
		$result->free();
		return $ary;
	}
	public function quote($str) {
		if ($str === null || $str === '') return $str;
		// \x1a
		return str_replace(array("\0", "\n", "\r", "'", "\""), array("\\\0", "\\\n", "\\\r", "\\'", "\\\""), $str);
		//return str_replace(array("\\", "'", "\0"), array("\\\\", "\\'", "\\\0"), $str);
		//return $this->_cons[0]->real_escape_string($str);
	}
	public function getClientCharset() {
		$charset = $this->_cons[0]->character_set_name();
		switch ($charset) {
		case 'utf8': return 'UTF-8';
		case 'ujis': return 'EUC-JP';
		case 'sjis': return 'SJIS';
		}
		return $charset;
	}
	public function getLastQuery() {
		return $this->_lastQuery;
	}
	public function autoCommit($isauto=true) {
		$commit = ($isauto) ? 1 : 0;
		return $this->_query('SET AUTOCOMMIT='.$commit);
	}
	public function beginTransaction() {
		return $this->_query('BEGIN');
	}
	public function commit() {
		return $this->_query('COMMIT');
	}
	public function rollback() {
		return $this->_query('ROLLBACK');
	}
	public function renameTable($oldname,$newname) {
		return $this->_query("ALTER TABLE ${oldname} RENAME ${newname}");
	}
	public function addColumn($table,$colname,$dtype) {
		return $this->_query("ALTER TABLE ${table} ADD ${colname} ${dtype}");
	}
	public function delColumn($table,$colname) {
		return $this->_query("ALTER TABLE ${table} DROP ${colname}");
	}
	/**
	 * SHOW TABLES LIKE 'a%';
	 * +---------------------------+
	 * | Tables_in_catalog (a%)    |
	 * +---------------------------+
	 * | address_book              |
	 * | address_book_to_customers |
	 * | address_format            |
	 * +---------------------------+
	 * @param $like
	 * @return unknown_type
	 */
	public function getTables($like=null) {
		$sql = "SHOW TABLES";
		if ($like!='') $sql .= " LIKE '".$like."'";
		return $this->executeQuery($sql);
	}
	/**
	 * SHOW COLUMNS FROM address_book LIKE 'a%';
	 * +-----------------+------------------+-----------+-----+---------+----------------+
	 * | Field           | Type             | Null      | Key | Default | Extra          |
	 * +-----------------+------------------+-----------+-----+---------+----------------+
	 * | address_book_id | int(11) unsigned | YES or NO | PRI | 1       | auto_increment |
	 * +-----------------+------------------+-----------+-----+---------+----------------+
	 * @param $table
	 * @param $like
	 * @return unknown_type
	 */
	public function getColumns($table,$like=null) {
		$sql = "SHOW COLUMNS FROM ${table}";
		if ($like!='') $sql .= " LIKE '".$like."'";
		return $this->executeQuery($sql);
	}
	private function _query($query) {
		if (substr($query, -1) === ';') {
			$query = substr($query, 0, -1);
		}
		$this->_lastQuery = $query;
		$this->_updateLines = 0;
		$this->_logger->query($this->_lastQuery);
		$n = 0;
		if (substr($query, 0, 7) == 'SELECT ') {
//		if (preg_match("/^SELECT /i", $query)) {
			$n = $this->_getSlaveNum(1);
		}
		$this->connect($n);
		$result = $this->_cons[$n]->query($query);
		if ($result === false) {
			throw new DbException($this->_lastQuery, $this->_cons[$n]->error, $this->_cons[$n]->errno);
		}
		$this->_logger->queryResult();
		return $result;
	}
	private function _getSlaveNum($n)
	{
		if ($this->_slaveNum > -1) return $this->_slaveNum;
		if ($n>0) {
			$cnt = count($this->_servers);
			if ($cnt==1) $n=0;
			else if ($cnt==2) $n=1;
			else $n=mt_rand(1, $cnt-1);
			$this->_slaveNum = $n;
		}
		return $n;
	}
}
?>