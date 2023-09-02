<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// settings cards

?>
<h1><?php echo _SETTINGS_MANAGER ?></h1>
<p><?php echo _SETTINGS_MANAGER_MSG ?></p>

<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
<?php

// settings cards
$c = 0;
foreach ($items as $i)
{
	$c++;
	echo '<button type="button" class="btn link"  @click="$dispatch(\'pager\', \''.BASE_URL.$i->url.'\')">'.$i->title.'</button>';
}
?>
</div>