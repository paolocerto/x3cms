<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// area map view

?>
<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">

<div class="small">
<?php

$len = 0;
$openul = $openli = 1;
foreach ($map as $i)
{
	$ilen = strlen($i->ordinal)/4;
	$deep = '';
	if ($ilen > $len)
	{
		// change subpages
		echo '<ul style="list-style:disc">';
		$openul++;
	}
	elseif ($ilen < $len)
	{
		// change subpages
		$n = $len - $ilen;
		for ($l = 0; $l < $n; $l++)
		{
			echo '</li>';
			$openli--;
			echo '</ul></li>';
			$openul--;
			$openli--;
		}
	}
	else
	{
		// normal subpage
		if ($i->ordinal == 'A')
		{
			echo '<ul>';
		}
		else
		{
			echo '</li>';
			$openli--;
		}
	}
    // deep
    if ($ilen >= 2)
    {
        $deep = str_pad($deep,  $ilen, '-');
    }

	$len = $ilen;
	$description = stripslashes($i->description);
	echo '<li>'.$deep.'&nbsp;<a class="link" @click="$dispatch(\'pager\', \''.BASE_URL.'pages/index/'.$area->id.'/'.$i->lang.'/'.str_replace('/', '$', $i->xfrom).'\');modal=false" title="'.$description.'">'.stripslashes($i->name).'</a>'._TRAIT_.$description;
	$openli++;
}

while ($openli > 0)
{
	echo '</li>';
	$openli--;
	if ($openul)
	{
		echo '</ul>';
		$openul--;
	}
}

?>
</div>
</div>
