<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

// include TCPDF lib
require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

class X4T_PDF extends TCPDF 
{
	/**
	 * Array for widths and heights
	 */
	protected $formats = array(
		'P' => array(
			'A4' => array(210, 297),
			'A3' => array(420, 594)
		),
		'L' => array(
			'A4' => array(297, 210),
			'A3' => array(594, 420)
		)
	);
	
	protected $img;
	protected $company;
	protected $width;
	protected $yy;
	
	/**
	 * Set the background image
	 *
	 */
	public function set_background($img, $orientation = 'P', $page_format = 'A4') 
	{
		if (!empty($img))
		{
			// get the current page break margin
			$bMargin = $this->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $this->AutoPageBreak;
			// disable auto-page-break
			$this->SetAutoPageBreak(false, 0);
			// set bacground image
			$img_file = FPATH.'img/file_manager/'.$img;
			
			$w = $this->formats[$orientation][$page_format][0];
			$h = $this->formats[$orientation][$page_format][1];
			
			$this->Image($img_file, 0, 0, $w, $h, '', '', '', false, 300, '', false, false, 0);
			// restore auto-page-break status
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$this->setPageMark();
		}
	}
	
	public function set_head($img, $author, $orientation = 'portrait') 
	{
		if (!empty($img))
		{
			$this->img = $img;
		}
		$this->company = $author;
		$this->width = ($orientation == 'portrait')
			? 210
			: 297;
	}
	
	public function put_line($y) 
	{
		$style = array("width" => 0.1, "dash" => 0, "color" => array(120,120,120));
		$this->Line(15, $y, $this->width, $y, $style);
	}
	/*
	public function put_vline() 
	{
		$style = array("width" => 0.1, "dash" => 0, "color" => array(170,170,170));
		$this->Line(85, 38, 85, $this->yy, $style);
	}
	*/
	public function put_fold() 
	{
		$style = array("width" => 0.2, "dash" => 0, "color" => array(0,0,0));
		$this->Line(0, 88, 10, 88, $style);
	}
	
	//Page header
    public function header() 
    {
        // Logo
        if (!is_null($this->img))
        {
        	$this->Image(K_PATH_IMAGES.$this->img,115,15,100,32,'','');
        }
        $this->SetFont('helvetica','',8);
        
        // Title
	    $this->writeHTMLCell(100,10,10,6,'<p>'.$this->company.'</p>', 0);
	    
		//$this->SetFont('helvetica','',8);
		$this->put_line(15);
		$this->yy = 50;
    }
	
	// Page footer
	public function footer() 
	{
		
	//	$this->put_fold();	// 
	//	$this->put_vline();
		
		$cur_y = $this->GetY() + 10;
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) 
		{
			$this->Ln();
			$this->write1DBarcode($barcode, 'C128B', 16, $cur_y, 80, 3, 0.2, '', '');
		}
		
		$this->SetY(-20);
        $this->SetFont('helvetica','',7);
        $this->Cell(0,0,_PAGE.' '.$this->PageNo().'/'.$this->getAliasNbPages(),0,0,'R');
	}
}
