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
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);

$HTMLOUT = $count2 ="";

if (isset($_GET["total_donors"])) {
    $total_donors = 0 + $_GET["total_donors"];
    if ($total_donors != '1')
        stderr("Error", "I smell a rat!");

    $res = sql_query("SELECT COUNT(*) FROM users WHERE total_donated != '0.00' AND enabled='yes'") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_array($res);
    $count = $row[0];
    $perpage = 15;
    
    $pager = pager($perpage, $count, "staffpanel.php?tool=donations&amp;action=donations&amp;");
    
    if (mysqli_num_rows($res) == 0)
        stderr("Sorry", "no donors found!");

    $users = number_format(get_row_count("users", "WHERE total_donated != '0.00'"));
    $HTMLOUT .= begin_frame("Donor List: All Donations [".htmlsafechars($users)."]", true);
    $res = sql_query("SELECT id, username, email, added, donated, donoruntil, total_donated FROM users WHERE total_donated != '0.00' ORDER BY id DESC ".$pager['limit']."") or sqlerr(__FILE__, __LINE__);
    }
    // ===end total donors
    else {
    $res = sql_query("SELECT COUNT(id) FROM users WHERE donor='yes'") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_array($res);
    $count = $row[0];
    $perpage = 15;
    $pager = pager($perpage, $count, "staffpanel.php?tool=donations&amp;action=donations&amp;");

    if (mysqli_num_rows($res) == 0)
        stderr("Sorry", "no donors found!");

    $users = number_format(get_row_count("users", "WHERE donor='yes'"));
    $HTMLOUT .= begin_frame("Donor List: Current Donors [".htmlsafechars($users)." ]", true);
    $res = sql_query("SELECT id, username, email, added, donated, total_donated, donoruntil FROM users WHERE donor='yes' ORDER BY id DESC ".$pager['limit']."") or sqlerr(__FILE__, __LINE__);
    }

if ($count > $perpage) 
$HTMLOUT .= $pager['pagertop'];

$HTMLOUT .= begin_table();

$HTMLOUT .="<tr><td colspan='9' align='center'><a class='altlink' href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=donations&amp;action=donations'>Current Donors</a> || <a class='altlink' href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=donations&amp;action=donations&amp;total_donors=1'>All Donations</a></td></tr>";



$HTMLOUT .="<tr><td class='colhead'>ID</td><td class='colhead' align='left'>Username</td><td class='colhead' align='left'>e-mail</td>" . "<td class='colhead' align='left'>Joined</td><td class='colhead' align='left'>Donor Until?</td><td class='colhead' align='left'>" . "Current</td><td class='colhead' align='left'>Total</td><td class='colhead' align='left'>PM</td></tr>";
while ($arr = mysqli_fetch_assoc($res)) {
   
    // =======change colors
    if ($count2 == 0) {
        $count2 = $count2 + 1;
        $class = "one";
    } else {
        $count2 = 0;
        $class = "two";
    }
    // =======end
    $HTMLOUT .="<tr><td valign='bottom' class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/userdetails.php?id=" . htmlsafechars($arr['id']) . "'>" . htmlsafechars($arr['id']) . "</a></td>" . "<td align='left' valign='bottom' class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/userdetails.php?id=" . htmlsafechars($arr['id']) . "'><b>" . htmlsafechars($arr['username']) . "</b></a>" . "</td><td align='left' valign='bottom' class='$class'><a class='altlink' href='mailto:" . htmlsafechars($arr['email']) . "'>" . htmlsafechars($arr['email']) . "</a>" . "</td><td align='left' valign='bottom' class='$class'><font size=\"-3\"> ".get_date($arr['added'], 'DATE'). "</font>" . "</td><td align='left' valign='bottom' class='$class'>";
    
    $donoruntil = (int)$arr['donoruntil'];
    if ($donoruntil == '0')
    $HTMLOUT .="n/a";
    else
    $HTMLOUT .="<font size=\"-3\"> ".get_date($arr['donoruntil'], 'DATE'). " [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go...</font>";

    $HTMLOUT .="</td><td align='left' valign='bottom' class='$class'><b>&#163;" . htmlsafechars($arr['donated']) . "</b></td>" . "<td align='left' valign='bottom' class='$class'><b>&#163;" . htmlsafechars($arr['total_donated']) . "</b></td>" . "<td align='left' valign='bottom' class='$class'><b><a class='altlink' href='{$INSTALLER09['baseurl']}/pm_system.php?action=send_message&amp;receiver=".(int)$arr['id']."'>PM</a></b></td></tr>";
}


$HTMLOUT .= end_table();
$HTMLOUT .= end_frame();

if ($count > $perpage)
$HTMLOUT .= $pager['pagerbottom'];
echo stdhead('Donations') . $HTMLOUT . stdfoot();
?>
