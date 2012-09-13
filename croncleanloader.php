<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
define('IN_INSTALLER09_CRON', true);
if (!isset($argv) OR !is_array($argv) OR (count($argv) != 2) OR !preg_match('/^[0-9a-fA-F]{32}$/i', $argv[1])) {
    exit('Go away!');
}
require_once "include/cronclean.php";
?>
