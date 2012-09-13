<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
//By Froggaard
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once (INCL_DIR.'user_functions.php');
dbconn();
$HTML = '';
$id = (int)$_REQUEST['id'];
$wtf = mysqli_num_rows(sql_query("SELECT id, type, torrentid, userid FROM thumbsup WHERE torrentid = ".sqlesc($id)));
$res = sql_query("SELECT id, type, torrentid, userid FROM thumbsup WHERE userid = ".sqlesc($CURUSER['id'])." AND torrentid = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$thumbsup = mysqli_num_rows($res);
if ($thumbsup == 0) {
    sql_query("INSERT INTO thumbsup (userid, torrentid) VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($id).")") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('thumbs_up_'.$id);
    $HTML.= "<img src='{$INSTALLER09['pic_base_url']}thumb_up.png' alt='Thumbs Up' title='Thumbs Up' width='12' height='12' /> (".($wtf + 1).")";
} else $HTML.= "<img src='{$INSTALLER09['pic_base_url']}thumb_up.png' alt='Thumbs Up' title='Thumbs Up' width='12' height='12' /> ({$wtf})";
echo $HTML;
?>
