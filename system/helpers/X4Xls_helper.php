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
 * Helper for XLS export
 *
 * @package X4WEBAPP
 */
class X4Xls_helper
{
    private static function filterData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

	/**
	 * Export XLS file
	 *
	 * @static
	 * @param string	$file_name
	 * @param array		$data
	 * @return boolean
	 */
	public static function export($file_name, $data)
	{
	    // file name for download
	    $fileName = X4Utils_helper::slugify($file_name. ' ') . date('Ymd') . '.xls';

	    header("Content-Disposition: attachment; filename=\"$fileName\"");
	    header("Content-Type: application/vnd.ms-excel");

	    $flag = false;
        foreach ($data as $row)
        {
            // cast to associative array
            $row = (array) $row;

            if(!$flag)
            {
                // display column names as first row
                echo implode("\t", array_keys($row)) . "\n";
                $flag = true;
            }
            // filter data
            array_walk($row, 'self::filterData');
            echo implode("\t", array_values($row)) . "\n";
        }
        exit;
	}
}
