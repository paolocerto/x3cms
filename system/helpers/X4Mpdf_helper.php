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
	 * @param	string	$output			Can be D|I|F
     * @param   string  $footer
     * @param   string  $background
	 * @return boolean
	 */
	public static function pdf_export($title, $css, $html, $page_format = 'A4', $orientation = 'P', $output = 'D', $footer = false, $background = '')
	{
	    require_once PATH . '/vendor/autoload.php';

	    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
	    $fontDirs = $defaultConfig['fontDir'];

	    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
	    $fontData = $defaultFontConfig['fontdata'];

	    $mpdf = new \Mpdf\Mpdf([
	        'fontDir' => array_merge($fontDirs, [
                PATH . '/vendor/mpdf/mpdf/ttfonts',
            ]),
            'fontdata' => $fontData + [
                'freesans' => [
                    'R' => 'FreeSans.ttf',
                    'B' => 'FreeSansBold.ttf',
                    'I' => 'FreeSansOblique.ttf',
                    'BI' => 'FreeSansBoldOblique.ttf'
                ],
                'dejavusans' => [
                    'R' => 'DejaVuSans.ttf',
                    'B' => 'DejaVuSans-Bold.ttf',
                    'I' => 'DejaVuSans-Oblique.ttf',
                    'BI' => 'DejaVuSans-BoldOblique.ttf'
                ],
                'impact' => [
                    'R' => 'impact.ttf'
                ],
                'dejavuserif' => [
                    'R' => 'DejaVuSerif.ttf',
                    'B' => 'DejaVuSerif-Bold.ttf',
                    'I' => 'DejaVuSerif-Oblique.ttf',
                    'BI' => 'DejaVuSerif-BoldOblique.ttf'
                ],
            ],
            'default_font' => 'freesans',
            'mode' => 'utf-8',
            'format' => $page_format.'-'.$orientation,
	    ]);

		//$title = $title.' - '.date('Y-m-d H:i:s');

		$mpdf->SetAuthor(SERVICE);	//$_SESSION['nickname']
		$mpdf->SetCreator(SERVICE);
		$mpdf->SetTitle($title);

		$mpdf->SetDisplayMode('fullwidth');

        $mpdf->AddPage();

        if (!empty($background))
        {
            $mpdf->SetDefaultBodyCSS('background', "url('".$background."')");
            $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
        }

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        if ($footer)
        {
            $mpdf->SetHTMLFooterByName('footer', 'E', true);
            $mpdf->SetHTMLFooterByName('footer', 'O', true);
        }

		$filename = X4Utils_helper::slugify(str_replace(' - ', '-', $title), true).'.pdf';

		$path = ($output == 'F')
			? PPATH.'tmp/'
			: '';

		$mpdf->Output($path.$filename, $output);
		if ($output != 'F')
		{
			exit;
		}
        else
        {
            return array('file' => $path.$filename, 'filename' => $filename);
        }
	}
}
