<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
if (!defined('IN_INSTALLER09_ADMIN')) {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
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
require_once (INCL_DIR.'user_functions.php');
require_once (INCL_DIR.'html_functions.php');
require_once INCL_DIR.'pager_functions.php';
require_once (CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
$lang = array_merge($lang);
$HTMLOUT = $count = '';
$res = sql_query("SELECT COUNT(id) FROM peers") or sqlerr(__FILE__, __LINE__);
$row = mysqli_fetch_row($res);
$count = $row[0];
$peersperpage = 15;
$HTMLOUT.= "<h2 align='center'>Site peers</h2>
<font class='small'>There is approx&nbsp;".htmlsafechars($count)."&nbsp;peers currently</font>";
$HTMLOUT.= begin_main_frame();
$pager = pager($peersperpage, $count, "staffpanel.php?tool=view_peers&amp;action=view_peers&amp;");
if ($count > $peersperpage) $HTMLOUT.= $pager['pagertop'];
$sql = "SELECT p.id, p.userid, p.torrent, p.passkey, p.peer_id, p.ip, p.port, p.uploaded, p.downloaded, p.to_go, p.seeder, p.started, p.last_action, p.connectable, p.agent, p.finishedat, p.downloadoffset, p.uploadoffset, u.username, t.name "."FROM peers AS p "."LEFT JOIN users AS u ON u.id=p.userid "."LEFT JOIN torrents AS t ON t.id=p.torrent WHERE started != '0'"."ORDER BY p.started DESC {$pager['limit']}";
$result = sql_query($sql) or sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($result) != 0) {
    $HTMLOUT.= "<table width='100%' border='1' cellspacing='0' cellpadding='5' align='center'>
<tr>
<td class='colhead' align='center' width='1%'>User</td>
<td class='colhead' align='center' width='1%'>Torrent</td>
<td class='colhead' align='center' width='1%'>Ip</td>
<td class='colhead' align='center' width='1%'>Port</td>
<td class='colhead' align='center' width='1%'>Up</td>
".($INSTALLER09['ratio_free'] ? "" : "<td class='colhead' align='center' width='1%'>Dn</td>")."
<td class='colhead' align='center' width='1%'>Pssky</td>
<td class='colhead' align='center' width='1%'>Con</td>
<td class='colhead' align='center' width='1%'>Seed</td>
<td class='colhead' align='center' width='1%'>Start</td>
<td class='colhead' align='center' width='1%'>Last</td>
<td class='colhead' align='center' width='1%'>Up/Off</td>
".($INSTALLER09['ratio_free'] ? "" : "<td class='colhead' align='center' width='1%'>Dn/Off</td>")."
<td class='colhead' align='center' width='1%'>To Go</td>
</tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $smallname = substr(htmlsafechars($row["name"]) , 0, 25);
        if ($smallname != htmlsafechars($row["name"])) {
            $smallname.= '...';
        }
        $HTMLOUT.= '<tr>
<td><a href="userdetails.php?id='.(int)($row['userid']).'">'.htmlsafechars($row['username']).'</a></td>
<td><a href="details.php?id='.(int)($row['torrent']).'">'.$smallname.'</a></td>
<td align="center">'.htmlsafechars($row['ip']).'</td>
<td align="center">'.htmlsafechars($row['port']).'</td>
<td align="center">'.htmlsafechars(mksize($row['uploaded'])).'</td>
'.($INSTALLER09['ratio_free'] ? '' : '<td align="center">'.htmlsafechars(mksize($row['downloaded'])).'</td>').'
<td align="center">'.htmlsafechars($row['passkey']).'</td>
<td align="center">'.($row['connectable'] == 'yes' ? "<img src='".$INSTALLER09['pic_base_url']."aff_tick.gif' alt='Yes' title='Yes' />" : "<img src='".$INSTALLER09['pic_base_url']."aff_cross.gif' alt='No' title='No' />").'</td>
<td align="center">'.($row['seeder'] == 'yes' ? "<img src='".$INSTALLER09['pic_base_url']."aff_tick.gif' alt='Yes' title='Yes' />" : "<img src='".$INSTALLER09['pic_base_url']."aff_cross.gif' alt='No' title='No' />").'</td>
<td align="center">'.get_date($row['started'], 'DATE', 0, 1).'</td>
<td align="center">'.get_date($row['last_action'], 'DATE', 0, 1).'</td>
<td align="center">'.htmlsafechars(mksize($row['uploadoffset'])).'</td>
'.($INSTALLER09['ratio_free'] ? '' : '<td align="center">'.htmlsafechars(mksize($row['downloadoffset'])).'</td>').'
<td align="center">'.htmlsafechars(mksize($row['to_go'])).'</td>
</tr>';
    }
    $HTMLOUT.= "</table>";
} else $HTMLOUT.= "No peers found";
if ($count > $peersperpage) $HTMLOUT.= $pager['pagerbottom'];
$HTMLOUT.= end_main_frame();
echo stdhead('Peer Overview').$HTMLOUT.stdfoot();
die;
?>
