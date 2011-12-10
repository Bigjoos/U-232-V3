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
require_once(INCL_DIR.'pager_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_MODERATOR);

$lang = array_merge( $lang, load_language('cheaters') );
$HTMLOUT="";

if (isset($_POST["nowarned"]) && $_POST["nowarned"] == "nowarned") {
    if (empty($_POST["desact"]) && empty($_POST["remove"]))
        stderr("Error...", "You must select a user.");

    if (!empty($_POST["remove"])) {
        sql_query("DELETE FROM cheaters WHERE id IN (" . implode(", ", $_POST["remove"]) . ")") or sqlerr(__FILE__, __LINE__);
    }

    if (!empty($_POST["desact"])) {
        sql_query("UPDATE users SET enabled = 'no' WHERE id IN (" . implode(", ", $_POST["desact"]) . ")") or sqlerr(__FILE__, __LINE__);
    }
}

$HTMLOUT .= begin_main_frame();
$HTMLOUT .= begin_frame("Cheating Users:", true);

$res = sql_query("SELECT COUNT(*) FROM cheaters") or sqlerr(__FILE__, __LINE__);
$row = mysqli_fetch_array($res);
$count = $row[0];
$perpage = 15;
$pager = pager($perpage, $count, "staffpanel.php?tool=cheaters&amp;action=cheaters&amp;");


$HTMLOUT .="<form action='staffpanel.php?tool=cheaters&amp;action=cheaters' method='post'>
<script type='text/javascript'>
/*<![CDATA[*/
function klappe(id)
{var klappText=document.getElementById('k'+id);var klappBild=document.getElementById('pic'+id);if(klappText.style.display=='none'){klappText.style.display='block';}
else{klappText.style.display='none';}}
function klappe_news(id)
{var klappText=document.getElementById('k'+id);var klappBild=document.getElementById('pic'+id);if(klappText.style.display=='none'){klappText.style.display='block';klappBild.src='{$INSTALLER09['pic_base_url']}minus.gif';}
else{klappText.style.display='none';klappBild.src='{$INSTALLER09['pic_base_url']}plus.gif';}}	
</script>
<script type='text/javascript'>
var checkflag = 'false';
function check(field) {
if (checkflag == 'false') {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = 'true';
return 'Uncheck All Disable'; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = 'false';
return 'Check All Disable'; }
}
function check2(field) {
if (checkflag == 'false') {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = 'true';
return 'Uncheck All Remove'; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = 'false';
return 'Check All Remove'; }
}
/*]]>*/
</script>";
if ($count > $perpage)
$HTMLOUT .= $pager['pagertop'];
$HTMLOUT .="<table width=\"80%\">
<tr>
<td class=\"table\" width=\"10\" align=\"center\" valign=\"middle\">#</td>
<td class=\"table\">{$lang['cheaters_uname']}</td>
<td class=\"table\" width=\"10\" align=\"center\" valign=\"middle\">{$lang['cheaters_d']}</td>
<td class=\"table\" width=\"10\" align=\"center\" valign=\"middle\">{$lang['cheaters_r']}</td></tr>\n";

$res = sql_query("SELECT c.id as cid, c.added, c.userid, c.torrentid, c.client, c.rate, c.beforeup, c.upthis, c.timediff, c.userip, u.id AS uid, u.username AS user, u.class, u.downloaded, u.uploaded, t.id AS tid, t.name AS tname FROM cheaters AS c LEFT JOIN users AS u ON u.id=c.userid LEFT JOIN torrents AS t ON t.id=c.torrentid ORDER BY added DESC ".$pager['limit']."") or sqlerr(__FILE__, __LINE__);
while ($arr = mysqli_fetch_assoc($res)) {
    $torrname = htmlspecialchars(CutName($arr["tname"], 80));
    $cheater = "<b><a href='{$INSTALLER09['baseurl']}/userdetails.php?id={$arr['uid']}'>{$arr['user']}</a></b>{$lang['cheaters_hbcc']}<br />
    <b>{$lang['cheaters_torrent']} <a href='{$INSTALLER09['baseurl']}/details.php?id=".(int)$arr['tid']."' title='{$torrname}'>{$torrname}</a></b>
<br />{$lang['cheaters_upped']} <b>".mksize((int)$arr['upthis'])."</b><br />{$lang['cheaters_speed']} <b>".mksize((int)$arr['rate'])."/s</b><br />{$lang['cheaters_within']} <b>".(int)$arr['timediff']." {$lang['cheaters_sec']}</b><br />{$lang['cheaters_uc']} <b>".htmlspecialchars($arr['client'])."</b><br />{$lang['cheaters_ipa']} <b>".htmlspecialchars($arr['userip'])."</b>";

    $HTMLOUT .="<tr><td class=\"table\" width=\"10\" align=\"center\">".(int)$arr['cid']."</td>
    <td class=\"table\" align=\"left\"><a href=\"javascript:klappe('a1".(int)$arr['cid']."')\">".htmlspecialchars($arr['user'])."</a> - Added: ".get_date($arr['added'], 'DATE')."
    <div id=\"ka1".(int)$arr['cid']."\" style=\"display: none;\"><font color=\"red\">{$cheater}</font></div></td>
    <td class=\"table\" valign=\"top\" width=\"10\"><input type=\"checkbox\" name=\"desact[]\" value=\"".(int)$arr["uid"]."\"/></td>
    <td class=\"table\" valign=\"top\" width=\"10\"><input type=\"checkbox\" name=\"remove[]\" value=\"".(int)$arr["cid"]."\"/></td></tr>";
}

$HTMLOUT .="<tr>
<td class=\"table\" colspan=\"4\" align=\"right\">
<input type=\"button\" value=\"{$lang['cheaters_cad']}\" onclick=\"this.value=check(this.form.elements['desact[]'])\"/> <input type=\"button\" value=\"{$lang['cheaters_car']}\" onclick=\"this.value=check(this.form.elements['remove[]'])\"/> <input type=\"hidden\" name=\"nowarned\" value=\"nowarned\" /><input type=\"submit\" name=\"submit\" value=\"{$lang['cheaters_ac']}\" />
</td>
</tr>
</table></form>";
if ($count > $perpage)
$HTMLOUT .= $pager['pagerbottom'];

$HTMLOUT .= end_frame();
$HTMLOUT .= end_main_frame();
echo stdhead('Ratio Cheats') . $HTMLOUT . stdfoot();
die;
?>
