<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

require_once('emoticons.php');
 
function source_highlighter($source,$lang2geshi)
{
	require_once('geshi/geshi.php');
	$source = str_replace(array("&#039;", "&gt;", "&lt;", "&quot;", "&amp;"), array("'", ">", "<", "\"", "&"),$source);
	$lang2geshi = ($lang2geshi == 'html' ? 'html4strict' : $lang2geshi);
			
		$geshi = new GeSHi($source,$lang2geshi);
		$geshi->set_header_type(GESHI_HEADER_PRE_VALID);
		$geshi->set_overall_style('font: normal normal 100% monospace; color: #000066;', false);
    $geshi->set_line_style('color: #003030;', 'font-weight: bold; color: #006060;', true);
		$geshi->set_code_style('color: #000020;font-family:monospace; font-size:12px;line-height:13px;', true);
		$geshi->enable_classes(false);
		$geshi->set_link_styles(GESHI_LINK, 'color: #000060;');
		$geshi->set_link_styles(GESHI_HOVER, 'background-color: #f0f000;');
		$return = "<div class=\"codetop\">Code</div><div class=\"codemain\">\n";
		$return .= $geshi->parse_code();
		$return .= "\n</div>\n";
	return  $return;	
}

 
//=== Inserts  smilies frame and smilie set: use by $use_this = smilies_frame($smilies,4,':thankyou:');
// $smilies_set > from emoticons
// 4 > number of columns
// $last_smilie_and_stop > blank to show all smilies, or smilie code to stop at say :thankyou: that is too big to display in the div
function smilies_frame($smilies_set,$number_of_columns,$last_smilie_and_stop)
  {
global $smilies, $customsmilies, $staff_smilies;
$count = 0;
$emoticons = '';
      
      while ((list($code, $url) = each($smilies_set)) && $code !== $last_smilie_and_stop) {
            
            $emoticons .= (((($count+1) % $number_of_columns == 0) || $count == 0) ? '<tr>' : '').'<td class="smilies_frame"><a href="#" title=" '.$code.' " class="emoticons"><img src="pic/smilies/'.$url.'" alt="" border="0" /></a></td>';
            $count++;
            $emoticons .= (($count+1) % $number_of_columns == 0 ? '</tr>' : '');
      }
return $emoticons;
}
//=== BBcode function will add a BBcode markup text area with smilies frame and tags if Javascript is enabled if not, it will just make a text area
  function BBcode($body)
  {
global $CURUSER, $smilies, $customsmilies, $staff_smilies, $INSTALLER09;
$emoticons_normal = smilies_frame($smilies,4,':hslocked:');
$emoticons_custom = smilies_frame($customsmilies,4,':wink_skull:');
$emoticons_staff = smilies_frame($staff_smilies,1,':dabunnies:');
$tags = '<tr><td>not yet added</td></tr>';

$bbcode = '<script type="text/javascript" src="bbcode/jquery.pack.js"></script>
	<script type="text/javascript" src="bbcode/markitup/jquery.markitup.pack.js"></script>
	<script type="text/javascript" src="bbcode/markitup/sets/bbcode/set.js"></script>
  <script type="text/javascript">
		/*<![CDATA[*/
		// set up the emoticon stuff
		$(document).ready(function()	{

			// hide custom and staff
			$("#box_1").hide();
			$("#box_2").hide();
			$("#box_3").hide();
			$("#box_4").hide();
			
			$("#box_1").fadeIn("slow");

				// show hide for all
				$("a#smilies").click(function(){
				$("#box_1").show("slow");
				$("#box_2").hide();
				$("#box_3").hide();
				$("#box_4").hide();
				});

				$("a#custom").click(function(){
				$("#box_1").hide();
				$("#box_2").show("slow");
				$("#box_3").hide();
				$("#box_4").hide();
				});

				$("a#staff").click(function(){
				$("#box_1").hide();
				$("#box_2").hide();
				$("#box_3").show("slow");
				$("#box_4").hide();
				});


	// Add editor
	$("#markItUp").markItUp(mySettings);

	// add smilies	
	$(".emoticons").click(function() {
 		$.markItUp( { 	openWith:$(this).attr("title")}
				);
 		return false;
	});
	
	// add more options
	$("#tool_open").click(function(){
	$("#tools").slideToggle("slow", function() {
	});
	$("#tool_open").hide();
	$("#tool_close").show();
	});
	
	$("#tool_close").click(function(){
	$("#tools").slideToggle("slow", function() {
	});
	
	$("#tool_close").hide();
	$("#tool_open").show();

	});

	// add attachments
	$("#more").click(function(){
	$("#attach_more").slideToggle("slow", function() {
	});
	});
	});
	/*]]>*/
  </script>
  <table>
	<tr>
		<td class="two;white-space:nowrap;">
		<textarea id="markItUp" cols="75" rows="18" name="body">'.$body.'</textarea>
		</td>
		<td class= "two" valign="top" width="200" align="center"><span class="postbody;white-space:nowrap;">
    <a href="#BBcode" id="smilies" class="altlink">Smilies</a> '.($CURUSER['smile_until'] > 0 ? '<a href="#BBcode" id="custom" class="altlink">Custom</a> ' : '')
		.($CURUSER['class'] < UC_STAFF ? '' : '<a href="#BBcode" id="staff" class="altlink">Staff</a> ').'</span>
		<div class="scroll" id="box_0" style="display:none">
	<table>
	<tr>
  <td class="smilies_frame"  valign="middle" align="center" width="80" height="300"><img src="pic/forums/updating.gif" alt="Loading..." /></td>
	</tr>
	</table>
	</div>
  <div class="scroll" id="box_1" style="display:none">
	<table>'.$emoticons_normal.'</table>
	</div>
		'.($CURUSER['smile_until'] > 0 ? '<div class="scroll" id="box_2" style="display:none">
	<table>'.$emoticons_custom.'</table>
	</div>' : '')
		.($CURUSER['class'] < UC_STAFF ? '' : '<div class="scroll" id="box_3" style="display:none">
	<table>'.$emoticons_staff.'</table>
	</div>').'
	</td></tr>
	</table>
	'.(($CURUSER['class'] < UC_UPLOADER &&  (isset($_GET['action']) && $_GET['action'] <> 'new_topic')) ? '' :
	'<span style="text-align: right;">
		<a class="altlink"  title="More Options"  id="tool_open" style="font-weight:bold;cursor:pointer;"><img src="pic/forums/more.gif" alt="+" width="18" /> More Options</a> 
		<a class="altlink"  title="Close More Options"  id="tool_close" style="font-weight:bold;cursor:pointer;display:none"><img src="pic/forums/less.gif" alt="-" width="18" /> Close More Options</a> 
	</span>');

return $bbcode;
}

//Finds last occurrence of needle in haystack
//in PHP5 use strripos() instead of this
function _strlastpos ($haystack, $needle, $offset = 0)
{
	$addLen = strlen ($needle);
	$endPos = $offset - $addLen;
	while (true)
	{
		if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
		$endPos = $newPos;
	}
	return ($endPos >= 0) ? $endPos : false;
}

//=== check images for nasty crud by CoLdFuSiOn
function xss_detect( $html )
    {
        /*
        * check for any nastiness < S r I p T > <    / sc  r  IP t> etc
        * If you wanted, you can quitely log any finds;)
        */
        if (preg_match( "#<(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", $html ))
            return true;
        if (preg_match( "#<(\s+?)?/(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", $html ))
            return true;
        /*
        * look for the usual candidates
        * feel free to add what you need
        */
        if( preg_match("/javascript|alert|about|onmouseover|onclick|onload|onsubmit|<body|<html|document\./i" , $html ))
            return true;
        /* still here? Must be sort of ok, maybe... */
        return false;
    }
    
    function check_image($url='')
    {
        static $image_count = 0; // do not alter this!
        $allow_dynamic_img = 0; //You alter this value at your own peril!
        $max_images = 2000; //Maximum number of images allowed, after which the raw string is returned.
        $img_ext = 'jpg,gif,png'; //image extension. Careful what you put here!
        if (!$url) return; //empty? send it back!
        $url = trim($url);
        $default = '[img]'.$url.'[/img]'; //this is what is returned after images are exceeded
        $image_count++;
        /*
        * is this true and have we exceeded it?
        */
        if ($max_images)
        {
            if ($image_count > $max_images)
            {
                return $default;
            }
        }
        /*
        * Check for any dynamic stuff!
        */
        if ($allow_dynamic_img != 1)
        {
            if (preg_match( "/[?&;]/", $url))
                return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
            if (preg_match( "/javascript(\:|\s)/i", $url ))
                return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
        }
        /*
        * Check the extension
        */
        if ($img_ext)
        {
            $extension = preg_replace( "#^.*\.(\S+)$#", "\\1", $url );    
            $extension = strtolower($extension);
            
            if ( (! $extension) OR ( preg_match( "#/#", $extension ) ) )
                return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
            $img_ext = strtolower($img_ext);
            
            if ( ! preg_match( "/".preg_quote($extension, '/')."(,|$)/", $img_ext ))
                return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
            //$url = xss_detect($url);
            if (xss_detect($url))
                return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
        }
        /*
        * Take a stab at getting a good image url
        */
        if (!preg_match( "/^(http|https|ftp):\/\//i", $url )) 
            return '<img src="pic/imagenotfound.jpg" alt="image not found" />';
        /*
        * done all we can at this point!
        */
        $url = str_replace(' ', '%20', $url );
        
        return '<img src="'.$url.'" alt="'.$url.'" title="'.$url.'" />';
}


//=== new test for BBcode errors from http://codesnippets.joyent.com/posts/show/959 by berto
function check_BBcode($html) {
	preg_match_all('#<(?!img|br|hr\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('#</([a-z]+)>#iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);
	if (count($closedtags) == $len_opened) {
		return $html;
	}
	$openedtags = array_reverse($openedtags);
	for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$html .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	}
	return $html;
}    
                               
           //==format quotes by Retro
           function format_quotes($s)
           {
            preg_match_all('/\\[quote.*?\\]/', $s, $result, PREG_PATTERN_ORDER);
            $openquotecount = count($openquote = $result[0]);
            preg_match_all('/\\[\/quote\\]/', $s, $result, PREG_PATTERN_ORDER);
            $closequotecount = count($closequote = $result[0]);
            if ($openquotecount != $closequotecount) return $s; // quote mismatch. Return raw string...
            // Get position of opening quotes
            $openval = array();
            $pos = -1;
            foreach($openquote as $val)
            $openval[] = $pos = strpos($s, $val, $pos + 1);
            // Get position of closing quotes
            $closeval = array();
            $pos = -1;
            foreach($closequote as $val)
            $closeval[] = $pos = strpos($s, $val, $pos + 1);
            for ($i = 0; $i < count($openval); $i++)
            if ($openval[$i] > $closeval[$i]) return $s; // Cannot close before opening. Return raw string...
            $s = str_replace("[quote]", "<b>Quote:</b><br /><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>", $s);
            $s = preg_replace("/\\[quote=(.+?)\\]/", "<b>\\1 wrote:</b><br /><table class='main' border='1' cellspacing='0' cellpadding='10'><tr><td style='border: 1px black dotted'>", $s);
            $s = str_replace("[/quote]", "</td></tr></table><br />", $s);
            return $s;
            }
      
 function islocal($link)
        {
            global $INSTALLER09;
            $flag = false;
            $limit = 60;
            $INSTALLER09['url']  = str_replace(array('http://','www','http://www'),'',$INSTALLER09['baseurl']);
            if (false !== stristr($link[0], '[url=')) {
                $url = trim($link[1]);
                $title = trim($link[2]);
                if (false !== stristr($link[2], '[img]')) {
                    $flag = true;
                    $title = preg_replace("/\[img](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i", "<img src=\"\\1\" alt=\"\" border=\"0\" />", $title);
                }
            } elseif (false !== stristr($link[0], '[url]'))
                $url = $title = trim($link[1]);
            else
                $url = $title = trim($link[2]);

            if (strlen($title) > $limit && $flag == false) {
                $l[0] = substr($title, 0, ($limit / 2));
                $l[1] = substr($title, strlen($title) - round($limit / 3));
                $lshort = $l[0] . "..." . $l[1];
            } else $lshort = $title;
            return "&nbsp;<a href=\"" . ((stristr($url, $INSTALLER09['url']) !== false) ? "" : "http://anonym.to?") . $url . "\" target=\"_blank\">" . $lshort . "</a>";
        }

        function format_urls($s)
        {
        return preg_replace_callback("/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^<>\s]+)/i", "islocal", $s);
        }


function format_comment($text, $strip_html = true, $urls = true)
{
	global $smilies, $staff_smilies, $customsmilies, $INSTALLER09, $CURUSER;

	$s = $text;
  unset($text);
  // This fixes the extraneous ;) smilies problem. When there was an html escaped
  // char before a closing bracket - like >), "), ... - this would be encoded
  // to &xxx;), hence all the extra smilies. I created a new :wink: label, removed
  // the ;) one, and replace all genuine ;) by :wink: before escaping the body.
  // (What took us so long? :blush:)- wyz
	$s = str_replace(';)', ':wink:', $s);
	// fix messed up links
	$s = str_replace('&amp;', '&', $s);
	if ($strip_html)
   $s = htmlentities($s, ENT_QUOTES, 'UTF-8');
   if( preg_match( "#function\s*\((.*?)\|\|#is", $s ) )
   {
		$s = str_replace( ":"     , "&#58;", $s );
		$s = str_replace( "["     , "&#91;", $s );
		$s = str_replace( "]"     , "&#93;", $s );
		$s = str_replace( ")"     , "&#41;", $s );
		$s = str_replace( "("     , "&#40;", $s );
		$s = str_replace( "{"	 , "&#123;", $s );
		$s = str_replace( "}"	 , "&#125;", $s );
		$s = str_replace( "$"	 , "&#36;", $s );   
  }
  // BBCode to find...
	$bb_code_in = array( 	 '/\[b\]\s*((\s|.)+?)\s*\[\/b\]/i',	
					 '/\[i\]\s*((\s|.)+?)\s*\[\/i\]/i',
					 '/\[u\]\s*((\s|.)+?)\s*\[\/u\]/i',
					 '/\[email\](.*?)\[\/email\]/i',
					 '/\[align=([a-zA-Z]+)\]((\s|.)+?)\[\/align\]/i',
					 '/\[blockquote\]\s*((\s|.)+?)\s*\[\/blockquote\]/i',
					 '/\[strike\]\s*((\s|.)+?)\s*\[\/strike\]/i',
					 '/\[s\]\s*((\s|.)+?)\s*\[\/s\]/i',
					 '/\[pre\]\s*((\s|.)+?)\s*\[\/pre\]/i',
					 '/\[marquee\](.*?)\[\/marquee\]/i',
					 '/\[collapse=(.*?)\]\s*((\s|.)+?)\s*\[\/collapse\]/i',
					 '/\[size=([1-7])\]\s*((\s|.)+?)\s*\[\/size\]/i',
					 '/\[color=([a-zA-Z]+)\]\s*((\s|.)+?)\s*\[\/color\]/i',
					 '/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]\s*((\s|.)+?)\s*\[\/color\]/i',
					 '/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i',
					 '/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]/i',
					 '/\[video=[^\s\'"<>]*youtube.com.*v=([^\s\'"<>]+)\]/ims',
					 "/\[video=[^\s'\"<>]*video.google.com.*docid=(-?[0-9]+).*\]/ims",
					 '/\[audio\](http:\/\/[^\s\'"<>]+(\.(mp3|aiff|wav)))\[\/audio\]/i',
					 '/\[list=([0-9]+)\]((\s|.)+?)\[\/list\]/i',
					 '/\[list\]((\s|.)+?)\[\/list\]/i',
					 '/\[\*\]\s?(.*?)\n/i',
					 '/\[li\]\s?(.*?)\n/i',
					 '/\[hr\]/'
	);


  // And replace them by...
	$bb_code_out = array('<span style="font-weight: bold;">\1</span>',
					 '<span style="font-style: italic;">\1</span>',
					 '<span style="text-decoration: underline;">\1</span>',
					 '<a class="altlink" href="mailto:\1">\1</a>',
					 '<div style="text-align: \1;">\2</div>',
					 '<blockquote class="style"><span>\1</span></blockquote>',
					 '<span style="text-decoration: line-through;">\1</span>',
					 '<span style="text-decoration: line-through;">\1</span>',
					 '<span style="white-space: nowrap;">\1</span>',
					 '<marquee class="style">\1</marquee>',
					 '<div style="padding-top: 2px; white-space: nowrap"><span style="cursor: hand; cursor: pointer; border-bottom: 1px dotted" onclick="if (document.getElementById(\'collapseobj\1\').style.display==\'block\') {document.getElementById(\'collapseobj\1\').style.display=\'none\' } else { document.getElementById(\'collapseobj\1\').style.display=\'block\' }">\1</span></div><div id="collapseobj\1" style="display:none; padding-top: 2px; padding-left: 14px; margin-bottom:10px; padding-bottom: 2px; background-color: #FEFEF4;">\2</div>',
					 '<span class="size\1">\2</span>',
					 '<span style="color:\1;">\2</span>',
					 '<span style="color:\1;">\2</span>',
					 '<span style="font-family:\'\1\';">\2</span>',
					 '<table cellspacing="0" cellpadding="10"><tr><td class="forum_head_dark" style="padding:5px">Spoiler! to view, roll over the spoiler box.</td></tr><tr><td class="spoiler"><a href="#">\\1</a></td></tr></table><br />',
					 '<object width="500" height="410"><param name="movie" value="http://www.youtube.com/v/\1"></param><embed src="http://www.youtube.com/v/\\1" type="application/x-shockwave-flash" width="500" height="410"></embed></object>',
					 "<embed style=\"width:500px; height:410px;\" id=\"VideoPlayback\" align=\"middle\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=\\1\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"#ffffff\" scale=\"noScale\" wmode=\"window\" salign=\"TL\"  FlashVars=\"playerMode=embedded\"> </embed>",
					 '<span style="text-align: center;"><p>Audio From: \1</p><embed type="application/x-shockwave-flash" src="http://www.google.com/reader/ui/3247397568-audio-player.swf?audioUrl=\\1" width="400" height="27" allowscriptaccess="never" quality="best" bgcolor="#ffffff" wmode="window" flashvars="playerMode=embedded" /></span>',
					 '<ol class="style" start="\1">\2</ol>',
					 '<ul class="style">\1</ul>',
					 '<li>\1</li>',
					 '<li>\1</li>',
					 '<hr />'
	);

	$s = preg_replace($bb_code_in, $bb_code_out, $s);
   if ($urls)
   $s = format_urls($s);
   if (stripos($s, '[url') !== false) {
   $s = preg_replace_callback("/\[url=([^()<>\s]+?)\](.+?)\[\/url\]/is", "islocal", $s);
   // [url]http://www.example.com[/url]
   $s = preg_replace_callback("/\[url\]([^()<>\s]+?)\[\/url\]/is", "islocal", $s);
   }
	// Linebreaks
	$s = nl2br($s);
   // Dynamic Vars
   $s = dynamic_user_vars($s);
	// [pre]Preformatted[/pre]
   if (stripos($s, '[pre]') !== false)
	$s = preg_replace("/\[pre\]((\s|.)+?)\[\/pre\]/i", "<tt><span style=\"white-space: nowrap;\">\\1</span></tt>", $s);
	// [nfo]NFO-preformatted[/nfo]
   if (stripos($s, '[nfo]') !== false)
	$s = preg_replace("/\[nfo\]((\s|.)+?)\[\/nfo\]/i", "<tt><span style=\"white-space: nowrap;\"><font face='MS Linedraw' size='2' style='font-size: 10pt; line-height:" ."10pt'>\\1</font></span></tt>", $s);
   //==Media tag
   if (stripos($s, '[media=') !== false) {
   $s = preg_replace( "#\[media=(youtube|liveleak|GameTrailers|imdb)\](.+?)\[/media\]#ies", "_MediaTag('\\2','\\1')" , $s );
   $s = preg_replace( "#\[media=(youtube|liveleak|GameTrailers|vimeo)\](.+?)\[/media\]#ies", "_MediaTag('\\2','\\1')" , $s );
   }
   if (stripos($s, '[img') !== false) {      
   // [img=http://www/image.gif]
   $s = preg_replace("/\[img\]((http|https):\/\/[^\s'\"<>]+(\.(jpg|gif|png|bmp)))\[\/img\]/i", "<a href=\"\\1\" rel=\"lightbox\"><img src=\"\\1\" border=\"0\" alt=\"\" style=\"max-width: 150px;\" /></a>", $s);
   // [img=http://www/image.gif]
   $s = preg_replace("/\[img=((http|https):\/\/[^\s'\"<>]+(\.(gif|jpg|png|bmp)))\]/i", "<a href=\"\\1\" rel=\"lightbox\"><img src=\"\\1\" border=\"0\" alt=\"\"  style=\"max-width: 150px;\" /></a>", $s);
   }
   // the [you] tag
   if (stripos($s, '[you]') !== false)
   $s = preg_replace("/\[you\]/i", $CURUSER['username'], $s);
   // [php]code[/php]
   if (stripos($s, '[php]') !== false)
   $s = preg_replace("#\[(php|sql|html)\](.+?)\[\/\\1\]#ise","source_highlighter('\\2','\\1')",$s);
	// Maintain spacing
	$s = str_replace('  ', ' &nbsp;', $s);
   if (isset($smilies)) 
	foreach($smilies as $code => $url) 
	{
	$s = str_replace($code, "<img border='0' src=\"{$INSTALLER09['pic_base_url']}smilies/{$url}\" alt=\"\" />", $s);
	//$s = str_replace($code, '<span id="'.$attr.'"></span>', $s); 
	}
	if (isset($staff_smilies)) 
	foreach($staff_smilies as $code => $url) 
	{
	$s = str_replace($code, "<img border='0' src=\"{$INSTALLER09['pic_base_url']}smilies/{$url}\" alt=\"\" />", $s);
	//$s = str_replace($code, '<span id="'.$attr.'"></span>', $s); 
	}
	if (isset($customsmilies)) 
	foreach($customsmilies as $code => $url) 
	{
	$s = str_replace($code, "<img border='0' src=\"{$INSTALLER09['pic_base_url']}smilies/{$url}\" alt=\"\" />", $s);
	//$s = str_replace($code, '<span id="'.$attr.'"></span>', $s); 
	}

	$s = format_quotes($s);
	$s = check_BBcode($s);
	return $s;
  }

//=== no bb code in post
function format_comment_no_bbcode($text, $strip_html = true)
{
	global $INSTALLER09;
	$s = $text;
	if ($strip_html)
		//$s = htmlspecialchars($s);
		$s = htmlentities($s, ENT_QUOTES, 'UTF-8');
  	// BBCode to find...
  	//=== basically will change this into a sort of strip tags but of bbcode shor of the code tag
	 $bb_code_in = array( 	 '/\[b\]\s*((\s|.)+?)\s*\[\/b\]/i',	
					 '/\[i\]\s*((\s|.)+?)\s*\[\/i\]/i',
					 '/\[u\]\s*((\s|.)+?)\s*\[\/u\]/i',
					 '#\[img\](.+?)\[/img\]#ie',
					 '#\[img=(.+?)\]#ie',
					 '/\[email\](.*?)\[\/email\]/i',
					 '/\[align=([a-zA-Z]+)\]((\s|.)+?)\[\/align\]/i',
					 '/\[blockquote\]\s*((\s|.)+?)\s*\[\/blockquote\]/i',
					 '/\[strike\]\s*((\s|.)+?)\s*\[\/strike\]/i',
					 '/\[s\]\s*((\s|.)+?)\s*\[\/s\]/i',
					 '/\[pre\]\s*((\s|.)+?)\s*\[\/pre\]/i',
					 '/\[marquee\](.*?)\[\/marquee\]/i',
					 '/\[url\="?(.*?)"?\]\s*((\s|.)+?)\s*\[\/url\]/i',
					 '/\[url\]\s*((\s|.)+?)\s*\[\/url\]/i',
					 '/\[collapse=(.*?)\]\s*((\s|.)+?)\s*\[\/collapse\]/i',
					 '/\[size=([1-7])\]\s*((\s|.)+?)\s*\[\/size\]/i',
					 '/\[color=([a-zA-Z]+)\]\s*((\s|.)+?)\s*\[\/color\]/i',
					 '/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]\s*((\s|.)+?)\s*\[\/color\]/i',
					 '/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i',
					 '/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i',
					 '/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i',
					 '/\[spoiler\]\s*((\s|.)+?)\s*\[\/spoiler\]\s*/i',
					 '/\[video=[^\s\'"<>]*youtube.com.*v=([^\s\'"<>]+)\]/ims',
					 "/\[video=[^\s'\"<>]*video.google.com.*docid=(-?[0-9]+).*\]/ims",
					 '/\[audio\](http:\/\/[^\s\'"<>]+(\.(mp3|aiff|wav)))\[\/audio\]/i',
					 '/\[list=([0-9]+)\]((\s|.)+?)\[\/list\]/i',
					 '/\[list\]((\s|.)+?)\[\/list\]/i',
					 '/\[\*\]\s?(.*?)\n/i',
					 '/\[hr\]\s?(.*?)\n/i'
	);
	// And replace them by...
	$bb_code_out = array(	 '\1',
					 '\1',
					 '\1',
					'\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\2',
					 '\2',
					 '\2',
					 '\2',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '\1',
					 '');
	$s = preg_replace($bb_code_in, $bb_code_out, $s);
	// Linebreaks
	$s = nl2br($s);
	// Maintain spacing
	$s = str_replace('  ', '&nbsp;', $s);
	return $s;
  }
            

    function _MediaTag( $content, $type ) {
	  global $INSTALLER09;
	  if( $content == '' OR $type == '' )
 	  return;
	  $return = '';
	
	  switch ($type) 
	  {
 	  case 'youtube':
 	  $return = preg_replace("#^http://(?:|www\.)youtube\.com/watch\?v=([a-zA-Z0-9\-]+)+?$#i","<object type='application/x-shockwave-flash' height='355' width='425' data='http://www.youtube.com/v/\\1'><param name='movie' value='http://www.youtube.com/v/\\1' /><param name='allowScriptAccess' value='sameDomain' /><param name='quality' value='best' /><param name='bgcolor' value='#FFFFFF' /><param name='scale' value='noScale' /><param name='salign' value='TL' /><param name='FlashVars' value='playerMode=embedded' /><param name='wmode' value='transparent' /></object>", $content);
 	  break;
 	
 	  case 'liveleak':
 	  $return = preg_replace("#^http://(?:|www\.)liveleak\.com/view\?i=([_a-zA-Z0-9\-]+)+?$#i","<object type='application/x-shockwave-flash' height='355' width='425' data='http://www.liveleak.com/e/\\1'><param name='movie' value='http://www.liveleak.com/e/\\1' /><param name='allowScriptAccess' value='sameDomain' /><param name='quality' value='best' /><param name='bgcolor' value='#FFFFFF' /><param name='scale' value='noScale' /><param name='salign' value='TL' /><param name='FlashVars' value='playerMode=embedded' /><param name='wmode' value='transparent' /></object>", $content);
 	  break;
 	
 	  case 'GameTrailers':
 	  $return = preg_replace("#^http://(?:|www\.)gametrailers\.com/video/([\-_a-zA-Z0-9\-]+)+?/([0-9]+)+?$#i","<object type='application/x-shockwave-flash' height='355' width='425' data='http://www.gametrailers.com/remote_wrap.php?mid=\\2'><param name='movie' value='http://www.gametrailers.com/remote_wrap.php?mid=\\2' /><param name='allowScriptAccess' value='sameDomain' /> <param name='allowFullScreen' value='true' /><param name='quality' value='high' /></object>", $content);
 	  break;
 	  
 	  case 'imdb':
    $return = preg_replace("#^http://(?:|www\.)imdb\.com/video/screenplay/([_a-zA-Z0-9\-]+)+?$#i","<div class='\\1'><div style=\"padding: 3px; background-color: transparent; border: none; width:690px;\"><div style=\"text-transform: uppercase; border-bottom: 1px solid #CCCCCC; margin-bottom: 3px; font-size: 0.8em; font-weight: bold; display: block;\"><span onclick=\"if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = ''; this.innerHTML = '<b>Imdb Trailer: </b><a href=\'#\' onclick=\'return false;\'>hide</a>'; } else { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerHTML = '<b>Imdb Trailer: </b><a href=\'#\' onclick=\'return false;\'>show</a>'; }\" ><b>Imdb Trailer: </b><a href=\"#\" onclick=\"return false;\">show</a></span></div><div class=\"quotecontent\"><div style=\"display: none;\"><iframe style='vertical-align: middle;' src='http://www.imdb.com/video/screenplay/\\1/player' scrolling='no' width='660' height='490' frameborder='0'></iframe></div></div></div></div>", $content);
 	  break;
 	  
 	  case 'vimeo':
    $return = preg_replace("#^http://(?:|www\.)vimeo\.com/([0-9]+)+?$#i","<object type='application/x-shockwave-flash' width='425' height='355' data='http://vimeo.com/moogaloop.swf?clip_id=\\1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1'>
    <param name='allowFullScreen' value='true' />
    <param name='allowScriptAccess' value='sameDomain' />
    <param name='movie' value='http://vimeo.com/moogaloop.swf?clip_id=\\1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1' />
    <param name='quality' value='high' />
    </object>", $content);
    break;
    
 	  default:
 	  $return = 'not found';
	  }

	  return $return;
    }
    
     
  //=== smilie function
  function get_smile()
  {
  global $CURUSER;
  return $CURUSER["smile_until"];
  }
 
////////////09 bbcode function by putyn///////////////
function textbbcode($form,$text,$content="") {
global $CURUSER, $INSTALLER09;
$custombutton = '';
if(get_smile() != '0')
$custombutton .=" <span style='font-weight:bold;font-size:8pt;'><a href=\"javascript:PopCustomSmiles('".$form."','".$text."')\">[ Custom Smilies ]</a></span>";
$smilebutton = "<a href=\"javascript:PopMoreSmiles('".$form."','".$text."')\">[ More Smilies ]</a>";
$bbcodebody =<<<HTML
<script type="text/javascript">
	var textBBcode = "{$text}";
</script>
<script type="text/javascript" src="./scripts/textbbcode.js"></script>
<div id="hover_pick" style="width:25px; height:25px; position:absolute; border:1px solid #333333; display:none; z-index:20;"></div>
<div id="pickerholder"></div>
<table cellpadding="5" cellspacing="0" align="center"  border="1" class="bb_holder">
  <tr>
    <td width="100%" style="background:#CCCCCC; padding:0" colspan="2"><div style="float:left;padding:4px 0px 0px 2px;">
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/bold.png" onclick="tag('b')" title="Bold" alt="B" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/italic.png" onclick="tag('i')" title="Italic" alt="I" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/underline.png" onclick="tag('u')" title="Underline" alt="U" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/strike.png" onclick="tag('s')" title="Strike" alt="S" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/link.png" onclick="clink()" title="Link" alt="Link" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/picture.png" onclick="cimage()" title="Add image" alt="Image"/> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/colors.png" onclick="colorpicker();" title="Select Color" alt="Colors" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/email.png" onclick="mail()" title="Add email" alt="Email" /> 
HTML;
if($CURUSER['class'] >= UC_MODERATOR)
$bbcodebody .=<<<HTML
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/php.png" onclick="tag('php')" title="Add php" alt="Php" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/sql.png" onclick="tag('sql')" title="Add sql" alt="Sql" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/script.png" onclick="tag('html')" title="Add html" alt="Html" /> 
	<img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/modcom.png" onclick="tag('mcom')" title="Mod comment" alt="Mod comment" />
HTML;
$bbcodebody .=<<<HTML
</div>
      <div style="float:right;padding:4px 2px 0px 0px;"> <img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/align_center.png" onclick="wrap('align','','center')" title="Align - center" alt="Center" /> <img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/align_left.png" onclick="wrap('align','','left')" title="Align - left" alt="Left" /> <img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/align_justify.png" onclick="wrap('align','','justify')" title="Align - justify" alt="justify" /> <img class="bb_icon" src="{$INSTALLER09['pic_base_url']}bbcode/align_right.png" onclick="wrap('align','','right')" title="Align - right" alt="Right" /> </div></td>
  </tr>
  <tr>
    <td width="100%" style="background:#CCCCCC; padding:0;" colspan="2"><div style="float:left;padding:4px 0px 0px 2px;">
        <select name="fontfont" id="fontfont"  class="bb_icon" onchange="font('font',this.value);" title="Font face">
          <option value="0">Font</option>
          <option value="Arial" style="font-family: Arial;">Arial</option>
          <option value="Arial Black" style="font-family: Arial Black;">Arial Black</option>
          <option value="Comic Sans MS" style="font-family: Comic Sans MS;">Comic Sans MS</option>
          <option value="Courier New" style="font-family: Courier New;">Courier New</option>
          <option value="Franklin Gothic Medium" style="font-family: Franklin Gothic Medium;">Franklin Gothic Medium</option>
          <option value="Georgia" style="font-family: Georgia;">Georgia</option>
          <option value="Helvetica" style="font-family: Helvetica;">Helvetica</option>
          <option value="Impact" style="font-family: Impact;">Impact</option>
          <option value="Lucida Console" style="font-family: Lucida Console;">Lucida Console</option>
          <option value="Lucida Sans Unicode" style="font-family: Lucida Sans Unicode;">Lucida Sans Unicode</option>
          <option value="Microsoft Sans Serif" style="font-family: Microsoft Sans Serif;">Microsoft Sans Serif</option>
          <option value="Palatino Linotype" style="font-family: Palatino Linotype;">Palatino Linotype</option>
          <option value="Tahoma" style="font-family: Tahoma;">Tahoma</option>
          <option value="Times New Roman" style="font-family: Times New Roman;">Times New Roman</option>
          <option value="Trebuchet MS" style="font-family: Trebuchet MS;">Trebuchet MS</option>
          <option value="Verdana" style="font-family: Verdana;">Verdana</option>
          <option value="Symbol" style="font-family: Symbol;">Symbol</option>
        </select>
        <select name="fontsize" id="fontsize" class="bb_icon" style="padding-bottom:3px;" onchange="font('size',this.value);" title="Font size">
          <option value="0">Font size</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
        </select>
      </div>
      <div style="float:right;padding:4px 2px 0px 0px;"> <img class="bb_icon" src="pic/bbcode/text_uppercase.png" onclick="text('up')" title="To Uppercase" alt="Up" /> <img class="bb_icon" src="pic/bbcode/text_lowercase.png" onclick="text('low')" title="To Lowercase" alt="Low" /> <img class="bb_icon" src="pic/bbcode/zoom_in.png" onclick="fonts('up')" title="Font size up" alt="S up" /> <img class="bb_icon" src="pic/bbcode/zoom_out.png" onclick="fonts('down')" title="Font size up" alt="S down" /> </div></td>
  </tr>
  <tr>
    <td><textarea id="{$text}" name="{$text}" rows="2" cols="2" style="width:530px; height:250px;font-size:12px;">{$content}</textarea></td>
    <td align="center" valign="top"><table width="0" cellpadding="2" border="1" class="em_holder" cellspacing="2">
         <tr>
          <td align="center"><a href="javascript:em(':-)');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/smile1.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':smile:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/smile2.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':-D');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/grin.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':w00t:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/w00t.gif" width="18" height="20" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':-P');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/tongue.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(';-)');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/wink.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':-|');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/noexpression.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':-/');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/confused.gif" width="18" height="18" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':-(');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/sad.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':baby:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/baby.gif" width="20" height="22" /></a></td>
          <td align="center"><a href="javascript:em(':-O');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/ohmy.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em('|-)');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/sleeping.gif" width="20" height="27" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':innocent:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/innocent.gif" width="18" height="22" /></a></td>
          <td align="center"><a href="javascript:em(':unsure:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/unsure.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':closedeyes:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/closedeyes.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':cool:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/cool2.gif" width="20" height="20" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':thumbsdown:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/thumbsdown.gif" width="27" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':blush:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/blush.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':yes:');"><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/yes.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':no:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/no.gif" width="20" height="20" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':love:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/love.gif" width="19" height="19" /></a></td>
          <td align="center"><a href="javascript:em(':?:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/question.gif" width="19" height="19" /></a></td>
          <td align="center"><a href="javascript:em(':!:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/excl.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':idea:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/idea.gif" width="19" height="19" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':arrow:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/arrow.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':arrow2:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/arrow2.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':hmm:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/hmm.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':hmmm:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/hmmm.gif" width="25" height="23" /></a></td>
        </tr>
        <tr>
          <td align="center"><a href="javascript:em(':huh:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/huh.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':rolleyes:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/rolleyes.gif" width="20" height="20" /></a></td>
          <td align="center"><a href="javascript:em(':kiss:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/kiss.gif" width="18" height="18" /></a></td>
          <td align="center"><a href="javascript:em(':shifty:');" ><img border="0" alt="Smilies" src="{$INSTALLER09['pic_base_url']}smilies/shifty.gif" width="20" height="20" /></a></td>
        </tr>
        <tr>
          <td colspan="4" align="center" style="white-space:nowrap;"><span style='font-weight:bold;font-size:8pt;'>{$smilebutton}</span>{$custombutton}</td>
        </tr>
      </table></td></tr></table>
HTML;
	return $bbcodebody;
}

function user_key_codes($key)
        {
            return "/\[$key\]/i";
        }

        function dynamic_user_vars($text)
        {
            global $CURUSER, $INSTALLER09;
            if (!isset($CURUSER)) 
            return;
            $zone = 0;        // GMT
            //$zone = 3600 * -5; // EST
            $tim = TIME_NOW + $zone;
            $cu = $CURUSER;
            // unset any variables ya dun want to display, or can't display
            unset($cu['passhash'], $cu['secret'], $cu['editsecret'], $cu['passkey'], $cu['modcomment']);
            $bbkeys = array_keys($cu);
            $bbkeys[] = 'curdate';
            $bbkeys[] = 'curtime';
            $bbvals = array_values($cu);
            $bbvals[] = gmdate('F jS, Y', $tim);
            $bbvals[] = gmdate('g:i A', $tim);
            $bbkeys = array_map('user_key_codes', $bbkeys);
            return @preg_replace($bbkeys, $bbvals, $text);
        }

?>
