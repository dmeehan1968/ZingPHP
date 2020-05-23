<?php


class TDateTime {

	private $_days = 0;
	private $_secs = 0;
	private $_dirty = true;
	private $_datetime;

	static private $_daysPerMonth = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	static private $_daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	static private $_monthsOfYear = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	public function __construct($datetime = null) {
		if (is_null($datetime)) {
			$datetime = time();
		}

		if (is_int($datetime)) {
			if ($datetime == -1) {
				$datetime = '9999-12-31 23:59:59';
			} else {
				$datetime = gmdate('Y-m-d H:i:s', $datetime);
			}
		}

		if (is_array($datetime)) {
			$this->days = $datetime[0];
			$this->secs = $datetime[1];
		}

		if (is_string($datetime)) {
			list($date, $time) = explode(' ', $datetime);
			list($year, $month, $day) = explode('-', $date);
			list($hours, $mins, $secs) = explode(':', $time);
			$this->secs = ($hours * 3600) + ($mins * 60) + $secs;
			$this->days = ($year * 365) + $this->_getDayOfYear($month, $day);
			$this->days += $this->_getLeapDays($year, $month, $day);
		}

		$this->refresh();
	}

	public function __set($name, $value) {
		$name = '_' . $name;
		if (isset($this->$name)) {
			if ($this->$name != $value) {
				$this->_dirty = true;
				$this->$name = $value;
			}
		} else {
			throw new Exception('Property \'' . $name . '\' unknown in class ' . get_class($this));
		}
	}

	public function __get($name) {
		$name = '_' . $name;
		if (isset($this->$name)) {
			return $this->$name;
		}
		return null;
	}

	public $year;
	public $month;
	public $day;
	public $hour;
	public $minute;
	public $second;
	public $dayOfWeek;
	public $leap;

	public function refresh() {
		if ($this->_dirty) {
			$this->_datetime = sprintf('%04d-%02d-%02d %02d:%02d:%02d',
									   $this->_getYear(), $this->_getMonth(), $this->_getDay(),
									   $this->_getHour(), $this->_getMins(), $this->_getSecs());
			$this->year = $this->_getYear();
			$this->month = $this->_getMonth();
			$this->day = $this->_getDay();
			$this->hour = $this->_getHour();
			$this->minute = $this->_getMins();
			$this->second = $this->_getSecs();
			$this->dayOfWeek = $this->_getDayOfWeek();
			$this->leap = $this->_isLeapYear($this->year);
			$this->_dirty = false;
		}
	}

	private function _getYear() {
		return (int) ($this->days / 365.2425);
	}

	private function _getMonth() {
		$leaps = array_fill(0, 13, 0);
		$days = $this->days - $this->_getLeapDays((int) ($this->days / 365.2425), 1, 1);
		if ($this->_isLeapYear()) {
			$leaps[2] = 1;
		}
		$days %= 365;
		if ($days == 0) $days = 365;
		$month = 1;
		while ($days > (self::$_daysPerMonth[$month] + $leaps[$month])) {
//zing::debug('days:' . $days . ' month:' . $month . ' dpm:' . (self::$_daysPerMonth[$month] + $leaps[$month]));
			$days -= self::$_daysPerMonth[$month] + $leaps[$month];
			$month++;
		}
		return $month;
	}

	private function _getDay() {
		$leaps = array_fill(0, 13, 0);
		$days = $this->days - $this->_getLeapDays((int) ($this->days / 365.2425), 1, 1);
		if ($this->_isLeapYear()) {
			$leaps[2] = 1;
		}
		$days %= 365;
		if ($days == 0) $days = 365;
		$month = 1;
		while ($days > (self::$_daysPerMonth[$month] + $leaps[$month])) {
//zing::debug('days:' . $days . ' month:' . $month . ' dpm:' . (self::$_daysPerMonth[$month] + $leaps[$month]));
			$days -= self::$_daysPerMonth[$month] + $leaps[$month];
			$month++;
		}
		return $days;
	}

	private function _getHour() {
		return (int) ($this->secs / (3600));
	}

	private function _getMins() {
		return (int) (($this->secs % 3600) / 60);
	}

	private function _getSecs() {
		return $this->secs % 60;
	}

	private function _getDayOfWeek() {
		return ($this->days - 1) % 7;
	}

	private function _getDayOfYear($month, $day) {
		$days = 0;
//zing::debug('month: ' . $month . ' day: ' . $day);
		while (--$month > 0) {
//zing::debug('days: ' . self::$_daysPerMonth[$month] . ' month: ' . $month);
			$days += self::$_daysPerMonth[$month];
		}

		$days += $day;
		return $days;
	}

	private function _isLeapYear($year = -1) {
		if ($year == -1) {
			$year = $this->_getYear();
		}
		$leap = false;
		if ($year == 0) {
			$leap = false;
		} else if (($year % 400) == 0) {
			$leap = true;
		} else if (($year % 100) == 0) {
			$leap = false;
		} else if (($year % 4) == 0) {
			$leap = true;
		}
		return $leap;
	}

	private function _isLeapMonth() {
		if ($this->_isLeapYear() && $this->_getMonth() == 2) {
			return true;
		}
		return false;
	}

	private function _getLeapDays($year, $month = 1, $day = 1) {
		$leaps = (int) ($year / 4);
		$leaps -= (int) ($year / 100);
		$leaps += (int) ($year / 400);
		$leap = false;

		if ($this->_isLeapYear($year) && $month <= 2 && $leaps) {
			$leaps--;
		}
		return $leaps;
	}

	public function adjust($year = 0, $month = 0, $day = 0, $hour = 0, $min = 0, $sec = 0) {
		if ($year != 0) {
			$curYear = $this->_getYear();
			$leapDays = $this->_getLeapDays($curYear + $year, $this->_getMonth(), $this->_getDay()) - $this->_getLeapDays($curYear, $this->_getMonth(), $this->_getDay());
			$this->days += $year * 365 + $leapDays;
		}

		if ($month != 0) {
			$curMonth = $this->_getMonth();
			$dir = $month < 0 ? -1 : 1;
			while ($month != 0) {
				$leap = $this->_isLeapMonth() ? 1 : 0;
				if ($dir < 0) {
					if (--$curMonth < 1) $curMonth = 12;
					$this->days -= self::$_daysPerMonth[$curMonth] + $leap;
				} else {
					$this->days += self::$_daysPerMonth[$curMonth++] + $leap;
					if ($curMonth > 12) $curMonth = 1;
				}
				$month -= $dir;
			}
		}

		if ($day != 0) {
			$this->days += $day;
		}

		$this->secs += ($hour * 3600) + ($min * 60) + $sec;
		if ($this->secs < 0 || $this->secs > 86399) {
			$this->days += (int)($this->secs / 86400);
			$this->secs = $this->secs % 86400;
			if ($this->secs < 0) {
				$this->days--;
				$this->secs += 86400;
			}
		}

		$this->refresh();
	}

	public function lessThan(TDateTime $when) {
		if ($this->days < $when->days) {
			return true;
		}

		if ($this->days > $when->days) {
			return false;
		}

		if ($this->secs < $when->secs) {
			return true;
		}
		return false;
	}

	public function __toString() {
		return $this->toString();
	}

	public function toString($format = 'Q') {
		$output = '';
		for ($i = 0 ; $i < strlen($format) ; $i++) {
			$char=substr($format, $i, 1);
			switch ($char) {
			case '\\':
				$output .= substr($format, ++$i, 1);
				break;

			case 'Q':
				$output .= sprintf('%04d-%02d-%02d %02d:%02d:%02d',
								$this->year, $this->month, $this->day,
								$this->hour, $this->minute, $this->second);
				break;
			case 'd':
				$output .= sprintf('%02d', $this->day);
				break;
			case 'D':
				$output .= substr(self::$_daysOfWeek[$this->dayOfWeek], 0, 3);
				break;
			case 'l':
				$output .= self::$_daysOfWeek[$this->dayOfWeek];
				break;
			case 'N':
				$output .= $this->dayOfWeek ? $this->dayOfWeek : 7;
				break;
			case 'w':
				$output .= $this->dayOfWeek;
				break;
			case 'j':
				$output .= $this->day;
				break;
			case 'S':
				$tens = ((int)($this->day / 10)) % 10;
				if ($tens == 1) {
					$output .= 'th';
				} else {
					$units = $this->day % 10;
					$ord = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
					$output .= $ord[$units];
				}
				break;
			case 'm':
				$output .= sprintf('%02d', $this->month);
				break;
			case 'M':
				$output .= substr(self::$_monthsOfYear[$this->month - 1], 0, 3);
				break;
			case 'F':
				$output .= self::$_monthsOfYear[$this->month - 1];
				break;
			case 'n':
				$output .= $this->month;
				break;
			case 't':
				$output .= self::$_daysPerMonth[$this->month];
				break;
			case 'Y':
				$output .= $this->year;
				break;
			case 'y':
				$output .= sprintf('%02d', $this->year % 1000);
				break;
			case 'a':
				$output .= $this->hour < 12 ? 'am' : 'pm';
				break;
			case 'A':
				$output .= $this->hour < 12 ? 'AM' : 'PM';
				break;
			case 'g':
				$output .= $this->hour % 12;
				break;
			case 'G':
				$output .= $this->hour;
				break;
			case 'h':
				$output .= sprintf('%02d', $this->hour % 12);
				break;
			case 'H':
				$output .= sprintf('%02d', $this->hour);
				break;
			case 'i':
				$output .= sprintf('%02d', $this->minute);
				break;
			case 's':
				$output .= sprintf('%02d', $this->second);
				break;
			case 'c':
				$format = substr($format, 0, $i) . 'Y-m-d\\TH:i:s+00:00' . substr($format, $i+1);
				$i--;
				break;
			case 'r':
				$format = substr($format, 0, $i) . 'D, d M Y H:i:s +0000' . substr($format, $i+1);
				$i--;
				break;
			default:
				$output .= $char;
				break;
			}
		}
		return $output;
	}
}


?>
