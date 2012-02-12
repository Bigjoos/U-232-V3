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
require_once(INCL_DIR.'pager_functions.php');
require_once(INCL_DIR.'html_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);

$lang = array_merge( $lang );
$HTMLOUT ='';

// //////////////////////
$count1 = get_row_count('shoutbox', "WHERE staff_shout='yes'");
$perpage = 15;
$pager = pager($perpage, $count1, 'staffpanel.php?tool=staff_shistory&amp;');


$res = sql_query( "SELECT s.id, s.userid, s.date , s.text, s.to_user, u.username, u.pirate, u.king, u.enabled, u.class, u.donor, u.warned, u.leechwarn, u.chatpost FROM shoutbox as s LEFT JOIN users as u ON s.userid=u.id WHERE staff_shout='yes' ORDER BY s.date DESC ".$pager['limit']."" ) or sqlerr( __FILE__, __LINE__ );

if ($count1 > $perpage)
$HTMLOUT .= $pager['pagertop'];

$HTMLOUT .= begin_main_frame();

  if ( mysqli_num_rows( $res ) == 0 )
  $HTMLOUT .="No staff shouts here";
  else {
  $HTMLOUT .="<table align='center' border='0' cellspacing='0' cellpadding='2' width='100%' class='small'>\n";
  while ( $arr = mysqli_fetch_assoc( $res ) ) {
  if(($arr['to_user'] != $CURUSER['id'] && $arr['to_user'] != 0) && $arr['userid'] != $CURUSER['id']) 
  continue;
	if($arr['to_user'] == $CURUSER['id'] || ($arr['userid'] == $CURUSER['id'] && $arr['to_user'] !=0) )
	$private = "<img src='{$INSTALLER09['pic_base_url']}private-shout.png' alt='Private shout' title='Private shout!' width='16' style='padding-left:2px;padding-right:2px;' border='0' />";
	else
	$private = "<img src='{$INSTALLER09['pic_base_url']}group.png' alt='Public shout' title='Public shout!' width='16' style='padding-left:2px;padding-right:2px;' border='0' />";
  $date = get_date($arr["date"], 0,1);
  $user_stuff = $arr;
  $user_stuff['id'] = (int)$arr['userid'];
  $HTMLOUT .="<tr><td><span style='font-size:11px;'>[$date]&nbsp;[$private]</span>\n ".format_username($user_stuff)."<span style='font-size:11px;'> " . format_comment( $arr["text"] ) . "\n</span></td></tr>\n";
  }
  $HTMLOUT .="</table>";
  }

if ($count1 > $perpage)
$HTMLOUT .= $pager['pagerbottom'];

$HTMLOUT .= end_main_frame();

echo stdhead('Staff Shout History') . $HTMLOUT . stdfoot();
?>
