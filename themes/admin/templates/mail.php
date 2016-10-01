<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3CMS - admin theme - mail view

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
	<head>
		<meta name="Generator" content="<?php echo SERVICE ?>" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo $this->site->site->domain ?>/themes/admin/css/mail.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<h1>
						<a href="<?php echo $this->site->site->domain ?>" title="<?php echo SERVICE ?>">X<span>3</span>CMS</a>
					</h1>
				</td>
			</tr>
			<tr>
				<td id="topic"style="">
					<h1><?php echo $subject ?></h1>
					<p><?php echo $message ?></p>
				</td>
			</tr>
			<tr>
				<td id="foot">
					<p><br /><a href="<?php echo $this->site->site->domain ?>" title="<?php echo SERVICE ?>"><?php echo SERVICE ?></a></p>
				</td>
			</tr>
		</table>
	</body>
</html>
