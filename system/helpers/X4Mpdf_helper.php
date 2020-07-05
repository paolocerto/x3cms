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
 * Helper for mPDF
 * 
 * @package X4WEBAPP
 */
class X4Mpdf_helper 
{
	/**
	 * Export a pdf
	 *
	 * @static
	 * @param	string	$title		Document title
	 * @param	string	$css		CSS Contents
	 * @param	string	$html		HTML Contents
	 * @param	string	$page_format 	Default A4
	 * @param	string	$orientation	Can be P|L
	 * @param	string	$path			Can be download|path
	 * @return mixed
	 */
	public static function pdf_export($title, $css, $html, $page_format = 'A4', $orientation = 'P', $path = '')
	{
		// language set
		$l = array();
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'rtl';
		$l['a_meta_language'] = X4Route_core::$lang;
		$l['w_page'] = _PAGE;
		
		X4Core_core::auto_load('mpdf_vendor');
		
		// create the PDF object 
		$mpdf = new \Mpdf\Mpdf([
		    'mode' => 'utf-8', 
		    'format' => $page_format, 
			'orientation' => $orientation,
			'setAutoTopMargin' => 'stretch',
			'autoMarginPadding' => 5
		]);
		
		// Set a simple Footer including the page number
		$mpdf->setFooter('{PAGENO}/{nbpg}');
		
		$title = SERVICE.' - '.$title.' - '.date('Y-m-d H:i:s');
		
		$mpdf->SetAuthor(SERVICE);
		$mpdf->SetCreator(SERVICE);
		$mpdf->SetTitle($title);
		
		$mpdf->SetDisplayMode('fullwidth');
		
		$mpdf->WriteHTML($css,1);
		$mpdf->WriteHTML($html,2);
		
		$filename = X4Utils_helper::unspace(str_replace(' - ', '-', $title), true);
		
		if (empty($path))
		{
			//download
			$mpdf->Output($filename.'.pdf', 'D');
			exit;
		}
		else
		{
			// save
		    $mpdf->Output($path.$filename.'.pdf', 'F');
		    return $path.$filename.'.pdf';
		}
	}
}

