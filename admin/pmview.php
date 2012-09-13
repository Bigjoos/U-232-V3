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
require_once (INCL_DIR.'bbcode_functions.php');
require_once (INCL_DIR.'pager_functions.php');
require_once (INCL_DIR.'html_functions.php');
require_once (CLASS_DIR.'class_check.php');
class_check(UC_SYSOP, true, true);
$lang = array_merge($lang, load_language('ad_pmview'));
$stdfoot = array(
    /** include js **/
    'js' => array(
        'checkall'
    )
);
$HTMLOUT = '';
if (isset($_POST["delmp"])) {
    $do = "DELETE FROM messages WHERE id IN (".implode(", ", $_POST['delmp']).")";
    $res = sql_query($do);
    header("Refresh: 0; url=staffpanel.php?tool=pmview&action=pmview");
    stderr("Success", "The messages where successfully deleted!");
}
$HTMLOUT.= '<script type="text/javascript">
/*<![CDATA[*/
var checkflag = "false";
var marked_row = new Array;
function check(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
}else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
}
}
/*]]>*/
</script>';
$res2 = sql_query("SELECT COUNT(*) FROM messages");
$row = mysqli_fetch_array($res2);
$count = $row[0];
$perpage = 15;
$pager = pager($perpage, $count, "staffpanel.php?tool=pmview&amp;action=pmview&amp;");
if ($count > $perpage) $HTMLOUT.= $pager['pagertop'];
$res = sql_query("SELECT msg.receiver, msg.subject, msg.sender, msg.unread, msg.msg, msg.added, msg.id, u1.username AS u1_username, u2.username AS u2_username FROM messages AS msg LEFT JOIN users AS u1 ON u1.id=msg.receiver LEFT JOIN users AS u2 ON u2.id=msg.sender ORDER BY msg.id DESC {$pager['limit']}") or sqlerr(__FILE__, __LINE__);
$HTMLOUT.= begin_main_frame("Administrative message overview");
$HTMLOUT.= "
<form method='post' action='staffpanel.php?tool=pmview&amp;action=pmview'>
<table border='1' cellspacing='0' cellpadding='5'>\n
<tr>
<td class='colhead' align='left' width='1%'>Info</td>
<td class='colhead' align='left' width='1%'>Subject</td>
<td class='colhead' align='left'>Text</td>
<td class='colhead' align='left' width='1%'>Date</td>
<td class='colhead' width='1%'>Del</td></tr>\n";
while ($arr = mysqli_fetch_assoc($res)) {
    $receiver = "<a href='userdetails.php?id=".(int)$arr["receiver"]."'><b>".htmlsafechars($arr["u1_username"])."</b></a>";
    if ($arr["sender"] != 0) $sender = "<a href='userdetails.php?id=".(int)$arr["sender"]."'><b>".htmlsafechars($arr["u2_username"])."</b></a>";
    else $sender = "<font color='red'><b>System</b></font>";
    $msg = format_comment($arr["msg"]);
    $added = get_date($arr["added"], 'DATE', 0, 1);
    $HTMLOUT.= "<tr>
<td align='left'><b>Sender:</b>&nbsp;&nbsp;&nbsp;&nbsp;$sender<br /><b>Reciever:</b>&nbsp;$receiver<br /><b>Read</b>&nbsp;&nbsp;&nbsp;&nbsp;".($arr["unread"] != "yes" ? "<b><font color='lightgreen'>Yes</font></b>" : "<b><font color='red'>No</font></b>")."</td>
<td align='left'>".format_comment($arr['subject'])."</td>
<td align='left'>$msg</td><td align='left'>$added</td><td align='center'><input type='checkbox' name='delmp[]' title='Mark' value='".(int)$arr['id']."' /></td></tr>\n";
}
$HTMLOUT.= "<tr>
<td colspan='4' align='right' class='colhead'>Mark&nbsp;all&nbsp;Messages </td>
<td width='2%' class='colhead'>
<input type='checkbox' title='Mark All' value='Mark All' onclick=\"this.value=check(form.elements);\" />
</td></tr>
<tr><td colspan='5' align='center'><input type='submit' value='Delete selected messages!' /></td></tr>";
$HTMLOUT.= "</table></form>";
$HTMLOUT.= end_main_frame();
if ($count > $perpage) $HTMLOUT.= $pager['pagerbottom'];
echo stdhead("{$lang['pmview_header']}").$HTMLOUT.stdfoot($stdfoot);
?>
