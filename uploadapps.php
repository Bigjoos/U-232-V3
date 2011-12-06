<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once INCL_DIR.'pager_functions.php';
dbconn(false);
loggedinorreturn();

$lang = array_merge( $lang, load_language('uploadapps') );

if ($CURUSER["class"] < UC_STAFF)
stderr($lang['uploadapps_user_error'], $lang['uploadapps_notmod']);

$possible_actions = array('show', 'viewapp', 'acceptapp', 'rejectapp','takedeleteapp','');      
$action = (isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '');

        if (!in_array($action, $possible_actions)) 
            stderr('Error', 'A ruffian that will swear, drink, dance, revel the night, rob, murder and commit the oldest of ins the newest kind of ways.');

$HTMLOUT = $where1 = '';

//== View applications
if (!$action || $action == "show") {
    if ($action == "show")
        $hide = "[<a href='{$INSTALLER09['baseurl']}/uploadapps.php'>{$lang['uploadapps_hide']}</a>]";
    else {
        $hide = "[<a href='{$INSTALLER09['baseurl']}/uploadapps.php?action=show'>{$lang['uploadapps_show']}</a>]";        
        $where = "WHERE status = 'pending'";
        $where1 = "WHERE uploadapp.status = 'pending'";
    }
  
    $where ="";
    $res = sql_query("SELECT count(id) FROM uploadapp $where") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_array($res);
    $url = "{$_SERVER['PHP_SELF']}?";
    $count = $row[0];
    $perpage = 15;
    $pager = pager($perpage, $count, $url);
    
    
    $HTMLOUT .="<h1 align='center'>{$lang['uploadapps_applications']}</h1>";
    if ($count == 0) {
        
        $HTMLOUT .="<table class='main' width='850' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
        <div align='right'><font class='small'>{$hide}</font></div></td></tr></table>
        <table width='100%' border='1' cellspacing='0' cellpadding='5'><tr><td>
        <div align='center'>{$lang['uploadapps_noapps']}</div>
        
        </td></tr></table>";
    } else {
        $HTMLOUT .="<form method='post' action='?action=takeappdelete'>";
        if ($count > $perpage)
        $HTMLOUT .= $pager['pagertop'];
        $HTMLOUT .="<table class='main' width='850' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
        <div align='right'><font class='small'>{$hide}</font></div>
        <table width='100%' border='1' cellspacing='0' cellpadding='5' align='center'>
        <tr>
        <td class='colhead' align='left'>{$lang['uploadapps_applied']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_application']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_username']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_joined']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_class']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_upped']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_ratio']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_status']}</td>
        <td class='colhead' align='left'>{$lang['uploadapps_delete']}</td>
        </tr>\n";
        
        
        $res = sql_query("SELECT uploadapp.*, users.id AS uid, users.username, users.class, users.added, users.uploaded, users.downloaded FROM uploadapp INNER JOIN users on uploadapp.userid = users.id $where1 ".$pager['limit']."") or sqlerr(__FILE__, __LINE__);
        while ($arr = mysqli_fetch_assoc($res)) {
            if ($arr["status"] == "accepted")
                $status = "<font color='green'>{$lang['uploadapps_accepted']}</font>";
            elseif ($arr["status"] == "rejected")
                $status = "<font color='red'>{$lang['uploadapps_rejected']}</font>";
            else
                $status = "<font color='blue'>{$lang['uploadapps_pending']}</font>";
            
            $membertime = get_date($arr['added'], '', 0, 1);
            $elapsed = get_date($arr['applied'], '', 0, 1);
            
            if ($arr["downloaded"] == 0 && $arr["uploaded"] == 0)
            $ratio = '---';
            else if ($arr["downloaded"] == 0 && $arr["uploaded"] != 0)
            $ratio = 'Inf.';
            else
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
            
            $HTMLOUT .="<tr>
            <td>{$elapsed}</td>
            <td><a href='?action=viewapp&amp;id=".(int)$arr['id']."'>{$lang['uploadapps_viewapp']}</a></td>
            <td><a href='{$INSTALLER09['baseurl']}/userdetails.php?id=".(int)$arr['uid']."'>".htmlspecialchars($arr['username'])."</a></td>
            <td>{$membertime}</td>
            <td>" . get_user_class_name($arr["class"]) . "</td>
            <td>" . mksize((int)$arr["uploaded"]) . "</td>
            <td>{$ratio}</td>
            <td>{$status}</td>
            <td><input type=\"checkbox\" name=\"deleteapp[]\" value=\"".(int)$arr['id']."\" /></td>
            </tr>\n";
        }
        $HTMLOUT .="</table>
        <div align='right'><input type='submit' value='Delete' /></div>
        </td></tr></table></form>\n";
        if ($count > $perpage)
        $HTMLOUT .= $pager['pagerbottom'];
        }
}

//== View application
if ($action == "viewapp") {
    $id = (int) $_GET["id"];
    $res = sql_query("SELECT uploadapp.*, users.id AS uid, users.username, users.class, users.added, users.uploaded, users.downloaded FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id=$id") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res);
    $membertime = get_date($arr['added'], '', 0, 1);
    $elapsed = get_date($arr['applied'], '', 0, 1);   
    if ($arr["downloaded"] == 0 && $arr["uploaded"] == 0)
    $ratio = '---';
    else if ($arr["downloaded"] == 0 && $arr["uploaded"] != 0)
    $ratio = 'Inf.';
    else
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
    $HTMLOUT .="<h1 align='center'>Uploader application</h1>
    <table width='750' border='1' cellspacing='0' cellpadding='5'>
    <tr>
    <td class='rowhead' width='25%'>{$lang['uploadapps_username1']} </td><td><a href='{$INSTALLER09['baseurl']}/userdetails.php?id=".(int)$arr['uid']."'>".htmlspecialchars($arr['username'])."</a></td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_joined']} </td><td>" . htmlspecialchars($membertime)."</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_upped1']} </td><td>" . htmlspecialchars( mksize($arr["uploaded"])) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_downed']} </td><td>" .htmlspecialchars( mksize($arr["downloaded"])) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_ratio1']} </td><td>"  . htmlspecialchars($ratio) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_connectable']} </td><td>" . htmlspecialchars($arr["connectable"])."</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_class1']} </td><td>" . get_user_class_name($arr["class"]) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_applied1']} </td><td>" . htmlspecialchars($elapsed)."</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_upspeed']} </td><td>" . htmlspecialchars($arr["speed"]) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_offer']} </td><td>" . htmlspecialchars($arr["offer"]) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_why']} </td><td>" . htmlspecialchars($arr["reason"]) . "</td>
    </tr>
    <tr>
    <td class='rowhead'>{$lang['uploadapps_uploader']} </td><td>" . htmlspecialchars($arr["sites"])."</td>
    </tr>";
    
    if ($arr["sitenames"] != "")
    $HTMLOUT .="<tr><td class='rowhead'>{$lang['uploadapps_sites']} </td><td>" . htmlspecialchars($arr["sitenames"]) . "</td></tr>
    <tr><td class='rowhead'>{$lang['uploadapps_axx']} </td><td>" . htmlspecialchars($arr["scene"])."</td></tr>
    <tr><td colspan='2'>{$lang['uploadapps_create']} <b>" . htmlspecialchars($arr["creating"])."</b><br />{$lang['uploadapps_seeding']} <b>" . htmlspecialchars($arr["seeding"])."</b></td></tr>";
    if ($arr["status"] == "pending")
    $HTMLOUT .="<tr><td align='center' colspan='2'><form method='post' action='?action=acceptapp'><input name='id' type='hidden' value='".(int)$arr["id"]."' /><b>Note: (optional)</b><br /><input type='text' name='note' size='40' /> <input type='submit' value='Accept' style='height: 20px' /></form><br /><form method='post' action='?action=rejectapp'><input name='id' type='hidden' value='".(int)$arr["id"]."' /><b>Reason: (optional)</b><br /><input type='text' name='reason' size='40' /> <input type='submit' value='Reject' style='height: 20px' /></form></td></tr></table>"; 
    else
    $HTMLOUT .="<tr><td colspan='2' align='center'>{$lang['uploadapps_application']} " . ($arr["status"] == "accepted" ? "accepted" : "rejected") . " by <b>" . htmlspecialchars($arr["moderator"])."</b><br />Comment: " . htmlspecialchars( $arr["comment"] ) . "</td></tr></table>
    <div align='center'><a href='{$INSTALLER09['baseurl']}/uploadapps.php'>Return to uploader applications page</a></div>";
    }
    
//== Accept application
if ($action == "acceptapp") {
    $id = 0 + $_POST["id"];
    if (!is_valid_id($id))
    stderr($lang['uploadapps_error'], $lang['uploadapps_noid']);
    $res = sql_query("SELECT uploadapp.id, users.username, users.modcomment, users.id AS uid FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id = $id") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res);
    $note = htmlentities($_POST["note"]);
    $subject = sqlesc("Uploader Promotion");
    $msg = sqlesc("Congratulations, your uploader application has been accepted! You have been promoted to Uploader and you are now able to upload torrents. Please make sure you have read the [url={$INSTALLER09['baseurl']}/rules.php]guidelines on uploading[/url] before you do.\n\nNote: $note");
    $msg1 = sqlesc("User [url={$INSTALLER09['baseurl']}/userdetails.php?id=".(int)$arr['uid']."][b]{$arr['username']}[/b][/url] has been promoted to Uploader by {$CURUSER['username']}.");
    $modcomment = get_date( TIME_NOW, 'DATE', 1 ) . " - Promoted to 'Uploader' by " . $CURUSER["username"] . "." . ($arr["modcomment"] != "" ? "\n" : "") . "{$arr['modcomment']}";
    $dt = TIME_NOW;
    sql_query("UPDATE uploadapp SET status = 'accepted', comment = " . sqlesc($note) . ", moderator = " . sqlesc($CURUSER["username"]) . " WHERE id=$id") or sqlerr(__FILE__, __LINE__);
    sql_query("UPDATE users SET class = ".UC_UPLOADER.", modcomment = " . sqlesc($modcomment) . " WHERE id={$arr['uid']} AND class < ".UC_MODERATOR."") or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('MyUser_'.$arr['uid']);
    $mc1->update_row(false, array('class' => 3));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user_stats_'.$arr['uid']);
    $mc1->update_row(false, array('modcomment' => $modcomment));
    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
    $mc1->begin_transaction('user'.$arr['uid']);
    $mc1->update_row(false, array('class' => 3));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    sql_query("INSERT INTO messages(sender, receiver, added, msg, subject, poster) VALUES(0, {$arr['uid']}, $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
    $subres = sql_query("SELECT id FROM users WHERE class = ".UC_STAFF."") or sqlerr(__FILE__, __LINE__);
    while ($subarr = mysqli_fetch_assoc($subres))
    sql_query("INSERT INTO messages(sender, receiver, added, msg, subject, poster) VALUES(0, {$subarr['id']}, $dt, $msg1, $subject, 0)") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('new_uploadapp_');
    stderr("Application accepted", "The application was succesfully accepted. The user has been promoted and has been sent a PM notification. Click <a href='{$INSTALLER09['baseurl']}/uploadapps.php'><b>Here</b></a> to return to the upload applications page.");
   }

//== Reject application
if ($action == "rejectapp") {
    $id = 0 + $_POST["id"];
    if (!is_valid_id($id))
    stderr("Error", "It appears that there is no uploader application with that ID.");
    $res = sql_query("SELECT uploadapp.id, users.id AS uid FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id=$id") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_assoc($res);
    $reason = htmlentities($_POST["reason"]);
    $subject = sqlesc("Uploader Promotion");
    $msg = sqlesc("Sorry, your uploader application has been rejected. It appears that you are not qualified enough to become uploader.\n\nReason: $reason");
    $dt = TIME_NOW;
    sql_query("UPDATE uploadapp SET status = 'rejected', comment = " . sqlesc($reason) . ", moderator = " . sqlesc($CURUSER["username"]) . " WHERE id=$id") or sqlerr(__FILE__, __LINE__);
    sql_query("INSERT INTO messages(sender, receiver, added, msg, subject, poster) VALUES(0, {$arr['uid']}, $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('new_uploadapp_');
    stderr("Application rejected", "The application was succesfully rejected. The user has been sent a PM notification. Click <a href='{$INSTALLER09['baseurl']}/uploadapps.php'><b>Here</b></a> to return to the upload applications page.");
}

//== Delete applications
    if ($action == "takeappdelete"){
    if (empty($_POST['deleteapp']))
    stderr('Silly Rabbit', 'Twix are for kids.. Check at least one application stupid...You cant delete nothing !');
    else {
    sql_query("DELETE FROM uploadapp WHERE id IN (".join(",",$_POST['deleteapp']).") ") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('new_uploadapp_');
    stderr("Deleted", "The upload applications were succesfully deleted. Click <a href='{$INSTALLER09['baseurl']}/uploadapps.php'><b>Here</b></a> to return to the upload applications page.");
    }
    }

echo stdhead('Uploader application page') . $HTMLOUT . stdfoot();
?>
