<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// user detail view

echo '<h1 class="mt-6">'.$page->icon.' '._USER_DETAIL.': '.$user->username.'</h1>';

echo '<table>
    <thead>
        <tr>
            <th colspan="3">'._USER.'</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="w-56 font-bold">'._GROUP.'</td>
            <td colspan="2">'.$user->groupname.'</td>
        </tr>
        <tr>
            <td class="font-bold">'._USERNAME.'</td>
            <td colspan="2">'.$user->username.'</td>
        </tr>
        <tr>
            <td class="font-bold">'._DESCRIPTION.'</td>
            <td colspan="2">'.$user->description.'</td>
        </tr>
        <tr>
            <td class="font-bold">'._EMAIL.'</td>
            <td colspan="2"><a class="link font-bold" href="mailto:'.$user->mail.'" title="'._MAIL_USER.'">'.$user->mail.'</a></td>
        </tr>
        <tr>
            <td class="font-bold">'._PHONE.'</td>
            <td colspan="2">'.$user->phone.'</td>
        </tr>
        <tr>
            <td class="font-bold">'._LEVEL.'</td>
            <td colspan="2">'.$user->level.'</td>
        </tr>
        <tr>
            <td class="w-56 font-bold">'._LAST_LOGIN.'</td>
            <td colspan="2">'.$user->last_in.'</td>
        </tr>';

if ($user->plevel > 2)
{
	echo '<tr>
            <td colspan="3" class="bg2 text-center">'._PERMISSIONS.'</td>
        </tr>
		<tr>
            <td><a class="link" @click="setter(\''.BASE_URL.'users/reset/'.$user->id.'\')" title="'._RESET_PRIVS.'">'._RESET_PRIVS.'</a></td>
            <td colspan="2">'._RESET_PRIVS_MSG.'</td>
        </tr>
		<tr>
            <td><a class="link" @click="setter(\''.BASE_URL.'users/refactory/'.$user->id.'\')" title="'._REFACTORY.'">'._REFACTORY.'</a></td>
            <td colspan="2">'._REFACTORY_MSG.'</td>
        </tr>
		<tr>
            <td colspan="3" class="bg2 text-center">'._DOMAIN.'</td>
        </tr>';

	foreach ($aprivs as $i)
	{
		echo '<tr>
                <td>'.$i->area.'</td>
                <td class="w-56">
                    <a class="link" @click="popup(\''.BASE_URL.'users/perm/'.$user->id.'/'.$i->id_area.'/1\')" title="'._EDIT_PRIV.'">
                        <i class="fa-solid fa-lg fa-sliders"></i>
                    </a>
                </td>
                <td>
                    <a class="link" @click="popup(\''.BASE_URL.'users/perm/'.$user->id.'/'.$i->id_area.'/0\')" title="'._GLOBAL_PRIVS.'">'._GLOBAL_PRIVS.'</a>
                </td>
			</tr>';
	}
}
echo '</tbody>
</table>';
