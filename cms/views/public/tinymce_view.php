<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

?>
<!-- TinyMCE -->
<script>
// reset
try {
tinyMCE.remove('textarea');
} catch (e) {}

setTimeout(function(){Tinit();},2);

// init
function Tinit() {
	tinyMCE.init({
		// General options
		selector: "textarea",
		theme: "modern",
		skin : "lightgray",

		language : "<?php echo X4Route_core::$lang ?>",
		selector: "textarea:not(.NoEditor)",

		autosave_interval: "30s",

<?php
if (RTL || isset($rtl)) echo 'directionality : "rtl",';
?>
		 plugins: [
		    "advlist autolink autosave lists link image imagetools charmap print preview hr anchor pagebreak",
		    "searchreplace wordcount visualblocks visualchars code fullscreen",
		    "insertdatetime media nonbreaking save table contextmenu directionality",
		    "emoticons template paste textcolor colorpicker textpattern",
			"responsivefilemanager importcss youtube"
		],

		autosave_ask_before_unload: false,

		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | responsivefilemanager | print preview media youtube | forecolor backcolor emoticons",

		imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",

		toolbar_items_size: 'small',

		style_formats: [
		    {title: 'Headers', items: [
		        {title: 'h1', block: 'h1'},
		        {title: 'h2', block: 'h2'},
		        {title: 'h3', block: 'h3'},
		        {title: 'h4', block: 'h4'},
		        {title: 'h5', block: 'h5'},
		        {title: 'h6', block: 'h6'}
		    ]},

		    {title: 'Blocks', items: [
		        {title: 'p', block: 'p'},
		        {title: 'div', block: 'div'},
		        {title: 'pre', block: 'pre'}
		    ]},

		    {title: 'Containers', items: [
		        {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
		        {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
		        {title: 'blockquote', block: 'blockquote', wrapper: true},
		        {title: 'hgroup', block: 'hgroup', wrapper: true},
		        {title: 'aside', block: 'aside', wrapper: true},
		        {title: 'figure', block: 'figure', wrapper: true}
		    ]}
		],
		visualblocks_default_state: true,
		end_container_on_empty_block: true,

		image_advtab: true,

		insertdatetime_formats: ["%H:%M:%S", "%Y-%m-%d", "%d/%m/%Y", "%I:%M:%S %p", "%D"],

		relative_urls : false,
		remove_script_host : true,
		document_base_url : "<?php echo (ROOT == '/') ? $this->site->site->domain : str_replace(ROOT, '', $this->site->site->domain.'/') ?>",

		extended_valid_elements : "article[class],header[class],section[class],div[class],p[class|style],a[href|title|class|onclick|id|name|rel|rev],figure[class],img[id|class|src|alt|style|onmouseover|onmouseout|name],span[class|style],hr[class|style],div[id|class|style],code,em[class],ul[class],ol[class],i[class]",
		invalid_elements : "script",

		// Example content CSS (should be your site CSS)
		importcss_append: true,
		content_css : "<?php echo THEME_URL ?>css/tinymce.css",

		template_selected_content_classes: ".fake",

		// Drop lists for link/image/media/template dialogs
		templates : "<?php echo BASE_URL.'admin/files/js/'.$id_area.'/template' ?>",
		link_list : "<?php echo BASE_URL.'admin/files/js/'.$id_area.'/files' ?>",
		image_list : "<?php echo BASE_URL.'admin/files/js/'.$id_area.'/img' ?>",
		media_list : "<?php echo BASE_URL.'admin/files/js/'.$id_area.'/media' ?>",

		pagebreak_separator : "<!--pagebreak-->",

		external_filemanager_path : "<?php echo ROOT ?>files/js/filemanager/",
		filemanager_title : "Filemanager" ,
		filemanager_access_key : "myPrivateKey"

	});
}
</script>
<!-- /TinyMCE -->
