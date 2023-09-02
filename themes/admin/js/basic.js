/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/* BASIC JS to handle back and reload actions */
//!window.location.href.includes("debug") && 
if (document.getElementById('topic') == undefined)
{
  var url = window.location.href.split('/admin/');
  window.location.href = url[0]+'/admin/home/start/'+ url[1].replace(/\//g, '-');
}