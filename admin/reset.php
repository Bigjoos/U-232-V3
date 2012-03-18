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
require_once(INCL_DIR.'password_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
    
//== Reset Lost Password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlentities($_POST['username']));
    $uid = (int)$_POST["uid"];
    $secret = mksecret();
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $newpassword = "";
    for($i = 0;$i < 10;$i++)
    $newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];
    $passhash =  make_passhash( $secret, md5($newpassword) ) ;
    $res = sql_query('UPDATE users SET secret='.sqlesc($secret).', passhash='.sqlesc($passhash).' WHERE username='.sqlesc($username).' AND id='.sqlesc($uid).' AND class<'.$CURUSER['class']) or sqlerr(__file__, __line__);
    $mc1->begin_transaction('MyUser_'.$uid);
    $mc1->update_row(false, array('secret' => $secret, 'passhash' => $passhash));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$uid);
    $mc1->update_row(false, array('secret' => $secret, 'passhash' => $passhash));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) != 1)
    stderr('Error', 'Password not updated. User not found or higher/equal class to yourself');
    
    write_log('passwordreset', 'Password reset for ' . htmlspecialchars($username) . ' by ' . htmlspecialchars($CURUSER['username']));
    stderr('Success', 'The password for account <b>' . htmlspecialchars($username) . '</b> is now <b>' . htmlspecialchars($newpassword) . '</b>.');
}

$HTMLOUT ="";

$HTMLOUT .="<h1>Reset User's Lost Password</h1>
<form method='post' action='staffpanel.php?tool=reset&amp;action=reset'>
<table border='1' cellspacing='0' cellpadding='5'>
<tr>
<td class='rowhead'>ID: </td><td>
<input type='text' name='uid' size='10' /></td></tr>
<tr>
<td class='rowhead'>User name</td><td>
<input size='40' name='username' /></td></tr>
<tr>
<td colspan='2'>
<input type='submit' class='btn' value='reset' />
</td>
</tr>
</table></form>";

echo stdhead("Reset Password") . $HTMLOUT . stdfoot();
?>
