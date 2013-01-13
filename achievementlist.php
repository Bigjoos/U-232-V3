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
require_once (CLASS_DIR . 'page_verify.php');
dbconn();
$newpage = new page_verify();
$newpage->check('takecounts');
$lang = array_merge(load_language('global'));
//$doUpdate = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURUSER['class'] >= UC_MAX) {
    $clienticon = htmlsafechars(trim($_POST["clienticon"]));
    $achievname = htmlsafechars(trim($_POST["achievname"]));
    $notes = htmlsafechars($_POST["notes"]);
    $clienticon = htmlsafechars($clienticon);
    $achievname = htmlsafechars($achievname);
    sql_query("INSERT INTO achievementist (achievname, notes, clienticon) VALUES(" . sqlesc($achievname) . ", " . sqlesc($notes) . ", " . sqlesc($clienticon) . ")") or sqlerr(__FILE__, __LINE__);
    $message = "A New achievment has been added. Achievement: [{$achievname}]";
    autoshout($message);
    //$doUpdate = true;
    
}
// == Query update by Putyn
$res = sql_query("SELECT a1.*, (SELECT COUNT(a2.id) FROM achievements AS a2 WHERE a2.achievement = a1.achievname) as count FROM achievementist AS a1 ORDER BY a1.id ") or sqlerr(__FILE__, __LINE__);
$HTMLOUT = '';
$HTMLOUT.= "<h1>Achievements List</h1>\n";
if (mysqli_num_rows($res) == 0) {
    $HTMLOUT.= "<p align='center'><b>There are currently no achievements added to the list!<br />staff has been slacking!</b></p>\n";
} else {
    $HTMLOUT.= "<table border='1' cellspacing='0' cellpadding='5'>
		<tr>
		<td class='colhead' align='left'>Achiev.</td>
		<td class='colhead' align='center'>Description</td>
		<td class='colhead' align='center'>Earned</td>
		</tr>\n";
    while ($arr = mysqli_fetch_assoc($res)) {
        $notes = htmlsafechars($arr["notes"]);
        $clienticon = '';
        if ($arr["clienticon"] != "") {
            $clienticon = "<img src='" . $INSTALLER09['pic_base_url'] . "achievements/" . htmlsafechars($arr["clienticon"]) . "' title='" . htmlsafechars($arr['achievname']) . "' alt='" . htmlsafechars($arr['achievname']) . "' />";
        }
        $HTMLOUT.= "<tr>
			<td class='one' align='center'>$clienticon</td>
			<td class='two' align='center'>$notes</td>
			<td class='one' align='center'>" . htmlsafechars($arr['count']) . "<br />times</td>
			</tr>\n";
    }
    $HTMLOUT.= "</table>\n";
}
if ($CURUSER['class'] == UC_MAX) {
    $HTMLOUT.= "<h2>Add an achievement to list.</h2>
		<form method='post' action='achievementlist.php'>
		<table border='1' cellspacing='0' cellpadding='5'>
		<tr>
		<td class='colhead'>AchievName</td><td class='one'><input type='text' name='achievname' size='40' /></td>
		</tr>
      <tr>
		<td class='colhead'>AchievIcon</td><td class='two'><textarea cols='60' rows='3' name='clienticon'></textarea></td>
		</tr>
		<tr>
		<td class='colhead'>Description</td><td class='one'><textarea cols='60' rows='6' name='notes'></textarea></td>
		</tr>
		<tr>
		<td colspan='2' align='center' class='two'><input type='submit' name='okay' value='Add Me!' class='btn' /></td>
		</tr>
		</table>
		</form>";
}
echo stdhead("Achievements List") . $HTMLOUT . stdfoot();
?>
