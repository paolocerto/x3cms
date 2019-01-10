<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 * Get meta
	 *
	 * @static
	 * @param string	$str
	 * @return array
	 */
    public static function getMetaTags($str)
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
	 *
	 * @static
	 * @param string	$str
	 * @return array
	 */
    public static function getTitle($str)
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
     *
     * @static
	 * @param string	$str
	 * @return str
     */
    public static function utf_fixer($str)
    {
        return X4Text_helper::fix_encoding($str);
        /*
        $src = [];
        $rpl = [];
        
        return str_replace($src, $rpl, $tmp);
        */
        //return \ForceUTF8\Encoding::fixUTF8($str);
        //return preg_replace('/[^\x20-\x7E]/','', $str);
    }
    
    
    /**
	 * Get meta
	 *
	 * @static
	 * @param string	$url
	 * @return array
	 */
    public static function extract_tags_from_url($url) 
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
        //$xml = implode($matches);
        
        try
        {
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML('<?xml encoding="utf-8" ?>'.$xml);
            
            $src = array('og:', 'twitter:');
            
            foreach($doc->getElementsByTagName('meta') as $metaTag) 
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
                //$xml = implode($matches);
                $doc = new DOMDocument();
                $doc->loadHTML($xml);
                foreach($doc->getElementsByTagName('title') as $title) 
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
	 *
	 * @static
	 * @param string	$url
	 * @return array
	 */
	public static function meta($url)
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
	 *
	 * @param   array    $data
	 * @return  string
	 */
	public static function youtube_embed($url)
	{
	    $uqs = explode('?', $url);
	    $t = explode('/', $uqs[0]);
	    $code = array_pop($t);
	    return 'https://www.youtube.com/embed/'.$code;
	}
	
	/**
	 * format post/comment contents
	 *
	 * @param   array    $data
	 * @return  string
	 */
	public static function format_content($data)
	{
	    $out = '';
	    if (isset($data['txt']))
	    {
            // post
            if (!empty($data['txt']))
            {
                $out = '<p>'.nl2br(X4Text_helper::linkify(stripslashes($data['txt']), ['http', 'https', 'mail'])).'</p>';
            }
            
            foreach($data['related'] as $i)
            {
                $out .= self::formatter($i);
            }
		}
		return $out;
	}
	
	public static function formatter($i)
	{
	    $out = '';
	    if (isset($i['img']))
        {
            // image
            $out .= '<div class="bordered"><img src="'.$i['img'].'" /></div>';
        }
        elseif (isset($i['question']))
        {
            $src = array('XGTX', 'XLTX', '<spam>', 'XQTX');
            $rpl = array('>', '<', '<span class="AM">', '"');
            
            $ol = array();
            for($c = 1; $c < 6; $c++)
            {
                $ol[] = '<li>'.str_replace($src, $rpl, $i['answer'.$c]).'</li>';
            }
            
            $out .= '<div class="bordered clearfix">
                    <blockquote>
                        <p><strong>'.$i['subject'].'</strong></p>
                        '.str_replace($src, $rpl, $i['question']).'
                        <ol type="A">
                            '.implode('', $ol).'
                        </ol>
                    </blockquote>
                </div>';
        }
        else
        {
            // link
            $msg = self::link_format($i);
            
            if (empty($i['url']))
            {
                $out .= '<div class="bordered clearfix">
                    '.$msg.'
                </div>';
            }
            else
            {
                $out .= '<div class="bordered clearfix">
                    <a target="_blank" href="'.$i['url'].'">'.$msg.'</a>
                </div>';
            }
        }
        return $out;
	}
	
	/**
	 * format link box
	 *
	 * @param   array    $data
	 * @return  string
	 */
	public static function link_format($data)
	{
	    // link
        $title = (empty($data['title']))
            ? ''
            : '<h4 class="navy pad-left pad-right">'.$data['title'].'</h4>';
        
        $img = (empty($data['image']))
            ? false
            : '<img src="'.$data['image'].'"/>';
            
        $desc = (empty($data['description']))
            ? ''
            :  '<p class="pad-left pad-right">'.$data['description'].'</p>';
          
        $site = (empty($data['site_name']))
            ? ''
            :  '<p class="pad-left pad-right">'.strtoupper($data['site_name']).'</p>';
            
        // video replace img
        if (!empty($data['video']))
        {
            $img = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="'.$data['video'].'?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
        }
        else if (!empty($data['player']))
        {
            $img = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="'.$data['player'].'?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
        }
            
        // disposition
        if ($img && (!empty($title) || !empty($desc)))
        {
            $msg = '<div class="col-xs-12 col-sm-5 no-pad">
                        '.$img.'
                    </div>
                    <div class="col-xs-12 col-sm-7 no-pad">
                        '.$title.$desc.$site.'
                    </div>';
        }
        else
        {
            $msg = '<div class="col-xs-12 no-pad">
                        '.$title.$desc.$site.'
                    </div>';
        }
        return $msg;
	}
	
	/**
	 * compact meta data array
	 *
	 * @param   array    $data
	 * @return  string
	 */
	public static function compact_meta($data)
	{
	    $a = array();
	    foreach($data as $k => $v)
	    {
	        if (!empty($v))
	        {
	            $a[] = $k.'|'.str_replace('"', '&quot;', $v);
	        }
	    }
	    return implode('§', $a);
	}
}

