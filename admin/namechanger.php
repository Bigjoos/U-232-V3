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
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);

$lang = array_merge( $lang );
$HTMLOUT='';

$mode = (isset($_GET['mode']) && htmlspecialchars($_GET['mode']));

if (isset($mode) && $mode == 'change') {
    $uid = (int)$_POST["uid"];
    $uname = htmlspecialchars($_POST["uname"]);

    if ($_POST["uname"] == "" || $_POST["uid"] == "")
        stderr("Error", "UserName or ID missing");

    $change = sql_query("UPDATE users SET username=".sqlesc($uname)." WHERE id=".sqlesc($uid)) or sqlerr(__FILE__, __LINE__);
    
    $mc1->begin_transaction('MyUser_'.$uid);
    $mc1->update_row(false, array('username' => $uname));
    $mc1->commit_transaction(900); // 15 mins
    $mc1->begin_transaction('user'.$uid);
    $mc1->update_row(false, array('username' => $uname));
    $mc1->commit_transaction(900); // 15 mins
    
    $added = TIME_NOW;
    $changed = sqlesc("Your Username Has Been Changed To $uname");
    $subject = sqlesc("Username changed");
    if (!$change) {
        if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 1062)
            stderr("Borked", "Username already exists!");
    }

    sql_query("INSERT INTO messages (sender, receiver, msg, subject, added) VALUES(0, $uid, $changed, $subject, $added)") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 2; url=staffpanel.php?tool=namechanger&amp;action=namechanger");
    stderr("Success","Username Has Been Changed To ".htmlspecialchars($uname)." please wait while you are redirected");
}

$HTMLOUT.="
<h1>Change UserName</h1>
<form method='post' action='staffpanel.php?tool=namechanger&amp;action=namechanger&amp;mode=change'>
<table border='1' cellspacing='0' cellpadding='3'>
<tr><td class='rowhead'>ID: </td><td><input type='text' name='uid' size='10' /></td></tr>
<tr><td class='rowhead'>New Username: </td><td><input type='text' name='uname' size='20' /></td></tr>
<tr><td colspan='2' align='center'>If You Are Sure Then: <input type='submit' value='Change Name!' class='btn' /></td></tr>
</table>
</form>";

echo stdhead('Username Changer') . $HTMLOUT . stdfoot();
?>
