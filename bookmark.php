<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//==bookmark.php - by pdq
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();

$lang =  array_merge( load_language('global') );

$HTMLOUT='';

if (!mkglobal("torrent"))
stderr("Error", "missing form data");

$userid = (int)$CURUSER['id'];
if (!is_valid_id($userid))
stderr("Error", "Invalid ID.");

if ($userid != $CURUSER["id"])
stderr("Error", "Access denied.");

$torrentid = 0 + $_GET["torrent"];
if (!is_valid_id($torrentid))
die();

if (!isset($torrentid))
stderr("Error", "Failed. No torrent selected");

$possible_actions = array('add','delete','public','private');      
$action = (isset($_GET['action']) ? htmlsafechars($_GET['action']) : '');

        if (!in_array($action, $possible_actions)) 
            stderr('Error', 'A ruffian that will swear, drink, dance, revel the night, rob, murder and commit the oldest of ins the newest kind of ways.');

if ($action == 'add')
{
$torrentid = (int)$_GET['torrent'];
$sure = isset($_GET['sure']) ? 0 + $_GET['sure'] : '';
if (!is_valid_id($torrentid))
stderr("Error", "Invalid ID.");

$hash = md5('s5l6t0mu55yt4hwa7e5'.$torrentid.'add'.'s5l6t0mu55yt4hwa7e5');
if (!$sure)
 stderr("Add Bookmark","Do you really want to add this bookmark? Click\n" .
"<a href='?torrent=$torrentid&amp;action=add&amp;sure=1&amp;h=$hash'>here</a> if you are sure.", FALSE);

if ($_GET['h'] != $hash)
stderr('Error','what are you doing?');

function addbookmark($torrentid) {
global $CURUSER, $mc1, $INSTALLER09;
if ((get_row_count("bookmarks", "WHERE userid=".sqlesc($CURUSER['id'])." AND torrentid = ".sqlesc($torrentid))) > 0)
stderr("Error", "Torrent already bookmarked");
sql_query("INSERT INTO bookmarks (userid, torrentid) VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($torrentid).")") or sqlerr(__FILE__,__LINE__);
$mc1->delete_value('bookmm_'.$CURUSER['id']);
make_bookmarks($CURUSER['id'], 'bookmm_');
}

$HTMLOUT .= addbookmark($torrentid);
$HTMLOUT .="<h2>Bookmark added!</h2>";
}

if ($action == 'delete')
{
$torrentid = (int)$_GET['torrent'];
$sure = isset($_GET['sure']) ? 0 + $_GET['sure'] : '';
if (!is_valid_id($torrentid))
stderr("Error", "Invalid ID.");

$hash = md5('s5l6t0mu55yt4hwa7e5'.$torrentid .'delete'.'s5l6t0mu55yt4hwa7e5');
if (!$sure)
stderr("Delete Bookmark","Do you really want to delete this bookmark? Click\n" .
"<a href='?torrent=$torrentid&amp;action=delete&amp;sure=1&amp;h=$hash'>here</a> if you are sure.", FALSE);

if ($_GET['h'] != $hash)
stderr('Error','what are you doing?');

function deletebookmark($torrentid) {
global $CURUSER, $mc1, $INSTALLER09;
sql_query("DELETE FROM bookmarks WHERE torrentid = ".sqlesc($torrentid)." AND userid = ".sqlesc($CURUSER['id']));
$mc1->delete_value('bookmm_'.$CURUSER['id']);
make_bookmarks($CURUSER['id'], 'bookmm_');
}

$HTMLOUT .= deletebookmark($torrentid);
$HTMLOUT .="<h2>Bookmark deleted!</h2>";
}

elseif ($action == 'public')
{
$torrentid = (int)$_GET['torrent'];
$sure = isset($_GET['sure']) ? 0 + $_GET['sure'] : '';
if (!is_valid_id($torrentid))
stderr("Error", "Invalid ID.");

$hash = md5('s5l6t0mu55yt4hwa7e5'.$torrentid.'public'.'s5l6t0mu55yt4hwa7e5');
if (!$sure)
stderr("Share Bookmark","Do you really want to mark this bookmark public? Click\n" .
"<a href='?torrent=$torrentid&amp;action=public&amp;sure=1&amp;h=$hash'>here</a> if you are sure.", FALSE);

if ($_GET['h'] != $hash)
stderr('Error','what are you doing?');

function publickbookmark($torrentid) {
global $CURUSER, $mc1, $INSTALLER09;
sql_query("UPDATE bookmarks SET private = 'no' WHERE private = 'yes' AND torrentid = ".sqlesc($torrentid)." AND userid = ".sqlesc($CURUSER['id']));
$mc1->delete_value('bookmm_'.$CURUSER['id']);
make_bookmarks($CURUSER['id'], 'bookmm_');
}

$HTMLOUT .= publickbookmark($torrentid);
$HTMLOUT .="<h2>Bookmark made public!</h2>";
}

elseif ($action == 'private')
{
$torrentid = (int)$_GET['torrent'];
$sure = isset($_GET['sure']) ? 0 + $_GET['sure'] : '';
if (!is_valid_id($torrentid))
stderr("Error", "Invalid ID.");

$hash = md5('s5l6t0mu55yt4hwa7e5'.$torrentid.'private'.'s5l6t0mu55yt4hwa7e5');
if (!$sure)
stderr("Make Bookmark Private","Do you really want to mark this bookmark private? Click\n" .
"<a href='?torrent=$torrentid&amp;action=private&amp;sure=1&amp;h=$hash'>here</a> if you are sure.", FALSE);

if ($_GET['h'] != $hash)
stderr('Error','what are you doing?');

if (!is_valid_id($torrentid))
stderr("Error", "Invalid ID.");

function privatebookmark($torrentid) {
global $CURUSER, $mc1, $INSTALLER09;
sql_query("UPDATE bookmarks SET private = 'yes' WHERE private = 'no' AND torrentid = ".sqlesc($torrentid)." AND userid = ".sqlesc($CURUSER['id']));
$mc1->delete_value('bookmm_'.$CURUSER['id']);
make_bookmarks($CURUSER['id'], 'bookmm_');
}

$HTMLOUT .= privatebookmark($torrentid);
$HTMLOUT .="<h2>Bookmark made private!</h2>";
}

if (isset($_POST["returnto"]))
$ret = "<a href=\"" . htmlsafechars($_POST["returnto"]) . "\">Go back to whence you came</a>";
else
$ret = "<a href=\"bookmarks.php\">Go to My Bookmarks</a><br /><br />
<a href=\"browse.php\">Go to Browse</a>";
    $HTMLOUT .= $ret;
echo stdhead('Bookmark') . $HTMLOUT . stdfoot();
?>
