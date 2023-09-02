<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// site/apache info

$modules = apache_get_modules();

echo '<table>
<thead>
    <tr>
        <th>'._INFO_APACHE.'</th>
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