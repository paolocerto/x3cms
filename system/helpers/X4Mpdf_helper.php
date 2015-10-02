<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
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
	 * @return boolean
	 */
	public static function pdf_export($title, $css, $html, $page_format = 'A4', $orientation = 'P')
	{
		// language set
		$l = array();
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'rtl';
		$l['a_meta_language'] = X4Route_core::$lang;
		$l['w_page'] = _PAGE;
		
		X4Core_core::auto_load('mpdf_library');
		
		// create the PDF object 
		$mpdf = new mPDF(X4Route_core::$lang, $page_format, 0, 0, 0, 0, 0, 0, $orientation);
		
		$title = SERVICE.' - '.$title.' - '.date('Y-m-d H:i:s');
		
		$mpdf->SetAuthor($_SESSION['nickname']);
		$mpdf->SetCreator(SERVICE);
		$mpdf->SetTitle($title);
		
		$mpdf->SetDisplayMode('fullwidth');
		
		$mpdf->WriteHTML($css,1);
		$mpdf->WriteHTML($html,2);
		
		$filename = X4Utils_helper::unspace(str_replace(' - ', '-', $title), true);
		
		$mpdf->Output($filename.'.pdf', 'D');
		exit;
	}
}


