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
 * Helper for media handling
 *
 * @package X4WEBAPP
 */
class X4getid3_helper
{
	/**
	 * Analize media file
	 */
	public static function analyze(string $filename) : array
	{
        $data = [];
		if (file_exists($filename))
		{
			X4Core_core::auto_load('getid3_library');

			$getID3 = new getID3;
			$data = $getID3->analyze($filename);

/*
echo("Duration: ".$file['playtime_string'].
" / Dimensions: ".$file['video']['resolution_x']." wide by ".$file['video']['resolution_y']." tall".
" / Filesize: ".$file['filesize']." bytes<br />");
*/
		}
		return $data;
	}

}
