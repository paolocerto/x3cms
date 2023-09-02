<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

// include Spreadsheet-Reader lib

$base_dir = dirname(__FILE__);
require_once($base_dir.'/spreadsheet-reader/php-excel-reader/excel_reader2.php');
require($base_dir.'/spreadsheet-reader/SpreadsheetReader.php');
