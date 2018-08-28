<?php
if (class_exists("mysqli") === false)
{
/**
 * mysqliクラスが使えない場合
 */
class mysqli
{
	static public $s_errno = 0;
	static public $s_error = null;
	public $errno = 0;
	public $error = null;
	public $affected_rows = 0;
	private $con = null;
	private $host = null;
	private $username = null;
	private $passwd = null;
	private $dbname = null;
	private $port = null;

	function __construct($host=null, $username=null, $passwd=null, $dbname=null, $port=null)
	{
		$this->host = ($host === null) ? ini_get("mysqli.default_host") : $host;
		$this->username = ($username === null) ? ini_get("mysqli.default_user") : $username;
		$this->passwd = ($passwd === null) ? ini_get("mysqli.default_pw") : $passwd;
		$this->dbname = ($dbname === null) ? '' : $dbname;
		$this->port = ($port === null) ? ini_get("mysqli.default_port") : $port;

		$this->con = @mysql_connect($this->host, $this->username, $this->passwd);
		if (is_resource($this->con) === false)
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			mysqli::$s_errno = $this->errno;
			mysqli::$s_error = $this->error;
		}
		else if (mysql_select_db($this->dbname, $this->con) === false)
		{
			$this->errno = mysql_errno($this->con);
			$this->error = mysql_error($this->con);
			mysqli::$s_errno = $this->errno;
			mysqli::$s_error = $this->error;
		}
	}

	function set_charset($charset)
	{
		if (is_resource($this->con) && function_exists('mysql_set_charset'))
		{
			@mysql_set_charset($charset, $this->con);
		}
		return true;
	}

	function character_set_name()
	{
		if (is_resource($this->con))
		{
			@mysql_client_encoding($this->con);
		}
		return true;
	}

	function close()
	{
		if (is_resource($this->con))
		{
			@mysql_close($this->con);
			$this->con = null;
		}
		return true;
	}

	function query($query)
	{
		$this->affected_rows = 0;
		$result = @mysql_query($query, $this->con);
		if ($result === false)
		{
			$this->errno = mysql_errno($this->con);
			$this->error = mysql_error($this->con);
			mysqli::$s_errno = $this->errno;
			mysqli::$s_error = $this->error;
		}
		else if (preg_match("/^(INSERT|UPDATE|REPLACE|DELETE) /i", $query))
		{
			$this->affected_rows = mysql_affected_rows($this->con);
		}
		return new mysqli_result($result);
	}

	function ping()
	{
		return is_resource($this->con);
	}
}
class mysqli_result
{
	private $result = null;

	function __construct($result)
	{
		$this->result = $result;
	}

	function fetch_row()
	{
		return mysql_fetch_row($this->result);
	}

	function fetch_assoc()
	{
		return mysql_fetch_assoc($this->result);
	}

	function free()
	{
		@mysql_free_result($this->result);
		$this->result = null;
		return true;
	}
}
function mysqli_connect_errno()
{
	return mysqli::$s_errno;
}
function mysqli_connect_error()
{
	return mysqli::$s_error;
}

}
?>