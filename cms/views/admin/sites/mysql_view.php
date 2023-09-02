<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// site/mysql info

$mod = new Log_model();
$server_info = $mod->get_attribute('SERVER_INFO');
$server_version = $mod->get_version();

echo '<table>
<thead>
    <tr>
        <th>'._INFO_MYSQL.'</th>
    </tr>
</thead>
<tbody>
    <tr>
		<td>MySQL version: '.$server_version.'</td>
	</tr>
    <tr>
		<td>'.$server_info.'</td>
	</tr>
</tbody>
</table>';