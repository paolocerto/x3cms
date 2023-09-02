<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// file list view

echo '<div class="switcher">';

 // area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
    $on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
    echo '<a '.$on.' @click="pager(\''.BASE_URL.'files/index/'.$i->id.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div></div>';

// filter selector
echo '<form name="xfilter" id="xfilter" action="'.BASE_URL.'files/index/'.$id_area.'" method="GET" onsubmit="return false">

	<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">';

echo '<div>
    <label for="xxtype">'._SWITCH_TYPE.'</label>
    <select id="xxtype" name="xxtype" class="w-full" @change="filter()">
        '.X4Form_helper::get_options($types, 'value', 'option', $qs['xxtype']).'
    </select>
</div>';

// category switcher
if (!empty($categories))
{
    echo '<div>
        <label for="xctg">'._SWITCH_CATEGORY.'</label>
        <select id="xctg" name="xctg" class="w-full" @change="filter()">
            '.X4Form_helper::get_options($categories, 'ctg', 'ctg', $qs['xctg'], ['', ucfirst(_UNCATEGORIZED)]).'
        </select>
    </div>';

    // subcategory switcher
	if (!empty($subcategories))
	{
        echo '<div>
            <label for="xsctg">'._SWITCH_SUBCATEGORY.'</label>
            <select id="xsctg" name="xsctg" class="w-full" @change="filter()">
                '.X4Form_helper::get_options($subcategories, 'sctg', 'sctg', $qs['xsctg'], ['', ucfirst(_UNCATEGORIZED)]).'
            </select>
        </div>';
	}
}

echo '<div>
        <label for="xstr">Search by text</label>
        <input
            type="text"
            id="xstr"
            name="xstr"
            class="w-full uppercase"
            value="'.$qs['xstr'].'"
            autocomplete="off"
            placeholder="'._ENTER_TO_FILTER.'"
            @keyup="if ($event.key === \'Enter\') { filter(); }" />
	</div>';

echo '</div>
    </form>';

$bulk_url = 'files/bulk/'.$id_area.'?'.http_build_query($qs);
?>
<h1 class="mt-6"><?php echo _FILE_LIST ?></h1>
<div x-data="bulkable()" x-init='setup("<?php echo $bulk_url ?>")' >

<div x-show="bulk.length > 0" class="buttons">
    <input type="hidden" id="bulk_action" x-model="xaction" value="delete" />
    <button type="button" @click="execute()" class="link"><?php echo _DELETE_BULK ?></button>
</div>

<?php
if (!empty($items[0]))
{
	echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';

	$what = array('img', 'files', 'media', 'template');
	foreach ($items[0] as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);
		$actions = '';
        // for filter
        $tmp = $qs;

		// check permission
		if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions = AdmUtils_helper::link('edit', 'files/edit/'.$i->id);

            // manager or admin user
            if ($i->level > 2)
            {
                $actions .= AdmUtils_helper::link('xon', 'files/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);
            }
            // filter
            $tmp['xctg'] = $i->category;
            $tmp['xsctg'] = $i->subcategory;
            $actions .= ' <a class="link" @click="pager(\''.BASE_URL.'files/index/'.$id_area.'?'.http_build_query($tmp).'\')" title="'._FILE_FILTER.'">
                <i class="fa-solid fa-filter fa-lg"></i>
            </a>';

            // admin user
            if ($i->level >= 4)
            {
                $actions .= AdmUtils_helper::link('xlock', 'files/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);
                $actions .= AdmUtils_helper::link('delete','files/delete/'.$i->id);
            }
		}

		// file info
		if ($i->xtype)
		{
			// no image
			$thumb = $size = '';
		}
		else
		{
			// cache fix
			$now = '?t='.time();

			// image thumbs
			if (file_exists(APATH.'files/'.SPREFIX.'/thumbs/img/'.$i->name))
			{
				$thumb = '<img class="thumb" src="'.APATH.'files/'.SPREFIX.'/thumbs/img/'.$i->name.$now.'" alt="'.$i->alt.'" />';
			}
			else
			{
				$thumb = '<img class="thumb" src="'.FPATH.'img/'.$i->name.$now.'" alt="'.$i->alt.'" />';
			}

			// image size
			$size = (file_exists($file_path.'img/'.$i->name))
				? @implode('x', @array_slice(@getimagesize($file_path.'img/'.$i->name), 0, 2)).' pixel'._TRAIT_
				: '';
		}

		// file size
		$kb = @filesize($file_path.$what[$i->xtype].'/'.$i->name);
		$filesize = ($kb)
			? '('.floor($kb/1024).' KB)'
			: '';

		echo '<div class="bg2 rounded flex items-stretch">
                <div class="flex-auto text-sm p-4 overflow-x-hidden">
                    <a href="'.FPATH.$what[$i->xtype].'/'.$i->name.'" title="'.$i->alt.'">'.$i->name.'</a><br />
                    <span class="text-xs">'.$size.$filesize.'</span><br />
                    <div class="acenter pad-top">'.$thumb.'</div>
                    <p>caption: '.$i->alt.'</p>
                </div>
                <div class="flex-none w-14 bg px-3 pt-3 text-center rounded-r leading-8">
                    '.$actions.'
                    <input type="checkbox" class="bulkable" x-model="bulk" value="'.$i->id.'" />
                </div>
            </div>';
	}

	echo '</div>
    </div>';

	// pagination
	echo '<div id="file_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'files/index/'.$id_area.'/', $items[1], 5, false, '?'.http_build_query($qs), '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
