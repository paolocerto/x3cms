<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

?>
<h2><?php echo _USER_DETAIL.': '.$u->username ?></h2>

<table class="zebra">
	<tr class="first">
		<th colspan="3"><?php echo _USER ?></th>
	</tr>
<?php
echo '<tr><td style="width:15em;"><strong>'._GROUP.'</td></strong></td><td colspan="2">'.$u->groupname.'</td></tr>
	<tr><td><strong>'._USERNAME.'</td></strong></td><td colspan="2">'.$u->username.'</td></tr>
	<tr><td><strong>'._DESCRIPTION.'</td></strong></td><td colspan="2">'.$u->description.'</td></tr>
	<tr><td><strong>'._EMAIL.'</td></strong></td><td colspan="2"><a href="mailto:'.$u->mail.'" title="'._MAIL_USER.'">'.$u->mail.'</a></td></tr>
	<tr><td><strong>'._PHONE.'</td></strong></td><td colspan="2">'.$u->phone.'</td></tr>
	<tr><td><strong>'._LEVEL.'</td></strong></td><td colspan="2">'.$u->level.'</td></tr>';
	
if (!isset($dialog) && ($u->plevel > 2 || $u->plevel == 4)) 
{
	echo '<tr><td colspan="3" class="menu">'._PERMISSIONS.'</td></tr>
			<tr><td><a class="btl" href="'.BASE_URL.'users/reset/'.$u->id.'" title="'._RESET_PRIVS.'">'._RESET_PRIVS.'</a></td><td colspan="2">'._RESET_PRIVS_MSG.'</td></tr>
			<tr><td><a class="btl" href="'.BASE_URL.'users/refactory/'.$u->id.'" title="'._REFACTORY.'">'._REFACTORY.'</a></td><td colspan="2">'._REFACTORY_MSG.'</td></tr>
		<tr><td colspan="3" class="menu">'._DOMAIN.'</td></tr>';

	foreach ($a as $i) 
	{
		echo '<tr>
			<td>'.$i->area.'</td>
			<td><a class="btop" href="'.BASE_URL.'users/perm/'.$u->id.'/'.$i->id_area.'/1" title="'._EDIT_PRIV.'"><i class="fa fa-cogs fa-lg"></i></a></td>
			<td><a class="btop" href="'.BASE_URL.'users/perm/'.$u->id.'/'.$i->id_area.'/0" title="'._GLOBAL_PRIVS.'">'._GLOBAL_PRIVS.'</a></td>
			</tr>';
	}
}
?>	
</table>
<script>
window.addEvent('domready', function()
{
<?php
if (!isset($dialog))
{
?>
	X3.content('filters','users/filter/<?php echo $u->id_group.'/'.$u->id ?>', null);
<?php
}
?>
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'btop', 'modal');
	actionize('topic',  'btl', 'tdown', escape('users/detail/<?php echo $u->id ?>'));
	zebraTable('zebra');
});
</script>
