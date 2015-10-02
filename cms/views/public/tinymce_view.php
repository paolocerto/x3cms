<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

$now = time();

?>
<!-- TinyMCE -->
<script src="<?php echo ROOT ?>files/js/tiny_mce/tiny_mce.js"></script>
<script>
	tinyMCE.init({
		// General options
		language : "<?php echo X4Route_core::$lang ?>",
		mode : "specific_textareas",
		editor_deselector : "NoEditor",
		theme : "advanced",
<?php
if (RTL) echo 'directionality : "rtl",';
?>

		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,preview,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,removeformat,visualaid,|,fullscreen,preview,|,link,unlink,anchor,image,media,cleanup,code",
		theme_advanced_buttons2 : "bold,italic,underline,strikethrough,cite,del,ins,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup",
		theme_advanced_buttons3 : "tablecontrols,|,insertdate,inserttime,|,pagebreak,|,charmap,iespell,hr,|,template",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		
		plugin_insertdate_dateFormat : "%d/%m/%Y",
		plugin_insertdate_timeFormat : "%H:%M:%S",
		relative_urls : false,
		remove_script_host : true,
		document_base_url : "<?php echo substr(ROOT, 0, -1) ?>",
		
		extended_valid_elements : "a[href|title|class|onclick|id|name],img[class|src|alt|style|onmouseover|onmouseout|name],span[class|style],hr[class|style],code",
		
		// Example content CSS (should be your site CSS)
		content_css : "<?php echo THEME_URL ?>css/tinymce.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?php echo ROOT ?>cms/files/js/template_list.js?t=<?php echo $now ?>",
		external_link_list_url : "<?php echo ROOT ?>cms/files/js/link_list.js?t=<?php echo $now ?>",
		external_image_list_url : "<?php echo ROOT ?>cms/files/js/image_list.js?t=<?php echo $now ?>",
		media_external_list_url : "<?php echo ROOT ?>cms/files/js/media_list.js?t=<?php echo $now ?>",
		
		pagebreak_separator : "<!--pagebreak-->"
<?php
if ($tb) {
?>
		,file_browser_callback : "tinyBrowser"
<?php
}
?>
		
	});
	
	function toggleEditor(id) {
		if (!tinyMCE.get(id))
			tinyMCE.execCommand('mceAddControl', false, id);
		else
			tinyMCE.execCommand('mceRemoveControl', false, id);
	}
	
</script>
<!-- /TinyMCE -->
