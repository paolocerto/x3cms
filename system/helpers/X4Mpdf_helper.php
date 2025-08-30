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
	 */
	public static function pdf_export(
        string $title,
        string $css,
        string $html,
        array $config = [
            'page_format' => 'A4',
            'orientation' => 'P',       // orientation can be [P|L], output can be [D|I|F]
            'output' => 'D'
        ],
        bool $fixed_footer = false,
        string $background = ''
    ) : mixed
	{
	    require_once PATH . 'vendor/autoload.php';

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
            'format' => $config['page_format'].'-'.$config['orientation'],
	    ]);

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

        if ($fixed_footer)
        {
            $mpdf->SetHTMLFooterByName('footer', 'E', true);
            $mpdf->SetHTMLFooterByName('footer', 'O', true);
        }
        elseif (isset($config['footer']))
        {
            $mpdf->SetHTMLFooter($config['footer']);
            $mpdf->SetHTMLFooter($config['footer'], 'E');
        }

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

		$filename = X4Utils_helper::slugify(str_replace(' - ', '-', $title), true).'.pdf';

		$path = ($config['output'] == 'F')
			? X4Files_helper::$secret_path.'pdf/'
			: '';

		$mpdf->Output($path.$filename, $config['output']);
		if ($config['output'] != 'F')
		{
			exit;
		}
        else
        {
            chmod($path.$filename, 0777);
            return array('file' => $path.$filename, 'filename' => $filename);
        }
	}

}
