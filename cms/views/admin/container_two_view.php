<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// Container for two cols
echo '<div class="band clearfix double-inner-pad">
		<div id="leftbox" class="four-fifth md-three-fourth sm-two-third xs-one-whole">
			'.$content.'
		</div>
		<div id="rightbox" class="one-fifth md-one-fourth sm-one-third xs-one-whole">
			'.$right.'
		</div>
	</div>';

if (isset($js))
{
	echo $js;
}
