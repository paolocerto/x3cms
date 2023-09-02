<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
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
		theme: "silver",
		skin : "oxide",
		branding: false,

        language : "<?php echo X4Route_core::$lang ?>",
        selector: "textarea:not(.NoEditor)",
        paste_as_text: true,
		autosave_interval: "30s",
		setup: function (editor) {
			editor.on('change', function () {
				tinymce.triggerSave();
			});
		},

<?php
if (RTL || isset($rtl)) echo 'directionality : "rtl",';
?>
         plugins: [
            "advlist autolink autosave lists link imagetools hr anchor pagebreak",
            "searchreplace visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking directionality",
            "template paste textpattern",
            "responsivefilemanager importcss"
        ],

        autosave_ask_before_unload: false,
        menubar: false,
        toolbar1: "styleselect forecolor bold italic alignleft aligncenter alignright alignjustify bullist numlist blockquote link code",

        toolbar_items_size: 'small',

<?php
if (isset($style_formats))
{
    echo 'style_formats: '.$style_formats.',';
}

/*
style_formats = [
            [
                'title' =>  'Headers',
                'items' => [
                    ['title' => 'h1', 'block' => 'h1'],
                    ['title' => 'h2', 'block' => 'h2'],
                    ['title' => 'h3', 'block' => 'h3'],
                    ['title' => 'h4', 'block' => 'h4'],
                    ['title' => 'h5', 'block' => 'h5'],
                    ['title' => 'h6', 'block' => 'h6']
                ]
            ],

            [
                'title' => 'Blocks',
                'items' => [
                    ['title' => 'p', 'block' => 'p']
                ]
            ],

            [
                'title' => 'Containers',
                'items' => [
                    ['title' => 'blockquote', 'block' => 'blockquote', 'wrapper' => true]
                ]
            ]
        ];
*/
?>
        /*
        style_formats: [
            {
                title: 'Headers',
                items: [
                    {title: 'h1', block: 'h1'},
                    {title: 'h2', block: 'h2'},
                    {title: 'h3', block: 'h3'},
                    {title: 'h4', block: 'h4'},
                    {title: 'h5', block: 'h5'},
                    {title: 'h6', block: 'h6'}
                ]
            },

            {title: 'Blocks', items: [
                {title: 'p', block: 'p'}
            ]},

            {title: 'Containers', items: [
                {title: 'blockquote', block: 'blockquote', wrapper: true}
            ]}
        ],

        image_advtab: false,
        image_dimensions: false,

        insertdatetime_formats: ["%H:%M:%S", "%Y-%m-%d", "%d/%m/%Y", "%I:%M:%S %p", "%D"],
        */

        visualblocks_default_state: true,
        end_container_on_empty_block: true,

        remove_script_host : false,
        document_base_url : "<?php echo (ROOT == '/') ? $this->site->site->domain : str_replace(ROOT, '', $this->site->site->domain.'/') ?>",
        relative_urls : false,

        extended_valid_elements : "p[class|style],a[href|title|atclick|target],em,span[class|style],button[type|class|atclick]",
        invalid_elements : "script",

        // Example content CSS (should be your site CSS)
        importcss_append: true,
<?php
if (file_exists(PATH.'themes/'.THEME_URL.'/css/tinymce'.$id_area.'.css'))
{
	// to set a personalized CSS
	echo 'content_css : "'.THEME_URL .'css/tinymce'.$id_area.'.css",';
}
else
{
    // dedicated small CSS
	echo 'content_css : "'.THEME_URL .'css/tinymce_small.css",';
}
?>

        template_selected_content_classes: ".fake",

        // Drop lists for link/image/media/template dialogs
        //templates : "<?php echo BASE_URL.'files/js/'.$id_area.'/template' ?>",
        link_list : "<?php echo BASE_URL.'files/js/'.$id_area.'/files' ?>",
        //image_list : "<?php echo BASE_URL.'files/js/'.$id_area.'/img' ?>",
        //media_list : "<?php echo BASE_URL.'files/js/'.$id_area.'/media' ?>",

        //pagebreak_separator : "<!--pagebreak-->",
        /*
        external_filemanager_path : "<?php echo ROOT ?>files/js/filemanager/",
        filemanager_title : "Filemanager" ,
        filemanager_access_key : "myPrivateKey"
        */
    });
}
</script>
