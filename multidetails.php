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
require_once INCL_DIR.'html_functions.php';
dbconn(false);
$lang = array_merge(load_language('global'));
$HTMLOUT = '';
$HTMLOUT.= "<table border='1' cellpadding='4' width='35%'>";
$ids = array();
$ids[] = 0 + $_GET["id1"];
$ids[] = 0 + $_GET["id2"];
$ids[] = 0 + $_GET["id3"];
$ids[] = 0 + $_GET["id4"];
$ids[] = 0 + $_GET["id5"];
//== This is the page which is displayed if the uploader has just uploaded the torrents//
if (array_key_exists('uploaded', $_GET) && htmlentities($_GET["uploaded"])) {
    $HTMLOUT.= "<tr><td colspan='2'>Successfully uploaded!
    You can start downloading them now and start seeding. <b>Note</b> that the torrent won't be visible until you do that!</td></tr>\n";
    $res = sql_query("SELECT torrents.filename FROM torrents WHERE torrents.id=$ids[0] OR torrents.id=$ids[1] OR torrents.id=$ids[2] OR torrents.id=$ids[3] OR torrents.id=$ids[4];") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT.= "<tr><td class='colhead'><b>Torrent Description</b></td><td class='colhead'><img src=\"{$INSTALLER09['pic_base_url']}download.gif\" alt=\"Download\" title=\"Download\" border=\"0\" /></td></tr>";
    $i = 0;
    while ($row = mysqli_fetch_array($res)) {
        $HTMLOUT.= "<tr><td><a class=\"index\" href=\"{$INSTALLER09['baseurl']}/details.php?id=$ids[$i]\">".htmlspecialchars($row["filename"])."</a></td>
    <td><a class=\"index\" href=\"{$INSTALLER09['baseurl']}/download.php?torrent=$ids[$i]\"><img src=\"{$INSTALLER09['pic_base_url']}download.gif\" alt=\"Download\" title=\"Download\" border=\"0\" /></a></td></tr>";
        $i++;
    }
    $HTMLOUT.= "</table>";
    //== This is the page which is displayed when a user views the uploaded torrents from the shoutbox link//
    
} else {
    $HTMLOUT.= "<tr><td colspan='2'>New Torrents have been Uploaded!
    Click on the Torrents below to see the full description or alternatively click the 'Download' Button to Download..now</td></tr>\n";
    $res = sql_query("SELECT * FROM torrents WHERE torrents.id=$ids[0] OR torrents.id=$ids[1] OR torrents.id=$ids[2] OR torrents.id=$ids[3] OR torrents.id=$ids[4];") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT.= "<tr><td class='colhead'><b>Torrent Description</b></td><td class='colhead'><img src=\"{$INSTALLER09['pic_base_url']}download.gif\" alt=\"Download\" title=\"Download\" border=\"0\" /></td></tr>";
    $i = 0;
    while ($row = mysqli_fetch_array($res)) {
        $HTMLOUT.= "<tr><td><a class=\"index\" href=\"{$INSTALLER09['baseurl']}/details.php?id=$ids[$i]\">".htmlspecialchars($row["name"])."</a></td>
    <td><a class=\"index\" href=\"{$INSTALLER09['baseurl']}/download.php?torrent=$ids[$i]\"><img src=\"{$INSTALLER09['pic_base_url']}download.gif\" alt=\"Download\" title=\"Download\" border=\"0\" /></a></td></tr>";
        $i++;
    }
    $HTMLOUT.= "</table>";
    $HTMLOUT.= "<br />";
}
echo stdhead("Multi-Details").$HTMLOUT.stdfoot();
?>
