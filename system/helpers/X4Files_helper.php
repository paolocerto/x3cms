<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Helper for files handling
 * 
 * @package X4WEBAPP
 */
class X4Files_helper 
{
	/**
	 * Arrays of handled mimetypes
	 */
	private static $mimg = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
	private static $mmedia = array('video/quicktime', 'application/vnd.rn-realmedia', 'audio/x-pn-realaudio', 'application/vnd.adobe.flash.movie', 'application/x-shockwave-flash', 'video/x-ms-wmv', 'video/avi', 'video/msvideo', 'video/x-msvideo','video/mpeg', 'video/mp4', 'video/3gpp', 'video/x-flv', 'video/ogg', 'application/ogg');
	private static $mtemplate = array('text/html');
// TODO: this must be defined in the administration
	private static $mfiles = array('application/vnd.ms-excel', 'application/pdf', 'application/zip', 'application/x-zip', 'application/x-zip-compressed', 'binary/octet-stream', 'audio/mpeg', 'audio/wav', 'audio/x-wav', 'application/x-compress', 'application/x-compressed', 'multipart/x-zip');
	private static $file = array();
	
	private static function phpv()
	{
		$v = explode('.', phpversion());
		return floatval($v[0].'.'.$v[1]);
	}
	
	// path to the folder managed by filemanager
	private static $file_path = 'files/filemanager/'; 
	
	/**
	 * Get a code related to the file type
	 *
	 * @param	string	file with path
	 * @param	boolean	switch between folder name and X3 CMS file type
	 * @return	mixed	code type or folder name
	 */
	public static function get_type($file, $folder = false) 
	{
		// this require PHP 5.3+
		if (self::phpv() >= 5.3)
		{
			$finfo = @new finfo(FILEINFO_MIME);
			$info = @$finfo->file($file);
			
			$mime = explode(';', $info);
			$type = explode('/', $mime[0]);
			
			switch ($type[0])
			{
				case 'img':
				case 'image':
					return ($folder)
						? 'img'
						: 0;
					break;
				case 'video':
					return ($folder)
						? 'media'
						: 2;
					break;
				case 'text':
					switch($type[1])
					{
					case 'htm':
					case 'html':
						return ($folder)
							? 'template'
							: 3;
						break;
					default:
						return ($folder)
							? 'files'
							: 1;
						break;
					}
					break;
				case 'xls':
				default:
					return ($folder)
						? 'files'
						: 1;
					break;
			}
		}
		else
		{
			// get by name
			self::get_type_by_name($file, $folder);
		}
	}
	
	/**
	 * Get a code related to the file type by file name
	 *
	 * @param	string	file with path
	 * @param	boolean	switch between folder name and X3 CMS file type
	 * @return	mixed	code type or folder name
	 */
	public static function get_type_by_name($file, $folder = false) 
	{
		// get extension from file name
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		switch($ext) 
		{
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
				return ($folder)
					? 'img'
					: 0;
				break;
			case 'swf':
			case 'flv':
			case 'rm':
			case 'wmv':
			case 'mov':
			case 'mp4':
			case 'ogv':
			case 'webm':
			case 'avi':
			case 'mpeg':
			case '3gp':
				return ($folder)
					? 'media'
					: 2;
				break;
			case 'htm':
			case 'html':
				return ($folder)
					? 'template'
					: 3;
				break;
			case 'xls':
			default:
				return ($folder)
					? 'files'
					: 1;
				break;
		}
	}
	
	/**
	 * Get the mimetype of a file
	 *
	 * @param	string	file with path
	 * @return	string
	 */
	public static function get_mime($file)
	{
		// this require PHP 5.3+
		if (self::phpv() >= 5.3)
		{
			$finfo = @new finfo(FILEINFO_MIME);
			$info = @$finfo->file($file);
			
			$mime = explode(';', $info);
			return $mime[0];
		}
		else
		{
			// old way
			// get mime from file extension
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			switch($ext) 
			{
				case 'jpg':
				case 'jpeg':
					return 'image/jpeg';
					break;
				case 'gif':
					return 'image/gif';
					break;
				case 'png':
					return 'image/png';
					break;
				case 'swf':
					return 'application/x-shockwave-flash';
					break;
				case 'flv':
					return 'video/x-flv';
					break;
				case 'rm':
					return 'application/vnd.rn-realmedia';
					break;
				case 'wmv':
					return 'video/x-ms-wmv';
					break;
				case 'mov':
					return 'video/quicktime';
					break;
				case 'mp4':
					return 'video/mp4';
					break;
				case 'webm':
					return 'video/webm';
					break;
				case 'ogv':
					return 'video/ogg';
					break;
				case '3gp':
					return 'video/3gpp';
					break;
				case 'mpeg':
					return 'video/mpeg';
					break;
				case 'avi':
					return 'video/avi';
					break;
				case 'htm':
				case 'html':
					return 'text/html';
					break;
				case 'zip':
					return 'application/zip';
					break;
				case 'pdf':
				    return 'application/pdf';
				    break;
				case 'xls':
				    return 'application/vnd.ms-excel';
				    break;
				default:
					return 'binary/octet-stream';
					break;
			}
		}
	}
	
	/**
	 * File upload
	 * Perform the upload with all required checks: file size, image size form images (crop and resize), check for overwrite and add prefixes, store the file inthe right folder.
	 *
	 * @param string	the name of the field
	 * @param string	the path where to store the file. The standard path is APATH.'files/filemanager/ '
	 * @param string	a prefix for the filename
	 * @param boolean	zip the file  if it not an image and is set to true
	 * @param array		the array contains (maximum width, maximum height, action_string, maximum file weight (kBytes) for images, maximum file weight (KBytes) for others documents). Possibles values for the action string are: 'NONE', 'CROP', 'RESIZE'
	 * @param array		the array contains valid mime types
	 * @return mixed	the filename string if upload only a file, an array of filename if upload many files
	 */
	public static function upload($file, $path, $prefix = '', $zip = 0, $limits = array(MAX_W, MAX_H, 'NONE', MAX_IMG, MAX_DOC), $mimes = array())
	{
		if (isset($_GET[$file]))
		{
			self::$file = new Upload_file_xhr($file);
			return X4Files_helper::gupload_file($file, $path, $prefix, $zip, $limits, $mimes);
		}
		else if (isset($_FILES[$file]))
		{
			return (is_array($_FILES[$file]['name'])) 
				? X4Files_helper::upload_files($file, $path, $prefix, $zip, $limits, $mimes) 
				: X4Files_helper::upload_file($file, $path, $prefix, $zip, $limits, $mimes);
		}
	}
	
	/**
	 * filter mimes array with mimes available in X3 CMS and return final file folder
	 *
	 * @param array	$mimes		the array contains required mime types
	 * @param string $file_type	the mime type of the uploaded file
	 * @return  string
	 */
	private static function filter_mime($mimes, $file_type)
	{
		$mime = array();
		$mime['img'] = (empty($mimes)) ? self::$mimg : array_intersect(self::$mimg, $mimes);
		$mime['media'] = (empty($mimes)) ? self::$mmedia : array_intersect(self::$mmedia, $mimes);
		$mime['template'] = (empty($mimes)) ? self::$mtemplate : array_intersect(self::$mtemplate, $mimes);
		$mime['files'] = (empty($mimes)) ? self::$mfiles : array_intersect(self::$mfiles, $mimes);
		
		// set default type if mimes is emtpy
		$type = (empty($mimes)) 
			? 'files'
			: '';

		// find right type
		foreach($mime as $k => $v) 
		{
			if (in_array($file_type, $v)) 
			{
				$type = $k;
			}
		}
		
		return $type;
	}
	
	/**
	 * Switch by available actions
	 *
	 * @param string $action	action to perform
	 * @param string $source	source file
	 * @param string $destination	destination file
	 * @param array	 $limits	data to perform the action
	 * @return  boolean
	 */
	private static function set_action($action, $source, $destination, $limits)
	{
		// actions
		switch($action)
		{
		case 'CROP': 
			$check = X4Files_helper::create_cropped($source, $destination, $limits);
			break;
		case 'RESIZE': 
			$check = X4Files_helper::create_resized($source, $destination, $limits, true, true);
			break;
		case 'SCALE': 
			$check = X4Files_helper::create_resized($source, $destination, $limits, false);
			break;
		case 'FIT':
			$check = X4Files_helper::create_fit($source, $destination, $limits, true);
			break;
		default:
			$check = 1;
		}
		return $check;
	}
	
	private static function gupload_file($file, $path, $prefix, $zip, $limits, $mimes)
	{
		$action = '';
		
		// get the file name
		$filename = self::$file->get_name($file);
		
		// mime, prefix and suffix
		if ($prefix == '__secret') 
		{
			$type =  '';
			$prefix = '';
			$suffix = '.x4w';
		}
		else 
		{
			$type = self::filter_mime($mimes, X4Files_helper::get_mime($filename));
			$suffix = '';
		}
		
		// check mime and type
		if (!empty($mimes) && empty($type)) 
		{
			header('Location: '.BASE_URL.'msg/message/_bad_mimetype');
			die;
		}
		
		// check size step 1
		if ($type == 'img' && $path == APATH.self::$file_path) 
		{
			// too big
			if (self::$file->get_size() > ($limits[3]*1024)) 
			{
				header('Location: '.BASE_URL.'msg/message/_file_size_is_too_big');
				die;
			}
		}
		else 
		{
			// too big
			if (self::$file->get_size() > ($limits[4]*1024) && $path == APATH.self::$file_path) 
			{ 
				header('Location: '.BASE_URL.'msg/message/_file_size_is_too_big');
				die;
			}
		}
		
		// file name
		$tmpname = X4Utils_helper::unspace(strtolower($prefix.self::$file->get_name($file)));
		// exists? added suffix
		$name = X4Files_helper::get_final_name($path.$type.'/', $tmpname.$suffix);
		// copy
		$check = self::$file->save($path.$type.'/'.$name);
		
		if ($check)	
		{
			// check size step 2
			if ($type == 'img' && $path == APATH.self::$file_path) 
			{
				// pixel dimensions
				$imageinfo = getImageSize(realpath($path.$type.'/'.$name));
				if (!X4Files_helper::checkImageSize($imageinfo, $limits)) 
				{
					if ($limits[2] == 'NONE') 
					{
						unlink($path.$type.'/'.$name);
						header('Location: '.BASE_URL.'msg/message/_image_size_is_too_big');
						die;
					}
					else 
					{
						$action = $limits[2];
					}
				}
			}
			
			// switch between actions
			$check = self::set_action($action, $path.$type.'/'.$name, $path.$type.'/'.$name, $limits);
			
			// return or delete
			if ($check)
			{
				return $name;
			}
			else
			{
				unlink($path.$type.'/'.$name);
			}
		}
		else 
		{
			header('Location: '.BASE_URL.'msg/message/_upload_error');
		}
		die;
	}
	
// TODO: merge with upload_files
	private static function upload_file($file, $path, $prefix, $zip, $limits, $mimes)
	{
		$names = '';
		$errors = array();
		
		if (is_uploaded_file($_FILES[$file]['tmp_name'])) 
		{
			$type = self::filter_mime($mimes, $_FILES[$file]['type']);
			
			if (!empty($mimes) && empty($type)) 
			{
				$errors[$file][] = '_bad_mimetype';
			}
			
			$suffix = '';
			$action = '';
			
			// checks for canonical files 
			if ($path == APATH.self::$file_path)
			{
				if ($type == 'img') 
				{
					// too big
					if ($_FILES[$file]['size'] > ($limits[3]*1024)) 
					{
						$errors[$file][] = '_file_size_is_too_big';
					}
					
					// pixel dimensions
					$imageinfo = getImageSize($_FILES[$file]['tmp_name']);
					if (!X4Files_helper::checkImageSize($imageinfo, $limits)) 
					{
						if ($limits[2] == 'NONE') 
						{
							$errors[$file][] = '_image_size_is_too_big';
						}
						else
						{
							$action = $limits[2];
						}
					}
				}
				else 
				{
					// too big
					if ($_FILES[$file]['size'] > ($limits[4]*1024) ) 
					{ 
						$errors[$file][] = '_file_size_is_too_big';
					}
				}
			}
			
			// handle type and folders
			$type = ($path == APATH.self::$file_path)
				? $type.'/'
				: '';
			
			// file name
			$tmpname = X4Utils_helper::unspace(strtolower($prefix.$_FILES[$file]['name']));
			// exists? added suffix
			$name = X4Files_helper::get_final_name($path.$type, $tmpname.$suffix);
			
			if (empty($errors))
			{
				// copy
				$check = X4Files_helper::copy_file($path.$type, $name, $_FILES[$file]['tmp_name']);
				if ($check)	
				{
					// define the filename with the complete path
					$filename = $path.$type.$name;
					
					// switch between actions
					// NOTE: source and destination are the same
					$check = self::set_action($action, $filename, $filename, $limits);
					
					// set return or delete
					if ($check)	
					{
						// handle zip
						if ($zip)
						{
							$check = X4Files_helper::zip_file($filename);
							
							if (!$check)
							{
								unlink($filename);
								$errors[$file][] = '_zip_error';
							}
						}
					}
					else 
					{
						// delete the file
						unlink($filename);
					}
				}
				else 
				{
					$errors[$file][] = '_upload_error';
				}
			}
		}
		
		if (!empty($errors))
		{
			// if errors
			if (!empty($filename))
			{
				// delete the file
				unlink($filename);
			}
			return array($errors, 0);
		}
		else if (empty($errors))
		{
			// if no errors
			if (!empty($filename))
			{
				// return the file name (only the name, no path)
				return $name;
			}
			else
			{
				$errors[$file][] = '_upload_error';
				return array($errors, 0);
			}
		}
	}
	
	/**
	 * Files upload
	 * Perform the upload of an array of files with all required checks: file size, image size form images (crop and resize), check for overwrite and add prefixes, store the file inthe right folder.
	 *
	 * @param string	the name of the field
	 * @param string	the path where to store the file. The standard path is APATH.'files/filemanager/'
	 * @param string	a prefix for the filename
	 * @param boolean	zip the file  if it not an image and is set to true
	 * @param array		the array contains (maximum width, maximum height, action_string). Possibles values for the action string are: 'NONE', 'CROP', 'RESIZE', 'SCALE'
	 * @param array		the array contains valid mime types
	 * @return mixed	the filename string if upload only a file, an array of filename if upload many files
	 */
	private static function upload_files($file, $path, $prefix, $zip, $limits, $mimes)
	{
		$names = array();
		$error = array();
		$n = sizeof($_FILES[$file]['tmp_name']);
		
		for($i = 0; $i < $n; $i++) 
		{
			if (is_uploaded_file($_FILES[$file]['tmp_name'][$i])) 
			{
				// define the final folder
				$type = self::filter_mime($mimes, $_FILES[$file]['type'][$i]);
				$suffix = '';
				$action = '';
				
				if (!empty($mimes) && empty($type)) 
				{
					$errors[$file][] = '_bad_mimetype';
				}
				
				if ($type == 'img' && $path == APATH.self::$file_path) 
				{
					// too big
					if ($_FILES[$file]['size'][$i] > ($limits[3]*1024)) 
					{ 
						$errors[$file][] = '_file_size_is_too_big';
					}
					
					// pixel dimensions
					$imageinfo = getImageSize($_FILES[$file]['tmp_name'][$i]);
					if (!X4Files_helper::checkImageSize($imageinfo, $limits)) 
					{
						if ($limits[2] == 'NONE') 
						{
							$errors[$file][] = '_image_size_is_too_big';
						}
						else
						{
							$action = $limits[2];
						}
					}
				}
				else 
				{
					// too big
					if ($_FILES[$file]['size'][$i] > ($limits[4]*1024) && $path == APATH.self::$file_path) 
					{ 
						$errors[$file][] = '_file_size_is_too_big';
					}
				}
				
				// handle type and folders
				$type = ($path == APATH.self::$file_path)
					? $type.'/'
					: '';
							
				// file name
				$tmpname = X4Utils_helper::unspace(strtolower($prefix.$_FILES[$file]['name'][$i]));
				// exists?
				$name = X4Files_helper::get_final_name($path.$type, $tmpname);
				
				if (empty($errors))
				{
					// copy
					$check = X4Files_helper::copy_file($path.$type.'/', $name, $_FILES[$file]['tmp_name'][$i]);
					if ($check)	
					{
						// define the filename with the complete path
						$filename = $path.$type.$name;
				
						// switch between actions
						// NOTE: source and destination are the same
						$check = self::set_action($action, $filename, $filename, $limits);
						
						// return or delete
						if ($check)
						{
							$names[$name] = $filename;
						}
						else
						{
							unlink($filename);
						}
					}
					else 
					{
						$errors[$file][] = '_upload_error';
					}
				}
			}
			else 
			{
				if (empty($names)) 
				{
					$errors[$file][] = '_upload_error';
				}
			}
		}
		
		if (empty($errors))
		{
			return array(array_keys($names), 1);
		}
		else
		{
			foreach($names as $k => $v)
			{
				// delete the file
				unlink($v);
			}
			return array($errors, 0);
		}
	}
	
	/**
	 * Check image size
	 *
	 * @param array		the size of the image
	 * @param array		the array contains (maximum width, maximum height)
	 * @return boolean	true if image size is lower than image size limits
	 */
	private static function checkImageSize($size, $limits = array(MAX_W, MAX_H)) 
	{
		return ($size[0] <= $limits[0] && $size[1] <= $limits[1]) 
			? true 
			: false;
	}
	
	/**
	 * Create a resized file image
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param array		the array contains (maximum width, maximum height)
	 * @param boolean	if true force the creation of the file even if smaller than required
	 * @return boolean
	 */
	public static function create_fit($src_img, $new_img, $sizes, $force = false)
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		
		if ($w > $sizes[0] || $h > $sizes[1]) 
		{
			// get minimum scale
			$sx = $sizes[0]/$w;
			$sy = $sizes[1]/$h;
			$s = max($sx, $sy);
			
			$sw = ceil($sizes[0]/$s);
			$sh = ceil($sizes[1]/$s);
			
			// centers the picture
			$swo = round(($w-$sw)/2);
			$sho = round(($h-$sh)/2);
			
			// create the file
			$res = self::create_image($image_type,
				$src_img, 
				$new_img,
				array(
					'w' => $sizes[0],
					'h' => $sizes[1],
					'dst_x' => 0,
					'dst_y' => 0,
					'src_x' => $swo,
					'src_y' => $sho,
					'dst_w' => $sizes[0],
					'dst_h' => $sizes[1],
					'src_w' => $sw,
					'src_h' => $sh
				)
			);
			
			return $res; 
		}
		else 
		{
			if ($force)
			{
				copy($src_img, $new_img);
				return 1;
			}
		}
		return 0;
	}
	
	/**
	 * Create a resized file image
	 *
	 * @param string	$src_img source image path to file
	 * @param string	$new_img destination image path to file
	 * @param array		$sizes the array contains (maximum width, maximum height)
	 * @param boolean	$resize if true re-frame the image
	 * @param boolean	$center if true put the new image in the center
	 * @param array		the array color of the background
	 * @return boolean
	 */
	public static function create_resized($src_img, $new_img, $sizes, $resize = true, $center = true, $rgb = array(255, 255, 255))
	{
		// get original sizes
		list($w, $h, $image_type) = getimagesize($src_img);
		
		$res = false;
		if ($w && $h)
		{
		    // get new sizes
		    $nw =  ($w > $sizes[0]) 
		        ? $sizes[0] 
		        : $w;
		    $nh = ($h > $sizes[1]) 
		        ? $sizes[1] 
		        : $h;
		    $ratio = $w/$h;
		    
		    if ($nw < $w) 
		    {
		        $nh = min($sizes[1], floor($nw/$ratio));	// too large, get h
		    }
		    if ($nh < $h)
		    {
		        $nw = floor($nh*$ratio);	// too high, get w
		    }
		    
		    // recursive reduction
		    while($nw > $w || $nh > $h) 
		    {
		        if ($nw < $w) 
		        {
		            $nh = min($sizes[1], floor($nw/$ratio));	// too large, get h
		        }
		        if ($nh < $h)
		        {
		            $nw = floor($nh*$ratio);	// too high, get w
		        }
		    }
		    
		    // handle options
		    $dx = $dy = 0;
		    if ($resize)
		    {
		        // final width and height are fixed
		        $fw = $sizes[0];
		        $fh = $sizes[1];
		        
		        if ($center)
		        {
		            // centerize
		            $dx = ($nw < $sizes[0])
		                ? floor(($sizes[0] - $nw)/2)
		                : 0;
		                
		            $dy = ($nh < $sizes[1])
		                ? floor(($sizes[1] - $nh)/2)
		                : 0;
		        }
		    }
		    else
		    {
		        // scale, final width and height are the result of the resize with the original ratio
		        $fw = $nw;
		        $fh = $nh;
		    }
		    
		    // create the file
		    $res = self::create_image($image_type,
		        $src_img, 
		        $new_img,
		        array(
		            'w' => $fw,
		            'h' => $fh,
		            'dst_x' => $dx,
		            'dst_y' => $dy,
		            'src_x' => 0,
		            'src_y' => 0,
		            'dst_w' => $nw,
		            'dst_h' => $nh,
		            'src_w' => $w,
		            'src_h' => $h
		        ),
		        $rgb
		    );
		}
		
		return $res;
	}
	
	/**
	 * Create a scaled thumb from an image
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param array		the array contains (width, height, start_width, start_height, scalex, scaley)
	 * @return boolean
	 */
	public static function create_thumb($src_img, $new_img, $sizes)
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		
		$nw = ceil($sizes[0] * $sizes[4]);
		$nh = ceil($sizes[1] * $sizes[5]);
		
		// create the file
		$res = self::create_image($image_type,
			$src_img, 
			$new_img,
			array(
				'w' => $nw,
				'h' => $nh,
				'dst_x' => 0,
				'dst_y' => 0,
				'src_x' => $sizes[2],
				'src_y' => $sizes[3],
				'dst_w' => $nw,
				'dst_h' => $nh,
				'src_w' => $sizes[0],
				'src_h' => $sizes[1]
			)
		);
		return $res;
	}
	
	/**
	 * Crop an image file
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param array		the array contains (maximum width, maximum height)
	 * @param array		the array contains source coordinates (X coords, Y coords)
	 * @param boolean	force creation
	 * @return boolean
	 */
	public static function create_cropped($src_img, $new_img, $sizes, $coords = array(0, 0), $force = false)
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		if ($w > $sizes[0] || $h > $sizes[1]) 
		{
			$nw = ($w > $sizes[0]) ? $sizes[0] : $w;
			$nh = ($h > $sizes[1]) ? $sizes[1] : $h;
			
			// create the file
			$res = self::create_image($image_type,
				$src_img, 
				$new_img,
				array(
					'w' => $nw,
					'h' => $nh,
					'dst_x' => 0,
					'dst_y' => 0,
					'src_x' => $coords[0],
					'src_y' => $coords[1],
					'dst_w' => $nw,
					'dst_h' => $nh,
					'src_w' => $nw,
					'src_h' => $nh
				)
			);
			return $res;
		}
		else if ($force)
		{
				copy($src_img, $new_img);
		}
		return 1;
	}
	
	/**
	 * Create an extended file image
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param array		the array contains (new width and new height)
	 * @param array		the array color of the border
	 * @return boolean
	 */
	public static function create_extended($src_img, $new_img, $sizes, $rgb = array(255, 255, 255))
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		
		// create the file
		$res = self::create_image($image_type,
			$src_img, 
			$new_img,
			array(
				'w' => $sizes[0],
				'h' => $sizes[1],
				'dst_x' => 0,
				'dst_y' => 0,
				'src_x' => 0,
				'src_y' => 0,
				'dst_w' => $w,
				'dst_h' => $h,
				'src_w' => $w,
				'src_h' => $h
			),
			$rgb
		);
		return $res;
	}
	
	/**
	 * Draw a rectangle into an file image
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param integer	the width of the rectangle container
	 * @param integer	the height of the rectangle container
	 * @param integer	the border inner the container
	 * @param array		the array color of the border
	 * @return boolean
	 */
	public static function create_borded($src_img, $new_img, $w, $h, $border, $rgb = array(255, 0, 0))
	{
		$tn = imagecreatefromjpeg($src_img);
		imagesetthickness($tn, 2);
		
		$color = imagecolorallocate($tn, $rgb[0], $rgb[1], $rgb[2]);
		imagerectangle($tn, $border, $border, ($w - $border), ($h - $border), $color);
		imagejpeg($tn, $new_img, 100);
		return file_exists($new_img);
	}
	
	/**
	 * Create a new image with an image inside
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param integer	the width of the new image
	 * @param integer	the height of the new image
	 * @param array		the array color of the background
	 * @return boolean
	 */
	public static function create_container($src_img, $new_img, $w, $h, $rgb = array(255, 255, 255))
	{
		list($sw, $sh, $image_type) = getimagesize($src_img);
		
		// width
		if ($w > $sw) 
		{
			$x = floor(($w - $sw)/2);
			$nw = $sw;
		}
		else 
		{
			$x = 0;
			$nw = $w;
		}
		
		// height
		if ($h > $sh) 
		{
			$y = floor(($h - $sh)/2);
			$nh = $sh;
		}
		else 
		{
			$y = 0;
			$nh = $h;
		}
		
		// create the file
		$res = self::create_image($image_type,
			$src_img, 
			$new_img,
			array(
				'w' => $w,
				'h' => $h,
				'dst_x' => $x,
				'dst_y' => $y,
				'src_x' => 0,
				'src_y' => 0,
				'dst_w' => $nw,
				'dst_h' => $nh,
				'src_w' => $nw,
				'src_h' => $nh
			),
			$rgb
		);
		return $res;
	}
	
	/**
	 * Rotate an image
	 *
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param integer	degrees of the rotation anticlockwise
	 * @param array		the array color of the background
	 * @return boolean
	 */
	public static function rotate($src_img, $new_img, $degrees = 0, $rgb = array(255, 255, 255))
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		
		switch ($image_type)
		{
			case 1: 
				$image = imagecreatefromgif($src_img); 
				break;
			case 2: 
				$image = imagecreatefromjpeg($src_img); 
				break;
			case 3: 
				$image = imagecreatefrompng($src_img); 
				break;
			default: 
				$image = false;
				break;
		}
		
		// rotate
		if ($degrees)
		{
			$image = imagerotate($image, $degrees, imageColorAllocateAlpha($image, $rgb[0], $rgb[1], $rgb[2], 127));	
		}
		
		switch ($image_type)
		{
			case 1: 
				imagegif($image, $new_img, 100); 
				break;
			case 2: 
				imagejpeg($image, $new_img, 100);
				break;
			case 3: 
				imagepng($image, $new_img, 0, NULL); 
				break;
		}
		@chmod($new_img, 0777);
		return file_exists($new_img);
	}
	
	/**
	 * Create a new image from another 
	 *
	 * @param integer	image type index (1 => gif, 2 => jpeg, 3 => png) 
	 * @param string	source image path to file
	 * @param string	destination image path to file
	 * @param array		array of sizes ($dst_width, $dst_height, $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h)
	 * @param array		the array color of the background
	 * @return boolean
	 */
	public static function create_image($image_type, $src_img, $new_img, $sizes, $rgb = array(255, 255, 255)) 
	{
		$tn = imagecreatetruecolor($sizes['w'], $sizes['h']);
		// bgcolor
		$bg = imagecolorallocate($tn, $rgb[0], $rgb[1], $rgb[2]);
		imagefilledrectangle($tn, 0, 0, $sizes['w'], $sizes['h'], $bg);
			
		switch ($image_type)
		{
			case 1: 
				$image = imagecreatefromgif($src_img); 
				break;
			case 2: 
				$image = @imagecreatefromjpeg($src_img); 
				break;
			case 3: 
				$image = imagecreatefrompng($src_img); 
				break;
			default: 
				$image = false;
				break;
		}
		
		if ($image) 
		{
			// imagecopyresampled ($dst_image , $src_image , $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h )
			imagecopyresampled($tn, $image, $sizes['dst_x'], $sizes['dst_y'], $sizes['src_x'], $sizes['src_y'], $sizes['dst_w'], $sizes['dst_h'], $sizes['src_w'], $sizes['src_h']);
			switch ($image_type)
			{
				case 1: 
					imagegif($tn, $new_img, 100); 
					break;
				case 2: 
					imagejpeg($tn, $new_img, 100);
					break;
				case 3: 
					imagepng($tn, $new_img, 0, NULL); 
					break;
			}
			@chmod($new_img, 0777);
			return file_exists($new_img);
		}
		return 0;
	}
	
	/**
	 * Return a not existent filename 
	 *
	 * @param string	path to file
	 * @param string	original filename
	 * @return string
	 */
	public static function get_final_name($path, $name, $suffix = '') 
	{
		while (file_exists($path.$name.$suffix)) 
		{
			$token = explode('.', $name);
			$tok = explode('_', $token[0]);
			if (!isset($tok[1])) 
			{
				// first 
				$token[0] .= '_1';
			}
			else
			{
				// next after the first 
				$token[0] = $tok[0].'_'.strval(intval($tok[1]) + 1);
			}
			$name = implode('.', $token);
		}
		return $name.$suffix;
	}
	
	/**
	 * Copy uploaded file and set chmod
	 *
	 * @param string	path where save the file
	 * @param string	filename
	 * @param string	uploaded file obj
	 * @return boolean
	 */
	public static function copy_file($path, $name, $obj) 
	{
		$check = move_uploaded_file($obj, $path.$name);
		if ($check) 
		{
			chmod($path.$name, 0777);
		}
		return $check;
	}
	
	/**
	 * Delete a file
	 *
	 * @param string	path where is stored the file
	 * @param string	filename
	 * @return boolean
	 */
	public static function del_file($path, $name) 
	{
		if (file_exists($path.$name)) 
		{
			@chmod($path.$name, 0777);
			$check = @unlink($path.$name);
		}
	}
	
	/**
	 * Browse a folder and return an array of contents
	 *
	 * @param string	path of the folder to browse
	 * @return array
	 */
	public static function browse($path)
	{
		// Open the folder 
		$dir = @opendir($path) or die('Unable to open '.$path);
		
		$a = array();
		
		// Loop through the files 
		while ($file = readdir($dir)) 
		{ 
			if($file != '.' && $file != '..' && !is_dir($file)) 
				$a[] = $file;
		}
		
		// Close 
		closedir($dir);
		
		return $a;
    }
    
    /**
	 * Check if a shell command is available for PHP
	 *
	 * @access	private
	 * @param	string $cmd	Command name
	 * @return	string
	 */
	public static function command_exist($cmd) 
	{
		return shell_exec("which $cmd");
	}
    
    /**
	 * Extract a frame from a video
	 *
	 * @param string	$video	the video
	 * @param string	$image	the frame
	 * @param integer	$time	second of the frame to capture
	 * @param array		$sizes	width and height to scale	
	 * @return mixed
	 */
	public static function extract_frame($video, $image, $time, $sizes = array()) 
	{
		// get the command, if exists
		$ffmpeg = str_replace(NL, '', self::command_exist('ffmpeg')); 		
		if (!empty($ffmpeg))
		{
			// we can extract a frame
			$ipath = APATH.'files/filemanager/img/';
			
			// set the new name
			$final_name = self::get_final_name($ipath, $image);
			
			//ffmpeg -i video_file -an -ss 27.888237 -vframes 1 -s 320x240 -f image2 image_file
			if (empty($sizes))
			{
				$chk = shell_exec($ffmpeg.' -i '.$video.' -an -ss '.$time.' -vframes 1 -f image2 '.$ipath.$final_name.' 2>&1');
			}
			else
			{
				$chk = shell_exec($ffmpeg.' -i '.$video.' -an -ss '.$time.' -vframes 1 -s '.$sizes[0].'x'.$sizes[1].' -f image2 '.$ipath.$final_name.' 2>&1');
			}
			
			if ($chk && file_exists($ipath.$final_name))
			{
				chmod($ipath.$final_name, 0777);
				return $final_name;
			}
		}
		return false;	
	}
    
	 /**
	 * Extract a frame from a remote video
	 *
	 * @param string	$video	the video
	 * @param string	$image	the frame
	 * @param array		$sizes	width and height to scale	
	 * @return mixed
	 */
	public static function remote_video_frame($video, $image, $sizes = array())
	{
		$frame = '';
		$image_url = parse_url($video);
		if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com')
		{
			$array = explode("&", $image_url['query']);
			$frame = 'http://img.youtube.com/vi/'.substr($array[0], 2).'/0.jpg';
		} 
		elseif ($image_url['host'] == 'youtu.be')
		{
			$path = explode('/', $image_url['path']);
			$frame = 'http://i.ytimg.com/vi/'.$path[1].'/0.jpg';
		}
		elseif ($image_url['host'] == 'www.vimeo.com' || $image_url['host'] == 'vimeo.com')
		{
			$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.substr($image_url['path'], 1).'.php'));
			$frame = $hash[0]['thumbnail_large'];
		}
		
		if (!empty($frame))
		{
			// we can extract a frame
			$ipath = APATH.'files/filemanager/img/';
			
			// set the new name
			$final_name = self::get_final_name($ipath, $image);
			
			// copy remote file
			$chk = copy($frame, $ipath.$final_name);
			
			if ($chk && file_exists($ipath.$final_name))
			{
				chmod($ipath.$final_name, 0777);
				
				$check = X4Files_helper::create_fit($ipath.$final_name, $ipath.$final_name, $sizes, true);
				
				return $final_name;
			}
			return '';
		}
		
	}
	
    /**
	 * Zip a file
	 *
	 * @param string	the file to zip
	 * @return boolean
	 */
	public static function zip_file($file) 
	{
		// get mimetype
		$mimetype = self::get_mime($file);
		
		if (strstr($mimetype, 'zip') != '' || strstr($mimetype, 'x-compress') != '')
		{
			// already zipped
			return true;
		}
		else
		{
			// try to zip
			$info = pathinfo($file);
			
			$zip = new ZipArchive();
			if ($zip->open($file.'.zip', ZIPARCHIVE::CREATE) === true) 
			{
				$chk = $zip->addFile($file, $info['basename']);
				$zip->close();
				return $chk;
			} 
			else 
			{
				return false;
			}
		}
	}
	
	/**
	 * Get a file
	 * the path of the file will remain anonymous
	 *
	 * @param string	$file file with path
	 * @param string	$filename file dname for the download
	 * @return file
	 */
	public static function get_file($file, $filename = '') 
	{
		if (file_exists($file)) 
		{
			$download_name = (empty($filename))
				? basename($file)
				: X4Utils_helper::unspace($filename);
		
			$mime = self::get_mime($file);
			//$out = file_get_contents($file);
			header('Content-Description: File Transfer');
		//	header('Cache-Control: public'); // needed for i.e.
			header('Content-type: '.$mime);
			header('Content-Disposition: attachment; filename='.$download_name);
			header('Content-Transfer-Encoding: Binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
            header('Content-Length:'.filesize($file));
            
            ob_clean();
            flush();
            readfile($file);
			exit;
		}
	}
	
	/**
	 * Check if a folder already exists
	 *
	 * @param   string	$folder Folder name
	 * @return  boolean
	 */
	public static function folder_exist($folder) 
	{
		// is set?
		if ($folder == '')
		{
			return false;
		}
		
		// exists?
		if (!is_dir(APATH.'files/'.$folder))
		{
			$chk = mkdir(APATH.'files/'.$folder);
			
			// is writable?
			if ($chk)
			{
			    $chk = chmod(APATH.'files/'.$folder, 0777);
			}
		}
		else
		{
		    // is writable?
		    $chk = (is_writable(APATH.'files/'.$folder));
		}
		
		return $chk;
	}
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class Upload_file_xhr 
{
    public $temp;
    public $real_size;
   
    public function __construct($field)
    {
    	$input = fopen("php://input", "r");
		$this->temp = tmpfile();
		$this->real_size = stream_copy_to_stream($input, $this->temp);
		fclose($input);
    }
    
	/**
     * Save the file to the specified path
     * @return integer greater than 0 on success
     */
    public function save($path) 
    {
		if ($this->real_size != $this->get_size())
			return 0;
		
		$target = fopen($path, "w");
		fseek($this->temp, 0, SEEK_SET);
		$check = stream_copy_to_stream($this->temp, $target);
		fclose($target);
		return $check;
    }
    
	public function get_name($field) 
	{
    	return $_GET[$field];
    }
    
    public function get_size() 
    {
        if (isset($_SERVER["CONTENT_LENGTH"]))
            return (int)$_SERVER["CONTENT_LENGTH"];
        else
        {
        	return 0;
        	//throw new Exception('Getting content length is not supported.');
            //header('Location: '.BASE_URL.'msg/message/_upload_error');
            //die;
        }
    }
}
