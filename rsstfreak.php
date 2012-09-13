<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once (INCL_DIR.'html_functions.php');
require_once (INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();
$html = '';
$lang = load_language('global');
$use_limit = true;
$limit = 15;
$xml = file_get_contents('http://feed.torrentfreak.com/Torrentfreak/');
preg_match_all('/\<(title|pubDate|dc:creator|link|description)\>(.+?)\<\/\\1\>/i', $xml, $out, PREG_PATTERN_ORDER);
$feeds = $out[2];
$c = count($feeds);
$html = begin_main_frame().begin_frame('Torrent Freak news');
for ($i = 5; $i < $c; $i+= 5) {
    $html.= '<h3><u>'.$feeds[$i].'</u></h3><font class="small">by '.$feeds[$i + 3].' on '.$feeds[$i + 1].'</font><br /><p>'.str_replace(array(
        '<![CDATA[',
        ']]>'
    ) , '', $feeds[$i + 4]).'</p><br /><a href="'.$feeds[$i + 4].'" target="_blank"><font class="small">Read more</font></a>';
    if ($use_limit && $i >= ($limit * 5)) break;
}
$html.= end_frame().end_main_frame();
echo (stdhead('Torrent freak news').$html.stdfoot());
?>
