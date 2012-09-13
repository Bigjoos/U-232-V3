<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
require_once ("include/ann_config.php");
require_once ('include/ann_functions.php');
function error($err)
{
    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    exit("d14:failure reason".strlen($err).":{$err}ed5:flagsd20:min_request_intervali1800eeee");
}
if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($INSTALLER09['mysql_host'], $INSTALLER09['mysql_user'], $INSTALLER09['mysql_pass']))) {
    exit();
}
@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE {$INSTALLER09['mysql_db']}")) or exit();
if (!isset($_GET['info_hash']) OR (strlen($_GET['info_hash']) != 20)) error('Invalid hash');
$numhash = count($_GET['info_hash']);
$torrents = array();
if ($numhash < 1) die("Scrape Error d5:filesdee");
elseif ($numhash == 1) {
    $torrent = get_torrent_from_hash(bin2hex($_GET['info_hash']));
    if ($torrent) $torrents[$_GET['info_hash']] = $torrent;
} else {
    foreach ($_GET['info_hash'] as $hash) {
        $torrent = get_torrent_from_hash(bin2hex($hash));
        if ($torrent) $torrents[$hash] = $torrent;
    }
}
$r = 'd5:filesd';
foreach ($torrents as $info_hash => $torrent) $r.= '20:'.$info_hash.'d8:completei'.$torrent['seeders'].'e10:downloadedi'.$torrent['times_completed'].'e10:incompletei'.$torrent['leechers'].'ee';
$r.= 'ee';
header('Content-Type: text/plain; charset=UTF-8');
header('Pragma: no-cache');
echo ($r);
die();
//die($r);

?>
