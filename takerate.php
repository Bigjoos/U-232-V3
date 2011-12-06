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
$lang = array_merge( load_language('global'), load_language('takerate') );


if (!isset($CURUSER))
	stderr("Error","{$lang['rate_login']}");

if (!mkglobal("rating:id"))
	stderr("Error","{$lang['rate_miss_form_data']}");

$id = 0 + $id;
if (!$id)
	stderr("Error","{$lang['rate_invalid_id']}");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	stderr("Error","{$lang['rate_invalid']}");

$f_r = sql_query("SELECT owner, numratings, ratingsum , IF(numratings < {$INSTALLER09['minvotes']}, NULL, ROUND(ratingsum / numratings, 1)) AS rating FROM torrents WHERE id = $id");
$r_f = mysqli_fetch_assoc($f_r);
if (!$r_f)
	stderr("Error","{$lang['rate_torrent_not_found']}");

$time_now = TIME_NOW;
$res = sql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, $time_now)");
if (!$res) {
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 1062)
		stderr("Error","{$lang['rate_already_voted']}");
	else
		((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
}

sql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
//$f_r = sql_query("SELECT ratingsum, numratings, IF(numratings < {$INSTALLER09['minvotes']}, NULL, ROUND(ratingsum / numratings, 1)) AS rating FROM torrents WHERE id = ".$id) or sqlerr(__FILE__, __LINE__);
//$r_f = mysqli_fetch_assoc($f_r);
$update['numratings'] = ($r_f['numratings'] + 1);
$update['ratingsum'] = ($r_f['ratingsum'] + $rating);
$mc1->begin_transaction('torrent_details_'.$id);
$mc1->update_row(false, array('numratings' => $update['numratings'], 'ratingsum' => $update['ratingsum'], 'rating' => $r_f['rating']));
$mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
if($INSTALLER09['seedbonus_on'] == 1){
//===add karma 
sql_query("UPDATE users SET seedbonus = seedbonus+5.0 WHERE id = ".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);
$update['seedbonus'] = ($CURUSER['seedbonus'] + 5);
$mc1->begin_transaction('userstats_'.$CURUSER["id"]);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus']));
$mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
$mc1->begin_transaction('user_stats_'.$CURUSER["id"]);
$mc1->update_row(false, array('seedbonus' => $update['seedbonus']));
$mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
//===end
}
header("Refresh: 0; url=details.php?id=$id&rated=1");

?>
