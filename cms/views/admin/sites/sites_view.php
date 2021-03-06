<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

$edit = $offline = '';
if ($_SESSION['level'] > 2) 
{
	// site status
	if ($this->site->site->xon) 
	{
		$status = _ONLINE;
		$on_status = 'orange';
	}
	else 
	{
		$status = _OFFLINE;
		$on_status = 'gray';
	}
	
	$edit = '<a class="bta" href="'.BASE_URL.'sites/config/'.$this->site->site->id.'" title="'._CONFIG.'"><i class="fas fa-cogs fa-lg"></i></a>';
	
	// if caching
	if (CACHE)
	{
		$edit .= '<a class="btl" href="'.BASE_URL.'sites/clear_cache" title="'._CLEAR_CACHE.'"><i class="fas fa-eraser fa-lg"></i></a>';
	}
	if (APC)
	{
		$edit .= ' <a class="btl" href="'.BASE_URL.'sites/clear_apc" title="'._CLEAR_CACHE.' APC"><i class="fas fa-eraser fa-lg"></i></a>';
	}
	$offline = '<a class="btl" href="'.BASE_URL.'sites/offline/'.$this->site->site->id.'/'.(($this->site->site->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fas fa-globe '.$on_status.' fa-lg"></a>';
}

// admin user
if ($_SESSION['level'] == 4) 
{
	$edit = '<a class="bta" href="'.BASE_URL.'sites/edit/'.$this->site->site->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a> '.$edit;
}
?>

<table class="zebra">
	<tr class="first">
		<th><?php echo _DOMAIN ?></th>
		<th style="width:8em"><?php echo _ACTIONS ?></th>
		<th style="width:4em"></th>
	</tr>
	<tr>
		<td><?php echo $this->site->site->domain ?></td>
		<td><?php echo $edit ?></td>
		<td class="aright"><?php echo $offline ?></td>
	</tr>
</table>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','sites/filter', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('sites/show'));
	zebraTable('zebra');
});
</script>

