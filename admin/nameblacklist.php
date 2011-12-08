<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//== Made by putyn
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
require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_MODERATOR);


$blacklist = file_exists($INSTALLER09['nameblacklist']) && is_array(unserialize(file_get_contents($INSTALLER09['nameblacklist'])))  ? unserialize(file_get_contents($INSTALLER09['nameblacklist'])) : array();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$badnames = isset($_POST['badnames']) && !empty($_POST['badnames']) ? trim($_POST['badnames']) : '';
	if(empty($badnames))
	stderr('Hmm','I think you forgot the name');
	if(strpos($badnames,',')) {
		foreach(explode(',',$badnames) as $badname)
		$blacklist[$badname] = (int)1;
	} else
		$blacklist[$badnames] = (int)1;

	if(file_put_contents($INSTALLER09['nameblacklist'],serialize($blacklist))) {
		header('Refresh:2; url=staffpanel.php?tool=nameblacklist');
		stderr('Success','The file was written...wait for redirect');
	} else
		stderr('Error','There was an error while saving the file check if this file <b>'.$INSTALLER09['nameblacklist'].'</b> is writable');
} else {

$out = begin_main_frame();
$out .= stdmsg('Current words on blacklist',count($blacklist) ? join(', ',array_keys($blacklist)) : 'There is no username on the blacklist');
$out .= stdmsg('Add word','<form action="staffpanel.php?tool=nameblacklist&amp;action=nameblacklist" method="post"><table width="90%" cellspacing="2" cellpadding="5" align="center" style="border-collapse:separate">
	<tr><td align="center"><textarea rows="3" cols="100" name="badnames"></textarea></td></tr>
    <tr><td align="center">Note if you want to submit more then one bad nick at a time separate them with a semicol</td></tr>
	<tr> <td align="center"><input type="submit" value="Update"/></td></tr>
	</table></form>');
$out .= end_main_frame();
echo(stdhead('Username blacklist').$out.stdfoot());
}
?>
