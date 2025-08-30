<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x4cookie settings view

// contents
echo '<div x-data="cookier()" x-cloak>
    <div class="pt-6">
        '.nl2br(_X4COOKIE_SETUP_MSG).'
        <div class="flex flex-col gap-4">
            <div>
                <label for="tech">
                    <input type="checkbox" name="tech" x-model="tech" checked onclick="return false;" >
                    '._X4COOKIE_TECHNICAL.'
                </label>
                <p class="text-sm">'.nl2br(_X4COOKIE_TECHNICAL_MSG).'</p>
            </div>';

if ($conf['third_party_cookies'])
{
    echo '  <div>
                <label for="thirdy">
                    <input type="checkbox" name="thirdy" x-model="thirdy" '.$thirdy.' >
                    '._X4COOKIE_THIRDY.'
                </label>
                <p class="text-sm">'.nl2br(_X4COOKIE_THIRDY_MSG).'</p>
            </div>';
}

if ($conf['profiling_cookies'])
{
    echo '  <div>
                <label for="thirdy">
                    <input type="checkbox" name="profile" x-model="profile" '.$profile.' >
                    '._X4COOKIE_PROFILE.'
                </label>
                <p class="text-sm">'.nl2br(_X4COOKIE_PROFILE_MSG).'</p>
            </div>';
}

echo '  </div>
    </div>
    <div class="mt-8 flex flex-col md:flex-row justify-end gap-4">
        '.$more.'
        <button type="button" class="btn link" @click="setup()">'._X4COOKIE_OK.'</button>
    </div>
</div>';
