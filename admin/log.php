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
require_once(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);

$lang = array_merge( $lang, load_language('ad_log') );
$txt = $where = '';
$search = isset($_POST['search']) ? strip_tags($_POST['search']) : '';
if (!empty($search))
$where = "WHERE txt LIKE " . sqlesc("%$search%") . "";

// delete items older than 1 month
$secs = 30 * 86400;
sql_query("DELETE FROM sitelog WHERE " . TIME_NOW . " - added > $secs") or sqlerr(__FILE__, __LINE__);

$resx = sql_query("SELECT COUNT(*) FROM sitelog $where");
$rowx = mysqli_fetch_array($resx, MYSQLI_NUM);
$count = $rowx[0];
$perpage = 50;
$pager = pager($perpage, $count, "staffpanel.php?tool=log&amp;action=log&amp;" . "");



    $HTMLOUT = '';
    $res = sql_query("SELECT added, txt FROM sitelog $where ORDER BY added DESC {$pager['limit']} ") or sqlerr(__FILE__, __LINE__);
    
    $HTMLOUT .= "<h1>{$lang['text_sitelog']}</h1>";
    $HTMLOUT .=  "<table border='1' cellspacing='0' width='115' cellpadding='5'>\n
             <tr>
			 <td class='tabletitle' align='left'>Search Log</td>\n
			 </tr>
             <tr>
			 <td class='table' align='left'>\n
			 <form method='post' action='staffpanel.php?tool=log&amp;action=log'>\n
			 <input type='text' name='search' size='40' value='' />\n
			 <input type='submit' value='Search' style='height: 20px' />\n
			 </form></td></tr></table>";
    
    if ($count > $perpage)
    $HTMLOUT .= $pager['pagertop'];
    
	if (mysqli_num_rows($res) == 0)
    {
      $HTMLOUT .= "<b>{$lang['text_logempty']}</b>";
    }
    else
    {
	  
	  
      $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td class='colhead' align='left'>{$lang['header_date']}</td>
        <td class='colhead' align='left'>{$lang['header_event']}</td>
      </tr>"; 
      
  while ($arr = mysqli_fetch_assoc($res))
  {
$color = '#333333'; 
if (strpos($arr['txt'],'was uploaded by')) $color = "#4799ad";
if (strpos($arr['txt'],'was created')) $color = "#CC9966";
if (strpos($arr['txt'],'section')) $color = "#ba79d8";
if (strpos($arr['txt'],'started')) $color = "#00E300";
if (strpos($arr['txt'],'Finished')) $color = "#00E300";
if (strpos($arr['txt'],'sticky')) $color = "#BBaF9B";
if (strpos($arr['txt'],'was invited by')) $color = "#CC9966";
if (strpos($arr['txt'],'was invited to the site.')) $color = "#CC9966";
if (strpos($arr['txt'],'was deleted by')) $color = "#CC6666";
if (strpos($arr['txt'],'was deleted by system')) $color = "#FF6600";
if (strpos($arr['txt'],'sent by')) $color = "#af0b0b";
if (strpos($arr['txt'],'Reason')) $color = "#d34e29";
if (strpos($arr['txt'],'for User')) $color = "#d34e29";
if (strpos($arr['txt'],'promoted')) $color = "#3ae2f1";
if (strpos($arr['txt'],'demoted')) $color = "#375d60";
if (strpos($arr['txt'],'was updated by')) $color = "#6699FF";
if (strpos($arr['txt'],'was edited by')) $color = "#BBaF9B";
  $date = explode( ',', get_date( $arr['added'], 'LONG' ) );


    $HTMLOUT .= "<tr class='table'><td style='background-color:$color'><font color='black'>{$date[0]}{$date[1]}</font></td><td style='background-color:$color' align='left'><font color='black'>".$arr['txt']."</font></td></tr>\n";
    }
    $HTMLOUT .= "</table>\n";
    }
	
   $HTMLOUT .= "<p>{$lang['text_times']}</p>";
	  
	 if ($count > $perpage)
   $HTMLOUT .= $pager['pagerbottom'];
	
echo stdhead("{$lang['stdhead_log']}") . $HTMLOUT . stdfoot();
?>
