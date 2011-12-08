<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 V3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn.
 **/
error_reporting(E_ALL);
////////////////// GLOBAL VARIABLES /////////////////////////////////////	
//== Php poop
$finished = $plist =  '';
$agent = $_SERVER["HTTP_USER_AGENT"];
$detectedclient = $_SERVER["HTTP_USER_AGENT"];
require_once("include/class/class_cache.php");
require_once("include/class/class_bt_options.php");
$mc1 = NEW CACHE();
//$mc1->MemcachePrefix = 'u232_3_';
define('TIME_NOW', time());
define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
define ('UC_STAFF', 4);
define('ANN_SQL_DEBUG', 1);
$INSTALLER09['announce_interval'] = 60 * 30;
$INSTALLER09['min_interval'] = 60 * 15;
$INSTALLER09['connectable_check'] = 1;
$INSTALLER09['ann_sql_error_log'] = 'logs/ann_sql_err_'.date('M_D_Y').'.log';
// DB setup
//$INSTALLER09['baseurl'] = 'http://'.$_SERVER['HTTP_HOST'];
$INSTALLER09['baseurl'] = '#baseurl';
$INSTALLER09['mysql_host'] = "#mysql_host";
$INSTALLER09['mysql_user'] = "#mysql_user";
$INSTALLER09['mysql_pass'] = "#mysql_pass";
$INSTALLER09['mysql_db']   = "#mysql_db";
$INSTALLER09['expires']['user_passkey'] = 3600*8;  // 8 hours 
$INSTALLER09['expires']['contribution'] = 3*86400; // 3 * 86400 3 days
$INSTALLER09['expires']['happyhour'] = 43200; // 43200 1/2 day
$INSTALLER09['expires']['sitepot'] = 86400; // 86400 1 day
$INSTALLER09['expires']['torrent_announce'] = 86400; // 86400 1 day
$INSTALLER09['expires']['torrent_details'] = 30*86400; // = 30 days
?>
