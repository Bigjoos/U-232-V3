<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
dbconn();

$passkey = (isset($_GET["passkey"]) ? htmlsafechars($_GET["passkey"]) : '');
$feed = (isset($_GET["type"]) && $_GET['type'] == 'dl'? 'dl' : 'web');
$cats = (isset($_GET["cats"]) ? $_GET["cats"] : "");

if(!empty($passkey))
{
	if(strlen($passkey) !=32)
	die("Your passkey is not long enough! Go to ".$INSTALLER09['site_name']." and reset your passkey");
	else 
	{
		if(get_row_count("users","where passkey=".sqlesc($passkey)) != 1)
		die("Your passkey is invalid !Go to ".$INSTALLER09['site_name']." and reset your passkey");
	}
}
else die('Your link doesn\'t have a passkey');

$INSTALLER09['rssdescr'] = $INSTALLER09['site_name']." some motto goes here!";

$where = !empty($cats) ? "t.category IN (".$cats.") AND " : '';

header("Content-Type: application/xml");
$HTMLOUT = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
"<title>" . $INSTALLER09['site_name'] . "</title>\n<link>" . $INSTALLER09['baseurl'] . "</link>\n<description>" . $INSTALLER09['rssdescr'] . "</description>\n" .
"<language>en-usde</language>\n<copyright>Copyright © ".date('Y')." " . $INSTALLER09['site_name'] . "</copyright>\n<webMaster>" . $INSTALLER09['site_email'] . "</webMaster>\n" .
"<image><title>" .$INSTALLER09['site_name']. "</title>\n<url>" . $INSTALLER09['baseurl'] . "/favicon.ico</url>\n<link>" . $INSTALLER09['baseurl'] . "</link>\n" .
"<width>16</width>\n<height>16</height>\n<description>" . $INSTALLER09['rssdescr'] . "</description>\n</image>\n";

$res = sql_query("SELECT t.id,t.name,t.descr,t.size,t.category,t.seeders,t.leechers,t.added, c.name as catname FROM torrents as t LEFT JOIN categories as c ON t.category = c.id WHERE $where t.visible='yes' ORDER BY t.added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
while ($a = mysqli_fetch_assoc($res)){
 $link = $INSTALLER09['baseurl'].($feed == "dl" ? "/download.php?torrent=".(int)$a['id'].'&amp;passkey='.$passkey : "/details.php?id=".(int)$a["id"]."&amp;hit=1");
 $br = "&lt;br/&gt;";
 $HTMLOUT .= "<item><title>".htmlsafechars($a["name"])."</title><link>{$link}</link><description>{$br}Category: ".htmlsafechars($a['catname'])." {$br} Size: ".mksize((int)$a["size"])." {$br} Leechers: ".(int)$a["leechers"]." {$br} Seeders: ".(int)$a["seeders"]." {$br} Added: ".get_date($a['added'],'DATE')." {$br} Description: ".htmlsafechars(substr($a["descr"],0,450))." {$br}</description>\n</item>\n";
}

$HTMLOUT .= "</channel>\n</rss>\n";
echo($HTMLOUT);
?>
