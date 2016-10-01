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
<h1><?php echo _ADMIN_AREA ?></h1>
<p><?php echo _HI.' <strong> '.$_SESSION['username'].'</strong>, '._LAST_LOGIN.' '.$_SESSION['last_in'] ?></p>

<div id="dashboard" class="band inner-pad clearfix">
<?php
// notices from x3cms.net
if (NOTICES) 
{
	echo '<div class="one-fourth md-one-half xs-one-whole pad-bottom xs-pad-none">
			<div class="widget">
				<div class="wtitle pad-left pad-right">'._NOTICES_AND_UPDATES.'</div>
				<div class="wbox pad-left pad-right">'.$notices.'</div>
			</div>
		</div>';
}

// widgets
$c = 0;
$buttonized = '';
foreach($widgets as $i)
{
	$c++;
	echo '<div class="one-fourth md-one-half xs-one-whole pad-bottom xs-pad-none"><div id="w'.$c.'" class="widget">'.$i[0].'</div></div>';
	if ($i[1])
		$buttonized .= 'buttonize(\'w'.$c.'\', \'btr\', \'w'.$c.'\', \'\', \'w'.$c.'\');'.NL;
}
?>
</div>
<script>
window.addEvent('domready', function()
{
	buttonize('dashboard', 'bta', 'topic');
	buttonize('topic', 'btt', 'topic');
	<?php echo $buttonized ?>
	blanking();
	zebraUl('zebra');
});
</script>

