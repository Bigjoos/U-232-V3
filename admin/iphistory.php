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
require_once(INCL_DIR.'geoip.inc');
require_once(INCL_DIR.'geoipcity.inc');
require_once(INCL_DIR.'geoipregionvars.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_MODERATOR);
error_reporting(0);
/*
function write_logs($text,$type) {
  $text = sqlesc($text);
  $type = sqlesc($type);
  $added = time();
  sql_query("INSERT INTO userlog (added, txt, type) VALUES($added, $text, $type)") or sqlerr(__FILE__, __LINE__);
  }
*/
    $lang = array_merge( $lang, load_language('ad_iphistory') );

        //Clear the fields for use.
        $id = $color = '';

    $id = 0 + $_GET["id"];

    if (!is_valid_id($id))
      stderr("{$lang['stderr_error']}", "{$lang['stderr_badid']}");
/// Custom function....
if (isset($_GET["remove"])) {
$remove = $_GET['remove'];
$username2 = $_GET['username2'];
$deleteip = $_GET['deleteip'];
//write_logs("<font color='#FA5858'><b>History Wipe:</b></font> (<a href='userdetails.php?id=$CURUSER[id]'><b>$CURUSER[username]</b></a>) has just wiped IP: (<b>$deleteip</b>) from (<a href='/userdetails.php?id=$id'><b>$username2</b></a>)'s Ip History.", 'log');
sql_query("DELETE FROM ips WHERE id=$remove") or sqlerr();
}

if (isset($_GET["setseedbox"])) {
$setseedbox = $_GET['setseedbox'];

if (is_valid_id($setseedbox))
{
sql_query("UPDATE ips SET seedbox=1 WHERE id = $setseedbox") or sqlerr();
}
}

if (isset($_GET["setseedbox2"])) {
$setseedbox2 = $_GET['setseedbox2'];

if (is_valid_id($setseedbox2))
{
sql_query("UPDATE ips SET seedbox=0 WHERE id = $setseedbox2") or sqlerr();
}
}

    $res = @sql_query("SELECT username FROM users WHERE id=$id") or sqlerr();
    $user = mysqli_fetch_array($res) or stderr("{$lang['stderr_error']}", "{$lang['stderr_noid']}");
        $username = $user["username"];

        $resip = sql_query("SELECT * FROM ips WHERE userid = '$id' GROUP BY ip ORDER BY id DESC") or sqlerr(__FILE__, __LINE__);
        $ipcount = mysqli_num_rows($resip);

   $HTMLOUT = '';
    
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>
                <tr><td class='colhead' align='center'>{$lang['iphistory_usedby']}<a class='altlink_white' href='userdetails.php?id=$id'><b>$username</b></a></td></tr>
                <tr>
                <td class='heading2' align='center'>{$lang['iphistory_total_unique']} <b>$username</b> {$lang['iphistory_total_logged']} <b><u>$ipcount</u></b>.</td></tr>
                <tr>
                <td class='heading2' align='center'><b><font color='blue'>{$lang['iphistory_single']}</font> - <font color='red'>{$lang['iphistory_banned']}</font> - <font color='black'>{$lang['iphistory_dupe']}</font></b></td>
                </tr>
                </table><br />

                <table border='1' cellspacing='0' cellpadding='5'>
                <tr>
                <td align='center' class='colhead'>{$lang['iphistory_last']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_address']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_isphost']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_location']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_type']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_seedbox']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_delete']}</td>
                <td align='center' class='colhead'>{$lang['iphistory_ban']}</td>
                </tr>";


        while ($iphistory = mysqli_fetch_array($resip))
        {
                $host = gethostbyaddr($iphistory['ip']); //Hostname
                $userip = $iphistory['ip']; //Users Ip
                $ipid = $iphistory['id']; // IP ID

                if ($host == $userip) {
                $host = "<font color='red'><b>{$lang['iphistory_notfound']}</b></font>";
                }
                $seedboxdetected = ''; //Clear the field
                if (strpos($host,'kimsufi.com')) $seedboxdetected = "yes";
                if (strpos($host,'leaseweb.com')) $seedboxdetected = "yes";
                if (strpos($host,'ovh.net')) $seedboxdetected = "yes";
                if (strpos($host,'powserv.com')) $seedboxdetected = "yes";
                if (strpos($host,'server.lu')) $seedboxdetected = "yes";
                if (strpos($host,'xirvik.com')) $seedboxdetected = "yes";
                if (strpos($host,'feralhosting.com')) $seedboxdetected = "yes";
                if ($seedboxdetected == 'yes') {
                sql_query("UPDATE ips SET seedbox=1 WHERE id = $ipid") or sqlerr();
                }

                $lastbrowse = $iphistory['lastbrowse']; // Last Browse DATE
                $lastlogin = $iphistory['lastlogin']; // Last Login DATE
                $lastannounce = $iphistory['lastannounce']; // Last Announce DATE
                $iptype = $iphistory['type']; // IP was first used on

$queryc = "SELECT COUNT(id) FROM
(
SELECT u.id FROM users AS u WHERE u.ip = " . sqlesc($iphistory['ip']) . "
UNION SELECT u.id FROM users AS u RIGHT JOIN ips ON u.id = ips.userid WHERE ips.ip = " . sqlesc($iphistory['ip']) . "
GROUP BY u.id
) AS ipsearch";
$resip2 = sql_query($queryc) or sqlerr(__FILE__, __LINE__);
$arrip2 = mysqli_fetch_row($resip2);
$ipcount = $arrip2[0];

$nip = ip2long($iphistory['ip']);
$banres = sql_query("SELECT COUNT(*) FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
$banarr = mysqli_fetch_row($banres);
if ($banarr[0] == 0)
if ($ipcount > 1){
$ipshow = "<b><a class='altlink' href='staffpanel.php?tool=ipsearch&amp;action=ipsearch&amp;ip=". $iphistory['ip'] ."'><font color='black'>" . $iphistory['ip'] ."</font></a></b>";
} else {
$ipshow = "<a class='altlink' href='staffpanel.php?tool=ipsearch&amp;action=ipsearch&amp;ip=". $iphistory['ip'] ."'><b><font color='blue'>" . $iphistory['ip'] ."</font></b></a>";
}
else
$ipshow = "<a class='altlink' href='staffpanel.php?tool=testip&amp;action=testip&amp;ip=" . $iphistory['ip'] . "'><font color='red'><b>" . $iphistory['ip'] . "</b></font></a>";

        // User IP listed for GeoIP tracing
        $gi = geoip_open("GeoIP/GeoIP.dat",GEOIP_STANDARD);
        $countrybyip = geoip_country_name_by_addr($gi, $userip);
        $listcountry =  $countrybyip;
        geoip_close($gi);
        // end fetch geoip code

        // User IP listed for GeoIP tracing
        $gi = geoip_open("GeoIP/GeoLiteCity.dat",GEOIP_STANDARD);
        $citybyip = geoip_record_by_addr($gi, $userip); 
        $listcity = $citybyip->city;
        $listregion = $citybyip->region;
        geoip_close($gi);
        // end fetch geoip code

                //Is this a seedbox check
                $seedbox = $iphistory['seedbox'];
                if ($seedbox == '0') {
                $seedbox = "<a href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$id&amp;setseedbox=$iphistory[id]'><font color='red'><b>{$lang['iphistory_no']}</b></font></a>";
    $HTMLOUT .= "<tr>
                <td class='heading2' align='center'>Browse: ".get_date($lastbrowse,'')."<br />Login: ".get_date($lastlogin,'')."<br />Announce: ".get_date($lastannounce,'')."</td>
                <td class='heading2' align='center'>$ipshow</td>
                <td class='heading2' align='center'>$host</td>
                <td class='heading2' align='center'>$listcity, $listregion<br />$listcountry</td>
                <td class='heading2' align='center'>$iptype</td>
                <td class='heading2' align='center'>$seedbox</td>
                <td class='heading2' align='center'><a href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$id&amp;remove=$ipid&amp;deleteip=$userip&amp;username2=$username'><b>{$lang['iphistory_delete']}</b></a></td>
                <td class='heading2' align='center'><a href='staffpanel.php?tool=iphistory&amp;action=bans&amp;banthisuser=$username&amp;banthisip=$userip'><b>{$lang['iphistory_ban']}</b></a></td>
                </tr>";
                } else {
                $seedbox = "<a class='altlink' href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$id&amp;setseedbox2=$iphistory[id]'><font color='Green'><b>{$lang['iphistory_yes']}</b></font></a>";
                $color = '#CCFFFF';
    $HTMLOUT .= "<tr>
                <td class='heading2' align='center' style='background-color:$color'>Browse: ".get_date($lastbrowse,'')."<br />Login: ".get_date($lastlogin,'')."<br />Announce: ".get_date($lastannounce,'')."</td>
                <td class='heading2' align='center' style='background-color:$color'>$ipshow</td>
                <td class='heading2' align='center' style='background-color:$color'>$host</td>
                <td class='heading2' align='center' style='background-color:$color'>$listcity, $listregion<br />$listcountry</td>
                <td class='heading2' align='center' style='background-color:$color'>$iptype</td>
                <td class='heading2' align='center' style='background-color:$color'>$seedbox</td>
                <td class='heading2' align='center' style='background-color:$color'><a href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$id&amp;remove=$ipid&amp;deleteip=$userip&amp;username2=$username'><b>{$lang['iphistory_delete']}</b></a></td>
                <td class='heading2' align='center' style='background-color:$color'><a href='staffpanel.php?tool=iphistory&amp;action=bans&amp;banthisuser=$username&amp;banthisip=$userip'><b>{$lang['iphistory_ban']}</b></a></td>
                </tr>";
                }  // End Seedbox Check
    }
    $HTMLOUT .= "</table>";

    echo stdhead("{$username}'s IP History") . $HTMLOUT . stdfoot(); 
?>
