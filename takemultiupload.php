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
require_once (INCL_DIR.'benc.php');
require_once (INCL_DIR.'user_functions.php');
require_once (CLASS_DIR.'page_verify.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global') , load_language('takeupload'));
$newpage = new page_verify();
$newpage->check('tamud');
if ($CURUSER['class'] < UC_UPLOADER OR $CURUSER["uploadpos"] == 0 || $CURUSER["uploadpos"] > 1 || $CURUSER['suspended'] == 'yes') header("Location: {$INSTALLER09['baseurl']}/multiupload.php");
$nfofilename = array();
$matches = array();
$fname = array();
if (!isset($_FILES["file1"]) && !isset($_FILES["file2"]) && !isset($_FILES["file3"]) && !isset($_FILES["file4"]) && !isset($_FILES["file5"])) {
    stderr("Ooops", "You didn't specify a filename!");
} else {
    $f1 = $_FILES["file1"];
    $nfofile1 = $_FILES['nfo1'];
    $fname[] = unesc($f1["name"]);
    if ($nfofile1['size'] > 65535) stderr("Oops", "No NFO! for #1 torrent OR NFO #1 is too big! Max 65,535 bytes.");
    $f2 = $_FILES["file2"];
    $nfofile2 = $_FILES['nfo2'];
    $fname[] = unesc($f2["name"]);
    if ($nfofile2['size'] > 65535) stderr("Error", "No NFO! for #2 torrent OR NFO #2 is too big! Max 65,535 bytes.");
    $f3 = $_FILES["file3"];
    $nfofile3 = $_FILES['nfo3'];
    $fname[] = unesc($f3["name"]);
    if ($nfofile3['size'] > 65535) stderr("Oops", "No NFO! for #3 torrent OR NFO #3 is too big! Max 65,535 bytes.");
    $f4 = $_FILES["file4"];
    $nfofile4 = $_FILES['nfo4'];
    $fname[] = unesc($f4["name"]);
    if ($nfofile4['size'] > 65535) stderr("Oops", "No NFO! for #4 torrent OR NFO #4 is too big! Max 65,535 bytes.");
    $f5 = $_FILES["file5"];
    $nfofile5 = $_FILES['nfo5'];
    $fname[] = unesc($f5["name"]);
    if ($nfofile5['size'] > 65535) stderr("Oops", "No NFO! #5 torrent OR NFO #5 is too big! Max 65,535 bytes.");
    function dict_check($d, $s)
    {
        // echo $d["type"];
        // print_r($d);
        if ($d["type"] != "dictionary") stderr("Oops", "not a dictionary");
        $a = explode(":", $s);
        $dd = $d["value"];
        $ret = array();
        foreach ($a as $k) {
            unset($t);
            if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
                $k = $m[1];
                $t = $m[2];
            }
            if (!isset($dd[$k])) stderr("Oops", "dictionary is missing key(s)");
            if (isset($t)) {
                if ($dd[$k]["type"] != $t) stderr("Oops", "invalid entry in dictionary");
                $ret[] = $dd[$k]["value"];
            } else $ret[] = $dd[$k];
        }
        return $ret;
    }
    function dict_get($d, $k, $t)
    {
        if ($d["type"] != "dictionary") stderr("Oops", "not a dictionary");
        $dd = $d["value"];
        if (!isset($dd[$k])) return;
        $v = $dd[$k];
        if ($v["type"] != $t) stderr("Oops", "invalid dictionary entry type");
        return $v["value"];
    }
    //== Some crucial checks
    if (!validfilename($fname[0]) || !validfilename($fname[1]) || !validfilename($fname[2]) || !validfilename($fname[3]) || !validfilename($fname[4])) stderr("Oops", "One of the filenames was invalid!");
    if (!preg_match('/^(.+)\.torrent$/si', $fname[0], $matches[0])) stderr("Oops", "Invalid filename 1(not a .torrent).");
    if (!preg_match('/^(.+)\.torrent$/si', $fname[1], $matches[1])) stderr("Oops", "Invalid filename 2(not a .torrent).");
    if (!preg_match('/^(.+)\.torrent$/si', $fname[2], $matches[2])) stderr("Oops", "Invalid filename 3(not a .torrent).");
    if (!preg_match('/^(.+)\.torrent$/si', $fname[3], $matches[3])) stderr("Oops", "Invalid filename 4(not a .torrent).");
    if (!preg_match('/^(.+)\.torrent$/si', $fname[4], $matches[4])) stderr("Oops", "Invalid filename 5(not a .torrent).");
    //== very important check in terms of security
    if ($nfofile1['name'] != '') $nfofilename[] = $nfofile1['tmp_name'];
    if (@!is_uploaded_file($nfofilename[0])) stderr("Oops", "NFO1 upload failed");
    if ($nfofile2['name'] != '') $nfofilename[] = $nfofile2['tmp_name'];
    if (@!is_uploaded_file($nfofilename[1])) stderr("Oops", "NFO2 upload failed");
    if ($nfofile3['name'] != '') $nfofilename[] = $nfofile3['tmp_name'];
    if (@!is_uploaded_file($nfofilename[2])) stderr("Oops", "NFO3 upload failed");
    if ($nfofile4['name'] != '') $nfofilename[] = $nfofile4['tmp_name'];
    if (@!is_uploaded_file($nfofilename[3])) stderr("Oops", "NFO4 upload failed");
    if ($nfofile5['name'] != '') $nfofilename[] = $nfofile5['tmp_name'];
    if (@!is_uploaded_file($nfofilename[4])) stderr("oOPS", "NFO5 upload failed");
}
$descr = unesc($_POST["description"]);
if (!$descr) {
    stderr("Oops", "Please select either 'Take description from its respective NFO' OR enter a custom description to go with all torrents'");
}
$cat = array();
$catid = (0 + $_POST["alltype"]);
if (!is_valid_id($catid)) stderr("Oops", "You must select a category to put ALL the torrent in!");
//== Use the posted type category first -- if not set then just apply from settings
if (isset($_POST["type1"])) {
    $cat[0] = 0 + $_POST["type1"];
    if (!is_valid_id($cat[0])) $cat[0] = 0 + $_POST["alltype"];
}
if (isset($_POST["type2"])) {
    $cat[1] = 0 + $_POST["type2"];
    if (!is_valid_id($cat[1])) $cat[1] = 0 + $_POST["alltype"];
}
if (isset($_POST["type3"])) {
    $cat[2] = 0 + $_POST["type3"];
    if (!is_valid_id($cat[2])) $cat[2] = 0 + $_POST["alltype"];
}
if (isset($_POST["type4"])) {
    $cat[3] = 0 + $_POST["type4"];
    if (!is_valid_id($cat[3])) $cat[3] = 0 + $_POST["alltype"];
}
if (isset($_POST["type5"])) {
    $cat[4] = 0 + $_POST["type5"];
    if (!is_valid_id($cat[4])) $cat[4] = 0 + $_POST["alltype"];
}
if (isset($_POST['uplver1']) && $_POST['uplver1'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
} else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
}
if (isset($_POST['uplver2']) && $_POST['uplver2'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
} else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
}
if (isset($_POST['uplver3']) && $_POST['uplver3'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
} else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
}
if (isset($_POST['uplver4']) && $_POST['uplver4'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
} else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
}
if (isset($_POST['uplver5']) && $_POST['uplver5'] == 'yes') {
    $anonymous = "yes";
    $anon = "Anonymous";
} else {
    $anonymous = "no";
    $anon = $CURUSER["username"];
}
//== Arrays
$shortname = array();
$tmpname = array();
$dict = array();
$ann = array();
$info = array();
$dbname = array();
$plen = array();
$pieces = array();
$filelist = array();
$totallen = array();
$infohash = array();
$torrent = array();
$nfo = array();
$ids = array();
$tmpname[] = $f1["tmp_name"];
$tmpname[] = $f2["tmp_name"];
$tmpname[] = $f3["tmp_name"];
$tmpname[] = $f4["tmp_name"];
$tmpname[] = $f5["tmp_name"];
$i = 0;
foreach ($tmpname as $value) {
    $shortfname[$i] = $torrent[$i] = $matches[$i];
    if (!is_uploaded_file($value)) stderr("Opps", "Bad filename found on file no #$i");
    if (!filesize($value)) stderr("Oops", "Empty file! $value");
    $dict[] = bdec_file($value, $INSTALLER09['max_torrent_size']);
    if (!isset($dict[$i])) stderr("Oops", "What the hell did you upload? This is not a bencoded file 1!");
    list($ann[$i], $info[$i]) = dict_check($dict[$i], "announce(string):info");
    list($dname[$i], $plen[$i], $pieces[$i]) = dict_check($info[$i], "name(string):piece length(integer):pieces(string)");
    if (!in_array($ann[$i], $INSTALLER09['announce_urls'], 1)) stderr("Oops", "invalid announce url! in file no #$i must be {$INSTALLER09['announce_urls'][0]} - Make sure its exactly like that even the port number should be in there like '80'");
    if (strlen($pieces[$i]) % 20 != 0) stderr("Oops", "invalid pieces in file $i");
    $totallen = dict_get($info[$i], "length", "integer");
    if (isset($totallen)) {
        $filelist[] = array(
            $dname[$i],
            $totallen
        );
        $type = "single";
    } else {
        $flist = dict_get($info[$i], "files", "list");
        if (!isset($flist)) {
            stderr("Oops", "missing both length and files in #$i torrent");
        }
        if (!count($flist)) {
            stderr("Oops", "Missing files in torrent #$i");
        }
        $totallen = 0;
        foreach ($flist as $fn) {
            list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
            $totallen+= $ll;
            $ffa = array();
            foreach ($ff as $ffe) {
                if ($ffe["type"] != "string") stderr("Oops", "filename error on torrent #$i");
                $ffa[] = $ffe["value"];
            }
            if (!count($ffa)) stderr("Oops", "filename error");
            $ffe = implode("/", $ffa);
            $filelist[] = array(
                $ffe,
                $ll
            );
        }
        $type = "multi";
    }
    /* Private Tracker mod code */
    $info[$i]['value']['source']['type'] = "string";
    $info[$i]['value']['source']['value'] = $INSTALLER09['site_name'];
    $info[$i]['value']['source']['strlen'] = strlen($info[$i]['value']['source']['value']);
    $info[$i]['value']['private']['type'] = "integer";
    $info[$i]['value']['private']['value'] = 1;
    $dict[$i]['value']['info'] = $info[$i];
    $dict[$i] = benc($dict[$i]);
    $dict[$i] = bdec($dict[$i]);
    list($ann[$i], $info[$i]) = dict_check($dict[$i], "announce(string):info");
    $tmaker = (isset($dict['value']['created by']) && !empty($dict['value']['created by']['value'])) ? sqlesc($dict['value']['created by']['value']) : sqlesc("Unknown");
    unset($dict['value']['created by']);
    $infohash[$i] = sha1($info[$i]["string"]);
    /* ...... end of Private Tracker mod */
    $torrent[$i] = str_replace("_", " ", $torrent[$i]);
    $torrent[$i] = str_replace("'", " ", $torrent[$i]);
    $torrent[$i] = str_replace("\"", " ", $torrent[$i]);
    $torrent[$i] = str_replace(",", " ", $torrent[$i]);
    $nfo[$i] = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename[$i])));
    $first = $shortfname[$i][1];
    $second = $dname[$i];
    $third = $torrent[$i][1];
    $vip = (isset($_POST["vip1"]) ? "1" : "0");
    $vip = (isset($_POST["vip2"]) ? "1" : "0");
    $vip = (isset($_POST["vip3"]) ? "1" : "0");
    $vip = (isset($_POST["vip4"]) ? "1" : "0");
    $vip = (isset($_POST["vip5"]) ? "1" : "0");
    $ret = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO torrents (search_text, filename, owner, username, visible, anonymous, vip, info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo) VALUES (".implode(",", array_map("sqlesc", array(
        searchfield("$first $second $third") ,
        $fname[$i],
        $CURUSER["id"],
        $CURUSER["username"],
        "no",
        $anonymous,
        $vip,
        $infohash[$i],
        $torrent[$i][1],
        $totallen,
        count($filelist[$i]) ,
        $type,
        $descr,
        $descr,
        $cat[$i],
        $dname[$i]
    ))).", '".TIME_NOW."', '".TIME_NOW."', $nfo[$i])");
    if (!$ret) {
        if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 1062) stderr("Oops", "#$i torrent was already uploaded!");
        stderr("Oops", "mysql puked: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    }
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    $ids[] = $id;
    $mc1->delete_value('MyPeers_'.$CURUSER['id']);
    $mc1->delete_value('lastest_tor_');
    sql_query("DELETE FROM files WHERE torrent = $id");
    foreach ($filelist as $file) {
        sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
    }
    $fp = fopen("{$INSTALLER09['torrent_dir']}/$id.torrent", "w");
    if ($fp) {
        @fwrite($fp, benc($dict[$i]) , strlen(benc($dict[$i])));
        fclose($fp);
    }
    $i++;
}
//unset($filelist);
//unset($flist);
// ===add karma
sql_query("UPDATE users SET seedbonus = seedbonus+75.0 WHERE id =".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);
// ===end
////////new torrent upload detail sent to shoutbox//////////
if ($CURUSER["anonymous"] == 'yes') $message = "[url={$INSTALLER09['baseurl']}/multidetails.php?id1=$ids[0]&id2=$ids[1]&id3=$ids[2]&id4=$ids[3]&id5=$ids[4]]Multiple Torrents were just uploaded! Click here to see them[/url] - Anonymous User";
else $message = "[url={$INSTALLER09['baseurl']}/multidetails.php?id1=$ids[0]&id2=$ids[1]&id3=$ids[2]&id4=$ids[3]&id5=$ids[4]]Multiple Torrents were just uploaded! Click here to see them[/url]  Uploaded by ".htmlspecialchars($CURUSER["username"])."";
// ///////////////////////////END///////////////////////////////////
// //////new torrent upload detail sent to shoutbox//////////
autoshout($message);
$mc1->delete_value('shoutbox_');
// ///////////////////////////end///////////////////////////////////
header("Location: {$INSTALLER09['baseurl']}/multidetails.php?id1=$ids[0]&id2=$ids[1]&id3=$ids[2]&id4=$ids[3]&id5=$ids[4]&uploaded=1");
?>
