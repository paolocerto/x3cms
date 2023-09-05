<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// file edit form

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $file->id,
    'name' => 'id'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $file->id_area,
    'name' => 'id_area'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $file->xtype,
    'name' => 'xtype'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

switch ($file->xtype)
{
    case 0:
        // image
        $folder = X4Files_helper::get_type_by_name($file->name, true);
        $action = (file_exists(APATH.'files/'.SPREFIX.'/filemanager/img/'.$file->name))
            ? '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>
                    <img class="thumb" src="'.FPATH.$folder.'/'.$file->name.'?t='.time().'" alt="'.$file->alt.'" />
                </div><div>
                    <a class="link" @click="pager(\''.BASE_URL.'files/editor/'.$file->id.'\');modal=false" title="'._IMAGE_EDIT.'"><i class="fa-solid fa-file-image fa-lg"></i> '._IMAGE_EDIT.'</a>
                </div></div>'
            : '';
        break;
    case 2:
        // video
        $action = (file_exists(APATH.'files/'.SPREFIX.'/filemanager/media/'.$file->name))
            ? '<div class="w-full">
                    <a class="link" @click="pager(\''.BASE_URL.'files/editor/'.$file->id.'\')" title="'._VIDEO_EDIT.'"><i class="fa-solid fa-file-video fa-lg"></i> '._VIDEO_EDIT.'</a>
                </div>'
            : '';
        break;
    case 3:
        // template
        $action = (file_exists(APATH.'files/'.SPREFIX.'/filemanager/template/'.$file->name))
            ? '<div class="w-full">
                <a class="link" @click="pager(\''.BASE_URL.'files/editor/'.$file->id.'\')" title="'._TEMPLATE_EDIT.'"><i class="fa-solid fa-file-code fa-lg"></i> '._TEMPLATE_EDIT.'</a>
            </div>'
            : '';
        break;
    default:
        // generic files
        $ext = pathinfo(APATH.'files/'.SPREFIX.'/filemanager/files/'.$file->name, PATHINFO_EXTENSION);
        if ($ext == 'txt' || $ext == 'csv')
        {
            $action = '<p><a class="link" @click="pager(\''.BASE_URL.'files/editor/'.$file->id.'\')" title="'._TEXT_EDIT.'"><i class="fa-solid fa-file-alt fa-lg"></i> '._TEXT_EDIT.'</a></p>';
        }
        else
        {
            $action = '';
        }
        break;
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<p><b>'.$area->title.'</b>: '.$file->name.'</p>'    // temprarily disabled .$action
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

$fields[] = array(
    'label' => _CATEGORY,
    'type' => 'text',
    'value' => $file->category,
    'name' => 'category',
    'extra' => 'class="w-full"',
    'rule' => 'required'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _SUBCATEGORY,
    'type' => 'text',
    'value' => $file->subcategory,
    'name' => 'subcategory',
    'extra' => 'class="w-full"',
    'rule' => 'required'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _COMMENT,
    'type' => 'textarea',
    'value' => $file->alt,
    'name' => 'alt'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
