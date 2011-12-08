<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//== pdq Class Checker and Verify Staff
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
require_once CLASS_DIR.'class_check.php';
class_check(UC_SYSOP);

//$stdfoot = array('js' => array('browse.foot.v1'));
$Output='';
do_your_balls_hang_low();

function do_your_balls_hang_low(){
    global $CURUSER, $INSTALLER09;
    $Output='';
    // users who are allowed access
    $pinky_toes=array(
        'Mindless'=>1,
        'pdq'=>1,
        'putyn'=>1
    );
   
    // check if they are allowed, have sent a username/pass and are using their own username
    if (isset($pinky_toes[$CURUSER['username']]) && isset($_SERVER['PHP_AUTH_USER'])
     && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER']===($CURUSER['username'])){
        // generate a passhash from the sent password
       // $hash=md5($CURUSER['secret'].$_SERVER['PHP_AUTH_PW'].$CURUSER['secret']);
         $hash=md5($INSTALLER09['site']['salt2'].$_SERVER['PHP_AUTH_PW'].$CURUSER['secret']);
        // if the password is correct, exit this function
        if(md5($INSTALLER09['site']['salt2'].$INSTALLER09['staff']['owner_pin'].$CURUSER['secret']) === $hash) return true;
    }
    // they're not allowed, the username doesn't match their own, the password is
    // wrong or they have not sent user/pass yet so we exit
    header('WWW-Authenticate: Basic realm="Administration"');
    header('HTTP/1.0 401 Unauthorized');
   	$Output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>ERROR</title>
</head><body>
<h1 align="center">ERROR</h1><p align="center">Sorry! Access denied!</p>
</body></html>';
	exit();
}

$ids = $names = array();

$res = sql_query("SELECT id,username from users WHERE class >=".UC_STAFF." ORDER by username ASC");
while($arr = mysqli_fetch_assoc($res)) {
    $ids[]   = (int)$arr['id'];
    $names[] = $arr['username'];
}

$id_file   =  CACHE_DIR.'ids.txt';
$name_file =  CACHE_DIR.'names.txt';

$handle_ids = fopen($id_file,"w+");
if ($handle_ids)
	fwrite($handle_ids,serialize($ids));
fclose($handle_ids);

$handle_names = fopen($name_file,"w+");
if ($handle_names)
	fwrite($handle_names,serialize($names));
fclose($handle_names);

$Output .= '<h2>If you promoted or demoted staff, please make sure their username is present or not present on list.</h2>';
$Output .="<pre>";
//$Output .= print_r($ids);
//$Output .= print_r($names);
$Output .="</pre>";


$Output .= '<h2>Files written - Once sure, you may use Back button to return</h2>';

echo stdhead('Staff Config') . $Output . stdfoot();
?>
