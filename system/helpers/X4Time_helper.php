<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

/**
 * Helper for time handling
 *
 * @package X4WEBAPP
 */
class X4Time_helper
{

	/**
	 * return an array with months
	 * you should already loaded the calendar or cal dictionary
	 */
	public static function months_array(bool $long = true, bool $keys = false) : array
	{
		if ($long)
		{
		    if ($keys)
		    {
		        return array(
		            'January' => _JANUARY,
		            'February' => _FEBRUARY,
		            'March' => _MARCH,
		            'April' => _APRIL,
		            'May' => _MAY,
		            'June' => _JUNE,
		            'July' => _JULY,
		            'August' => _AUGUST,
		            'September' => _SEPTEMBER,
		            'October' => _OCTOBER,
		            'November' => _NOVEMBER,
		            'December' => _DECEMBER);
		    }
		    else
		    {
		        return array('', _JANUARY, _FEBRUARY, _MARCH, _APRIL, _MAY, _JUNE, _JULY, _AUGUST, _SEPTEMBER, _OCTOBER, _NOVEMBER, _DECEMBER);
		    }
		}
		else
		{
			return array('', _JAN, _FEB, _MAR, _APR, _MAY, _JUN, _JUL, _AUG, _SEP, _OCT, _NOV, _DEC);
		}
	}

	/**
	 * return an array with week's days
	 * you should already loaded the week or week_long dictionary
	 */
	public static function week_array(bool $long = true, bool $keys = false) : array
	{
		if ($long)
		{
		    if ($keys)
		    {
		        return array(
		            'Monday' => _MONDAY,
		            'Tuesday' => _TUESDAY,
		            'Wednesday' => _WEDNESDAY,
		            'Thursday' => _THURSDAY,
		            'Friday' => _FRIDAY,
		            'Saturday' => _SATURDAY,
		            'Sunday' => _SUNDAY
		        );
		    }
		    else
		    {
		        return array('', _MONDAY, _TUESDAY, _WEDNESDAY, _THURSDAY, _FRIDAY, _SATURDAY, _SUNDAY);
		    }
		}
		else
		{
			return array('', _MON, _TUE, _WED, _THU, _FRI, _SAT, _SUN);
		}
	}

	/**
	 * return last monday or today
	 */
	public static function last_monday(
        string $output_format = 'Y-n-j',
        string $variation = ''
    ) : string
	{
		$today = date('w');
		$monday = ($today == 1)
			? date('Y-m-d')
			: date('Y-m-d', strtotime('last monday'));

		return date($output_format, strtotime($monday.' '.$variation));
	}

    /*
     * days_in_month($month, $year)
     * Returns the number of days in a given month and year, taking into account leap years.
     *
     * $month: numeric month (integers 1-12)
     * $year: numeric year (any integer)
     */
    public static function days_in_month(int $month, int $year) : int
    {
        // calculate number of days in a month
        return $month == 2
            ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29)))
            : (($month - 1) % 7 % 2 ? 30 : 31);
    }


	/**
	 * return a time without seconds
	 */
	public static function no_seconds(string $time) : string
	{
		$t = explode(':', $time);
		array_pop($t);
		return implode(':', $t);
	}

	/**
	 * format a datetime
	 */
	public static function format(string $datetime, string $format) : string
	{
		$t = strtotime($datetime);
		return date($format, $t);
	}

	/**
	 * reformat a datetime
	 *
	 * @static
	 * @param string	date
	 * @param string	format
	 * @return string	modified datetime
	 */
	public static function reformat($date, $old_format, $new_format)
	{
		$date = str_replace(array('/', '\\', '-', '.'), '-', trim($date));
		$old_format = str_replace(array('/', '\\', '-', '.'), '-', $old_format);
		$d = self::parse_date($date, $old_format);
		if ($d['error_count'])
		{
			$date = self::fix_date($date, $old_format);
			$d = self::parse_date($date, $old_format);
		}
		$t = mktime($d['hour'], $d['minute'], $d['second'], $d['month'], $d['day'], $d['year']);
		return date($new_format, $t);
    }

    /**
	 * Change date format from mysql date to italian date
	 *
	 * @static
	 * @param string	$date
     * @param string	$sep
	 * @return string	date
	 */
	public static function change_date($date, $sep = '-')
	{
		return self::reformat($date, 'Y-m-d', str_replace('-' , $sep, 'd-m-Y'));
	}

	/**
	 * Parse a date
	 *
	 * @static
	 * @param string	date
	 * @param string	format
	 * @return string	parsed datetime
	 */
	public static function parse_date($date, $old_format)
	{
		return date_parse_from_format($old_format, $date);
	}


	/**
	 * fix an incomplete date
	 */
	public static function fix_date(string $date, string $old_format) : string
	{
		$l = strlen($date);

		$y = date('y');
		$m = '01';
		$d = '01';
		$h = $i = $s ='00';

		switch($l)
		{
		case 4:
			$y = $date;
			break;

		case 7:
			list($y, $m) = explode(DATE_SEP, $date);
			break;

		case 10:
			list($y, $m, $d) = explode(DATE_SEP, $date);
			break;

		default:
			// ???
			break;
		}

        return (strlen($old_format) == 10)
            ? str_replace(array('Y', 'm', 'd'), array($y, $m, $d), $old_format)
            : str_replace(array('Y', 'm', 'd', 'H', 'i', 's'), array($y, $m, $d, $h, $i, $s), $old_format);
	}

	/**
	 * Sum a time over a time
	 */
	public static function sum_time(string $time1, string $time2, int $mod = 0) : string
	{
		$t1 = array_reverse(explode(':', $time1));
		$t2 = array_reverse(explode(':', $time2));
		$t3 = array();
		$r = 0;
		for($i = 0; $i < 2;$i++)
		{
			$t = intval($t1[$i]) + intval($t2[$i]);
			$r = intval($t/60);
			$t3[] = $t % 60;
		}
		$t3[] = ($mod)
			? ($t1[2] + $t2[2] + $r) % $mod
			: $t1[2] + $t2[2] + $r;

		return $t3[2].':'.$t3[1].':'.$t3[0];
	}

	/**
	 * Sum a time over a date
	 */
	public static function date_plus_time(string $date, string $time, bool $datetime = true) : string
	{
		$t = explode(':', $time);
		if (!isset($t[2]))
        {
			$t[2] = 0;
        }
		$new_datetime = strtotime($date.' + '.$t[0].' hours + '.$t[1].' minutes + '.$t[2].' seconds');

		return ($datetime)
			? date('Y-m-d H:i:s', $new_datetime)
            : date('Y-m-d', $new_datetime);
	}

    /**
     * Time 2 datetime
     */
    public static function time2datetime(string $time) : string
    {
        return (strlen($time == 8))
            ? date('Y-m-d').' '.$time //prefix a date
            : $time;
    }

	/**
	 * Get time between two datetime
	 *
	 * @static
	 * @param string	$start datetime
	 * @param string	$end datetime
	 * @param string	$out switcher to select output
	 * @param boolean   $absolute get an absolute value
	 * @return string	time
	 */
	public static function datetime_diff(
        string $start_datetime,
        mixed $end_datetime = 0,
        string $out = 'time',
        bool $absolute = true
    ) : string
	{
        $start_datetime = self::time2datetime($start_datetime);
        $end_datetime = self::time2datetime($end_datetime);

		$sdate = new DateTime($start_datetime);
        $edate = ($end_datetime != 0) ? new DateTime($end_datetime) : new DateTime();
        $time = $sdate->diff($edate, $absolute);

        switch ($out)
        {
        case 'years':
			return $time->format('%y');
			break;
        case 'time':
			// hours - minutes - seconds
			return $time->format('%H:%I:%S');
			break;
		case 'hours':
			// only hours
			return $time->days*24+$time->h;
			break;
		case 'seconds':
			// only seconds
            return $time->h*3600+$time->m*60+$time->s;
			break;
		case 'days':
			// number of days
			return ($absolute)
                ? $time->days
                : $time->format('%R%a');
			break;
		case 'elapsed':
			// incremental
			$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
			$d = $time->days;
			if ($d)
			{
				if ($d < 30)
				{
					// n days ago
					$msg = $dict->get_word('_TIME_DAYS_AGO', 'time');
					return $d.' '.$msg;
				}
				else
				{
					// a formatted date
					return self::format($start, DATE_FORMAT);
				}
			}
			else
			{
				$th = $time->h;
				$h = $th%24;
				if ($h)
				{
					// n hours ago
					$msg = $dict->get_word('_TIME_HOURS_AGO', 'time');
					return $h.' '.$msg;
				}
				else
				{
					$tm = $time->i;
					$m = intval($tm % 60);
					if ($m)
					{
						// n minutes ago
						$msg = $dict->get_word('_TIME_MINUTES_AGO', 'time');
						return $m.' '.$msg;
					}
					else
					{
						// n seconds ago
						$s = $time->s;
						$msg = $dict->get_word('_TIME_SECONDS_AGO', 'time');
						return $s.' '.$msg;
					}
				}
			}
			break;
		}
	}

	/**
	 * Convert minutes to time
	 */
	public static function minute2time(int $minutes) : string
	{
		if ($minutes == 0)
		{
            return '00:00:00';
        }

        $h = floor($minutes/60);
        $m = $minutes % 60;
        return str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT).':00';
	}

	/**
	 * Get elapsed time from 2 times
	 */
	public static function elapsed_time($available_time, $end_time) : string
	{
		$available_time = self::time2sec($available_time);
		$end_time = self::time2sec($end_time);
		$diff = $available_time - $end_time;

		$days = round($diff / 86400);
		return ($days > 0)
		    ? $days._TRAIT_.gmdate('H:i:s', $diff%86400)     // with days
            : gmdate('H:i:s', $diff);                        // H:m:s
	}

	/**
	 * Get the percentage of elapsed time from the total time
	 */
	public static function progress(string $total_time, string $elapsed_time) : string
	{
		$tot = self::time2sec($total_time);
		$progress = self::time2sec($elapsed_time);
		return number_format(($progress * 100 / $tot), 3, ',', '');
	}

	/**
	 * Convert time (hh:mm:ss) to seconds
	 */
	public static function time2sec(string $time) : int
	{
		$t = array_reverse(explode(':', $time));

		$s = $t[0];
		if (isset($t[1]))
		{
			$s += $t[1] * 60;
		}

		if (isset($t[2]))
		{
			$s += $t[2] * 3600;
		}
		return $s;
	}

	/**
	 * Convert time (hh:mm:ss) to minutes
	 */
	public static function time2minutes(string $time) : int
	{
		$t = array_reverse(explode(':', $time));
		$m = $t[0] > 45
			? 1
			: 0;
		if (isset($t[1]))
		{
			$m += $t[1];
		}

		if (isset($t[2]))
		{
			$m += $t[2]*60;
		}
		return $m;
	}

	/**
	 * Convert time (hh:mm:ss) to money
	 */
	public static function time2money(string $time, float $fee) : string
	{
		$t = explode(':', $time);
		return number_format(($t[0] * $fee + $t[1] * $fee / 60), 2, '.', '');
	}

	/**
	 * Get age by birthday date
	 */
	public static function age(string $birthday_date) : int
	{
		list($Y,$m,$d) = explode('-',$birthday_date);
		return (date('md') < $m.$d)
			? date('Y') - $Y - 1
			: date('Y') - $Y;
	}

	/**
	 * Get array of months between dates
	 */
	public static function get_months(string $start_date, string $end_date) : array
	{
		$m = array();
		$start = new DateTime($start_date);
		$end = new DateTime($end_date);

		$m_start = $start->format('n');
		$m_end = $end->format('n');

		if ($m_start == $m_end)
		{
			// start and end are in the same month
			$m[] = $m_start;
		}
		else
		{
			// start and end are in differents months
			$start->modify('first day of this month');
			$end->modify('first day of this month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);

			foreach ($period as $dt)
			{
				$m[] = $dt->format('n');
			}
		}
		return $m;
	}

    /**
	 * Get month dates with gap
	 */
	public static function get_month_dates_with_gap(int $gap) : array
	{
		list($year, $month, $last_day) = explode('-', date('Y-m-t', strtotime('last day of +'.$gap.' month')));

        // set start and end
        $start_date = $year.'-'.$month.'-01';
        $end_date = $year.'-'.$month.'-'.$last_day;

        return ['start' => $start_date, 'end' => $end_date];
	}

}
