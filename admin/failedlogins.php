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
require_once(INCL_DIR.'html_functions.php');
require_once INCL_DIR.'pager_functions.php';
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);

$HTMLOUT="";
$lang = array_merge( $lang , load_language('failedlogins'));
$mode = (isset($_GET['mode']) ? $_GET['mode'] : '');
$id = isset($_GET['id']) ? (int) $_GET['id'] : '';

function validate ($id)
{
global $lang;
if (!is_valid_id($id))
stderr($lang['failed_sorry'], "{$lang['failed_bad_id']}");
else
return true;
}

//==Actions
if ($mode == 'ban'){
validate($id);
sql_query("UPDATE failedlogins SET banned = 'yes' WHERE id=".sqlesc($id)."");
header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=failedlogins');
stderr($lang['failed_success'],"{$lang['failed_message_ban']}");
exit();
}

if ($mode == 'removeban') {
validate($id);
sql_query("UPDATE failedlogins SET banned = 'no' WHERE id=".sqlesc($id)."") ;
header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=failedlogins');
stderr($lang['failed_success'],"{$lang['failed_message_unban']}");
exit();
}

if ($mode == 'delete') {
validate($id);
sql_query("DELETE FROM failedlogins WHERE id=".sqlesc($id)."");
header('Refresh: 2; url='.$INSTALLER09['baseurl'].'/staffpanel.php?tool=failedlogins');
stderr($lang['failed_success'],"{$lang['failed_message_deleted']}");
exit();
}
//==End
//==Main output
$where = '';
$search = isset($_POST['search']) ? strip_tags($_POST['search']) : '';
if (!$search)
$where = "WHERE attempts LIKE " . sqlesc("%$search%") . "";
else
$where = "WHERE attempts LIKE" . sqlesc("%$search%") . "";
$res = sql_query("SELECT COUNT(id) FROM failedlogins $where") or sqlerr();
$row = mysqli_fetch_row($res);
$count = $row[0];
$perpage = 15;
$pager = pager($perpage, $count, "staffpanel.php?tool=failedlogins&amp;action=failedlogins&amp;");

if (!$where)
  stderr("No failedlogins", "It appears that there are currently no failed logins matching your search criteria.");


$HTMLOUT ="";

$HTMLOUT .=  "<table border='1' cellspacing='0' width='115' cellpadding='5'>\n
             <tr>
			 <td class='tabletitle' align='left'>Search failedlogins</td>\n
			 </tr>
             <tr>
			 <td class='table' align='left'>\n
			 <form method='post' action='staffpanel.php?tool=failedlogins&amp;action=failedlogins'>\n
			 <input type='text' name='search' size='40' value='' />\n
			 <input type='submit' value='Search' style='height: 20px' />\n
			 </form></td></tr></table>";

if ($count > $perpage)
$HTMLOUT .= $pager['pagertop'];
$HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5' width='80%'>\n";

$res = sql_query("SELECT f.*,u.id as uid, u.username FROM failedlogins as f LEFT JOIN users as u ON u.ip = f.ip $where ORDER BY f.added DESC ".$pager['limit']."") or sqlerr(__FILE__,__LINE__);

if (mysqli_num_rows($res) == 0)
  $HTMLOUT .="<tr><td colspan='2'><b>{$lang['failed_message_nothing']}</b></td></tr>\n";
else
{  
  $HTMLOUT .="<tr><td class='colhead'>ID</td><td class='colhead' align='left'>{$lang['failed_main_ip']}</td><td class='colhead' align='left'>{$lang['failed_main_added']}</td>".
	"<td class='colhead' align='left'>{$lang['failed_main_attempts']}</td><td class='colhead' align='left'>{$lang['failed_main_status']}</td></tr>\n";
  while ($arr = mysqli_fetch_assoc($res))
  {
  $HTMLOUT .="<tr><td align='left'><b>".(int)$arr['id']."</b></td>
  <td align='left'><b>".htmlentities($arr['ip'])." ".((int)$arr['uid'] ? "<a href='{$INSTALLER09['baseurl']}/userdetails.php?id=".(int)$arr['uid']."'>" : "" )." ".( htmlspecialchars($arr['username']) ? "(".htmlspecialchars($arr['username']).")" : "" ) . "</a></b></td>
  <td align='left'><b>".get_date($arr['added'], '', 1,0)."</b></td>
  <td align='left'><b>".(int)$arr['attempts']."</b></td>
  <td align='left'>
  ".($arr['banned'] == "yes" ? "<font color='red'><b>{$lang['failed_main_banned']}</b></font> 
  <a href='staffpanel.php?tool=failedlogins&amp;action=failedlogins&amp;mode=removeban&amp;id=".(int)$arr['id']."'> 
  <font color='green'>[<b>{$lang['failed_main_remban']}</b>]</font></a>" : "<font color='green'><b>{$lang['failed_main_noban']}</b></font> 
  <a href='staffpanel.php?tool=failedlogins&amp;action=failedlogins&amp;mode=ban&amp;id=".(int)$arr['id']."'><font color='red'>[<b>{$lang['failed_main_ban']}</b>]</font></a>")."  
  
  <a onclick=\"return confirm('{$lang['failed_main_delmessage']}');\" href='staffpanel.php?tool=failedlogins&amp;action=failedlogins&amp;mode=delete&amp;id=".(int)$arr['id']."'>[<b>{$lang['failed_main_delete']}</b>]</a></td></tr>\n";
  }
  }
$HTMLOUT .="</table>\n";
if ($count > $perpage)
$HTMLOUT .= $pager['pagerbottom'];
echo stdhead($lang['failed_main_logins']) .$HTMLOUT . stdfoot();
?>
