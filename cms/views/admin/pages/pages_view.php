<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// drag and drop examples
// library https://github.com/bevacqua/dragula

echo '<div class="switcher">';
// language switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $page->lang) ? 'class="link"' : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'pages/index/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div></div>';

?>
<h1 class="mt-6"><?php echo _PAGE_LIST.' \''.$area.'\''._TRAIT_._LANGUAGE.' \''.$lang ?>'</h1>

<?php

// do not delete pages
$no_del = array('home', 'msg', 'search', 'logout', 'offline');

// do not move pages
$no_menu = array('home', 'msg', 'search', 'offline', 'x3admin');

if (isset($page->url) && $page->url != 'home')
{
	// parent page
	$parent = str_replace('/', '§', $page->xfrom);
	echo '<p><a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$parent.'/1\')" title="'._GO_BACK.'">
        <i class="fa-solid fa-circle-arrow-left fa-lg"></i>
        '.stripslashes($page->name).'</a>
    </p>';
}

// menu arrangement
$amenu = array();
if ($page->url == 'home')
{
	foreach ($menus as $i)
	{
		$amenu[$i->id] = $i->description;
	}
}
else
{
	foreach ($menus as $i)
	{
		if ($i->id == $page->id_menu) $amenu[$i->id] = $i->description;
	}
}

if (!empty($pages))
{
    $width = (ADVANCED_EDITING)
        ? 'w-60'
        : 'w-56';

    echo '<table>
        <thead>
            <tr>
                <th class="text-left pl-4">'.ucfirst(_PAGES).'</th>
                <th class="'.$width.'">'._ACTIONS.'</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($pages as $i)
	{
		if ($i->url != 'x3admin')
        {
			$statuses = AdmUtils_helper::statuses($i);

			$actions = '';

			// check permission
			if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
            {
				$actions = (ADVANCED_EDITING)
                    ? '<a class="link" @click="pager(\''.BASE_URL.'sections/compose/'.$i->id.'\')" title="'._EDIT.'">
                            <i class="fa-solid fa-lg fa-pen-to-square"></i>
                        </a>'
                    : '<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/1/0/'.$i->id.'\')" title="'._EDIT.'">
                            <i class="fa-solid fa-lg fa-pen-to-square"></i>
                        </a>';

				// manager user
				if ($i->level > 2)
				{
					if (in_array($i->url, $no_del))
                    {
                        $actions .= '<a><i class="far fa-lightbulb fa-lg on"></i></a>
                            <a><i class="fa-solid fa-lg fa-arrows-up-down-left-right off"></i></a>';
                    }
                    else
                    {
                        $actions .= AdmUtils_helper::link('xon', 'pages/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);
                        $actions .= '<a class="link" @click="popup(\''.BASE_URL.'pages/move/'.$i->id.'\')" title="'._MENU_AND_ORDER.'">
                        <i class="fa-solid fa-lg fa-arrows-up-down-left-right"></i>
                            </a>';
                    }

					$actions .= AdmUtils_helper::link('settings', 'pages/seo/'.$i->id);

					// admin user
					if ($i->level >= 4)
					{
						// add sections editing
						if (ADVANCED_EDITING)
						{
							$actions .= '<a class="link" @click="pager(\''.BASE_URL.'sections/index/'.$i->id_area.'/'.$i->id.'\')" title="'._SECTIONS.'">
                                <i class="fa-regular fa-object-group fa-lg"></i>
                            </a>';
						}
						$actions .= AdmUtils_helper::link('xlock', 'pages/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

						$actions .= (!in_array($i->url, $no_del))
						    ? AdmUtils_helper::link('delete', 'pages/delete/'.$i->id)
                            : '<a><i class="fa-solid fa-lg fa-trash off"></i></a>';
					}
				}
			}

			// menus
			if (isset($amenu[$i->id_menu]))
			{
				echo '<tr>
                        <td colspan="2" class="bg pl-4">
						    '._MENU.': '.stripslashes($amenu[$i->id_menu]).'
                        </td>
                    </tr>';
				unset($amenu[$i->id_menu]);
			}

            echo '<tr>
                    <td><a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$i->id_area.'/'.$i->lang.'/'.str_replace('/', '§', $i->url).'/1\')"  title="'._SUBPAGES.'">'.stripslashes($i->name).'</a></td>
                    <td class="space-x-2 text-right">'.$actions.'</td>
                </tr>';
		}
	}

    // empty menus
	foreach ($amenu as $k => $v)
	{
		echo '<tr>
            <td colspan="2" class="bg pl-4">
                '._MENU.': '.stripslashes($amenu[$k]).'
            </td>
        </tr>';
	}

    echo '</tbody>
        </table>';
}
else
{
	if ($_SESSION['level'] > 3)
	{
		if (!isset($page->url) || $page->url == 'home')
		{
			echo '<p>'._NO_SUBPAGES._TRAIT_.'<a class="link" @click="setter(\''.BASE_URL.'pages/init/'.$id_area.'/'.$lang.'\')" title="'._INIZIALIZE_AREA.'">'._INIZIALIZE_AREA.'</a></p>';
		}
		else
		{
			echo '<p>'._NO_SUBPAGES.'</p>';
		}
	}
	else
	{
		echo '<p>'._NOT_PERMITTED.'</p>';
	}
}
