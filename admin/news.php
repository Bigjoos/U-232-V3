<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
if (!defined('IN_INSTALLER09_ADMIN')) {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
    echo $HTMLOUT;
    exit();
}
require_once (INCL_DIR.'user_functions.php');
require_once (INCL_DIR.'html_functions.php');
require_once (INCL_DIR.'bbcode_functions.php');
require_once (CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
$HTMLOUT = '';
$stdhead = array(
    /** include css **/
    'css' => array(
        'forums',
        'style',
        'style2'
    )
);
$stdfoot = array(
    /** include js **/
    'js' => array(
        'shout',
        'check_selected'
    )
);
$lang = array_merge($lang, load_language('ad_news'));
$possible_modes = array(
    'add',
    'delete',
    'edit',
    'news'
);
$mode = (isset($_GET['mode']) ? htmlsafechars($_GET['mode']) : '');
if (!in_array($mode, $possible_modes)) stderr('Error', 'A ruffian that will swear, drink, dance, revel the night, rob, murder and commit the oldest of ins the newest kind of ways.');
//==Delete news
if ($mode == 'delete') {
    $newsid = (int)$_GET['newsid'];
    if (!is_valid_id($newsid)) stderr("Error", "Invalid ID.");
    $hash = md5('the@@saltto66??'.$newsid.'add'.'@##mu55y==');
    $sure = '';
    $sure = (isset($_GET['sure']) ? intval($_GET['sure']) : '');
    if (!$sure) stderr("Confirm Delete", "Do you really want to delete this news entry? Click\n"."<a href='staffpanel.php?tool=news&amp;mode=delete&amp;sure=1&amp;h=$hash&amp;newsid=$newsid'>here</a> if you are sure.", false);
    if ($_GET['h'] != $hash) stderr('Error', 'what are you doing?');
    function deletenewsid($newsid)
    {
        global $CURUSER, $mc1;
        sql_query("DELETE FROM news WHERE id = ".sqlesc($newsid)." AND userid = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('latest_news_');
    }
    $HTMLOUT.= deletenewsid($newsid);
    header("Refresh: 3; url=staffpanel.php?tool=news&mode=news");
    stderr("Success", "<h2>News entry deleted - Please wait while you are redirected!</h2>");
    echo stdhead('News', true, $stdhead).$HTMLOUT.stdfoot();
    die;
}
//==Add news
if ($mode == 'add') {
    $body = isset($_POST['body']) ? htmlsafechars($_POST['body']) : '';
    $sticky = isset($_POST['sticky']) ? $_POST['sticky'] : 'yes';
    if (!$body) stderr("Error", "The news item cannot be empty!");
    $title = htmlsafechars($_POST['title']);
    if (!$title) stderr("Error", "The news title cannot be empty!");
    $added = isset($_POST["added"]) ? $_POST["added"] : '';
    if (!$added) $added = TIME_NOW;
    sql_query("INSERT INTO news (userid, added, body, title, sticky) VALUES (".sqlesc($CURUSER['id']).",".sqlesc($added).", ".sqlesc($body).", ".sqlesc($title).", ".sqlesc($sticky).")") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('latest_news_');
    header("Refresh: 3; url=staffpanel.php?tool=news&mode=news");
    mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 1 ? stderr("Success", "News entry was added successfully.") : stderr("oopss", "Something's wrong !! .");
}
//==Edit/change news
if ($mode == 'edit') {
    $newsid = (int)$_GET["newsid"];
    if (!is_valid_id($newsid)) stderr("Error", "Invalid news item ID.");
    $res = sql_query("SELECT id, body, title, userid, added, sticky FROM news WHERE id=".sqlesc($newsid)) or sqlerr(__FILE__, __LINE__);
    if (mysqli_num_rows($res) != 1) stderr("Error", "No news item with that ID .");
    $arr = mysqli_fetch_assoc($res);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $body = isset($_POST['body']) ? htmlsafechars($_POST['body']) : '';
        $sticky = isset($_POST['sticky']) ? $_POST['sticky'] : 'yes';
        if ($body == "") stderr("Error", "Body cannot be empty!");
        $title = htmlsafechars($_POST['title']);
        if ($title == "") stderr("Error", "Title cannot be empty!");
        sql_query("UPDATE news SET body=".sqlesc($body).", sticky=".sqlesc($sticky).", title=".sqlesc($title)." WHERE id=".sqlesc($newsid)) or sqlerr(__FILE__, __LINE__);
        $mc1->delete_value('latest_news_');
        header("Refresh: 3; url=staffpanel.php?tool=news&mode=news");
        stderr("Success", "News item was edited successfully - Please wait while you are redirected!");
    } else {
        $HTMLOUT.= "<h1>Edit News Item</h1>
        <form method='post' name='compose' action='staffpanel.php?tool=news&amp;mode=edit&amp;newsid=$newsid'>
        <table border='1' cellspacing='0' cellpadding='5'>
        <tr><td><input type='text' name='title' value='".htmlsafechars($arr['title'])."' /></td></tr>
        <tr><td align='left' style='padding: 0px'>
         ".BBcode(htmlsafechars($arr["body"]) , FALSE)."
        </td></tr>
        <tr><td colspan='2' class='rowhead'>Sticky<input type='radio' ".($arr["sticky"] == "yes" ? " checked='checked'" : "")." name='sticky' value='yes' />Yes<input name='sticky' type='radio' value='no' ".($arr["sticky"] == "no" ? " checked='checked'" : "")." />No</td></tr>
        <tr><td colspan='2' align='center'><input type='submit' value='Okay' class='btn' /></td></tr>
        </table>
        </form>\n";
        echo stdhead('News Page', true, $stdhead).$HTMLOUT.stdfoot($stdfoot);
        die;
    }
}
//==Final Actions
if ($mode == 'news') {
    $res = sql_query("SELECT n.id AS newsid, n.body, n.title, n.userid, n.added, u.id, u.username, u.class, u.warned, u.chatpost, u.pirate, u.king, u.leechwarn, u.enabled, u.donor FROM news AS n LEFT JOIN users AS u ON u.id=n.userid ORDER BY sticky, added DESC") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT.= begin_main_frame();
    $HTMLOUT.= begin_frame();
    $HTMLOUT.= "<form method='post' name='compose' action='staffpanel.php?tool=news&amp;mode=add'>
    <h1>Submit News Item</h1><table border='1' cellspacing='0' cellpadding='5'>
    <tr><td><input type='text' name='title' value='' /></td></tr>\n";
    $HTMLOUT.= "<tr>
    <td align='left' style='padding: 0px'>".BBcode(FALSE)."</td></tr>";
    $HTMLOUT.= "<tr><td colspan='2' class='rowhead'>Sticky<input type='radio' checked='checked' name='sticky' value='yes' />Y<input name='sticky' type='radio' value='no' />N</td></tr>\n
    <tr><td colspan='2' class='rowhead'><input type='submit' value='Okay' class='btn' /></td></tr>\n
    </table></form><br /><br />\n";
    while ($arr = mysqli_fetch_assoc($res)) {
        $newsid = (int)$arr["newsid"];
        $body = $arr["body"];
        $title = $arr["title"];
        $added = get_date($arr["added"], 'LONG', 0, 1);
        $by = "<b>".format_username($arr)."</b>";
        $hash = md5('the@@saltto66??'.$newsid.'add'.'@##mu55y==');
        $HTMLOUT.= "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
        $added.&nbsp;News&nbsp;entry&nbsp;created&nbsp;by&nbsp$by
        - [<a href='staffpanel.php?tool=news&amp;mode=edit&amp;newsid=$newsid'><b>Edit</b></a>]
        - [<a href='staffpanel.php?tool=news&amp;mode=delete&amp;newsid=$newsid&amp;sure=1&amp;h=$hash'><b>Delete</b></a>]
        </td></tr></table>\n";
        $HTMLOUT.= begin_table(true);
        $HTMLOUT.= "<tr valign='top'><td class='comment'><b>".htmlsafechars($title)."</b><br />".format_comment($body)."</td></tr>\n";
        $HTMLOUT.= end_table();
        $HTMLOUT.= "<br />";
    }
    $HTMLOUT.= end_frame();
    $HTMLOUT.= end_main_frame();
}
echo stdhead('News Page', true, $stdhead).$HTMLOUT.stdfoot($stdfoot);
die;
?>
