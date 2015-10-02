<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

$js = '';
 
switch($file->xtype)
{
case 0:
	// images
	$chk = file_exists(APATH.'files/filemanager/img'.$file->name);
	if (!$chk)
	{
		sleep(1);
	}
?>
<h1><?php echo _IMAGE_EDIT.': '.$file->name ?></h1>
<div id="image_editor"  class="acenter bmiddlegray padded hide-o">
	<img src="<?php echo FPATH.'img/'.$file->name ?>" id="img" />
</div>
<script src="<?php echo ROOT ?>files/js/mootools/Lasso.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Lasso.Crop.js"></script>
<script>
Crop_handler = new Class({
		
	initialize : function(){
		this.ratio = $('ratio').addEvent('change',this.changed.bind(this,'ratio'));
		
		this.minx = $('xcoord').addEvent('change',this.changed.bind(this,'xcoord'));
		this.miny = $('ycoord').addEvent('change',this.changed.bind(this,'ycoord'));
		
		this.maxx = $('width').addEvent('change',this.changed.bind(this,'width'));
		this.maxy = $('height').addEvent('change',this.changed.bind(this,'height'));
		
		this.locx = $('xcoord');
		this.locy = $('ycoord');
		this.locw = $('width');
		this.loch = $('height');
		
		this.cropper = new Lasso.Crop('img', {
			ratio : false,
			preset: [0, 0, <?php echo $width.', '.$height ?>],
			min: [10, 10],
			handleSize: 8,
			opacity: 0.6,
			color: '#666666',
			border: '#ff6600',
			onResize : this.updateCoords.bind(this)
		});
		
		// update zoom
		this.zoom = this.cropper.options.zoom;
		$('zoom').set('value', this.cropper.options.zoom);
		$('zoom_label').set('html', this.cropper.options.zoom);
	},
	
	updateCoords : function(pos){
		if (this.zoom != undefined) {
			this.locx.set('value',pos.x*this.zoom);
			this.locy.set('value',pos.y*this.zoom);
			this.locw.set('value',pos.w*this.zoom);
			this.loch.set('value',pos.h*this.zoom);
		}
	},

	changed : function(flag){
		var x0 = parseInt(this.minx.get('value').trim()), 
			y0 = parseInt(this.miny.get('value').trim()), 
			x1 = parseInt(this.maxx.get('value').trim()), 
			y1 = parseInt(this.maxy.get('value').trim());
			
		switch(flag){
			case 'ratio' :
				if(!this.ratio.checked) {
					this.cropper.options.ratio = false;
				}
				else {
					this.cropper.options.ratio = [x1/this.zoom, y1/this.zoom];
				}
				this.cropper.hideHandlers();
				break;
			case 'xcoord' : 
				this.cropper.reclip(x0, y0, x1, y1, 0);
				break;
			case 'ycoord' :
				this.cropper.reclip(x0, y0, x1, y1, 1);
				break;
			case 'width' : 
				this.cropper.reclip(x0, y0, x1, y1, 2);
				break;
			case 'height' :
				this.cropper.reclip(x0, y0, x1, y1, 3);
				break;
		}
		this.cropper.refreshHandlers();
	}
});

var reset_editor = function() {
	window.location = root+'home/start/files-editor-<?php echo $file->id ?>/'+escape('Image editor');
}

window.addEvent('domready', function()
{
	X3.content('filters','files/filter/0', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	
	// refresh image
	img = $('img').get('src');
	$('img').set('src', img+'<?php echo '?t='.time() ?>');
	$('img').set('width', <?php echo $width ?>);
	$('img').set('height', <?php echo $height ?>);
	
	new Crop_handler();
	
	var slider = $('slider');
	new Slider(slider, slider.getElement('.knob'), {
		range: [0, 359],
		steps: 359,
		snap:true,
		wheel:true,
		onChange: function(value){
			old = parseInt($('rotate').get('value'));
			$('rotate').set('value', value);
			rotateElement('imagethumb', value);
		}
	 });
	
});
</script>
<?php
	// end image case
	break;

case 1:
	// generic file
	
	echo '<h1>'._TEXT_EDIT.': '.$file->name.'</h1>
		'.$form;
		
	$js .= '		
<script>
var reset_editor = function() {
	window.location = root+"home/start/files-editor-'.$file->id.'/"+escape("Template editor");
}

var ratio = 0;
window.addEvent("domready", function()
{
	X3.content("filters","files/filter/0", "'.X4Utils_helper::navbar($navbar, ' . ', false).'");
});
</script>';

	break;



	
case 2:
	// media file
	
	$mimes = array(
		'video/mp4',
		'video/webm',
		'video/ogg',
		'application/ogg',
		'application/vnd.adobe.flash.movie', 
		'application/x-shockwave-flash',
	);
	
	// for swf or flv file no capture
	$capture = '';
			
	switch ($mime)
	{
		case 'video/x-flv':
			// check min width
			$width = ($width > 620)
				? $width
				: 620;
			
			$video = '<div id="player_9330">
						<a href="http://get.adobe.com/flashplayer/">Flash plugin is missing</a>
					</div>';
			
			$js .= '
<script>
var flashvars_9330 = {};
var params_9330 = {
	quality: "high",
	bgcolor: "#333333",
	allowScriptAccess: "always",
	allowFullScreen: "true",
	flashvars: "fichier='.$this->site->site->domain.'/cms/files/filemanager/media/'.$file->name.'"
};
var attributes_9330 = {};
var video_editor_width = windowWidth();	
if (video_editor_width - 590 < '.$width.') {
	w = video_editor_width - 590;
} else {
	w = '.$width.';
}

flashObject("'.$this->site->site->domain.'/files/js/flv_player.swf", "player_9330", w, "'.$height.'", "8", false, flashvars_9330, params_9330, attributes_9330);
</script>';
			break;
			
		case 'application/vnd.adobe.flash.movie':
		case 'application/x-shockwave-flash':
			// swf files
			$video = '';
			
			$js .= '
<script>
// "ProjectId=<%= @project.id.to_s %>&amp;Language=<%= @locale %>"
var flashvars = {};

var params = {
	allowScriptAccess: "sameDomain", 
	allowFullScreen: "true",
	wmode: "opaque",
	quality: "high",
	bgcolor: "#333333",
	menu: "true"
};
var attributes = {}; 
attributes.styleclass="playerBox";
swfobject.embedSWF("'.$this->site->site->domain.'/cms/files/filemanager/media/'.$file->name.'", "video_editor", "'.$width.'", "'.$height.'", "9", "expressInstall.swf", flashvars, params, attributes);
</script>';
			break;
			
		case 'video/mp4':
		case 'video/webm':
		case 'video/ogg':
		case 'application/ogg':
			$video = '<video id="movie" preload controls><source id="source" src="'.FPATH.'media/'.$file->name.'" />Your browser does not support the video tag.</video>';
				
			// to capture a frame frmo a video
			$capture = '
	$("capture").addEvent("click", function() {
		if (this.checked) {
			$("video_section").hide();
			$("image_section").show();
		} else {
			$("image_section").hide();
			$("video_section").show();
		}
	});
	
	v = $("movie");
	v.onpause = function() {
		$("sec").set("value", v.currentTime);
	}
	
	v.onseeked = function() {
    	$("sec").set("value", v.currentTime);
    };';
    		break;
	}
	
	echo '<h1>'._VIDEO_EDIT.': '.$file->name.'</h1>
		<div id="video_editor" class="band acenter bmiddlegray padded hide-o">
			'.$video.'
		</div>
		'._VIDEO_EDIT_MSG;
		
	$js .= '
		
<script>
var reset_editor = function() {
	window.location = root+"home/start/files-editor-'.$file->id.'/"+escape("Video editor");
}

var ratio = 0;
window.addEvent("domready", function()
{
	$("image_section").hide();
	
	X3.content("filters","files/filter/0", "'.addslashes(X4Utils_helper::navbar($navbar, ' . ', false)).'");
	
	$("ratio").addEvent("change", function() {
		if (this.checked) {
			ratio = $("width").get("value")/$("height").get("value");
		} else {
			ratio = 0;
		}
	});
	
	$("width").addEvent("change", function() {
		w = parseInt(this.get("value"));
		if (ratio > 0 && w > 0) {
			$("height").set("value", Math.round(w/ratio));
		}
	});
	
	$("height").addEvent("change", function() {
		h = parseInt(this.get("value"));
		if (ratio > 0 && h > 0) {
			$("width").set("value", Math.round(ratio*h));
		}
	});
	
	'.$capture.'
});
</script>';
	break;



	
case 3:
	// templates
	
	echo $tinymce;
	
	echo '<h1>'._TEMPLATE_EDIT.': '.$file->name.'</h1>
		<p>'._TEMPLATE_MSG.'</p>
		'.$form;
		
	$js .= '
<script>
var reset_editor = function() {
	window.location = root+"home/start/files-editor-'.$file->id.'/"+escape("Template editor");
}

var ratio = 0;
window.addEvent("domready", function()
{
	X3.content("filters","files/filter/0", "'.addslashes(X4Utils_helper::navbar($navbar, ' . ', false)).'");
});
</script>';
	
	break;
}

echo $js;
	
