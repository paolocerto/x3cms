<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */
?>
<!DOCTYPE html>
<html>
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Warning</title>

	<link title="normal" rel="stylesheet" type="text/css" href="<?php echo ROOT.'css/screen.css' ?>" media="screen" />

</head>
<body>

	<h1><?php echo $title ?></h1>
	<p><?php echo $msg ?></p>

	<p class="copyright">
		Copyright &copy; 2010 - <?php echo date('Y') ?> Cblu.net<br />
		X4WebApp v. {x4wa_version} - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}
	</p>

</body>
</html>
