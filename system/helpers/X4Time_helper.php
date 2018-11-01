<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 *
	 * @static
	 * @param boolean	$long 
	 * @param boolean	$keys 
	 * @return array
	 */
	public static function months_array($long = true, $keys = false)
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
	 *
	 * @static
	 * @param boolean	$long 
	 * @return array
	 */
	public static function week_array($long = true)
	{
		if ($long)
		{
			return array('', _MONDAY, _TUESDAY, _WEDNESDAY, _THURSDAY, _FRIDAY, _SATURDAY, _SUNDAY);
		}
		else
		{
			return array('', _MON, _TUE, _WED, _THU, _FRI, _SAT, _SUN);
		}
	}
	 
	
	/**
	 * return a time without seconds
	 *
	 * @static
	 * @param string	time string 
	 * @return string	time without seconds
	 */
	public static function no_seconds($time)
	{
		$t = explode(':', $time);
		array_pop($t);
		return implode(':', $t);
	}
	
	/**
	 * format a datetime
	 *
	 * @static
	 * @param string	datetime 
	 * @param string	format
	 * @return string	modified datetime
	 */
	public static function format($datetime, $format)
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
	 * Parse a date
	 *
	 * @static
	 * @param string	date
	 * @param string	format
	 * @return string	parsed datetime
	 */
	public static function parse_date($date, $old_format = '')
	{
		if (function_exists('date_parse_from_format'))
		{
			$d = date_parse_from_format($old_format, $date);
		}
		else
		{
			$d = date_parse($date);
		}
		return $d;
	}
	
	
	/**
	 * fix an incomplete date
	 *
	 * @static
	 * @param string	datetime 
	 * @return string
	 */
	public static function fix_date($date, $old_format)
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
		if (strlen($old_format) == 10)
		{
			$nd = str_replace(array('Y', 'm', 'd'), array($y, $m, $d), $old_format);
		}
		else
		{
			$nd = str_replace(array('Y', 'm', 'd', 'H', 'i', 's'), array($y, $m, $d, $h, $i, $s), $old_format);
		}
		return $nd;
	}
	
	/**
	 * Sum a time over a time
	 *
	 * @static
	 * @param string	time
	 * @param string	time
	 * @return string	time
	 */
	public static function sum_time($time1, $time2, $mod = 0)
	{
		$t1 = array_reverse(explode(':', $time1));
		$t2 = array_reverse(explode(':', $time2));
		$t3 = array();
		$r = 0;
		for($i = 0; $i < 2;$i++) 
		{
			$t = $t1[$i] + $t2[$i];
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
	 *
	 * @static
	 * @param string	datetime
	 * @param string	time string 
	 * @return string	datetime
	 */
	public static function date_plus_time($date, $time, $datetime = true)
	{
		$t = explode(':', $time);
		if (!isset($t[2])) 
			$t[2] = 0;
		
		$new_datetime = strtotime($date.' + '.$t[0].' hours + '.$t[1].' minutes + '.$t[2].' seconds');
		if ($datetime)
			return date('Y-m-d H:i:s', $new_datetime);
		else
			return date('Y-m-d', $new_datetime);
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
	public static function datetime_diff($start, $end = 0, $out = 'time', $absolute = true)
	{
		$sdate = new DateTime($start);
        $edate = ($end != 0) ? new DateTime($end) : new DateTime();
        $time = $sdate->diff($edate, $absolute);
        
        switch($out) 
        {
        case 'time':
			// hours - minutes - seconds
			$h = $time->format('%H');
			$m = $time->format('%i');
			$s = $time->format('%s');
			return $h.':'.$m.':'.$s;
			break;
		case 'seconds':
			// only seconds
			$h = $time->h;
			$m = $time->i;
			$s = $time->s;
			
			return $h*3600+$m*60+$s;
			break;
		case 'days':
			// number of days
			if ($absolute)
			{
				return $time->days;
			}
			else
			{
				return $time->format('%R%a');
			}
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
	 *
	 * @static
	 * @param integer	$minutes
	 * @return string	time (00:00:00)
	 */
	public static function minute2time($minutes)
	{
		if ($minutes > 0)
		{
		    $h = floor($minutes/60);
		    $m = $minutes % 60;
		    return str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT).':00';
		}
		else
		{
		    return '00:00:00';
		}
	}
	
	/**
	 * Get elapsed time from 2 times
	 *
	 * @static
	 * @param string	at available time
	 * @param string	et end time 
	 * @return string	time or days + time
	 */
	public static function elapsed_time($at, $et)
	{
		$available_time = self::time2sec($at);
		$end_time = self::time2sec($et);
		$diff = $available_time - $end_time;
		
		$days = round($diff / 86400);
		if ($days > 0)
		{
			// with days
			return $days._TRAIT_.gmdate('H:i:s', $diff%86400);
		}
		else
		{
			// H:m:s
			return gmdate('H:i:s', $diff);
		}
	}
	
	/**
	 * Get the percentage of time that elapsed from the time estimated
	 *
	 * @static
	 * @param string	estimated time (hh:mm:ss)
	 * @param string	elapsed time (hh:mm:ss)
	 * @return string	formatted number
	 */
	public static function progress($estimated, $elapsed)
	{
		$tot = self::time2sec($estimated);
		$progress = self::time2sec($elapsed);
		return number_format(($progress*100/$tot), 3, ',','');
	}
	
	/**
	 * Convert time (hh:mm:ss) to seconds
	 *
	 * @static
	 * @param string	time (hh:mm:ss)
	 * @return integer	seconds
	 */
	public static function time2sec($time)
	{
		$t = array_reverse(explode(':', $time));
		
		$s = $t[0];
		if (isset($t[1]))
		{
			$s += $t[1]*60;
		}
		
		if (isset($t[2]))
		{
			$s += $t[2]*3600;
		}
		
		return $s;
	}
	
	/**
	 * Convert time (hh:mm:ss) to money
	 *
	 * @static
	 * @param string	time (hh:mm:ss)
	 * @param float		hourly fee
	 * @return string	formatted number
	 */
	public static function time2money($time, $fee)
	{
		$t = explode(':', $time);
		return number_format(($t[0]*$fee+$t[1]*$fee/60), 2, '.','');
	}
	
	/**
	 * Get age by birthday date
	 *
	 * @static
	 * @param	string	$date date in Y-m-d format
	 * @return	integer	Age in years
	 */
	public static function age($date)
	{
		list($Y,$m,$d)    = explode('-',$date);
		return (date('md') < $m.$d)
			? date('Y') - $Y - 1 
			: date('Y') - $Y;
	}
	
	/**
	 * Get months between dates
	 *
	 * @static
	 * @param string	start date
	 * @param string	end date 
	 * @return	array
	 */
	public static function get_months($start, $end)
	{
		$m = array();
		
		$start = new DateTime($start);
		$end = new DateTime($end);
		
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
	
}
