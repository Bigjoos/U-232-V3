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
require_once (INCL_DIR.'pager_functions.php');
require_once (CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
$lang = array_merge($lang);
$stdfoot = array(
    /** include js **/
    'js' => array(
        'acp'
    )
);
$HTMLOUT = "";
if (isset($_POST['ids'])) {
    $ids = $_POST["ids"];
    foreach ($ids as $id) if (!is_valid_id($id)) stderr('Error...', 'Invalid ID!');
    $do = isset($_POST["do"]) ? htmlsafechars(trim($_POST["do"])) : '';
    if ($do == 'enabled') sql_query("UPDATE users SET enabled = 'yes' WHERE ID IN(".join(', ', $ids).") AND enabled = 'no'") or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('MyUser_'.$ids);
    $mc1->update_row(false, array(
        'enabled' => 'yes'
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$ids);
    $mc1->update_row(false, array(
        'enabled' => 'yes'
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    //else
    if ($do == 'confirm') sql_query("UPDATE users SET status = 'confirmed' WHERE ID IN(".join(', ', $ids).") AND status = 'pending'") or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('MyUser_'.$ids);
    $mc1->update_row(false, array(
        'status' => 'confirmed'
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$ids);
    $mc1->update_row(false, array(
        'status' => 'confirmed'
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    //else
    if ($do == 'delete') sql_query("DELETE FROM users WHERE ID IN(".join(', ', $ids).") AND class < 3") or sqlerr(__FILE__, __LINE__);
    else {
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }
}
$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
$pending = number_format(get_row_count("users", "WHERE status='pending'"));
$count = number_format(get_row_count("users", "WHERE enabled='no' OR status='pending' ORDER BY username DESC"));
$perpage = 25;
$pager = pager($perpage, $count, "staffpanel.php?tool=acpmanage&amp;action=acpmanage&amp;");
$res = sql_query("SELECT id, username, added, downloaded, uploaded, last_access, class, donor, warned, enabled, status FROM users WHERE enabled='no' OR status='pending' ORDER BY username DESC {$pager['limit']}");
$HTMLOUT.= begin_main_frame("Disabled Users: [$disabled] | Pending Users: [$pending]");
if (mysqli_num_rows($res) != 0) {
    if ($count > $perpage) $HTMLOUT.= $pager['pagertop'];
    $HTMLOUT.= "<form action='staffpanel.php?tool=acpmanage&amp;action=acpmanage' method='post'>";
    $HTMLOUT.= begin_table('', true);
    $HTMLOUT.= "<tr align='center'><td class='colhead'>
	  <input style='margin:0' type='checkbox' title='Mark All' value='Mark All' onclick=\"this.value=check(form);\" /></td>
	  <td class='colhead'>Username</td>
	  <td class='colhead' style='white-space: nowrap;'>Registered</td>
	  <td class='colhead' style='white-space: nowrap;'>Last access</td>
	  <td class='colhead'>Class</td>
	  <td class='colhead'>Downloaded</td>
	  <td class='colhead'>UpLoaded</td>
	  <td class='colhead'>Ratio</td>
	  <td class='colhead'>Status</td>
	  <td class='colhead' style='white-space: nowrap;'>Enabled</td>
	  </tr>";
    while ($arr = mysqli_fetch_assoc($res)) {
        $uploaded = mksize((int)$arr["uploaded"]);
        $downloaded = mksize((int)$arr["downloaded"]);
        $ratio = $arr['downloaded'] > 0 ? $arr['uploaded'] / $arr['downloaded'] : 0;
        $ratio = number_format($ratio, 2);
        $color = get_ratio_color($ratio);
        if ($color) $ratio = "<font color='$color'>$ratio</font>";
        $added = get_date($arr['added'], 'LONG', 0, 1);
        $last_access = get_date($arr['last_access'], 'LONG', 0, 1);
        $class = get_user_class_name($arr["class"]);
        $status = htmlsafechars($arr['status']);
        $enabled = htmlsafechars($arr['enabled']);
        $HTMLOUT.= "<tr align='center'><td><input type=\"checkbox\" name=\"ids[]\" value=\"".(int)$arr['id']."\" /></td><td><a href='/userdetails.php?id=".(int)$arr['id']."'><b>".htmlsafechars($arr['username'])."</b></a>".($arr["donor"] == "yes" ? "<img src='pic/star.gif' border='0' alt='Donor' />" : "").($arr["warned"] >= 1 ? "<img src='pic/warned.gif' border='0' alt='Warned' />" : "")."</td>
		<td style='white-space: nowrap;'>{$added}</td>
		<td style='white-space: nowrap;'>{$last_access}</td>
		<td>{$class}</td>
		<td>{$downloaded}</td>
		<td>{$uploaded}</td>
		<td>{$ratio}</td>
		<td>{$status}</td>
		<td>{$enabled}</td>
		</tr>\n";
    }
    $HTMLOUT.= "<tr><td colspan='10' align='center'><select name='do'><option value='enabled' disabled='disabled' selected='selected'>What to do?</option><option value='enabled'>Enable selected</option><option value='confirm'>Confirm selected</option><option value='delete'>Delete selected</option></select><input type='submit' value='Submit' /></td></tr>";
    $HTMLOUT.= end_table();
    $HTMLOUT.= "</form>";
    if ($count > $perpage) $HTMLOUT.= $pager['pagerbottom'];
} else $HTMLOUT.= stdmsg('Sorry', 'Nothing found!');
$HTMLOUT.= end_main_frame();
echo stdhead('Account manage').$HTMLOUT.stdfoot($stdfoot);
?>
