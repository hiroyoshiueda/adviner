<?php
/**
 * Enter description here...
 *
 */
class UtilDate {

	/**
	 * Enter description here...
	 *
	 * @var int
	 */
	private $timestamp = 0;

	/**
	 * Enter description here...
	 *
	 * @param String $dateStr
	 * @return UtilDate
	 */
	function UtilDate($dateStr=null) {
		if ($dateStr === null) {
			$this->timestamp = time();
		} else {
			$this->timestamp = self::getTimestamp($dateStr);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param int $day
	 */
	public function addDay($day=1) {
		//$this->timestamp += (86400 * $day);
		$date = getdate($this->timestamp);
		$this->timestamp = mktime(0, 0, 0, $date["mon"], $date["mday"]+$day, $date["year"]);
	}

	/**
	 * Enter description here...
	 *
	 * @param int $month
	 */
	public function addMonth($month=1) {
		$date = getdate($this->timestamp);
		$this->timestamp = mktime(0, 0, 0, $date["mon"]+$month, $date["mday"], $date["year"]);
	}

	/**
	 * Enter description here...
	 *
	 * @param int $year
	 */
	public function addYear($year=1) {
		$date = getdate($this->timestamp);
		$this->timestamp = mktime(0, 0, 0, $date["mon"], $date["mday"], $date["year"]+$year);
	}

	/**
	 * Enter description here...
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 */
	public function addYmd($year, $month, $day) {
		$date = getdate($this->timestamp);
		$this->timestamp = mktime(0, 0, 0, $date["mon"]+$month, $date["mday"]+$day, $date["year"]+$year);
	}

	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	public function getDayOfWeek() {
		$date = getdate($this->timestamp);
		return intval($date["wday"]) + 1;
	}

	/**
	 * Enter description here...
	 *
	 * @return String
	 */
	public function getDayOfWeekJp() {
		$days = array ("日", "月", "火", "水", "木", "金", "土");
		$day = $this->getDayOfWeek();
		return $days[$day];
	}

	/**
	 * Enter description here...
	 *
	 * @param String $format
	 * @return String
	 */
	public function toString($format="Y-m-d") {
		return self::format($this->timestamp, $format);
	}

	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	public function toTimestamp() {
		return $this->timestamp;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $dateStr
	 * @return int
	 */
	public static function getTimestamp($dateStr) {
		$time = strtotime($dateStr);
		if ($time == -1 || $time === false) {
			// strtotime() was not able to parse $string, use "now":
			$time = time();
		}
		return $time;
	}

	/**
	 * Enter description here...
	 *
	 * @param int $time
	 * @param String $format
	 * @return String
	 */
	public static function format($time, $format="Y-m-d") {
		return date($format, $time);
	}

	/**
	 * Enter description here...
	 *
	 * @param String $dateStr
	 * @param int $offset
	 * @return String
	 */
	public static function getDateAddDay($dateStr, $offset=1) {
		$date = new UtilDate($dateStr);
		$date->addDay($offset);
		return $date->toString();
	}

	/**
	 * Enter description here...
	 *
	 * @param String $dateStr
	 * @param int $offset
	 * @return String
	 */
	public static function getDateAddMonth($dateStr, $offset=1) {
		$date = new UtilDate($dateStr);
		$date->addMonth($offset);
		return $date->toString();
	}

	/**
	 * Enter description here...
	 *
	 * @param UtilDate $dateStr1
	 * @param UtilDate $dateStr2
	 * @return int
	 */
	public static function compare($dateStr1, $dateStr2) {
		if (is_object($dateStr1)) {
			$time1 = $dateStr1->toTimestamp();
		} else {
			$time1 = self::getTimestamp($dateStr1);
		}
		if (is_object($dateStr2)) {
			$time2 = $dateStr2->toTimestamp();
		} else {
			$time2 = self::getTimestamp($dateStr2);
		}
		// 負:> 0:= 正:<
		return ($time1 - $time2);
	}
}
?>