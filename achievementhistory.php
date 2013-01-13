<?php
/**
 * https://github.com/Bigjoos/
 * Licence Info: GPL
 * Copyright (C) 2010 U-232 v.3
 * A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 * Project Leaders: Mindless, putyn.
 *
 */
// Achievements mod by MelvinMeow
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'bittorrent.php');
require_once (INCL_DIR . 'user_functions.php');
require_once (INCL_DIR . 'pager_functions.php');
require_once (CLASS_DIR . 'page_verify.php');
dbconn();
loggedinorreturn();
$newpage = new page_verify();
$newpage->create('takecounts');
$lang = array_merge(load_language('global'));
$HTMLOUT = "";
$id = (int)$_GET["id"];
if (!is_valid_id($id)) stderr("Error", "It appears that you have entered an invalid id.");
$res = sql_query("SELECT users.id, users.username, usersachiev.achpoints, usersachiev.spentpoints FROM users LEFT JOIN usersachiev ON users.id = usersachiev.id WHERE users.id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$arr = mysqli_fetch_assoc($res);
if (!$arr) stderr("Error", "It appears that there is no user with that id.");
$achpoints = (int)$arr['achpoints'];
$spentpoints = (int)$arr['spentpoints'];
$res = sql_query("SELECT COUNT(*) FROM achievements WHERE userid =" . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$row = mysqli_fetch_row($res);
$count = $row[0];
$perpage = 15;
if (!$count) stderr("No Achievements", "It appears that <a class='altlink' href='userdetails.php?id=" . (int)$arr['id'] . "'>" . htmlsafechars($arr['username']) . "</a> currently has no achievements.");
$pager = pager($perpage, $count, "?id=$id&amp;");
if ($id == $CURUSER['id']) {
    $HTMLOUT.= "<span class='btn'><a href='/achievementlist.php'>[<b>Achievements List</b>]</a></span>&nbsp;&nbsp;<span class='btn'><a href='/postcounter.php'>[<b>Forum Post Counter</b>]</a></span>&nbsp;&nbsp;<span class='btn'><a href='/topiccounter.php'>[<b>Forum Topic Counter</b>]</a></span>&nbsp;&nbsp;<span class='btn'><a href='/invitecounter.php'>[<b>Invite Counter</b>]</a></span>";
}
$HTMLOUT.= "<h1>Achievements for user: <a class='altlink' href='{$INSTALLER09['baseurl']}/userdetails.php?id=" . (int)$arr['id'] . "'>" . htmlsafechars($arr['username']) . "</a></h1>
  <h2>Currently " . htmlsafechars($row['0']) . " achievement" . ($row[0] == 1 ? "" : "s") . ".</h2>\n";
if ($id == $CURUSER['id']) {
    $HTMLOUT.= "<h2><a class='altlink' href='achievementbonus.php'>" . htmlsafechars($achpoints) . " Points Available // " . htmlsafechars($spentpoints) . " Points spent.</a></h2>\n";
}
if ($count > $perpage) $HTMLOUT.= $pager['pagertop'];
$HTMLOUT.= "<table border='0' cellspacing='0' cellpadding='5'>
  <tr>
  <td class='colhead' align='center'>Award</td>
  <td class='colhead' align='center'>Description</td>
  <td class='colhead' align='center'>Date Earned</td>
  </tr>\n";
$res = sql_query("SELECT * FROM achievements WHERE userid=" . sqlesc($id) . " ORDER BY date DESC {$pager['limit']}") or sqlerr(__FILE__, __LINE__);
while ($arr = mysqli_fetch_assoc($res)) {
    $HTMLOUT.= "<tr>
  <td class='one' align='center'><img src='pic/achievements/" . htmlsafechars($arr['icon']) . "' alt='" . htmlsafechars($arr['achievement']) . "' title='" . htmlsafechars($arr['achievement']) . "' /></td>
  <td class='two' align='center'>" . htmlsafechars($arr['description']) . "</td>
  <td class='one' align='center'>" . get_date($arr['date'], '') . "</td>
  </tr>\n";
}
$HTMLOUT.= "</table>\n";
if ($count > $perpage) $HTMLOUT.= $pager['pagerbottom'];
echo stdhead('Achievement History') . $HTMLOUT . stdfoot();
die;
?>
