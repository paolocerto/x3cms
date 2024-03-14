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
 * Helper for URL handling
 *
 * @package X4WEBAPP
 */
class X4Url_helper
{
    /**
	 * Get meta data
	 */
    public static function getMetaTags(string $str) : array
    {
        $pattern = '

~<\s*meta\s

# using lookahead to capture type to $1
(?=[^>]*?
\b(?:name|property)\s*=\s*
(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
)

# capture content to $2
[^>]*?\bcontent\s*=\s*
(?|"\s*\S([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
[^>]*>

~ix';
        $str = self::utf_fixer($str);
        if(preg_match_all($pattern, $str, $out, PREG_PATTERN_ORDER))
        {
            return $out[0];
        }
        return array();
    }

    /**
	 * Get title
	 */
    public static function getTitle(string $str) : array
    {
        $pattern = '~<title>[\s\S]*<\/title>~ix';
        if(preg_match_all($pattern, $str, $out, PREG_PATTERN_ORDER))
        {
            return $out[0];
        }
        return array();
    }

    /**
     * Fix UTF8 encoding
     */
    public static function utf_fixer(string $str) : string
    {
        return X4Text_helper::fix_encoding($str);
    }


    /**
	 * Get meta
	 */
    public static function extract_tags_from_url(string $url) : array
    {
        $tags = array();

        // url replacer
        $url = str_replace(
            array(
                'https://www.youtube.com/watch?v=',
            ),
            array(
                'https://youtu.be/',
            ),
            $url
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $contents = curl_exec($ch);
        curl_close($ch);

        if (empty($contents))
        {
            return $tags;
        }

        $matches = self::getMetaTags($contents);
        // fix non UTF8
        $xml = self::utf_fixer(implode($matches));

        try
        {
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML('<?xml encoding="utf-8" ?>'.$xml);

            $src = array('og:', 'twitter:');

            foreach ($doc->getElementsByTagName('meta') as $metaTag)
            {
                if($metaTag->getAttribute('name') != "" && !empty($metaTag->getAttribute('content')))
                {
                    $tags[str_replace($src, '', $metaTag->getAttribute('name'))] = $metaTag->getAttribute('content');
                }
                elseif ($metaTag->getAttribute('property') != "" && !empty($metaTag->getAttribute('content')))
                {
                    $tags[str_replace($src, '', $metaTag->getAttribute('property'))] = $metaTag->getAttribute('content');
                }
            }

            if (!isset($tags['title']))
            {
                $matches = self::getTitle($contents);
                // fix non UTF8
                $xml = self::utf_fixer(implode($matches));
                $doc = new DOMDocument();
                $doc->loadHTML($xml);
                foreach ($doc->getElementsByTagName('title') as $title)
                {
                    $tags['title'] = $title->nodeValue;
                }
            }

            // fix url
            if (isset($tags['url']))
            {
                $tags['url'] = $url;
            }

            if(!isset($tags['title']) || !isset($tags['description']))
            {
                $tags = array();
            }
        }
        catch (Exception $e)
        {
             // do nothing
        }

        return $tags;
    }

	/**
	 * Handle meta
	 */
	public static function meta(string $url) : array
	{
	    $metas = self::extract_tags_from_url($url);

	    $am = array('title', 'description', 'url', 'player', 'video', 'image');

	    $a = array();
	    foreach ($metas as $k => $v)
        {
            $keys = explode(':', $k);
            if (in_array($keys[0], $am))
            {
                switch (sizeof($keys))
                {
                    case 1:
                        $a[$k] = $v;
                        break;
                    case 2:
                        if ($k == 'video:url' && strstr($v, 'www.youtube.com') != '')
                        {
                            $a[$keys[0]] = self::youtube_embed($v);
                        }
                        else
                        {
                            $a[$keys[1]] = $v;
                        }
                        break;
                    default:
                        // none
                        break;
                }
            }
        }
        if (!empty($a) && !isset($a['url']))
        {
            $a['url'] = $url;
        }
        return $a;
	}

	/**
	 * convert youtube url to embed url
	 */
	public static function youtube_embed(string $url) : string
	{
	    $uqs = explode('?', $url);
	    $t = explode('/', $uqs[0]);
	    $code = array_pop($t);
	    return 'https://www.youtube.com/embed/'.$code;
	}

	/**
	 * format post/comment contents
	 */
	public static function format_content(array $data) : string
	{
	    $out = '';
	    if (isset($data['txt']))
	    {
            // post
            if (!empty($data['txt']))
            {
                $out = '<p class="mb-4">
                    '.nl2br(X4Text_helper::linkify(stripslashes($data['txt']), ['http', 'https', 'mail'])).'
                    </p>';
            }

            foreach ($data['related'] as $k => $v)
            {
                $out .= self::formatter($k, $v);
            }
		}
		return $out;
	}

    /**
	 * format post/comment contents
	 */
	public static function formatter(string $k, array $v) : string
	{
	    $out = '';
	    if ($k === 'img')
        {
            // image
            foreach ($v as $ii)
            {
                $out .= '<img class="mb-2" src="'.$ii.'" />';
            }
        }
        elseif (isset($v['question']))
        {
            $src = array('XGTX', 'XLTX', '<spam>', 'XQTX', 'XEQX');
            $rpl = array('>', '<', '<span class="AM">', '"', '=');

            $ol = array();
            for($c = 1; $c < 6; $c++)
            {
                $ol[] = '<li class="pl-4 py-1">'.str_replace($src, $rpl, $v['answer'.$c]).'</li>';
            }

            $out .= '<div class="w-full text-sm border border-gray-300">
                    <blockquote class="w-full border-l-4 border-gray-300 py-4 pl-4">
                        <p><strong>'.$v['subject'].'</strong></p>
                        '.str_replace($src, $rpl, $v['question']).'
                        <ol style="list-style-type:upper-alpha;margin:.5em 0 0 2em;">
                            '.implode('', $ol).'
                        </ol>
                    </blockquote>
                </div>';
        }
        else
        {
            // link
            $msg = self::link_format($v);

            if (empty($v['url']))
            {
                $out .= '<div class="py-2 border-l-4 border-gray-300 pl-4">
                    '.$msg.'
                </div>';
            }
            else
            {
                $out .= '<div class="py-2 border-l-4 border-gray-300 pl-4">
                    <a target="_blank" href="'.$v['url'].'">'.$msg.'</a>
                </div>';
            }
        }
        return $out;
	}

	/**
	 * format link box
	 */
	public static function link_format(array $data) : string
	{
	    // link
        $title = (empty($data['title']))
            ? ''
            : '<h4 class="pr-4">'.$data['title'].'</h4>';

        $img = (empty($data['image']))
            ? false
            : '<img src="'.$data['image'].'"/>';

        $desc = (empty($data['description']))
            ? ''
            : '<p class="pr-4 text-sm">'.$data['description'].'</p>';

        $site = (empty($data['site_name']))
            ? ''
            : '<p class="pr-4">'.strtoupper($data['site_name']).'</p>';

        // video replace img
        if (!empty($data['video']))
        {
            $img = '<div class="w-full">
                <iframe
                    class="w-full"
                    src="'.$data['video'].'?rel=0&amp;controls=0&amp;showinfo=0"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                ></iframe>
            </div>';
        }
        elseif (!empty($data['player']))
        {
            $img = '<div class="w-full">
                <iframe
                    class="w-full"
                    src="'.$data['player'].'?rel=0&amp;controls=0&amp;showinfo=0"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                ></iframe>
            </div>';
        }

        // disposition
        if ($img && (!empty($title) || !empty($desc)))
        {
            $msg = '<div class="flex flex-col md:flex-row mb-2">
                <div class="w-full md:w-5/12">
                    '.$img.'</div>
                <div class="w-full pl-0 md:pl-4 pt-4 md:pt-0 md:w-7/12">
                    '.$title.$desc.$site.'
                </div>
            </div>';
        }
        else
        {
            $msg = '<div class="w-full mb-2">
                '.$title.$desc.$site.'
            </div>';
        }
        return $msg;
	}

	/**
	 * compact meta data array
	 */
	public static function compact_meta(aray $data) : string
	{
	    $a = array();
	    foreach ($data as $k => $v)
	    {
	        if (!empty($v))
	        {
	            $a[] = $k.'|'.str_replace('"', '&quot;', $v);
	        }
	    }
	    return implode('§', $a);
	}
}

