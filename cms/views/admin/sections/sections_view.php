<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// sections view
$parent = str_replace('/', '§', $xpage->xfrom);
?>
<h1 class="mt-6">
    <?php echo _SECTIONS.' '._PAGE.'
    <a
        class="link"
        @click="pager(\''.BASE_URL.'pages/index/'.$xpage->id_area.'/'.$xpage->lang.'/'.$parent.'/1\')"
        title="'._GO_BACK.'"
    >'.$xpage->name ?></a>
</h1>
<p><?php echo $page->icon._SECTIONS_SORT_MSG ?></p>
<table class="mb-0 border-b-2 border-orange-500">
    <thead>
        <tr>
            <th class="w-8">#</th>
            <th><?php echo _SECTION ?></th>
            <th ><?php echo _SECTION_COL_SIZES ?></th>
            <th class="w-40"><?php echo _ACTIONS ?></th>
        </tr>
    </thead>
    <tbody>

<?php
$sort = false;
foreach ($items as $i)
{
    $statuses = AdmUtils_helper::statuses($i);
    $actions = '';

    // check permissions
    if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
    {
        $actions = AdmUtils_helper::link('edit', 'sections/edit/'.$i->id_area.'/'.$i->id_page.'/'.$i->id);

        if ($i->level > 2)
        {
            $actions .= AdmUtils_helper::link('xon', 'sections/set/xon/'.$i->id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

            if ($i->level >= 4)
            {
                $actions .= AdmUtils_helper::link('xlock', 'sections/set/xlock/'.$i->id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                $actions .= AdmUtils_helper::link('delete', 'sections/delete/'.$i->id);
            }
        }
    }
    // handle settings
    $set = json_decode($i->settings, true);

    if ($i->progressive < $sections)
    {
        // not sortable items
        echo '<tr>
                <td class="w-8">'.$i->progressive.'</td>
                <td>'.$i->name.'</td>
                <td style="width:15em;">'.$set['columns'].'/'.$set['col_sizes'].'</td>
                <td class="w-40 space-x-2 text-right">'.$actions.'</td>
            </tr>';

    }
    elseif ($i->progressive == $sections)
    {
        // last not sortable
        echo '<tr>
                <td class="w-8">'.$i->progressive.'</td>
                <td>'.$i->name.'</td>
                <td style="width:15em;">'.$set['columns'].'/'.$set['col_sizes'].'</td>
                <td class="w-40 space-x-2 text-right">'.$actions.'</td>
            </tr>
            </tbody>
            </table>
            <div x-data="xsortable()" x-init="setup(\'sortable\', \'sections/ordering/'.$xpage->id_area.'/'.$xpage->id.'\')">
                <div id="sortable">';

        // switcher to close sortable box
        $sort = true;
    }
    else
    {
        echo '<div class="sort-item" id="'.$i->id.'">
        <table class="my-0"><tr>
            <td class="w-8">'.$i->progressive.'</td>
            <td>'.$i->name.'</td>
            <td style="width:15em;">'.$set['columns'].'/'.$set['col_sizes'].'</td>
            <td class="w-40 space-x-2 text-right">'.$actions.'</td>
        </tr></table>
        </div>';
    }
}

if ($sort)
{
    echo '</div></div>';
}