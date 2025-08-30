<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x4cookie default view

echo '
<div class="pt-6">'.nl2br(_X4COOKIE_MESSAGE.$edit).'</div>
<div x-data="cookier()" x-cloak class="mt-8 flex flex-col md:flex-row justify-end gap-4">
    '.$more.'
    <button type="button" class="btn gray" @click="settings()">'._X4COOKIE_SETUP.'</button>
    <button type="button" class="btn link" @click="setup()">'._X4COOKIE_OK.'</button>
</div>';