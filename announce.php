<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 V3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn.
 *
 */
require_once ("include/ann_config.php");
require_once ("include/ann_functions.php");
if (isset($_SERVER['HTTP_COOKIE']) || isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || isset($_SERVER['HTTP_ACCEPT_CHARSET'])) exit('It takes 46 muscles to frown but only 4 to flip \'em the bird.');
gzip();
$parts = array();
if (!isset($_GET['passkey']) OR !preg_match('/^[0-9a-fA-F]{32}$/i', $_GET['passkey'], $parts)) err("Invalid Passkey");
else $GLOBALS['passkey'] = $parts[0];
foreach (array(
    "info_hash",
    "peer_id",
    "event",
    "ip",
    "localip"
) as $x) {
    if (isset($_GET["$x"])) $GLOBALS[$x] = "".$_GET[$x];
}
foreach (array(
    "port",
    "downloaded",
    "uploaded",
    "left"
) as $x) {
    $GLOBALS[$x] = 0 + $_GET[$x];
}
foreach (array(
    "passkey",
    "info_hash",
    "peer_id",
    "port",
    "downloaded",
    "uploaded",
    "left"
) as $x) if (!isset($x)) err("Missing key: $x");
foreach (array(
    "info_hash",
    "peer_id"
) as $x) if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (".strlen($GLOBALS[$x])." - ".urlencode($GLOBALS[$x]).")");
unset($x);
$info_hash = bin2hex($info_hash);
$ip = $_SERVER['REMOTE_ADDR'];
$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;
$rsize = 30;
foreach (array(
    "num want",
    "numwant",
    "num_want"
) as $k) {
    if (isset($_GET[$k])) {
        $rsize = (int)$_GET[$k];
        break;
    }
}
if (!$port || $port > 0xffff) err("invalid port");
if (!isset($event)) $event = "";
$seeder = ($left == 0) ? "yes" : "no";
if (!($db = @($GLOBALS["___mysqli_ston"] = mysqli_connect($INSTALLER09['mysql_host'], $INSTALLER09['mysql_user'], $INSTALLER09['mysql_pass'])) AND $select = @((bool)mysqli_query($db, "USE {$INSTALLER09['mysql_db']}")))) err('Please call back later');
if (($user = $mc1->get_value('u_passkey_'.$passkey)) === false) {
    $user_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id, uploaded, downloaded, class, downloadpos, parked, perms, ip, free_switch, hnrwarn, highspeed, enabled FROM users WHERE passkey=".ann_sqlesc($passkey)) or ann_sqlerr(__FILE__, __LINE__);
    if (mysqli_num_rows($user_query) != 1) err("Unknown passkey. Please redownload the torrent from {$INSTALLER09['baseurl']}.");
    $user = mysqli_fetch_assoc($user_query);
    $user['id'] = (int)$user['id'];
    $user['uploaded'] = (float)$user['uploaded'];
    $user['downloaded'] = (float)$user['downloaded'];
    $user['class'] = (int)$user['class'];
    $user['downloadpos'] = (int)$user['downloadpos'];
    $user['free_switch'] = (int)$user['free_switch'];
    $user['ip'] = $user['ip'];
    $user['perms'] = (int)$user['perms'];
    $mc1->cache_value('u_passkey_'.$passkey, $user, $INSTALLER09['expires']['user_passkey']);
}
$userid = (int)$user["id"];
$user['perms'] = (int)$user['perms'];
if ($user['enabled'] == 'no') err('Permission denied, you\'re not enabled');
//== Start ip logger - Melvinmeow, Mindless, pdq
$no_log_ip = ($user['perms'] & bt_options::PERMS_NO_IP);
if ($no_log_ip) {
    $ip = '127.0.0.1';
    $userid = (int)$user["id"];
}
if (!$no_log_ip) {
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM ips WHERE ip = ".ann_sqlesc($ip)." AND userid =".ann_sqlesc($userid)) or ann_sqlerr(__FILE__, __LINE__);
    if (mysqli_num_rows($res) == 0) {
        mysqli_query($GLOBALS["___mysqli_ston"], "INSERT LOW_PRIORITY INTO ips (userid, ip, lastannounce, type) VALUES (".ann_sqlesc($userid).", ".ann_sqlesc($ip).", ".TIME_NOW.",'announce')") or ann_sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('ip_history_'.$userid);
        $mc1->delete_value('u_passkey_'.$passkey);
    } else {
        mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE LOW_PRIORITY ips SET lastannounce = ".TIME_NOW." WHERE ip = ".ann_sqlesc($ip)." AND userid =".ann_sqlesc($userid)) or ann_sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('ip_history_'.$userid);
        $mc1->delete_value('u_passkey_'.$passkey);
    }
} // End Ip logger
$realip = $_SERVER['REMOTE_ADDR'];
$torrent = get_torrent_from_hash($info_hash, $userid);
if (!$torrent) err("torrent query error - contact site admin");
$torrentid = (int)$torrent["id"];
$torrent_modifier = get_slots($torrentid, $userid);
$torrent['freeslot'] = $torrent_modifier['freeslot'];
$torrent['doubleslot'] = $torrent_modifier['doubleslot'];
$happy_multiplier = get_happy($torrentid, $userid);
$fields = 'seeder, peer_id, ip, port, uploaded, downloaded, userid, ('.TIME_NOW.' - last_action) AS announcetime, last_action AS ts, '.TIME_NOW.' AS nowts, prev_action AS prevts';
//== Wantseeds - Retro
$limit = '';
if ($torrent['numpeers'] > $rsize) $limit = "ORDER BY RAND() LIMIT $rsize";
// if user is a seeder, then only supply leechers.
$wantseeds = '';
if ($seeder == 'yes') $wantseeds = 'AND seeder = "no"';
$res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT $fields FROM peers WHERE torrent = $torrentid $wantseeds $limit") or ann_sqlerr(__FILE__, __LINE__);
unset($wantseeds);
//== compact mod
if ($_GET['compact'] != 1) {
    $resp = "d".benc_str("interval")."i".$INSTALLER09['announce_interval']."e".benc_str("private").'i1e'.benc_str("peers")."l";
} else {
    $resp = "d".benc_str("interval")."i".$INSTALLER09['announce_interval']."e".benc_str("private").'i1e'.benc_str("min interval")."i".300."e5:"."peers";
}
$peer = array();
$peer_num = 0;
while ($row = mysqli_fetch_assoc($res)) {
    if ($_GET['compact'] != 1) {
        $row["peer_id"] = str_pad($row["peer_id"], 20);
        if ($row["peer_id"] === $peer_id) {
            $self = $row;
            continue;
        }
        $resp.= "d".benc_str("ip").benc_str($row["ip"]);
        if (!$_GET['no_peer_id']) {
            $resp.= benc_str("peer id").benc_str($row["peer_id"]);
        }
        $resp.= benc_str("port")."i".$row["port"]."e"."e";
    } else {
        $peer_ip = explode('.', $row["ip"]);
        $peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
        $peer_port = pack("n*", (int)$row["port"]);
        $time = intval((TIME_NOW % 7680) / 60);
        if ($_GET['left'] == 0) {
            $time+= 128;
        }
        $time = pack("C", $time);
        $peer[] = $time.$peer_ip.$peer_port;
        $peer_num++;
    }
}
if ($_GET['compact'] != 1) $resp.= "ee";
else {
    $o = "";
    for ($i = 0; $i < $peer_num; $i++) {
        $o.= substr($peer[$i], 1, 6);
    }
    $resp.= strlen($o).':'.$o.'e';
}
$selfwhere = "torrent = $torrentid AND ".hash_where("peer_id", $peer_id);
if (!isset($self)) {
    $res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT $fields FROM peers WHERE $selfwhere") or ann_sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        $userid = (int)$row['userid'];
        $self = $row;
    }
}
//// Up/down stats shit////////////////////////////////////////////////////////////
$useragent = substr($peer_id, 0, 8);
$agentarray = array(
    "R34",
    "-AZ21",
    "-AZ22",
    "-AZ24",
    "AZ2500BT",
    "BS",
    "exbc",
    "-TS",
    "Mbrst",
    "-BB",
    "-SZ",
    "XBT",
    "turbo",
    "A301",
    "A310",
    "-UT11",
    "-UT12",
    "-UT13",
    "-UT14",
    "-UT15",
    "FUTB",
    "-BC",
    "LIME",
    "eX",
    "-ML",
    "FRS",
    "-AG"
);
foreach ($agentarray as $bannedclient) if (strpos($useragent, $bannedclient) !== false) err("Client is banned. Please use uTorrent 1.6 > or Azureus 2.5 >!");
//== Anti flood by Retro
$announce_wait = 10;
if (isset($self) && ($self['prevts'] > ($self['nowts'] - $announce_wait))) err('There is a minimum announce time of '.$announce_wait.' seconds');
if ($torrent['vip'] == 1 && $user['class'] < UC_VIP) err('VIP Access Required, You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you with more Vip-Only Torrents!');
if (!isset($self)) {
    $valid = mysqli_fetch_row(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM peers WHERE torrent=".ann_sqlesc($torrentid)." AND passkey=".ann_sqlesc($passkey))) or ann_sqlerr(__FILE__, __LINE__);
    if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");
} else {
    $upthis = max(0, $uploaded - $self["uploaded"]);
    $downthis = max(0, $downloaded - $self["downloaded"]);
    //==sitepot
    if (($Pot_query = $mc1->get_value('Sitepot_')) === false) {
        $Pot_query = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT value_s, value_i FROM avps WHERE arg = 'sitepot'")) or ann_sqlerr(__FILE__, __LINE__);
        $Pot_query['value_s'] = (int)$Pot_query['value_s'];
        $Pot_query['value_i'] = (int)$Pot_query['value_i'];
        $mc1->cache_value('Sitepot_', $Pot_query, $INSTALLER09['expires']['sitepot']);
    }
    if ($Pot_query["value_s"] == 1 && $Pot_query["value_i"] >= 10000) {
        $downthis = 0;
    }
    //== happyhour
    if ($happy_multiplier) {
        $upthis = $upthis * $happy_multiplier;
        $downthis = 0;
    }
    //== Karma contribution system by ezero updated by putyn/Mindless
    if (($contribution = $mc1->get_value('freecontribution_')) === false) {
        $contribution = mysqli_fetch_assoc(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT startTime, endTime, freeleechEnabled, duploadEnabled, hdownEnabled FROM events ORDER BY startTime DESC LIMIT 1")) or ann_sqlerr(__FILE__, __LINE__);
        $contribution["startTime"] = (int)$contribution["startTime"];
        $contribution["endTime"] = (int)$contribution["endTime"];
        $contribution["freeleechEnabled"] = (int)$contribution["freeleechEnabled"];
        $contribution["duploadEnabled"] = (int)$contribution["duploadEnabled"];
        $contribution["hdownEnabled"] = (int)$contribution["hdownEnabled"];
        $mc1->cache_value('freecontribution_', $contribution, $INSTALLER09['expires']['contribution']);
    }
    if ($contribution["startTime"] < TIME_NOW && $contribution["endTime"] > TIME_NOW) {
        if ($contribution['freeleechEnabled'] == 1) {
            $downthis = 0;
        }
        if ($contribution['duploadEnabled'] == 1) {
            $upthis = $upthis * 2;
            $downthis = 0;
        }
        if ($contribution['hdownEnabled'] == 1) {
            $downthis = $downthis / 2;
        }
    }
    if ($upthis > 0 || $downthis > 0) {
        $isfree = $isdouble = $issilver = '';
        include ("cache/free_cache.php");
        if (isset($free)) {
            foreach ($free as $fl) {
                $isfree = ($fl['modifier'] == 1 || $fl['modifier'] == 3) && $fl['expires'] > TIME_NOW;
                $isdouble = ($fl['modifier'] == 2 || $fl['modifier'] == 3) && $fl['expires'] > TIME_NOW;
                $issilver = ($fl['modifier'] == 4) && $fl['expires'] > TIME_NOW;
            }
        }
        //== Silver torrents
        if ($torrent['silver'] != 0 || $issilver) {
            $upthis = $upthis;
            $downthis = $downthis / 2;
        }
        $RatioFreeCondition = ($INSTALLER09['ratio_free'] ? "downloaded = downloaded + 0" : "downloaded = downloaded + $downthis");
        $crazyhour_on = crazyhour_announce();
        if (!($crazyhour_on || $isfree || $user['free_switch'] != 0 || $torrent['free'] != 0 || $torrent['vip'] != 0 || ($torrent['freeslot'] != 0))) $updq[0] = $RatioFreeCondition;
        if ($crazyhour_on) // crazyhour
        $updq[1] = "uploaded = uploaded + ($upthis*3)";
        else $updq[1] = "uploaded = uploaded + ".(($torrent['doubleslot'] != 0 || $isdouble) ? ($upthis * 2) : $upthis);
        $udq = implode(',', $updq);
        mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE LOW_PRIORITY users SET $udq WHERE id=".$userid) or ann_sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('userstats_'.$userid);
        $mc1->delete_value('user_stats_'.$userid);
    }
    //=== abnormal upload detection
    if ($user['highspeed'] == 'no' && $upthis > 103872) {
        //=== Work out time difference
        $diff = (TIME_NOW - $self['ts']);
        $rate = ($upthis / ($diff + 1));
        $last_up = (int)$user['uploaded'];
        //=== about 1 MB/s
        if ($rate > 103872) {
            auto_enter_abnormal_upload($userid, $rate, $upthis, $diff, $torrentid, $agent, $realip, $last_up);
        }
    } //=== end abnormal upload detection
    
}
//== Snatchlist and Hit and Run begin
if (portblacklisted($port)) {
    err("Port $port is blacklisted.");
} elseif ($INSTALLER09['connectable_check']) {
    //== connectable checking - pdq
    $connkey = 'conn:'.md5($realip.':'.$port);
    if (($connectable = $mc1->get_value($connkey)) === false) {
        $sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
        if (!$sockres) {
            $connectable = 'no';
            $conn_ttl = 15;
        } else {
            $connectable = 'yes';
            $conn_ttl = 900;
            @fclose($sockres);
        }
        $mc1->cache_value($connkey, $connectable, $conn_ttl);
    }
}
//==
$a = 0;
$res_snatch = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT seedtime, uploaded, downloaded, finished, start_date AS start_snatch FROM snatched WHERE torrentid = ".ann_sqlesc($torrentid)." AND userid = ".ann_sqlesc($userid)) or ann_sqlerr(__FILE__, __LINE__);
if (mysqli_num_rows($res_snatch) > 0) {
    $a = mysqli_fetch_assoc($res_snatch);
}
if (!mysqli_affected_rows($GLOBALS["___mysqli_ston"]) && $seeder == "no") mysqli_query($GLOBALS["___mysqli_ston"], "INSERT LOW_PRIORITY INTO snatched (torrentid, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, start_date, last_action, seeder, agent) VALUES (".ann_sqlesc($torrentid).", ".ann_sqlesc($userid).", ".ann_sqlesc($peer_id).", ".ann_sqlesc($realip).", ".ann_sqlesc($port).", ".ann_sqlesc($connectable).", ".ann_sqlesc($uploaded).", ".($INSTALLER09['ratio_free'] ? "0" : "".ann_sqlesc($downloaded)."").", ".ann_sqlesc($left).", ".TIME_NOW.", ".TIME_NOW.", ".ann_sqlesc($seeder).", ".ann_sqlesc($agent).")") or ann_sqlerr(__FILE__, __LINE__);
$updateset = $snatch_updateset = array();
if (isset($self) && $event == "stopped") {
    mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM peers WHERE ".ann_sqlesc($selfwhere)) or ann_sqlerr(__FILE__, __LINE__);
    //=== only run the function if the ratio is below 1
    if (($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis) && $a['finished'] == 'yes') {
        $HnR_time_seeded = ($a['seedtime'] + $self['announcetime']);
        //=== get times per class
        switch (true) {
        case ($user['class'] <= UC_POWER_USER):
            $days_3 = 1 * 86400; //== 1 days
            $days_14 = 1 * 86400; //== 1 days
            $days_over_14 = 86400; //== 1 day
            break;

        case ($user['class'] < UC_STAFF):
            $days_3 = 43200; //== 12 hours
            $days_14 = 43200; //== 12 hours
            $days_over_14 = 43200; //== 12 hours
            break;

        case ($user['class'] >= UC_STAFF):
            $days_3 = 43200; //== 12 hours
            $days_14 = 43200; //== 12 hours
            $days_over_14 = 43200; //== 12 hours
            break;
        }
        switch (true) {
        case (($a['start_snatch'] - $torrent['ts']) < 7 * 86400):
            $minus_ratio = ($days_3 - $HnR_time_seeded);
            break;

        case (($a['start_snatch'] - $torrent['ts']) < 21 * 86400):
            $minus_ratio = ($days_14 - $HnR_time_seeded);
            break;

        case (($a['start_snatch'] - $torrent['ts']) >= 21 * 86400):
            $minus_ratio = ($days_over_14 - $HnR_time_seeded);
            break;
        }
        $hit_and_run = (($minus_ratio > 0 && ($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis)) ? "seeder='no', hit_and_run= '".TIME_NOW."'" : "hit_and_run = '0'");
    } //=== end if not 1:1 ratio
    else $hit_and_run = "hit_and_run = '0'";
    //=== end hit and run
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        if ($self['seeder'] == "yes") adjust_torrent_peers($torrentid, -1, 0, 0);
        else adjust_torrent_peers($torrentid, 0, -1, 0);
        $updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");
        if ($a) {
            $snatch_updateset[] = "ip = ".ann_sqlesc($realip).", port = ".ann_sqlesc($port).", connectable = ".ann_sqlesc($connectable).", uploaded = uploaded + $upthis, ".($INSTALLER09['ratio_free'] ? "downloaded = downloaded + 0" : "downloaded = downloaded + $downthis").", to_go = ".ann_sqlesc($left).", upspeed = ".($upthis > 0 ? $upthis / $self["announcetime"] : 0).", downspeed = ".($downthis > 0 ? $downthis / $self["announcetime"] : 0).", ".($self["seeder"] == "yes" ? "seedtime = seedtime + {$self['announcetime']}" : "leechtime = leechtime + {$self['announcetime']}").", last_action = ".TIME_NOW.", seeder = ".ann_sqlesc($seeder).", agent = ".ann_sqlesc($agent).", $hit_and_run";
        }
    }
} elseif (isset($self)) {
    if ($event == "completed") {
        if ($a) $snatch_updateset[] = "complete_date = ".TIME_NOW.", finished = 'yes'";
        $updateset[] = "times_completed = times_completed + 1";
        $finished = ", finishedat = ".TIME_NOW."";
        adjust_torrent_peers($torrentid, 0, 0, 1);
    }
    $prev_action = ann_sqlesc($self['ts']);
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE LOW_PRIORITY peers SET connectable = ".ann_sqlesc($connectable).", uploaded = ".ann_sqlesc($uploaded).", ".($INSTALLER09['ratio_free'] ? "downloaded = 0" : "downloaded = ".ann_sqlesc($downloaded)."").", to_go = ".ann_sqlesc($left).", last_action = ".TIME_NOW.", prev_action = $prev_action, seeder = ".ann_sqlesc($seeder).", agent = ".ann_sqlesc($agent)." $finished WHERE $selfwhere") or ann_sqlerr(__FILE__, __LINE__);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        if ($seeder <> $self["seeder"]) {
            if ($seeder == "yes") adjust_torrent_peers($torrentid, 1, -1, 0);
            else adjust_torrent_peers($torrentid, -1, 1, 0);
            $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");
        }
        if ($a) $snatch_updateset[] = "ip = ".ann_sqlesc($realip).", port = ".ann_sqlesc($port).", connectable = ".ann_sqlesc($connectable).", uploaded = uploaded + $upthis, ".($INSTALLER09['ratio_free'] ? "downloaded = downloaded + 0" : "downloaded = downloaded + $downthis").", to_go = ".ann_sqlesc($left).", upspeed = ".($upthis > 0 ? $upthis / $self["announcetime"] : 0).", downspeed = ".($downthis > 0 ? $downthis / $self["announcetime"] : 0).", ".($self["seeder"] == "yes" ? "seedtime = seedtime + {$self['announcetime']}" : "leechtime = leechtime + {$self['announcetime']}").", last_action = ".TIME_NOW.", seeder = ".ann_sqlesc($seeder).", agent = ".ann_sqlesc($agent).", timesann = timesann + 1";
    }
} else {
    if ($user["parked"] == "yes") err("Your account is parked! (Read the FAQ)");
    elseif ($user["downloadpos"] == 0 OR $user["downloadpos"] > 1 AND $user['hnrwarn'] == 'no') err("Your downloading priviledges have been disabled! (Read the rules)");
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT LOW_PRIORITY INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey) VALUES (".ann_sqlesc($torrentid).", ".ann_sqlesc($userid).", ".ann_sqlesc($peer_id).", ".ann_sqlesc($realip).", ".ann_sqlesc($port).", ".ann_sqlesc($connectable).", ".ann_sqlesc($uploaded).", ".($INSTALLER09['ratio_free'] ? "0" : "".ann_sqlesc($downloaded)."").", ".ann_sqlesc($left).", ".TIME_NOW.", ".TIME_NOW.", ".ann_sqlesc($seeder).", ".ann_sqlesc($agent).", ".($INSTALLER09['ratio_free'] ? "0" : "".ann_sqlesc($downloaded)."").", ".ann_sqlesc($uploaded).", ".ann_sqlesc($passkey).")") or ann_sqlerr(__FILE__, __LINE__);
    if (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");
        if ($seeder == "yes") adjust_torrent_peers($torrentid, 1, 0, 0);
        else adjust_torrent_peers($torrentid, 0, 1, 0);
        if ($a) {
            $snatch_updateset[] = "ip = ".ann_sqlesc($realip).", port = ".ann_sqlesc($port).", connectable = ".ann_sqlesc($connectable).", to_go = ".ann_sqlesc($left).", last_action = ".TIME_NOW.", seeder = ".ann_sqlesc($seeder).", agent = ".ann_sqlesc($agent).", timesann = timesann + 1, hit_and_run = '0', mark_of_cain = 'no'";
        }
    }
}
if ($seeder == 'yes') {
    if ($torrent['banned'] != 'yes') $updateset[] = 'visible = \'yes\'';
    $updateset[] = 'last_action = '.TIME_NOW;
    $mc1->begin_transaction('torrent_details_'.$torrentid);
    $mc1->update_row(false, array(
        'visible' => 'yes'
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['torrent_details']);
    $mc1->begin_transaction('last_action_'.$torrentid);
    $mc1->update_row(false, array(
        'lastseed' => TIME_NOW
    ));
    $mc1->commit_transaction(1800);
}
if (count($updateset)) mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE LOW_PRIORITY torrents SET ".join(",", $updateset)." WHERE id = ".ann_sqlesc($torrentid)) or ann_sqlerr(__FILE__, __LINE__);
if (count($snatch_updateset)) mysqli_query($GLOBALS["___mysqli_ston"], 'UPDATE LOW_PRIORITY snatched SET '.join(',', $snatch_updateset).' WHERE torrentid = '.ann_sqlesc($torrentid).' AND userid = '.ann_sqlesc($userid)) or ann_sqlerr(__FILE__, __LINE__);
benc_resp_raw($resp);
?>
