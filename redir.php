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
require_once (INCL_DIR.'user_functions.php');
dbconn(false);
if (!isset($CURUSER)) die();
$url = '';
while (list($var, $val) = each($_GET)) $url.= "&$var=$val";
if (preg_match("/([<>'\"]|&#039|&#33;|&#34|%27|%22|%3E|%3C|&#x27|&#x22|&#x3E|&#x3C|\.js)/i", $url)) header("Location: http://www.urbandictionary.com/define.php?term=twat");
$i = strpos($url, "&url=");
if ($i !== false) $url = substr($url, $i + 5);
if (substr($url, 0, 4) == "www.") $url = "http://".$url;
if (strlen($url) < 10) die();
echo "<html><head><meta http-equiv='refresh' content='3;url=$url'></head><body>\n";
echo "<div style='width:100%;text-align:center;background: #E9D58F;border: 1px solid #CEAA49;margin: 5px 0 5px 0;padding: 0 5px 0 5px;font-weight: bold;'>Redirecting you to:<br />\n";
echo htmlsafechars($url)."</div></body></html>\n";
?>
