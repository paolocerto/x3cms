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
 * Helper for CSV export
 *
 * @package X4WEBAPP
 */
class X4CSV_helper
{
	/**
	 * Export CSV file
	 */
	public static function export(string $file_name, array $head, array $data) : void
	{
	    // file name for download
	    $file_name = X4Utils_helper::slugify($file_name).'-'.date('Ymd').'.csv';

	    header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=".$file_name);
        header("Pragma: no-cache");
        header("Expires: 0");

        $df = fopen("php://output", 'w');

	    fputcsv($df, $head, ';');
        foreach ($data as $i)
        {
            fputcsv($df, array_values($i), ';');
        }
        fclose($df);
	}
}
