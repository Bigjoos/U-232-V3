<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once (INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'));
if ($CURUSER['class'] < UC_STAFF) stderr("No Permision", "system file");
$id = (int)$_GET['id'];
if (!is_valid_id($id)) die();
$action = isset($_GET['action']) ? htmlsafechars($_GET['action']) : '';
$res = sql_query("SELECT hash1, username, passhash FROM users WHERE id = ".sqlesc($id)." AND class >= ".UC_STAFF) or sqlerr(__FILE__, __LINE__);
$arr = mysqli_fetch_assoc($res);
$hash1 = md5($arr['username'].TIME_NOW.$arr['passhash']);
$hash2 = md5($hash1.TIME_NOW.$arr['username']);
$hash3 = md5($hash1.$hash2.$arr['passhash']);
$hash1.= $hash2.$hash3;
if ($action == 'reset') {
    $sure = isset($_GET['sure']) ? (int)($_GET['sure']) : 0;
    if ($sure != '1') stderr("Sanity check...", "You are about to reset your login link: Click <a href='createlink.php?action=reset&amp;id=$id&amp;sure=1'>here</a> if you are sure.");
    sql_query("UPDATE users SET hash1 = ".sqlesc($hash1)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('user'.$id);
    $mc1->update_row(false, array(
        'hash1' => $hash1
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    $mc1->begin_transaction('MyUser_'.$id);
    $mc1->update_row(false, array(
        'hash1' => $hash1
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('hash1_'.$id);
    $mc1->update_row(false, array(
        'hash1' => $hash1
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_hash']);
    header("Refresh: 1; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
    stderr("Success", "Your login link reset successfully.");
} else {
    if ($arr['hash1'] === '') {
        sql_query("UPDATE users SET hash1 = ".sqlesc($hash1)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        header("Refresh: 2; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
        $mc1->begin_transaction('user'.$id);
        $mc1->update_row(false, array(
            'hash1' => $hash1
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
        $mc1->begin_transaction('MyUser_'.$id);
        $mc1->update_row(false, array(
            'hash1' => $hash1
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
        $mc1->begin_transaction('hash1_'.$id);
        $mc1->update_row(false, array(
            'hash1' => $hash1
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['user_hash']);
        stderr('Success', "Your login link was created successfully.");
    } else {
        header("Refresh: 2; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
        stderr('Error', "You have already created your login link.");
    }
}
?>
