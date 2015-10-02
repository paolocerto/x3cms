<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

?>

<table class="zebra">
<?php
switch($case)
{
case 'apache':
	$modules = apache_get_modules();
	
	echo '<tr>
		<th>'._INFO_APACHE.'</th>
	</tr>';
	sort($modules);
	foreach($modules as $i)
	{
		echo '<tr>
		<td>'.$i.'</td>
		</tr>';
	}
	break;
	
case 'mysql':
	echo '<tr>
		<th>'._INFO_MYSQL.'</th>
	</tr>';
	
	echo '<tr>
		<td>'.$sinfo.'</td>
		</tr>';
	break;
	
case 'php':
	$modules = get_loaded_extensions();
	echo '<tr>
		<th>'._INFO_PHP.'</th>
	</tr>';
	sort($modules);
	foreach($modules as $i)
	{
		echo '<tr>
		<td>'.$i.'</td>
		</tr>';
	}
	break;
default:
	echo '<tr>
		<th style="width:25em">'._INFO_KEY.'</th>
		<th>'._INFO_VALUE.'</th>
	</tr>
	<tr><td>OS</td><td>'.php_uname().'</td></tr>';
	
	foreach($_SERVER as $k => $v)
	{
		echo '<tr>
		<td>'.$k.'</td>
		<td>'.$v.'</td>
		</tr>';
	}
	break;
}
?>
</table>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','info/filter', null);
	zebraTable('zebra');
});
</script>

