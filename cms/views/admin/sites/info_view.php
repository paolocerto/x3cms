<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// site/info

echo '<table>
<thead>
    <tr>
        <th class="w-4">'._INFO_KEY.'</th>
        <th>'._INFO_VALUE.'</th>
    </tr>
</thead>
<tbody>
	<tr>
        <td>OS</td>
        <td>'.php_uname().'</td>
    </tr>';

	foreach ($_SERVER as $k => $v)
	{
        $value = is_array($v)
            ? json_encode($v)
            : $v;

		echo '<tr>
		<td>'.$k.'</td>
		<td>'.$value.'</td>
		</tr>';
	}

echo '</tbody>
</table>';