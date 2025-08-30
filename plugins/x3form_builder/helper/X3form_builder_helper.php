<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Helper for form_builder
 *
 * @package X4WEBAPP
 */
class X3form_builder_helper
{
    /**
	 * build message without spam check
	 */
	public static function messagize_without_spam_check(string $form, array $fields, array $files = []) : string
	{
		$str = 'FORM <strong>'.$form.'</strong> '.date('Y-m-d H:i:s').BR.BR;
		foreach ($fields as $k => $v)
		{
			if ($k != strrev($form)) {
				if (is_array($v)) {
					$str .= strtoupper($k).': <ul>';
					foreach ($v as $i)
					{
						$str .= '<li><strong>'.$i.'</strong></li>';
					}
					$str .= '</ul>';
				}
				else
				{
					if ($k != 'x4token')
					{
						$str .= strtoupper($k).': <strong>'.$v.'</strong>'.BR;
					}
				}
			}
		}

        $str .= BR.'---';

        // for files
		if (!empty($files))
		{
			foreach ($files as $k => $v)
			{
				if (!empty($v))
                {
                    $str .= strtoupper($k).': <a href="'.$v.'" title="download">'.$v.'</a>'.BR;
                }
			}
		}
		$str .= BR.'---';

		$str = '<html><head><style>html, body {font-family:helvetica, arial, sans-serif;}</style></head><body>'.$str.'</body></html>';

		return iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8//ignore", $str);
	}
}
