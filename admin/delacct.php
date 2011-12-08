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
class_check(UC_MODERATOR);


    $lang = array_merge( $lang, load_language('ad_delacct') );
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $username = trim($_POST["username"]);
      $password = trim($_POST["password"]);
      if (!$username || !$password)
        stderr("{$lang['text_error']}", "{$lang['text_please']}");
        
      $res = sql_query("SELECT * FROM users WHERE username=" . sqlesc($username) 
                          . "AND passhash=md5(concat(secret,concat(" . sqlesc($password) . ",secret)))") 
                          or sqlerr();
      if (mysqli_num_rows($res) != 1)
        stderr("{$lang['text_error']}", "{$lang['text_bad']}");
      $arr = mysqli_fetch_assoc($res);

      $id = $arr['id'];
      $res = sql_query("DELETE FROM users WHERE id=$id") or sqlerr();
      if (mysqli_affected_rows($GLOBALS["___mysqli_ston"]) != 1)
        stderr("{$lang['text_error']}", "{$lang['text_unable']}");
        
      stderr("{$lang['stderr_success']}", "{$lang['text_success']}");
    }
    
    $HTMLOUT = "
    <h1>{$lang['text_delete']}</h1>
    <form method='post' action='staffpanel.php?tool=delacct&amp;action=delacct'>
    <table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td class='rowhead'>{$lang['table_username']}</td>
        <td><input size='40' name='username' /></td>
      </tr>
      <tr>
        <td class='rowhead'>{$lang['table_password']}</td>
        <td><input type='password' size='40' name='password' /></td>
      </tr>
      <tr>
        <td colspan='2'><input type='submit' class='btn' value='{$lang['btn_delete']}' /></td>
      </tr>
    </table>
    </form>";

    echo stdhead("{$lang['stdhead_delete']}") . $HTMLOUT . stdfoot();
?>
