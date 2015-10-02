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
 * Helper for TcPDF
 * 
 * @package X4WEBAPP
 */
class X4Pdf_helper 
{
	/**
	 * Create a pdf
	 *
	 * @static
	 * @param	string	$author	Author
	 * @param	string	$logo	Image to use as logo
	 * @param	array	$data	Array of contents
	 * @param	string	$template	Template name
	 * @param	string	$format	Can be portrait|landscape
	 * @return boolean
	 */
	public static function pdfize($author, $logo, $data, $template, $format = 'portrait')
	{
		// get template
		$tpl = file_get_contents(APATH.'files/filemanager/template/pdf_'.$template.'_template.htm');
			
		// language set
		$l = array();
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'rtl';
		$l['a_meta_language'] = 'it';
		$l['w_page'] = _PAGE;
		
		X4Core_core::auto_load('tcpdf_library');
		
		$orientation = ($format == 'portrait')
			? 'P'
			: 'L';
		
		$pdf = new X4T_PDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$title = $author.' - '.$data['XTITLEX'].' - '.date('Y-m-d H:i:s');
		$data['XTITLEX'] = $title;
		
		// check if the image exists
		$img = (file_exists(APATH.'files/filemanager/img/'.$logo))
			? APATH.'files/filemanager/img/'.$logo
			: PATH.'files/fnull.gif'; 
		
		// set default header data
		$pdf->set_head($img, $title, $format);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($author);
		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		$pdf->SetKeywords($author);
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		//initialize document
		//$pdf->AliasNbPages();
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		$pdf->AddPage();
		
	//	$pdf->setBarcode($title); 
		
		$pdf->SetFont('helvetica', '', 9, '', true);
		
		$src = array_keys($data);
		$rpl = array_values($data);
		$txt = str_replace($src, $rpl, $tpl);
		
		$pdf->SetY(5);
		
		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		$pdf->writeHTML($txt, true, false, true, false, '');
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		//$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $out, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		// reset pointer to the last page
		$pdf->lastPage();
		
		//Change To Avoid the PDF Error
		ob_end_clean();
		
		// Close and output PDF document
		$pdf->Output(X4Utils_helper::unspace($title).'.pdf', 'I');
	}
	
	/**
	 * Export a pdf
	 *
	 * @static
	 * @param	string	$background	Image to use as background
	 * @param	string	$title		Document title
	 * @param	string	$html		HTML Contents
	 * @param	string	$page_format 	Default A4
	 * @param	string	$orientation	Can be P|L
	 * @return boolean
	 */
	public static function pdf_export($background, $title, $html, $page_format = 'A4', $orientation = 'P')
	{
		// language set
		$l = array();
		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'rtl';
		$l['a_meta_language'] = X4Route_code::$lang;
		$l['w_page'] = _PAGE;
		
		X4Core_core::auto_load('tcpdf_library');
		
		// create the PDF object 
		$pdf = new X4T_PDF($orientation, PDF_UNIT, $page_format, true, 'UTF-8', false);
		
		$title = X4Utils::unspace(SERVICE.'_'.$title, true).'_'.date('Y-m-d_H:i:s');
		
		// check if the bg image exists
		if (file_exists(APATH.'files/filemanager/img/'.$background))
		{
			$img = APATH.'files/filemanager/img/'.$background;
			
			// set the background
			$pdf->set_background($img, $orientation, $page_format);
		}
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(SERVICE);
		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		$pdf->SetKeywords(SERVICE);
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins to zero we will use absolute position
		$pdf->SetMargins(0, 0, 0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(false);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		//initialize document
		//$pdf->AliasNbPages();
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		$pdf->AddPage();
		
	//	$pdf->setBarcode($title); 
		
		$pdf->SetFont('helvetica', '', 9, '', true);
		
		$src = array_keys($data);
		$rpl = array_values($data);
		$txt = str_replace($src, $rpl, $tpl);
		
		$pdf->SetY(5);
		
		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		$pdf->writeHTML($txt, true, false, true, false, '');
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		//$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $out, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		// reset pointer to the last page
		$pdf->lastPage();
		
		//Change To Avoid the PDF Error
		ob_end_clean();
		
		// Close and output PDF document
		$pdf->Output(X4Utils_helper::unspace($title).'.pdf', 'I');
	}
}


