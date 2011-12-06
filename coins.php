<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();
$lang = array_merge( load_language('global'));
// / Mod by dokty - tbdev.net

$id = 0 + $_GET["id"];
$points = 0 + $_GET["points"];
if (!is_valid_id($id) || !is_valid_id($points))
    die();

$pointscangive = array("10", "20", "50", "100", "200", "500", "1000");
if (!in_array($points, $pointscangive))
    stderr("Error", "You can't give that amount of points!!!");

$sdsa = sql_query("SELECT 1 FROM coins WHERE torrentid=" . sqlesc($id) . " AND userid =" . sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
$asdd = mysqli_fetch_array($sdsa);
if ($asdd)
    stderr("Error", "You already gave points to this torrent.");

$res = sql_query("SELECT owner,name,points FROM torrents WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);

$row = mysqli_fetch_assoc($res) or stderr("Error", "Torrent was not found");
$userid = (int)$row["owner"];

if ($userid == $CURUSER["id"])
    stderr("Error", "You can't give your self points!");

if ($CURUSER["seedbonus"] < $points)
    stderr("Error", "You dont have enough points");

$sql = sql_query('SELECT seedbonus '.
                       'FROM users '.
                       'WHERE id = '.$userid) or sqlerr(__FILE__, __LINE__);
      $User = mysqli_fetch_assoc($sql);

sql_query("INSERT INTO coins (userid, torrentid, points) VALUES (" . sqlesc($CURUSER["id"]) . ", " . sqlesc($id) . ", " . sqlesc($points) . ")") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE users SET seedbonus=seedbonus+" . $points . " WHERE id=" . sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE users SET seedbonus=seedbonus-" . $points . " WHERE id=" . sqlesc($CURUSER["id"])) or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE torrents SET points=points+" . $points . " WHERE id=" . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$msg = sqlesc("You have been given " . htmlspecialchars($points) . " points by " . $CURUSER["username"] . " for torrent [url=" . $INSTALLER09['baseurl'] . "/details.php?id=" . $id . "]" . htmlspecialchars($row["name"]) . "[/url].");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES({$INSTALLER09['bot_id']}, $userid, $msg, " . TIME_NOW . ", 'You have been given a gift')") or sqlerr(__FILE__, __LINE__);
$update['points'] = ($row['points']+$points);
$update['seedbonus_uploader'] = ($User['seedbonus']+$points);
$update['seedbonus_donator'] = ($CURUSER['seedbonus']-$points);
//==The torrent
$mc1->begin_transaction('torrent_details_'.$id);
$mc1->update_row(false, array('points' => $update['points']));
$mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
//==The uploader
$mc1->begin_transaction('userstats_'.$userid);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus_uploader']));
$mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
$mc1->begin_transaction('user_stats_'.$userid);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus_uploader']));
$mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
//==The donator
$mc1->begin_transaction('userstats_'.$CURUSER["id"]);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus_donator']));
$mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
$mc1->begin_transaction('user_stats_'.$CURUSER["id"]);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus_donator']));
$mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
//== delete the pm keys
$mc1->delete_value('inbox_new_'.$userid);
$mc1->delete_value('inbox_new_sb_'.$userid);
$mc1->delete_value('coin_points_'.$id);

header("Refresh: 3; url=details.php?id=$id");
stderr("Done", "Successfully gave points to this torrent.");
?>
