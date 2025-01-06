
<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// legend view

echo '<p class="mt-6 text-xl border-b">'._LEGEND.'</p>
        <table class="mt-0 mb-6">';

foreach ($items as $k => $v)
{
    echo '<tr class="no_border">
        <td class="w-8">'.$k.'</td>
        <td>'.$v.'</td>
    </tr>';
}

echo '</table>';
