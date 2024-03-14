<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	private static $mimg = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/svg+xml');
	private static $mmedia = array('video/quicktime', 'application/vnd.rn-realmedia', 'audio/x-pn-realaudio', 'application/vnd.adobe.flash.movie', 'application/x-shockwave-flash', 'video/x-ms-wmv', 'video/avi', 'video/msvideo', 'video/x-msvideo','video/mpeg', 'video/mp4', 'video/3gpp', 'video/x-flv', 'video/ogg', 'application/ogg', 'video/webm');
	private static $mtemplate = array('text/html', 'text/xml', 'application/xml');
// TODO: this must be defined in the administration
	private static $mfiles = array('text/plain', 'application/pdf',
	    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	    'application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/x-rar', 'application/x-rar-compressed',
	    'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet',
	    'binary/octet-stream', 'audio/mpeg', 'audio/wav', 'audio/x-wav', 'application/x-compress', 'application/x-compressed', 'multipart/x-zip',
	    'application/octet-stream', 'application/pkcs7-mime', 'application/x-pkcs7-mime', 'application/x-dike');

    private static $bad_mimetypes = array('text/x-sh', 'application/x-sh', 'text/javascript', 'text/php', 'application/x-httpd-php', 'text/x-python', 'application/x-python-code');

    /**
     * Files array
     */
    private static $file = array();

    /**
     * Get PHP version
     */
	private static function phpv()
	{
		$v = explode('.', phpversion());
		return floatval($v[0].'.'.$v[1]);
	}

	// path to the folder managed by filemanager
	private static $file_path = 'files/'.SPREFIX.'/filemanager/';

	/**
	 * Get a code related to the file type
	 */
	public static function get_type(string $file, $folder = false)
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
            case 'sh':
                case 'js':
                case 'php':
                case 'py':
                case 'rb':
                    return false;
                    break;
            case 'xls':
            case 'xlsx':
            case 'doc':
            case 'docx':
            case 'ppt':
            case 'pptx':
            case 'odt':
            case 'odp':
            case 'ods':
            default:
                return ($folder)
                    ? 'files'
                    : 1;
                break;
        }
	}

	/**
	 * Get a code related to the file type by file name
	 */
	public static function get_type_by_name(string $file, bool $folder = false)
	{
		// get extension from file name
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		switch ($ext)
		{
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
            case 'svg':
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
            case 'sh':
            case 'js':
            case 'php':
            case 'py':
            case 'rb':
                return false;
                break;
			case 'xls':
            case 'xlsx':
            case 'doc':
            case 'docx':
            case 'ppt':
            case 'pptx':
            case 'odt':
            case 'odp':
            case 'ods':
			default:
				return ($folder)
					? 'files'
					: 1;
				break;
		}
	}

	/**
	 * Get the mimetype of a file
	 */
	public static function get_mime(string $file)
	{
        $finfo = @new finfo(FILEINFO_MIME);
        $info = @$finfo->file($file);

        $mime = explode(';', $info);
        return $mime[0];
	}

    /**
	 * filter mimes array with mimes available in X3 CMS and return final file folder
	 */
	private static function filter_mime(array $mimes, mixed $file_type)
	{
        if (in_array($file_type, self::$bad_mimetypes))
        {
            return false; // is a bad file
        }

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
		foreach ($mime as $k => $v)
		{
			if (in_array($file_type, $v))
			{
				$type = $k;
			}
		}
		return $type;
	}

	/**
	 * File upload
	 * Perform the upload with all required checks: file size, image size form images (crop and resize), check for overwrite and add prefixes, store the file inthe right folder
	 */
	public static function upload(string $file, string $path, string $prefix = '', array $limits = [MAX_W, MAX_H, 'NONE', MAX_IMG, MAX_DOC], array $mimes = [], bool $zip = false, bool $force_resize = false)
	{
		if (isset($_GET[$file]))
		{
			self::$file = new Upload_file_xhr($file);
			return X4Files_helper::gupload_file($file, $path, $prefix, $zip, $limits, $mimes);
		}
		elseif (isset($_FILES[$file]))
		{
			return (is_array($_FILES[$file]['name']))
				? X4Files_helper::upload_files($file, $path, $prefix, $limits, $mimes)
				: X4Files_helper::upload_file($file, $path, $prefix, $limits, $mimes, $zip, $force_resize);
		}
	}

    /**
     * Upload file trought XHR
     */
	private static function gupload_file(string $file, string $path, string $prefix, int $zip, array $limits, array $mimes)
	{
		$action = '';

		// get the file name
		$filename = self::$file->get_name($file);

		// mime, prefix and suffix
        $type = $suffix = '';
		if ($prefix == '__secret')
		{
			$prefix = '';
			$suffix = '.x4w';
		}
		else
		{
			$type = self::filter_mime($mimes, X4Files_helper::get_mime($filename));
		}

		// check mime and type
		if ((!empty($mimes) && empty($type)) || $type === false)
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
		$tmpname = X4Utils_helper::slugify(strtolower($prefix.self::$file->get_name($file)));
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

    /**
     * Upload single file
     */
	private static function upload_file(
        string $file,
        string $path,
        string $prefix,
        array $limits,
        array $mimes,
        bool $zip,
        bool $force_resize
    )
	{
		$errors = array();
		if (is_uploaded_file($_FILES[$file]['tmp_name']))
		{
            list($file_name, $file_path, $error) = self::uploading([$file, 0], $path, $prefix, $limits, $mimes, $zip);

            if (!empty($error))
            {
                $errors[$file] = $error;
            }
		}

		// set return or delete
		if (empty($errors) && !empty($file_name))
		{
            // return the file name (only the name, no path)
			return $file_name;
        }
        else
        {
            unlink($file_path);
            return array($errors, 0);
        }
	}

	/**
	 * Files upload
	 * Perform the upload of an array of files with all required checks: file size, image size form images (crop and resize), check for overwrite and add prefixes, store the file inthe right folder.
	 *
	 * @param string	the name of the field
	 * @param string	the path where to store the file. The standard path is APATH.'files/'.SPREFIX.'/filemanager/'
	 * @param string	a prefix for the filename
	 * @param array		the array contains (maximum width, maximum height, action_string). Possibles values for the action string are: 'NONE', 'CROP', 'RESIZE', 'SCALE'
	 * @param array		the array contains valid mime types
	 * @return mixed	the filename string if upload only a file, an array of filename if upload many files
	 */
	private static function upload_files(string $file, string $path, string $prefix, array $limits, array $mimes) : array
	{
		$file_names = $errors = array();
		$n = sizeof($_FILES[$file]['tmp_name']);

		for($i = 0; $i < $n; $i++)
		{
            if (is_uploaded_file($_FILES[$file]['tmp_name'][$i]))
            {
                list($file_name, $file_path, $error) = self::uploading([$file, $i], $path, $prefix, $limits, $mimes);

                if (!empty($file_path))
                {
                    $file_names[$file_name] = $file_path;
                }

                if (!empty($error))
                {
                    $errors[$file] = $error;
                }
            }
            else
            {
                $errors[$file][] = '_upload_error';
            }
        }

		if (empty($errors))
		{
			return array(array_keys($file_names), 1);
		}
		else
		{
			foreach ($file_names as $v)
			{
				unlink($v);
			}
			return array($errors, 0);
		}
	}

    /**
     * Extract file data from _FILES array
     */
    private static function file_data(string $file, int $i, string $what) : mixed
    {
        $tmp = $i
            ? $_FILES[$file][$what][$i]
            : $_FILES[$file][$what];

        if (is_array($tmp))
        {
            $values= array_unique($tmp);
            return $values[0];
        }
        return $tmp;
    }

    /**
     * perform checks on file uploaded and return filename or errors
     */
    private static function uploading(
        array $file_n,
        string $path,
        string $prefix,
        array $limits,
        array $mimes,
        bool $zip = false
    ) : array
    {
        list($file, $i) = $file_n;

        $errors = [];
        $action = $file_name = $tmp_file_path = '';
        // define the final folder
        $type = self::filter_mime($mimes, self::file_data($file, $i, 'type'));
        if ((!empty($mimes) && empty($type)) || $type === false)
        {
            $errors[] = '_bad_mimetype';
        }

        if ($type == 'img' && $path == APATH.self::$file_path)
        {
            if (self::file_data($file, $i, 'size') > ($limits[3] * 1024))
            {
                $errors[] = '_file_size_is_too_big';
            }

            $imageinfo = getImageSize(self::file_data($file, $i, 'tmp_name'));
            if (!X4Files_helper::checkImageSize($imageinfo, $limits))
            {
                if ($limits[2] == 'NONE')
                {
                    $errors[] = '_image_size_is_too_big';
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
            if (self::file_data($file, $i, 'size') > ($limits[4] * 1024) && $path == APATH.self::$file_path)
            {
                $errors[] = '_file_size_is_too_big';
            }
        }

        if (empty($errors))
        {
            // handle type folder
            $type = ($path == APATH.self::$file_path)
                ? $type.'/'
                : '';

            // file name
            $tmp_name = X4Utils_helper::slugify(strtolower($prefix.self::file_data($file, $i, 'name')));
            // exists?
            $file_name = X4Files_helper::get_final_name($path.$type, $tmp_name);

            // copy
            $check = X4Files_helper::copy_file($path.$type, $file_name, self::file_data($file, $i, 'tmp_name'));
            if ($check)
            {
                // define the filename with the complete path
                $tmp_file_path = $path.$type.$file_name;

                // switch between actions
                // NOTE: source and destination are the same
                if (self::set_action($action, $tmp_file_path, $tmp_file_path, $limits))
                {
                    // handle zip
						if ($zip && !X4Files_helper::zip_file($tmp_file_path))
						{
                            unlink($tmp_file_path);
                            $tmp_file_path = '';
                            $errors[] = '_zip_error';
						}
                }
                else
                {
                    unlink($tmp_file_path);
                    $tmp_file_path = '';
                }
            }
            else
            {
                $errors[] = '_upload_error';
            }
        }
        return [$file_name, $tmp_file_path, $errors];
    }

    /**
	 * Switch by available actions
	 */
	private static function set_action(string $action, string $source, string $destination, array $limits) : bool
	{
		// disable action for SVG
        if (self::get_mime($source) == 'image/svg+xml')
        {
            $action = 'NONE';
        }

        // perform action
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
			$check = true;
		}
		return $check;
	}

	/**
	 * Check image size
	 */
	private static function checkImageSize(array $size, array $limits = array(MAX_W, MAX_H)) : bool
	{
		return ($size[0] <= $limits[0] && $size[1] <= $limits[1])
			? true
			: false;
	}

	/**
	 * Create a resized file image
	 */
	public static function create_fit(string $src_img, string $new_img, array $sizes, bool $force = false) : bool
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
			return self::create_image($image_type,
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
		}
		else
		{
			if ($force)
			{
				copy($src_img, $new_img);
				return true;
			}
		}
		return false;
	}

	/**
	 * Create a resized file image
	 */
	public static function create_resized(string $src_img, string $new_img, array $sizes, bool $resize = true, bool $center = true, array $rgb = [255, 255, 255]) : bool
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
	 */
	public static function create_thumb(string $src_img, string $new_img, array $sizes) : bool
	{
        // sizes = array(width, height, start_width, start_height, scalex, scaley)
		list($w, $h, $image_type) = getimagesize($src_img);

		$nw = ceil($sizes[0] * $sizes[4]);
		$nh = ceil($sizes[1] * $sizes[5]);

		// create the file
		return self::create_image($image_type,
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
	}

	/**
	 * Crop an image file
	 */
	public static function create_cropped(string $src_img, string $new_img, array $sizes, array $coords = [0, 0], bool $force = false) : bool
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		if ($w > $sizes[0] || $h > $sizes[1])
		{
			$nw = ($w > $sizes[0]) ? $sizes[0] : $w;
			$nh = ($h > $sizes[1]) ? $sizes[1] : $h;

			// create the file
			return self::create_image($image_type,
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
		}
		elseif ($force)
		{
			copy($src_img, $new_img);
		}
		return true;
	}

	/**
	 * Create an extended file image
	 */
	public static function create_extended(string $src_img, string $new_img, array $sizes, $rgb = [255, 255, 255]) : bool
	{
		list($w, $h, $image_type) = getimagesize($src_img);

		// create the file
		return self::create_image($image_type,
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
	 */
	public static function create_container(string $src_img, string $new_img, int $w, int $h, array $rgb = [255, 255, 255]) : bool
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
		return self::create_image($image_type,
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
	}

	/**
	 * Rotate an image
	 */
	public static function rotate(string $src_img, string $new_img, int $degrees = 0, array $rgb = [255, 255, 255]) : bool
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
     * Check if a png has alpha channel
     */
    public static function is_transparent_png(string $file) : bool
    {
        if (filesize($file) > 18000)
        {
            return false; // too big to check
        }
        // 32-bit pngs
        // 4 checks for greyscale + alpha and RGB + alpha
        if ((ord(file_get_contents($file, false, null, 25, 1)) & 4) > 0)
        {
            return true;
        }
        // 8 bit pngs
        $fd = fopen($file, 'r');
        $continue = true;
        $plte = false;
        $trns = false;
        $idat = false;
        while($continue === true)
        {
            $continue = false;
            $line = fread($fd, 1024);
            if ($plte === false)
            {
                $plte = (stripos($line, 'PLTE') !== false);
            }
            if ($trns === false){
                $trns = (stripos($line, 'tRNS') !== false);
            }
            if ($idat === false){
                $idat = (stripos($line, 'IDAT') !== false);
            }
            if ($idat === false && !($plte === true && $trns === true))
            {
                $continue = true;
            }
        }
        fclose($fd);
        return ($plte === true && $trns === true);
    }

	/**
	 * Create a new image from another
	 * $image_type index (1 => gif, 2 => jpeg, 3 => png)
     * $sizes = array($dst_width, $dst_height, $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h)
	 */
	public static function create_image(int $image_type, string $src_img, string $new_img, array $sizes, array $rgb = [255, 255, 255]) : bool
	{
        $tn = imagecreatetruecolor($sizes['w'], $sizes['h']);

        if ($image_type == 3)
        {
            // bgcolor
            // integer representation of the color black (rgb: 0,0,0)
            $black = imagecolorallocate($tn , 0, 0, 0);
            $transparent = self::is_transparent_png($src_img);
            if ($transparent)
            {
                // removing the black from the placeholder
                imagecolortransparent($tn, $black);

                imagealphablending($tn, true);
                imagesavealpha($tn,true);
                $bg = imagecolorallocatealpha($tn, $rgb[0], $rgb[1], $rgb[2], 127);
            }
            else
            {
                $bg = imagecolorallocate($tn, $rgb[0], $rgb[1], $rgb[2]);
            }
        }
        else
        {
            $bg = imagecolorallocate($tn, $rgb[0], $rgb[1], $rgb[2]);
        }
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
                    if ($transparent)
                    {
                        // removing the black from the placeholder
                        imagecolortransparent($tn, $black);
                        imageAlphaBlending($tn, false);
                        imageSaveAlpha($tn, true);
                    }
					imagepng($tn, $new_img, 0, NULL);
					break;
			}
			@chmod($new_img, 0777);
			return file_exists($new_img);
		}
		return false;
	}

	/**
	 * Return a not existent filename to avoid overwrite of files
	 */
	public static function get_final_name(string $path, string $name, string $suffix = '') : string
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
	 */
	public static function copy_file(string $path, string $name, string $obj) : bool
	{
		$check = move_uploaded_file($obj, $path.$name);
		if ($check)
		{
			chmod($path.$name, 0777);
		}
		return $check;
	}

    /**
	 * Empty file
	 */
	public static function empty_file(string $path, string $name) : bool
	{
		chmod($path.$name, 0777);
		return file_put_contents($path.$name, '');
	}

	/**
	 * Delete a file
	 */
	public static function delete_file(string $path, string $name)
	{
		if (file_exists($path.$name))
		{
			@chmod($path.$name, 0777);
			@unlink(PATH.$path.$name);
		}
	}

	/**
	 * Browse a folder and return an array of contents
	 */
	public static function browse(string $path) : array
	{
		// Open the folder
		$dir = @opendir($path) or die('Unable to open '.$path);
		$a = array();
		// Loop through the files
		while ($file = readdir($dir))
		{
			if($file != '.' && $file != '..' && !is_dir($file))
			{
				$a[] = $file;
			}
		}
		// Close
		closedir($dir);
		return $a;
    }

    /**
	 * Check if a shell command is available for PHP
	 */
	public static function command_exist(string $cmd) : string
	{
		return (string) @shell_exec("which $cmd");
	}

    /**
	 * Extract a frame from a video
	 */
	public static function extract_frame(string $video, string $image_name, int $time, array $sizes = [])
	{
		// get the command, if exists
		$ffmpeg = str_replace(NL, '', self::command_exist('ffmpeg'));
		if (!empty($ffmpeg))
		{
			// we can extract a frame
			$ipath = APATH.'files/'.SPREFIX.'/filemanager/img/';

			// set the new name
			$final_name = self::get_final_name($ipath, $image_name);

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
	 */
	public static function remote_video_frame(string $video, string $image_name, array $sizes = [])
	{
		$frame = '';
		$image_url = parse_url($video);
		if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com')
		{
			$array = explode("&", $image_url['query']);
			$frame = 'https://img.youtube.com/vi/'.substr($array[0], 2).'/0.jpg';
		}
		elseif ($image_url['host'] == 'youtu.be')
		{
			$path = explode('/', $image_url['path']);
			$frame = 'https://i.ytimg.com/vi/'.$path[1].'/0.jpg';
		}
		elseif ($image_url['host'] == 'www.vimeo.com' || $image_url['host'] == 'vimeo.com')
		{
			$hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.substr($image_url['path'], 1).'.php'));
			$frame = $hash[0]['thumbnail_large'];
		}

		if (!empty($frame))
		{
			// we can extract a frame
			$ipath = APATH.'files/'.SPREFIX.'/filemanager/img/';

			// set the new name
			$final_name = self::get_final_name($ipath, $image_name);

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
	 */
	public static function zip_file(string $file) : bool
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
	 */
	public static function get_file(string $file, string $download_name = '', bool $download = true)
	{
		if (file_exists($file))
		{
            set_time_limit(0);
			$download_name = (empty($download_name))
				? basename($file)
				: X4Utils_helper::slugify($download_name);

			$mime = self::get_mime($file);
			if ($download)
			{
			    header('Content-Description: File Transfer');
                header('Cache-Control: private');
                header('Content-type: '.$mime);
                header('Content-Disposition: attachment; filename='.$download_name);
                header('Content-Transfer-Encoding: Binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length:'.filesize($file));
            }
            else
            {
                header('Content-Description: File Transfer');
                header('Cache-Control: private');
                header('Content-type: '.$mime);
                header('Content-Disposition: inline; filename='.$download_name);
                header('Content-Transfer-Encoding: Binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                // for adobe reader
                if ($mime == 'application/pdf')
                {
                    header("Content-Range: bytes");
                }
                header('Content-Length:'.filesize($file));
            }

            ob_clean();
            flush();
            if (ob_get_level())
            {
                ob_end_clean();
            }
            readfile($file);
			exit;
		}
	}

    /**
	 * Set header
	 */
	public static function setHeader(string $filename, int $filesize)
	{
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 01 Jan 2001 00:00:01 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Type: text/x-csv');

		// disposition / encoding on response body
		if (isset($filename) && strlen($filename) > 0)
        {
			header("Content-Disposition: attachment;filename={$filename}");
        }
		if (isset($filesize))
        {
			header("Content-Length: ".$filesize);
        }
		header("Content-Transfer-Encoding: binary");
		header("Connection: close");
	}

    /**
	 * Return array as CSV file
	 */
	public static function get_csv(string $filename, array $items, bool $header = false)
	{
		$csv = tmpfile();
		foreach ($items as $i)
		{
			$row = (array) $i;
			if ($header)
			{
				fputcsv($csv, array_keys($row));
				$header = false;
			}
			fputcsv($csv, array_values($row), ';', '"');
		}

		rewind($csv);
		$filename = X4Utils_helper::slugify($filename.' '.date('Y-m-d').'.csv');

		$fstat = fstat($csv);
		self::setHeader($filename, $fstat['size']);

		fpassthru($csv);
		fclose($csv);
	}

	/**
	 * Check if a folder already exists
	 *
	 * @param   string	$folder Folder name
	 * @return  boolean
	 */
	public static function folder_exist(string $folder) : bool
	{
		if (empty($folder))
		{
			return false;
		}

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

	/**
	 * Copy file from
	 */
	public static function copy_file_from(string $old_file, string $path)
	{
		if (file_exists($old_file) && !empty($path))
		{
		    $info = pathinfo($old_file);
		    $name = self::get_final_name($path, X4Utils_helper::slugify($info['filename']).'.'.$info['extension']);

		    $chk = copy($old_file, $path.$name);
		    if ($chk)
		    {
		        chmod($path.$name, 0777);
		        return $name;
		    }
		}
		return null;
	}

	/**
	 * Get a remote file
	 */
	public static function get_remote_file(string $remote, string $path) : string
	{
        $headers = get_headers($remote);
        if (substr($headers[0], 9, 3) == '200')
        {
            $str = file_get_contents($remote);
            // $http_response_header should be filled by file_get_contents
            $filename = self::get_real_filename($http_response_header, $remote);

            $name = self::get_final_name($path, X4Utils_helper::slugify(str_replace('..', '.', $filename)));

            $chk = file_put_contents($path.$name, $str);
            if ($chk)
            {
                chmod($path.$name, 0777);
                return $name;
            }
        }
        return '';
	}

	/**
	 * Get the real name of a remote URL
	 */
	public static function get_real_filename(array $headers, string $url) : string
    {
        foreach ($headers as $header)
        {
            if (strpos(strtolower($header),'content-disposition') !== false)
            {
                $tmp_name = explode('=', $header);
                if ($tmp_name[1])
                {
                    return trim($tmp_name[1],'";\'');
                }
            }
        }
        $stripped_url = preg_replace('/\\?.*/', '', $url);
        return basename($stripped_url);
    }

	/**
	 * Get the mime type of a remote file
	 */
	public static function get_remote_file_mime_type(string $url) : string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_exec($ch);
        return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    }

	/**
	 * Get Fontawesome icon
	 */
	public static function get_icon(string $file, string $size= 'fa-2x') : string
	{
	    $ext = substr($file, -3);
	    switch ($ext)
	    {
	        case 'doc':
	        case 'docx':
	            $icon = '<span class="fa fa-file-word-o '.$size.'" aria-hidden="true"></span>';
	            break;
	        case 'xls':
	        case 'xlsx':
	            $icon = '<span class="fa fa-file-excel-o '.$size.'" aria-hidden="true"></span>';
	            break;
	        case 'pdf':
	            $icon = '<span class="fa fa-file-pdf-o '.$size.'" aria-hidden="true"></span>';
	            break;
	        default:
	            $icon = '<span class="fa fa-file-o '.$size.'" aria-hidden="true"></span>';
	            break;
	    }
	    return $icon;
	}
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class Upload_file_xhr
{
    public $temp;
    public $real_size;

    public function __construct()
    {
    	$input = fopen("php://input", "r");
		$this->temp = tmpfile();
		$this->real_size = stream_copy_to_stream($input, $this->temp);
		fclose($input);
    }

	/**
     * Save the file to the specified path
     */
    public function save(string $path) : int
    {
		if ($this->real_size != $this->get_size())
        {
			return 0;
        }
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
        return (isset($_SERVER["CONTENT_LENGTH"]))
            ? (int)$_SERVER["CONTENT_LENGTH"]
            : 0;

        //throw new Exception('Getting content length is not supported.');
        //header('Location: '.BASE_URL.'msg/message/_upload_error');
        //die;
    }
}
