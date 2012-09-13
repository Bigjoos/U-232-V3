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
class_check(UC_ADMINISTRATOR);
$lang = array_merge($lang);
$HTMLOUT = '';
if (isset($_POST["delcloud"])) {
    $do = "DELETE FROM searchcloud WHERE id IN (".implode(", ", $_POST['delcloud']).")";
    $res = sql_query($do);
    $mc1->delete_value('searchcloud');
    header("Refresh: 3; url=staffpanel.php?tool=cloudview&action=cloudview");
    stderr("Success", "The obscene terms where successfully deleted!");
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
$search_count = sql_query("SELECT COUNT(id) FROM searchcloud");
$row = mysqli_fetch_array($search_count);
$count = $row[0];
$perpage = 15;
$pager = pager($perpage, $count, "staffpanel.php?tool=cloudview&amp;action=cloudview&amp;");
if ($count > $perpage) $HTMLOUT.= $pager['pagertop'];
$search_q = sql_query('SELECT id, searchedfor, ip, howmuch FROM searchcloud ORDER BY howmuch DESC '.$pager['limit']) or sqlerr(__FILE__, __LINE__);
$HTMLOUT.= begin_main_frame("Searchcloud overview");
$HTMLOUT.= "
<form method='post' action='staffpanel.php?tool=cloudview&amp;action=cloudview'>
<table border='1' cellspacing='0' cellpadding='5'>\n
<tr>
<td class='colhead' align='left' width='1%'>Searched phrase</td>
<td class='colhead' align='left' width='1%'>Hits</td>
<td class='colhead' align='left' width='1%'>Ip</td>
<td class='colhead' width='1%'>Del</td></tr>\n";
while ($arr = mysqli_fetch_assoc($search_q)) {
    $search_phrase = htmlsafechars($arr['searchedfor']);
    $hits = (int)$arr['howmuch'];
    $ip = htmlsafechars($arr['ip']);
    $HTMLOUT.= "<tr>
<td class='one' align='left'>$search_phrase</td>
<td class='two' align='left'>$hits</td>
<td class='two' align='left'>$ip</td>
<td class='one' align='center'><input type='checkbox' name='delcloud[]' title='Mark' value='".(int)$arr['id']."' /></td></tr>\n";
}
$HTMLOUT.= "<tr>
<td colspan='4' class='colhead' align='right'>Mark&nbsp;all&nbsp;searches<input type='checkbox' title='Mark All' value='Mark All' onclick=\"this.value=check(form.elements);\" /></td></tr>
<tr><td colspan='4' class='colhead' align='center'><input type='submit' value='Delete selected terms!' /></td></tr>";
$HTMLOUT.= "</table></form>";
$HTMLOUT.= end_main_frame();
if ($count > $perpage) $HTMLOUT.= $pager['pagerbottom'];
echo stdhead("Cloud View").$HTMLOUT.stdfoot();
?>
