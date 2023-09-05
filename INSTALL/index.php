<?php
/**
 * X3 CMS - A smart Content Management System
 * Installer
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// DEBUG
// print_r(apache_get_modules());
// echo phpinfo();
// die;

/**
 * Start session
 */
!ini_get('session.auto_start') ? session_start() : '';
$SID = session_id();

/**
 * Define constants
 */

// X4 WEBAPP VERSION
define('X4VERSION', '0.9.99');

// X3 CMS VERSION
define('X3VERSION', '0.9.99 STABLE');

// DEFINE THE INSTALL ROOT
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
define('INSTALL_ROOT', $root.'/');

// DEFINE PATH
define('PATH', $_SERVER['DOCUMENT_ROOT'].INSTALL_ROOT);

// DEFINE THE FINAL PATH TO ROOT
define('FINAL_ROOT', str_replace('INSTALL/', '', PATH));

// STEP SWITCHER
$step = (isset($_REQUEST['s']))
	? $_REQUEST['s']
	: 0;

define('NL', "\n");
define('BR', '<br />');
define ('SPACER', '<span>&nbsp;&nbsp;+&nbsp;&nbsp;</span>');

// DEFAULT MESSAGES
define('OK', '<span class="green">OK</span>');
define('FAILED', '<span class="error">FAILED</span>');
define('RECOMMENDED', '<span class="blue">RECOMMENDED FOR HEAVY LOADS</span>');

// SOME FACILITIES

/**
 * Check if an apache module is loaded
 *
 * @param   string	Apache module name
 * @return  boolean
 */
function apache_is_module_loaded($mod_name)
{
	if(function_exists('apache_get_modules'))
	{
		$modules = apache_get_modules();
	}
	else
	{
		$modules = array('mod_php', 'mod_rewrite');
	}
	return (in_array($mod_name, $modules));
}

/**
 * Check if a PHP extesion is loaded
 *
 * @param   string	PHP extension name
 * @return  boolean
 */
function php_is_extension_loaded($ext_name)
{
	$extensions = get_loaded_extensions();
	return (in_array($ext_name, $extensions));
}

/**
 * Replace checkdnsrr on windows
 *
 * @param   string	Host name
 * @param   string	DNS record type
 * @return  boolean
 */
function win_checkdnsrr($host, $type='MX')
{
	if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
	{
		return;
	}

	if (empty($host))
	{
		return;
	}

	$types=array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY');
	if (!in_array($type,$types))
	{
		user_error("checkdnsrr() Type '$type' not supported", E_USER_WARNING);
		return;
	}
	@exec('nslookup -type='.$type.' '.escapeshellcmd($host), $output);
	foreach ($output as $line)
	{
		if (preg_match('/^'.$host.'/',$line))
		{
			return true;
		}
	}
	return true;
}

/**
 * Check if an email address is valid
 * First check if address is valid
 * Then check if exists a domain for the address, if ther'is no Internet connection return true
 *
 * @param   string	Email address
 * @return  boolean
 */
function check_email($mail)
{
	$pattern = "/^[\w-]+(\.[\w-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
	if (preg_match($pattern, $mail))
	{
		$parts = explode("@", $mail);
		if (!function_exists('checkdnsrr'))
		{
			$check = win_checkdnsrr($parts[1], "MX");
		}
		elseif (fsockopen("www.google.com", 80))
		{
			$check = checkdnsrr($parts[1], "MX");
		}
		else
		{
			return true;	// no check possible
		}
	   return $check;
	}
	else
	{
		return false; 		// e-mail address contains invalid characters
	}
}

/**
 * Get the final domain name
 *
 * @param   string	URL
 * @return  URL
 */
function get_domain()
{
	$url = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['HTTP_HOST'].str_replace('/INSTALL/', '', INSTALL_ROOT);
    if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)
    {
        return false;
    }

    // get the url parts
    $parts = parse_url($url);
    // return the host domain
    return $parts['scheme'].'://'.$parts['host'].$parts['path'];
}

/**
 * Split an SQL file and return an array
 *
 * @param   string	SQL file path
 * @return  array
 */
function get_queries($file)
{
	$a = array();
	$sql = explode("\n", file_get_contents($file));

	foreach ($sql as $i)
	{
		$r = trim($i);
		if (!empty($r) && substr($r, 0, 2) != '--')
		{
			$chk = substr($r, 0, 6);
			switch($chk)
			{
				case 'CREATE':
				case 'INSERT':
					// old previous query
					if (isset($tmp))
					{
						$a[] = $tmp;
					}
					// start a new query
					$tmp = $r;
					break;
				default:
					$tmp .= $r;
					break;
			}
		}
	}

	if (isset($tmp))
	{
		$a[] = $tmp;
	}
	return $a;
}

/**
 * Build Timezone options for select
 *
 * @param   string	Time zone selected value
 * @return  string
 */
function get_timezone($value)
{
	$o = '';

	$t = DateTimeZone::listIdentifiers();
	foreach ($t as $i)
	{
		$selected = ($i == $value) ? ' selected="selected"' : '';
		$o .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	}
	return $o;
}

$head = '
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>X3 CMS '.X3VERSION.' - INSTALLER</title>

    <meta name="description" content="New X3CMS installer">
    <meta name="keywords" content="">
    <meta name="robots" content="all">

    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
    <link rel="manifest" href="../site.webmanifest">

    <link rel="stylesheet" href="../themes/admin/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../themes/admin/css/x3ui.css">

    <script src="https://kit.fontawesome.com/2e7ce67797.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="../themes/admin/js/alpine.min.js"></script>

    <script src="../themes/admin/js/x3ui.js"></script>

</head>
<body>
    <header id="head" class="bg-white px-6 py-4">
		<h1>X3 CMS '.X3VERSION.' - Installer</h1>
        <div class="w-full">
            <img src="../themes/admin/img/x3cms_extended_dark.png" alt="X3 CMS" class="w-96 m-auto" />
        </div>
    </header>
    <div id="main" class="text-gray-300">';

// INSTALLER STEPS
switch($step)
{
	case 0:
		// INTRO
		$body = '<div class="px-4 py-4 bg">
					<span>Intro</span>
				</div>
			</div>
			<div class="bg-gray-600 py-4">
				<div class="w-96 m-auto text-gray-300">
					<h3>Welcome to X3 CMS!</h3>
					<p>This procedure will help you to install X3 CMS into this web server.</p>

					<p>The process is very easy: only <strong>5 steps</strong> and you can enjoy with X3 CMS.</p>
					<ol>
						<li>License agreement</li>
						<li>Requirements check</li>
						<li>Permissions check</li>
						<li>Configuration</li>
						<li>Database building</li>
					</ol>

					<p>Please, pay attention to warning messages.<br />
					<div class="py-6 text-center">
						<a class="btn link" href="./index.php?s=1" title="License agreement">Next</a>
					</div>
				</div>
			</div>';
		break;
	case 1:
		// LICENSE
		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
                    <span>+</span>
                    <span>Step 1/5</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-2/3 px-4 md:m-auto text-gray-300">
					<h3>License agreement</h3>
					<p>The X3 CMS is distributed under the <strong>GPL</strong> License (GNU General Public License)</p>
					<p>You can read it online at <a href="https://www.gnu.org/licenses/gpl-3.0.html" title="GNU General Public License">https://www.gnu.org/licenses/gpl-3.0.html</a> or a standalone copy <a href="../gpl-3.0.html" title="GPL 3.0">AGPL-3.0</a>
					<h4 class="mt-6">What you should know</h4>
					<p><strong>A short summary of what does the GPL</strong> (What you can do and can not do)</p>
					<ul>
						<li>Allows people to copy, modify and distribute the X3 CMS code.</li>
						<li>Forces any modifications done to the X3 CMS code to be shared back to the project and other users.</li>
						<li class="error"><u>Does not allow rebranding of the system for commercial purposes.</u></li>
						<li class="error"><u>Does not allow removal of the original copyright notices.</u></li>
						<li class="error"><u>Requires you keep a convenient and prominently visible link to the X3 CMS Legal Notices.</u></li>
						<li>Forces a clear message that the modified version is based on the original and links back to X3 CMS.</li>
						<li>Plugins should be available under their own license and should not be in anyway dependent on the X3 CMS license.</li>
					</ul>

					<h4 class="mt-6">If you need more freedom</h4>
					<p>You can choose an other license as you can see at  <a href="http://www.x3cms.net/en/download_x3cms" title="X3 CMS download">www.x3cms.net</a>.</p>
					<p>If you for whatever reason need to remove the link to X3 CMS Legal Notices and the copyright notices in the templates the only solution is to buy a commercial license. More informations at <a href="http://www.x3cms.net" title="X3 your next Content Management System">www.x3cms.net</a></p>
					<p class="green">NOTE: this solution is for a lifetime for just one domain name and does not remove the other constraints of the license.</p>

					<div class="flex justify-center space-x-2 py-6">
						<a class="btn gray" href="./index.php?s=6" title="Not install">I not agree</a>
                        <a class="btn link" href="./index.php?s=2" title="Requirements check">I agree</a>
					</div>
				</div>
			</div>';
		break;
	case 2:
		// REQUIREMENTS CHECK
		$check = true;
		// APACHE
		$apache_modules = '';
		if (stristr($_SERVER['SERVER_SIGNATURE'], 'Apache'))
		{
			$apache = OK;
            $tmp = explode('.', phpversion());
            $php_version = array_shift($tmp);
			// check modules
			$apache_modules = '<li>Apache Modules<ul>';
			$mod = array('mod_rewrite', 'mod_headers', 'mod_php');

			foreach ($mod AS $m)
			{
				if (apache_is_module_loaded($m))
				{
					$apache_modules .= '<li>'.$m.': '.OK.'</li>';
				}
				else
				{
					$apache_modules .= '<li class="error">'.$m.': '.FAILED.'</li>';
					if ($m != 'mod_headers')
					{
						$check = false;
					}
				}
			}
			$apache_modules .= '</ul></li>';
		}
		else
		{
			$apache = FAILED.' You need Apache web server to run X3 CMS';
			$check = false;
		}
		// PHP
		$php_ext = '';
		if ($php_version >= 7)
		{
			$phpv = phpversion().' '.OK;
			// memory limit
			$php_ext = (intval(ini_get('memory_limit')) >= 32) ?
				'<li>PHP Memory allocated: <span class="green">'.ini_get('memory_limit').'</span>' :
				'<li>PHP Memory allocated: <span class="error">'.ini_get('memory_limit').'</span> (minimum 16M)';
			// check extensions
			$php_ext .= '<li>PHP Extensions<ul>';
			$ext = array('session', 'date', 'PDO', 'pdo_mysql', 'gd', 'mbstring', 'apcu');
			if ($php_version > 7)
			{
			    $mod[] = 'mysql';
			}
			foreach ($ext AS $e)
			{
				if (php_is_extension_loaded($e))
				{
					$php_ext .= '<li>'.$e.': '.OK.'</li>';
				}
				else
				{
					if ($e == 'apc')
					{
						$php_ext .= '<li>'.$e.': '.RECOMMENDED.'</li>';
					}
					else
					{
						$php_ext .= '<li>'.$e.': '.FAILED.'</li>';
						$checlk = false;
					}
				}
			}
			$php_ext .= '</ul></li>';
		}
		else
		{
			$phpv = '<span class="error">'.phpversion().' '.FAILED.' You need PHP version 5 or later</span>';
			$check = false;
		}

		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
					<span>+</span>
					<a class="link" href="./index.php?s=1" title="Back to License agreement">Step 1</a>
                    <span>+</span>
					<span>Step 2/5</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-1/3 px-4 md:m-auto text-gray-300">
					<h3>Requirements check</h3>
					<ul>
						<li>Apache Web Server: '.$apache.'</li>
						'.$apache_modules.'
						<li>PHP Version: '.$phpv.'</li>
						'.$php_ext.'
					</ul>';

		$body .= ($check)
            ? '<div class="flex justify-center space-x-2 py-6">
                    <a class="btn link" href="./index.php?s=3" title="Permissions check">Next</a>
                </div>'
            : '<p class="failed px-4 py-4 rounded">One or more items failed check web server settings before continue.</p>
                <div class="flex justify-center space-x-2 py-6">
                    <a class="btn gray" href="./index.php?s=6" title="Not install">Exit</a>
                </div>';

		$body .= '</div></div>';
		break;
	case 3:
		// CHECK PERMISSIONS
		$check = true;

		$perms = explode("\n", file_get_contents('permissions.txt'));
		// Give the system a couple seconds ! //
		sleep(1);

		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
					<span>+</span>
					<a class="link" href="./index.php?s=1" title="Back to License agreement">Step 1</a>
					<span>+</span>
					<a class="link" href="./index.php?s=2" title="Back to Requirements check">Step 2</a>
                    <span>+</span>
					<span>Step 3/5</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-1/3 px-4 md:m-auto text-gray-300">
					<h3>Permissions check</h3>
					<p>Set permissions to 777 for the following files and folders:</p>
					<p>';
		foreach ($perms as $i => $perm)
		{
			if (!empty($perm))
			{
				$perm = str_replace("\r", "", $perm);
				if (!is_writable('../'.$perm))
				{
					if (is_dir('../'.$perm))
					{
						@chmod('../'.$perm, "0777");
					}
					else
					{
						@chmod('../'.$perm, "0666");
					}

					// Check again //
					if (!is_writable('../'.$perm))
					{
						if (is_dir('../'.$perm))
						{
							$body .= $perm.' '.FAILED.' directory must be writable<br />';
						}
						else
						{
							$body .= $perm.' '.FAILED.' file must be writable<br />';
						}
						$check = false;
					}
					else
					{
						$body .= $perm.' '.OK.'<br />';
					}
				}
				else
				{
					$body .= $perm.' '.OK.'<br />';
				}
			}
		}
		$body .= '</p>
                ';
		$body .= ($check)
			? '<div class="flex justify-center space-x-2 py-6">
                <a class="btn link" href="./index.php?s=4" title="Configuration check">Next</a>
                </div>'
			: '<p class="failed px-4 py-4 rounded">One or more files or directories have wrong permissions. Change them before continue.</p>
                <div class="flex justify-center space-x-2 py-6">
				    <a class="btn gray" href="./index.php?s=6" title="Not install">Exit</a>
                </div>';
		$body .= '</div></div>';
		break;
	case 4:
		// CHECK CONFIG
		$fields = array();
		$fields['hash'] = array('label' => 'Encryption method', 'value' =>'MD5');
		$fields['dbhost'] = array('label' => 'Database host', 'value' => 'localhost');
		$fields['dbsocket'] = array('label' => 'Socket', 'value' => '');
		$fields['dbname'] = array('label' => 'Database name', 'value' => '');
		$fields['dbuser'] = array('label' => 'Database user', 'value' => '');
		$fields['dbpass'] = array('label' => 'Database password', 'value' =>'');
		$fields['auser'] = array('label' => 'Admin user', 'value' => '');
		$fields['apass'] = array('label' => 'Admin password', 'value' => '');
		$fields['amail'] = array('label' => 'Admin email', 'value' => '');
		$fields['tzone'] = array('label' => 'Time Zone', 'value' => '');

		$msg = array();
		if (isset($_POST) && !empty($_POST))
		{
			// validation
			foreach ($fields as $k => $i)
			{
				$fields[$k]['value'] = $_POST[$k];
				if (!isset($_POST[$k]) || trim($_POST[$k]) == '' && $k != 'dbsocket')
				{
					$msg[] = '<strong>'.$i['label'].'</strong> is a required field';
				}
				else
				{
					switch($k)
					{
					case 'auser':
					case 'apass':
						if (strlen($_POST[$k]) < 5)
						{
							$msg[] = '<strong>'.$i['label'].'</strong> is too short';
						}
						break;
					case 'amail':
						if (!check_email($_POST[$k]))
						{
							$msg[] = '<strong>'.$i['label'].'</strong> is not a valid email address';
						}
						break;
					}
				}
			}

			if (empty($msg))
			{
				// check connection
				$dsn = (empty($_POST['dbsocket']))
					? 'mysql:host='.$_POST['dbhost'].';dbname='.$_POST['dbname']
					: 'mysql:unix_socket='.$_POST['dbsocket'].';dbname='.$_POST['dbname'];

				try
				{
					$db = new PDO($dsn, $_POST['dbuser'], $_POST['dbpass'], array(PDO::ATTR_PERSISTENT=>'x3cms', PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$stmt = $db->query('show tables');
					$tables = $stmt->fetch(PDO::FETCH_ASSOC);

					if ($tables || !empty($tables))
					{
						$msg[] = '<strong>This database is not empty!</strong>';
					}
				}
				catch (PDOException $e)
				{
					$msg[] = '<strong>Connection to database failed!</strong>';
					$msg[] = $e->getMessage();
				}
			}

			if (empty($msg))
			{
				$domain = get_domain();

				// set configuration
				$search = array("define('HASH', 'md5');", "'db_host' => 'localhost',", "'db_socket' => '',", "'db_name' => 'x3cms',", "'db_user' => 'root',", "'db_pass' => 'root',", "'TIMEZONE', 'timezone'");
				$replace = array("define('HASH', '".$_POST['hash']."');", "'db_host' => '".$_POST['dbhost']."',", "'db_socket' => '".$_POST['dbsocket']."',", "'db_name' => '".$_POST['dbname']."',", "'db_user' => '".$_POST['dbuser']."',", "'db_pass' => '".$_POST['dbpass']."',", "'TIMEZONE', '".$_POST['tzone']."'");
				$config = str_replace($search, $replace, file_get_contents(FINAL_ROOT.'cms/config/config.php'));
				$check_config = file_put_contents(FINAL_ROOT.'cms/config/config.php', $config);

				if ($check_config)
				{
					// set sql

					// get domain name
					$domain = get_domain();

					$search = array('ZZZDOMAIN', 'ZZZAUSER', 'ZZZAPASS', 'ZZZAMAIL');
					$replace = array($domain, $_POST['auser'], hash($_POST['hash'], $_POST['apass']), $_POST['amail']);
					$sql = str_replace($search, $replace, file_get_contents(PATH.'mysql.sql'));

					// tmp file
					$tmp = md5(time());
					$_SESSION['tmp'] = $tmp;
					$check_sql = file_put_contents(FINAL_ROOT.'cms/files/'.$tmp, $sql);

					// memo of domain
					$_SESSION['domain'] = $domain;
					// memo of db connection
					$_SESSION['dbhost'] = $_POST['dbhost'];
					$_SESSION['dbsocket'] = $_POST['dbsocket'];
					$_SESSION['dbname'] = $_POST['dbname'];
					$_SESSION['dbuser'] = $_POST['dbuser'];
					$_SESSION['dbpass'] = $_POST['dbpass'];
					// redirect
					header('Location: ./index.php?s=5');
					die;
				}
				$msg[] = '<strong>An error occurred during configuration.</strong>';
			}
		}

		// CONFIGURATION
		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
					<span>+</span>
					<a class="link" href="./index.php?s=1" title="Back to License agreement">Step 1</a>
					<span>+</span>
					<a class="link" href="./index.php?s=2" title="Back to Requirements check">Step 2</a>
					<span>+</span>
					<a class="link" href="./index.php?s=3" title="Back to Permissions check">Step 3</a>
                    <span>+</span>
					<span>Step 4/5</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-1/3 px-4 md:m-auto text-gray-300">
					<h3>Configuration</h3>
					<p class="mb-6">You should have already set an <strong>empty MySQL database</strong> for this web site</p>';

		if (!empty($msg))
		{
			$body .= '<div id="msg" class="failed px-4 py-4 rounded mb-4">
                <h4>One or more fields are not filled in correctly:</h4>
                <p class="mt-4">'.implode(BR, $msg).'</p>
            </div>';
		}

		$hashes = array('md5', 'sha1', 'tiger192,4', 'sha512', 'whirlpool');
		$hash_options = '';
		foreach ($hashes as $i)
		{
			$selected = ($i == $fields['hash']['value'])
				? 'selected="selected"'
				: '';
			$hash_options .= '<option value="'.$i.'" '.$selected.'>'.strtoupper($i).'</option>';
		}

		$body .= '<form name="config" id="config" action="./index.php?s=4" method="post">

                    <div class="w-full" x-data="{selected:1}">
                        <ul class="shadow-box">

                            <li class="relative mb-1">
                                <button
                                    type="button"
                                    class="w-full bg rounded px-8 py-6 text-left"
                                    @click="selected !== 1 ? selected = 1 : selected = null"
                                >
                                    <h4>MySQL Database</h4>
                                    <p>To connect to the database</p>
                                </button>

                                <div
                                    class="relative overflow-hidden transition-all max-h-0 duration-700"
                                    x-ref="container1"
                                    x-bind:style="selected == 1 ? \'max-height: \' + $refs.container1.scrollHeight + \'px\' : \'\'"
                                >
				                    <div class="p-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="dbhost">Database host *</label>
                                                <input name="dbhost" id="dbhost" type="text" class="w-full" value="'.$fields['dbhost']['value'].'" />
                                                <span class="suggestion">(if you do not know what does it means and use X3 CMS in your own computer leave this)</span>
                                            </div>
                                            <div>
                                                <label for="dbsocket">Database socket</label>
                                                <input name="dbsocket" id="dbsocket" type="text" class="w-full" value="'.$fields['dbsocket']['value'].'" />
                                                <span class="suggestion">(if you do not know what does it means and use X3 CMS in your own computer leave this empty)</span>
                                            </div>

                                            <div>
                                                <label for="dbname">Database name *</label>
                                                <input name="dbname" id="dbname" type="text" class="w-full" value="'.$fields['dbname']['value'].'" />
                                            </div>
                                            <div></div>

                                            <div>
                                                <label for="dbuser">Database user *</label>
                                                <input name="dbuser" id="dbuser" type="text" class="w-full" value="'.$fields['dbuser']['value'].'" />
                                                <span class="suggestion">(an user who has all permissions on the database)</span>
                                            </div>
                                            <div>
                                                <label for="dbpass">Database password *</label>
                                                <input name="dbpass" id="dbpass" type="text" class="w-full" value="'.$fields['dbpass']['value'].'" />
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </li>
                            <li class="relative mb-1">

                                <button
                                    type="button"
                                    class="w-full bg rounded px-8 py-6 text-left"
                                    @click="selected !== 2 ? selected = 2 : selected = null"
                                >
                                    <h4>Admin user</h4>
                                    <p>To access the administration panel of X3 CMS</p>
                                </button>
                                <div
                                    class="relative overflow-hidden transition-all max-h-0 duration-700"
                                    style=""
                                    x-ref="container2"
                                    x-bind:style="selected == 2 ? \'max-height: \' + $refs.container2.scrollHeight + \'px\' : \'\'"
                                >
				                <div class="p-6">

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="amail">Administrator email *</label>
                                            <input name="amail" id="amail" type="text" class="w-full" value="'.$fields['amail']['value'].'" />
                                            <span class="suggestion">(will be the default sender email address from the web site)</span>
                                        </div>
                                        <div>
                                            <label for="auser">Administrator username *</label>
                                            <input name="auser" id="auser" type="text" class="w-full" value="'.$fields['auser']['value'].'" />
                                            <span class="suggestion">(at least 5 characters)</span>
                                        </div>

                                        <div>
                                            <label for="auser">Encryption method</label>
                                            <select name="hash" class="w-full" id="hash">'.$hash_options.'</select>
                                            <span class="suggestion">(for tests use MD5 else if you are paranoic you can set SHA512 or WHIRLPOOL)</span>
                                        </div>
                                        <div>
                                            <label for="apass">Administrator password *</label>
                                            <input name="apass" id="apass" type="text" class="w-full" value="'.$fields['apass']['value'].'" />
                                            <span class="suggestion">(at least 5 characters)</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </li>
                        <li class="relative mb-1">

                            <button
                                type="button"
                                class="w-full bg rounded px-8 py-6 text-left"
                                @click="selected !== 3 ? selected = 3 : selected = null"
                            >
                                <h4>Global settings</h4>
                                <p></p>
							</button>

			                <div
                                class="relative overflow-hidden transition-all max-h-0 duration-700"
                                x-ref="container3"
                                x-bind:style="selected == 3 ? \'max-height: \' + $refs.container3.scrollHeight + \'px\' : \'\'"
                            >
				                <div class="p-6">

                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label for="tzone">TimeZone *</label>
                                            <select name="tzone" class="w-full" id="tzone">
                                                '.get_timezone($fields['tzone']['value']).'
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </li>

                    </ul>
                </div>

				<p class="suggestion">NOTE: all fields with * are required</p>

				<div class="flex justify-center space-x-2 py-6">
					<button class="btn link" type="submit">Next</button>
				</div>
			</form>
		</div></div>';
		break;
	case 5:
		// INSTALL DB
		$check = false;
		// db connection
		$dsn = (empty($_SESSION['dbsocket']))
			? 'mysql:host='.$_SESSION['dbhost'].';dbname='.$_SESSION['dbname']
			: 'mysql:unix_socket='.$_SESSION['dbsocket'].';dbname='.$_SESSION['dbname'];

		$db = new PDO($dsn, $_SESSION['dbuser'], $_SESSION['dbpass'], array(PDO::ATTR_PERSISTENT=>'x3cms', PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// get sql
		$query = get_queries(FINAL_ROOT.'cms/files/'.$_SESSION['tmp']);

		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
					<span>+</span>
					<a class="link" href="./index.php?s=1" title="Back to License agreement">Step 1</a>
					<span>+</span>
					<a class="link" href="./index.php?s=2" title="Back to Requirements check">Step 2</a>
					<span>+</span>
					<a class="link" href="./index.php?s=3" title="Back to Permissions check">Step 3</a>
					<span>+</span>
					<a class="link" href="./index.php?s=4" title="Back to Configuration">Step 4</a>
                    <span>+</span>
					<span>Step 5/5</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-1/3 px-4 md:m-auto text-gray-300">
					<h3>Database building</h3>
					<ul>';

		$tables = array();
		foreach ($query AS $q)
		{
			if (!empty($q))
			{
				if (strstr($q, 'CREATE TABLE') == $q)
				{
					$str = explode(' ', $q);
					$t = str_replace('`', '', $str[5]);
					$chk = true;
				}
				else
				{
					$chk = false;
				}

				try
				{
					if ($chk)
					{
						$db->exec('DROP TABLE IF EXISTS '.$t);
						$body .= '<li>TABLE '.$t.' '.OK.'</li>';
						$tables[] = $t;
					}

					$db->exec($q);
					$check = true;
				}
				catch (PDOException $e)
				{
					$db->exec('DROP TABLE IF EXISTS '.implode(', ', $tables));
					$body .= '<li>QUERY '.$q.' '.FAILED.'</li>';
					$check = false;
					$db = null;
					break;
				}
			}
		}
		$body .= '</ul>';

		if ($check)
		{
			// delete db tmp file
			unlink(FINAL_ROOT.'cms/files/'.$_SESSION['tmp']);

			// unset db data
			unset($_SESSION['dbhost'], $_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
			$body .= '<div class="flex justify-center space-x-2 py-6">
                        <a class="btn link" href="./index.php?s=6&r=1" title="Finish">Next</a>
                    </div>';
		}
		else
		{
			$body .= '<p class="failed px-4 py-4 rounded">Database building is incomplete.</p>
                    <div class="flex justify-center space-x-2 py-6">
                        <a class="btn gray" href="./index.php?s=6" title="Not install">Exit</a>
                    </div>';
		}

		$body .= '</div></div>';
		break;
	case 6:
		// END
		$body = '<div class="px-4 py-4 bg flex space-x-2">
					<a class="link" href="./index.php" title="Back to Intro">Intro</a>
					<span>+</span>
					<a class="link" href="./index.php?s=1" title="Back to License agreement">Step 1</a>
					<span>+</span>
					<a class="link" href="./index.php?s=2" title="Back to Requirements check">Step 2</a>
					<span>+</span>
					<a class="link" href="./index.php?s=3" title="Back to Permissions check">Step 3</a>
					<span>+</span>
					<a class="link" href="./index.php?s=4" title="Back to Configuration">Step 4</a>
					<span>+</span>
					<a class="link" href="./index.php?s=5" title="Back to Database Building">Step 5</a>
                    <span>+</span>
					<span>End</span>
				</div>
			</div>
			<div id="page" class="bg-gray-600 py-4">
                <div class="w-full md:w-1/3 px-4 md:m-auto text-gray-300">';

		if (isset($_REQUEST['r']) && $_REQUEST['r'] == 1)
		{
			$domain = get_domain();

			// update .htaccess
			if(function_exists('apache_get_modules'))
			{
				$path = PATH.'file.htaccess';
			}
			else
			{
				$path = PATH.'simple_file.htaccess';
			}

			$hta_root = str_replace('INSTALL/', '', INSTALL_ROOT);
			$txt = @file_get_contents($path);
			$www = str_replace('http://', '', $domain);
			$file = str_replace(array('HTADOMAIN', 'HTAROOT', 'WWWDOMAIN'), array($domain, $hta_root, $www), $txt);
			$check = @file_put_contents (FINAL_ROOT.'.htaccess', $file);
			$check2 = @file_put_contents (FINAL_ROOT.'robots.txt', 'User-agent: *'.NL.'Disallow: /files/'.NL.'Disallow: /admin/'.NL.'Sitemap: '.$_SESSION['domain'].'/sitemap.xml');
			if ($check && $check2)
			{
				@chmod(FINAL_ROOT.'.htaccess', 0755);
				@chmod(FINAL_ROOT.'robots.txt', 0755);
				@chmod(FINAL_ROOT.'cms/config/config.php', 0755);
				$body .= '<h3>Installation completed</h3>
						<p>You have installed X3 CMS successfully!</p>
						<h2>After this step you should delete or rename the INSTALL directory</h2>
						<p>Good luck<br />X3 CMS Team</p>
						<div class="flex justify-center space-x-2 py-6">
							<a class="btn link" href="'.$_SESSION['domain'].'" title="Web site home page">Open the site</a>
						</div>';
			}
			else
			{
				$body .= '<h3>Installation aborted</h3>
					<p class="failed px-4 py-4 rounded">Something went wrong while updating .htaccess file.<br />Check it and try again.</p>';
			}
		}
		else
		{
			$body .= '<h3>Installation aborted</h3>
					<p class="failed px-4 py-4 rounded">Something went wrong.<br />Check it and try again.</p>';
		}
		$body .= '</div></div>';

		break;
}

$foot = '
<footer class="bg  text-center text-xs py-6">
	<a href="http://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> &copy; <a href="http://www.cblu.net" title="CBlu.net - Freelance PHP Developer">CBlu.net</a>
</footer>
</body>
</html>';

echo $head.$body.$foot;
