<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// site/php info

$modules = get_loaded_extensions();
$version = phpversion();

echo '<table>
<thead>
    <tr>
        <th>PHP '.$version.' - '._INFO_PHP.'</th>
    </tr>
</thead>
<tbody>';

	sort($modules);
	foreach ($modules as $i)
	{
		echo '<tr>
		<td>'.$i.'</td>
		</tr>';
	}

echo '</tbody>
</table>';
