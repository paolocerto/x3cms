<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
echo '<div id="close-modal" title="'._CLOSE.'"><i class="fas fa-times fa-lg"></i></div>';

echo '<h2>'._UPLOAD_FILE.'</h2>
	<p class="small">'.ucfirst(_FILE_SIZES).' '.MAX_W.'x'.MAX_H.' px - '.ceil(MAX_IMG/1024).' MB / '.ceil(MAX_DOC/1024).' MB</p>';
	
// show message
if (isset($msg))
{
	echo $msg;
}

echo '<div id="msg-container"></div>';

echo $form;

	// build the URL
	$tokens = array();
	if (!empty($category))
	{
		$tokens[] = $category;
	}
	
	if (!empty($subcategory))
	{
		$tokens[] = $subcategory;
	}
	
	$url = (empty($tokens))
		? ''
		: '/'.implode('/', $tokens);
			
?>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent("domready", function()
{
	X3.droppize("upload", "xname", "<?php echo _DROP_MSG ?>");
	X3.single_upload("upload", "xname");
});

function setUploadForm(id_form, input_name) {
	X3.uploadize(id_form, input_name, "topic", "<?php echo BASE_URL.'files/index/'.$id_area.$url ?>");
}
</script>
