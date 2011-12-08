<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);

$lang = array_merge( $lang );

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!is_valid_id($id))
    stderr("Error", "Invalid ID.");

if ($CURUSER['class'] >= UC_STAFF) {
    
    $dt = TIME_NOW;
    $res = sql_query("SELECT username FROM users WHERE id= $id") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res);
    $username = $arr['username'];
    sql_query("DELETE FROM peers WHERE userid=".$id);
    $effected = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
    //=== write to log
    write_log("Staff flushed " . $username . "'s ghost torrents at " . get_date($dt, 'LONG',0,1) . ". $effected torrents where sucessfully cleaned.");
    //write_log("User " . $username . " just flushed torrents at " . get_date($dt, 'LONG',0,1) . ". $effected torrents where sucessfully cleaned.");
    header("Refresh: 3; url=index.php");
    stderr('Success', "$effected ghost torrent" . ($effected ? 's' : '') . 'where sucessfully cleaned. You may now restart your torrents, The tracker has been updated, and your ghost torrents where sucessfully flushed. please remember to put the seat down. Redirecting to homepage in 3...2...1.');
} else
    stderr("Oops", "Your not a member of staff.");
?>
