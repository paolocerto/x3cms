<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

if (empty($langs))
{
?>
<p><?php echo _NOT_PERMITTED ?></p>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','languages/filter', null);
});
</script>
<?php
}
else
{
?>

<table class="zebra">
	<tr class="first">
		<th style="width:3em;"></th>
		<th><?php echo ucfirst(_LANGUAGE) ?></th>
		<th style="width:6em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
<?php
	foreach ($langs as $i)
	{
		if ($i->xon)
		{
			$status = _ON;
			$on_status = 'orange';
		}
		else
		{
			$status = _OFF;
			$on_status = 'gray';
		}

		if ($i->xlock)
		{
			$lock = _LOCKED;
			$lock_status = 'lock';
		}
		else
		{
			$lock = _UNLOCKED;
			$lock_status = 'unlock-alt';
		}
		$actions = $delete = '';

		// check permission
		if (($i->level > 2 && $i->xlock == 0) || $i->level == 4)
		{
			$actions = '<a  class="bta" href="'.BASE_URL.'languages/edit/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a>';
			$actions .= ($i->code != $this->site->area->lang)
				? '<a class="btl" href="'.BASE_URL.'languages/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i>'
				: '<a><i class="far fa-lightbulb fa-lg invisible"></i></a>';

			// admin user
			if ($i->level == 4)
			{
				$delete ='<a class="btl" href="'.BASE_URL.'languages/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a> ';

				$delete .= ($i->code == X4Route_core::$lang)
					? '<a><i class="fas fa-trash fa-lg invisible"></i></a>'
					: '<a class="bta" href="'.BASE_URL.'languages/delete/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
			}
		}

		echo '<tr>
				<td><strong>['.$i->code.']</strong></td>
				<td><a class="btm" href="'.BASE_URL.'dictionary/keys/'.$i->code.'" title="'._SHOW_LANG_KEYS.'">'.$i->language.'</a><span></span></td>
				<td>'.$actions.'</td>
				<td class="aright">'.$delete.'</td>
				</tr>';
	}
?>
</table>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','languages/filter', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('languages'));
	zebraTable('zebra');
});
</script>
<?php
}
