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
require_once (CLASS_DIR.'class_check.php');
class_check(UC_MAX);
$lang = array_merge($lang);
$input = array_merge($_GET, $_POST);
$input['mode'] = isset($input['mode']) ? $input['mode'] : '';
$now_date = "";
$reputationid = 0;
$time_offset = 0;
$a = explode(",", gmdate("Y,n,j,G,i,s", TIME_NOW + $time_offset));
$now_date = array(
    'year' => $a[0],
    'mon' => $a[1],
    'mday' => $a[2],
    'hours' => $a[3],
    'minutes' => $a[4],
    'seconds' => $a[5]
);
switch ($input['mode']) {
case 'modify':
    show_level();
    break;

case 'add':
    show_form('new');
    break;

case 'doadd':
    do_update('new');
    break;

case 'edit':
    show_form('edit');
    break;

case 'doedit':
    do_update('edit');
    break;

case 'doupdate':
    do_update();
    break;

case 'dodelete':
    do_delete();
    break;

case 'list':
    view_list();
    break;

case 'dolist':
    do_list();
    break;

case 'editrep':
    show_form_rep('edit');
    break;

case 'doeditrep':
    do_edit_rep();
    break;

case 'dodelrep':
    do_delete_rep();
    break;

default:
    show_level();
    break;
}
function show_level()
{
    $title = "User Reputation Manager - Overview";
    $html = "<p>On this page you can modify the minimum amount required for each reputation level. Make sure you press Update Minimum Levels to save your changes. You cannot set the same minimum amount to more than one level.<br />From here you can also choose to edit or remove any single level. Click the Edit link to modify the Level description (see Editing a Reputation Level) or click Remove to delete a level. If you remove a level or modify the minimum reputation needed to be at a level, all users will be updated to reflect their new level if necessary.</p><br />";
    $query = sql_query('SELECT * FROM reputationlevel ORDER BY minimumreputation ASC');
    if (!mysqli_num_rows($query)) {
        do_update('new');
        return;
    }
    $css = "style='font-weight: bold;color: #ffffff;background-color: #1E1E1E; padding: 5px;'";
    $html.= "<h2>User Reputation Manager</h2>";
    $html.= "<p><span class='btn'><a href='staffpanel.php?tool=reputation_ad&amp;mode=list'>View comments</a></span></p><br />";
    $html.= "<form action='staffpanel.php?tool=reputation_ad' name='show_rep_form' method='post'>
				<input name='mode' value='doupdate' type='hidden' />";
    $html.= "<table cellpadding='3px'><tr>
		<td width='5%' $css>ID</td>
		<td width='60%'$css>Reputation Level</td>
		<td width='20%' $css>Minimum Reputation Level</td>
		<td width='15%' $css>Controls</td></tr>";
    while ($res = mysqli_fetch_assoc($query)) {
        $html.= "<tr>\n"."	<td>#".$res['reputationlevelid']."</td>\n"."	<td>User <b>".htmlsafechars($res['level'])."</b></td>\n"."	<td align='center'><input type='text' name='reputation[".$res['reputationlevelid']."]' value='".$res['minimumreputation']."' size='12' /></td>\n"."	<td align='center'><span class='btn'><a href='staffpanel.php?tool=reputation_ad&amp;mode=edit&amp;reputationlevelid=".$res['reputationlevelid']."'>Edit</a></span>&nbsp;<span class='btn'><a href='staffpanel.php?tool=reputation_ad&amp;mode=dodelete&amp;reputationlevelid=".$res['reputationlevelid']."'>Delete</a></span></td>\n"."</tr>\n";
    }
    $html.= "<tr><td colspan='3' align='center'>
					<input type='submit' value='Update' accesskey='s' class='btn' /> 
					<input type='reset' value='Reset' accesskey='r' class='btn' /></td>
					<td align='center'><span class='btn'><a href='staffpanel.php?tool=reputation_ad&amp;mode=add'>Add New</a></span>
					</td></tr>";
    $html.= "</table>";
    $html.= "</form>";
    html_out($html, $title);
}
function show_form($type = 'edit')
{
    global $input;
    $html = "This allows you to add a new reputation level or edit an existing reputation level.";
    if ($type == 'edit') {
        $query = sql_query('SELECT * FROM reputationlevel WHERE reputationlevelid='.intval($input['reputationlevelid'])) or sqlerr(__LINE__, __FILE__);
        if (!$res = mysqli_fetch_assoc($query)) {
            stderr("Error:", "Please specify an ID.");
        }
        $title = "Edit Reputation Level";
        $html.= "<br /><span style='font-weight:normal;'>".htmlsafechars($res['level'])." (ID:#{$res['reputationlevelid']})</span><br />";
        $button = "Update";
        $extra = "<input type='button' class='button' value='Back' accesskey='b' class='btn' onclick='javascript:history.back(1)' />";
        $mode = 'doedit';
    } else {
        $title = "Add New Reputation Level";
        $button = "Save";
        $mode = 'doadd';
        $extra = "<input type='button' value='Back' accesskey='b' class='btn' onclick='javascript:history.back(1)' />";
    }
    $css = "style='font-weight: bold;color: #ffffff;background-color: #0055A4;padding: 5px;'";
    $replevid = isset($res['reputationlevelid']) ? $res['reputationlevelid'] : '';
    $replevel = isset($res['level']) ? $res['level'] : '';
    $minrep = isset($res['minimumreputation']) ? $res['minimumreputation'] : '';
    $html.= "<form action='staffpanel.php?tool=reputation_ad' name='show_rep_form' method='post'>
				<input name='reputationlevelid' value='{$replevid}' type='hidden' />
				<input name='mode' value='{$mode}' type='hidden' />";
    $html.= "<h2>$title</h2><table width='500px' cellpadding='5px'><tr>
		<td width='67%' $css>&nbsp;</td>
		<td width='33%' $css>&nbsp;</td></tr>";
    $html.= "<tr><td>Level Description<div class='desctext'>This is what is displayed for the user when their reputation points are above the amount entered as the minimum.</div></td>";
    $html.= "<td><input type='text' name='level' value=\"{$replevel}\" size='35' maxlength='250' /></td></tr>";
    $html.= "<tr><td>Minimum amount of reputation points required for this level<div>This can be a positive or a negative amount. When the user's reputation points reaches this amount, the above description will be displayed.</div></td>";
    $html.= "<td><input type='text' name='minimumreputation' value=\"{$minrep}\" size='35' maxlength='10' /></td></tr>";
    $html.= "<tr><td colspan='2' align='center'><input type='submit' value='$button' accesskey='s' class='btn' /> <input type='reset' value='Reset' accesskey='r' class='btn' /> $extra</td></tr>";
    $html.= "</table>";
    $html.= "</form>";
    html_out($html, $title);
}
/////////////////////////////////////
//	Update rep function
/////////////////////////////////////
function do_update($type = "")
{
    global $input;
    if ($type != "") {
        $level = strip_tags($input['level']);
        $level = trim($level);
        if ((strlen($input['level']) < 2) || ($level == "")) {
            stderr('', 'The text you entered was too short.');
        }
        if (strlen($input['level']) > 250) {
            stderr('', 'The text entry is too long.');
        }
        $level = sqlesc($level);
        $minrep = sqlesc(intval($input['minimumreputation']));
        $redirect = 'Saved Reputation Level <i>'.htmlsafechars($input['level'], ENT_QUOTES).'</i> Successfully.';
    }
    // what we gonna do?
    if ($type == 'new') {
        @sql_query("INSERT INTO reputationlevel ( minimumreputation, level ) 
							VALUES  ($minrep, $level )");
    } elseif ($type == 'edit') {
        $levelid = intval($input['reputationlevelid']);
        if (!is_valid_id($levelid)) stderr('', 'Not a valid try');
        // check it's a valid rep id
        $query = sql_query("SELECT reputationlevelid FROM reputationlevel WHERE 
									reputationlevelid={$levelid}");
        if (!mysqli_num_rows($query)) {
            stderr('', 'Not a valid ID.');
        }
        @sql_query("UPDATE reputationlevel SET minimumreputation = $minrep, level = $level 
							WHERE reputationlevelid = $levelid");
    } else {
        $ids = $input['reputation'];
        if (is_array($ids) && count($ids)) {
            foreach ($ids as $k => $v) {
                @sql_query("UPDATE reputationlevel SET minimumreputation = ".intval($v)." WHERE reputationlevelid = ".intval($k));
            }
        } else {
            stderr('', 'No valid ID.');
        }
        $redirect = "Saved Reputation Level Successfully.";
    }
    rep_cache();
    redirect('staffpanel.php?tool=reputation_ad&amp;mode=done', $redirect);
}
//////////////////////////////////////
//	Reputaion delete
//////////////////////////////////////
function do_delete()
{
    global $input;
    if (!isset($input['reputationlevelid']) || !is_valid_id($input['reputationlevelid'])) stderr('', 'No valid ID.');
    $levelid = intval($input['reputationlevelid']);
    // check the id is valid within db
    $query = sql_query("SELECT reputationlevelid FROM reputationlevel WHERE reputationlevelid=$levelid");
    if (!mysqli_num_rows($query)) {
        stderr('', 'Rep ID doesn\'t exist');
    }
    // if we here, we delete it!
    @sql_query("DELETE FROM reputationlevel WHERE reputationlevelid=$levelid");
    rep_cache();
    redirect('staffpanel.php?tool=reputation_ad&amp;mode=done', 'Reputation deleted successfully', 5);
}
//////////////////////////////////////
//	Reputaion edit
//////////////////////////////////////
function show_form_rep()
{
    global $input;
    if (!isset($input['reputationid']) || !is_valid_id($input['reputationid'])) stderr('', 'Nothing here by that ID.');
    $title = 'User Reputation Manager';
    $query = sql_query("SELECT r.*, p.topic_id, t.topic_name, leftfor.username as leftfor_name, 
					leftby.username as leftby_name
					FROM reputation r
					left join posts p on p.id=r.postid
					left join topics t on p.topic_id=t.id
					left join users leftfor on leftfor.id=r.userid
					left join users leftby on leftby.id=r.whoadded
					WHERE reputationid = ".intval($input['reputationid']));
    if (!$res = mysqli_fetch_assoc($query)) {
        stderr('', 'Erm, it\'s not there!');
    }
    $html = "<form action='staffpanel.php?tool=reputation_ad' name='show_rep_form' method='post'>
				<input name='reputationid' value='{$res['reputationid']}' type='hidden' />
				<input name='oldreputation' value='{$res['reputation']}' type='hidden' />
				<input name='mode' value='doeditrep' type='hidden' />";
    $html.= "<h2>Edit Reputation</h2>";
    $html.= "<table cellpadding='5px'>";
    $html.= "<tr><td width='37%'>Topic</td><td width='63%'><a href='forums.php?action=viewtopic&amp;topicid={$res['topic_id']}&amp;page=p{$res['postid']}#{$res['postid']}' target='_blank'>".htmlsafechars($res['topic_name'])."</a></td></tr>";
    $html.= "<tr><td>Left By</td><td>{$res['leftby_name']}</td></tr>";
    $html.= "<tr><td>Left For</td><td width='63%'>{$res['leftfor_name']}</td></tr>";
    $html.= "<tr><td>Comment</td><td width='63%'><input type='text' name='reason' value='".htmlsafechars($res['reason'])."' size='35' maxlength='250' /></td></tr>";
    $html.= "<tr><td>Reputation</td><td><input type='text' name='reputation' value='{$res['reputation']}' size='35' maxlength='10' /></td></tr>";
    $html.= "<tr><td colspan='2' align='center'><input type='submit' value='Save' accesskey='s' class='btn' /> <input type='reset' tabindex='1' value='Reset' accesskey='r' class='btn' /></td></tr>";
    $html.= "</table></form>";
    html_out($html, $title);
}
/////////////////////////////////////
//	View reputation comments function
/////////////////////////////////////
function view_list()
{
    global $now_date, $time_offset, $input;
    $title = 'User Reputation Manager';
    $html = "<h2>View Reputation Comments</h2>";
    $html.= "<p>This page allows you to search for reputation comments left by / for specific users over the specified date range.</p>";
    $html.= "<form action='staffpanel.php?tool=reputation_ad' name='list_form' method='post'>
				<input name='mode' value='list' type='hidden' />
				<input name='dolist' value='1' type='hidden' />";
    $html.= "<table width='500px' cellpadding='5px'>";
    $html.= "<tr><td width='20%'>Left For</td><td width='80%'><input type='text' name='leftfor' value='' size='35' maxlength='250' tabindex='1' /></td></tr>";
    $html.= "<tr><td colspan='2'><div>To limit the comments left for a specific user, enter the username here. Leave this field empty to receive comments left for every user.</div></td></tr>";
    $html.= "<tr><td>Left By</td><td><input type='text' name='leftby' value='' size='35' maxlength='250' tabindex='2' /></td></tr>";
    $html.= "<tr><td colspan='2'><div>To limit the comments left by a specific user, enter the username here. Leave this field empty to receive comments left by every user.</div></td></tr>";
    $html.= "<tr><td>Start Date</td><td>
		<div>
				<span style='padding-right:5px; float:left;'>Month<br /><select name='start[month]' tabindex='3'>".get_month_dropdown(1)."</select></span>
				<span style='padding-right:5px; float:left;'>Day<br /><input type='text' name='start[day]' value='".($now_date['mday'] + 1)."' size='4' maxlength='2' tabindex='3' /></span>
				<span>Year<br /><input type='text' name='start[year]' value='".$now_date['year']."' size='4' maxlength='4' tabindex='3' /></span>
			</div></td></tr>";
    $html.= "<tr><td class='tdrow2' colspan='2'><div class='desctext'>Select a start date for this report. Select a month, day, and year. The selected statistic must be no older than this date for it to be included in the report.</div></td></tr>";
    $html.= "<tr><td>End Date</td><td>
			<div>
				<span style='padding-right:5px; float:left;'>Month<br /><select name='end[month]' class='textinput' tabindex='4'>".get_month_dropdown()."</select></span>
				<span style='padding-right:5px; float:left;'>Day<br /><input type='text' class='textinput' name='end[day]' value='".$now_date['mday']."' size='4' maxlength='2' tabindex='4' /></span>
				<span>Year<br /><input type='text' class='textinput' name='end[year]' value='".$now_date['year']."' size='4' maxlength='4' tabindex='4' /></span>
			</div></td></tr>";
    $html.= "<tr><td class='tdrow2' colspan='2'><div class='desctext'>Select an end date for this report. Select a month, day, and year. The selected statistic must not be newer than this date for it to be included in the report. You can use this setting in conjunction with the 'Start Date' setting to create a window of time for this report.</div></td></tr>";
    $html.= "<tr><td colspan='2' align='center'><input type='submit' value='Search' accesskey='s' class='btn' tabindex='5' /> <input type='reset' value='Reset' accesskey='r' class='btn' tabindex='6' /></td></tr>";
    $html.= "</table></form>";
    //echo $html; exit;
    // I hate work, but someone has to do it!
    if (isset($input['dolist'])) {
        $links = "";
        $input['orderby'] = isset($input['orderby']) ? $input['orderby'] : '';
        //$cond = ''; //experiment
        $who = isset($input['who']) ? (int)$input['who'] : 0;
        $user = isset($input['user']) ? $input['user'] : 0;
        $first = isset($input['page']) ? intval($input['page']) : 0;
        $cond = $who ? "r.whoadded=".sqlesc($who) : '';
        $start = isset($input['startstamp']) ? intval($input['startstamp']) : mktime(0, 0, 0, $input['start']['month'], $input['start']['day'], $input['start']['year']) + $time_offset;
        $end = isset($input['endstamp']) ? intval($input['endstamp']) : mktime(0, 0, 0, $input['end']['month'], $input['end']['day'] + 1, $input['end']['year']) + $time_offset;
        if (!$start) {
            $start = TIME_NOW - (3600 * 24 * 30);
        }
        if (!$end) {
            $end = TIME_NOW;
        }
        if ($start >= $end) {
            stderr('Time', 'Start date is after the end date.');
        }
        if (!empty($input['leftby'])) {
            $left_b = @sql_query("SELECT id FROM users WHERE username = ".sqlesc($input['leftby']));
            if (!mysqli_num_rows($left_b)) {
                stderr('DB ERROR', 'Could not find user '.htmlsafechars($input['leftby'], ENT_QUOTES));
            }
            $leftby = mysqli_fetch_assoc($left_b);
            $who = $leftby['id'];
            $cond = "r.whoadded=".$who;
        }
        if (!empty($input['leftfor'])) {
            $left_f = @sql_query("SELECT id FROM users WHERE username = ".sqlesc($input['leftfor']));
            if (!mysqli_num_rows($left_f)) {
                stderr('DB ERROR', 'Could not find user '.htmlsafechars($input['leftfor'], ENT_QUOTES));
            }
            $leftfor = mysqli_fetch_assoc($left_f);
            $user = $leftfor['id'];
            $cond.= ($cond ? " AND" : "")." r.userid=".$user;
        }
        if ($start) {
            $cond.= ($cond ? " AND" : "")." r.dateadd >= $start";
        }
        if ($end) {
            $cond.= ($cond ? " AND" : "")." r.dateadd <= $end";
        }
        switch ($input['orderby']) {
        case 'leftbyuser':
            $order = 'leftby.username';
            $orderby = 'leftbyuser';
            break;

        case 'leftforuser':
            $order = 'leftfor.username';
            $orderby = 'leftforuser';
            break;

        default:
            $order = 'r.dateadd';
            $orderby = 'dateadd';
        }
        $css = "style='font-weight: bold;color: #ffffff;background-color: #0055A4;padding: 5px;'";
        $html = "<h2>Reputation Comments</h2>";
        $table_header = "<table width='80%' cellpadding='5' border='1'><tr $css>";
        $table_header.= "<td width='5%'>ID</td>";
        $table_header.= "<td width='20%'><a href='staffpanel.php?tool=reputation_ad&amp;mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=leftbyuser&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Left By</a></td>";
        $table_header.= "<td width='20%'><a href='staffpanel.php?tool=reputation_ad&amp;mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=leftforuser&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Left For</a></td>";
        $table_header.= "<td width='17%'><a href='staffpanel.php?tool=reputation_ad&amp;mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=date&amp;startstamp=$start&amp;endstamp=$end&amp;page=$first'>Date</a></td>";
        $table_header.= "<td width='5%'>Point</td>";
        $table_header.= "<td width='23%'>Reason</td>";
        $table_header.= "<td width='10%'>Controls</td></tr>";
        $html.= $table_header;
        // do the count for pager etc
        $query = sql_query("SELECT COUNT(*) AS cnt FROM reputation r WHERE $cond");
        //echo_r($input); exit;
        $total = mysqli_fetch_assoc($query);
        if (!$total['cnt']) {
            $html.= "<tr><td colspan='7' align='center'>No Matches Found!</td></tr>";
        }
        // do the pager thang!
        $deflimit = 10;
        $links = "<span style=\"background: #F0F5FA; border: 1px solid #072A66;padding: 1px 3px 1px 3px;\">{$total['cnt']}&nbsp;Records</span>";
        if ($total['cnt'] > $deflimit) {
            require_once INCL_DIR.'pager_functions.php';
            $links = pager_rep(array(
                'count' => $total['cnt'],
                'perpage' => $deflimit,
                'start_value' => $first,
                'url' => "staffpanel.php?tool=reputation_ad&amp;mode=list&amp;dolist=1&amp;who=".intval($who)."&amp;user=".intval($user)."&amp;orderby=$orderby&amp;startstamp=$start&amp;endstamp=$end"
            ));
        }
        // mofo query!
        $query = sql_query("SELECT r.*, p.topic_id, leftfor.id as leftfor_id, 
									leftfor.username as leftfor_name, leftby.id as leftby_id, 
									leftby.username as leftby_name 
									FROM reputation r 
									left join posts p on p.id=r.postid 
									left join users leftfor on leftfor.id=r.userid 
									left join users leftby on leftby.id=r.whoadded 
									WHERE $cond ORDER BY $order LIMIT $first,$deflimit");
        if (!mysqli_num_rows($query)) stderr('DB ERROR', 'Nothing here');
        while ($r = mysqli_fetch_assoc($query)) {
            $r['dateadd'] = date("M j, Y, g:i a", $r['dateadd']);
            $html.= "<tr><td>#{$r['reputationid']}</td>";
            $html.= "<td><a href='userdetails.php?id={$r['leftby_id']}' target='_blank'>{$r['leftby_name']}</a></td>";
            $html.= "<td><a href='userdetails.php?id={$r['leftfor_id']}' target='_blank'>{$r['leftfor_name']}</a></td>";
            $html.= "<td>{$r['dateadd']}</td>";
            $html.= "<td align='right'>{$r['reputation']}</td>";
            $html.= "<td><a href='forums.php?action=viewtopic&amp;topicid={$r['topic_id']}&amp;page=p{$r['postid']}#{$r['postid']}' target='_blank'>".htmlsafechars($r['reason'])."</a></td>";
            $html.= "<td><a href='staffpanel.php?tool=reputation_ad&amp;mode=editrep&amp;reputationid={$r['reputationid']}'><span class='btn'>Edit</span></a>&nbsp;<a href='reputation_ad.php?mode=dodelrep&amp;reputationid={$r['reputationid']}'><span class='btn'>Delete</span></a></td></tr>";
        }
        $html.= "</table>";
        $html.= "<br /><div>$links</div>";
    }
    html_out($html, $title);
}
///////////////////////////////////////////////
//	Reputation do_delete_rep function
///////////////////////////////////////////////
function do_delete_rep()
{
    global $input;
    if (!is_valid_id($input['reputationid'])) stderr('ERROR', 'Can\'t find ID');
    // check it's a valid ID.
    $query = sql_query("SELECT reputationid, reputation, userid FROM reputation WHERE reputationid=".intval($input['reputationid']));
    if (false === ($r = mysqli_fetch_assoc($query))) {
        stderr('DELETE', 'No valid ID.');
    }
    $sql = sql_query('SELECT reputation '.'FROM users '.'WHERE id = '.sqlesc($input['reputationid'])) or sqlerr(__FILE__, __LINE__);
    $User = mysqli_fetch_assoc($sql);
    // do the delete
    sql_query("DELETE FROM reputation WHERE reputationid=".intval($r['reputationid']));
    sql_query("UPDATE users SET reputation = (reputation-{$r['reputation']} ) WHERE id=".intval($r['userid']));
    $update['rep'] = ($User['reputation'] - $r['reputation']);
    $mc1->begin_transaction('MyUser_'.$r['userid']);
    $mc1->update_row(false, array(
        'reputation' => $update['rep']
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$r['userid']);
    $mc1->update_row(false, array(
        'reputation' => $update['rep']
    ));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    redirect("staffpanel.php?tool=reputation_ad&amp;mode=list", "Deleted Reputation Successfully", 5);
}
///////////////////////////////////////////////
//	Reputation do_edit_rep function
///////////////////////////////////////////////
function do_edit_rep()
{
    global $input;
    if (isset($input['reason']) && !empty($input['reason'])) {
        $reason = str_replace("<br />", "", $input['reason']);
        $reason = trim($reason);
        if ((strlen(trim($reason)) < 2) || ($reason == "")) {
            stderr('TEXT', 'The text you entered was too short.');
        }
        if (strlen($input['reason']) > 250) {
            stderr('TEXT', 'The text entry is too long.');
        }
    }
    $oldrep = intval($input['oldreputation']);
    $newrep = intval($input['reputation']);
    // valid ID?
    $query = sql_query("SELECT reputationid, reason, userid FROM reputation WHERE reputationid=".intval($input['reputationid']));
    if (false === $r = mysqli_fetch_assoc($query)) {
        stderr('INPUT', 'No ID');
    }
    if ($oldrep != $newrep) {
        if ($r['reason'] != $reason) {
            @sql_query("UPDATE reputation SET reputation = ".intval($newrep).", reason = ".sqlesc($reason)." WHERE reputationid = ".intval($r['reputationid']));
        }
        $sql = sql_query('SELECT reputation '.'FROM users '.'WHERE id = '.sqlesc($input['reputationid'])) or sqlerr(__FILE__, __LINE__);
        $User = mysqli_fetch_assoc($sql);
        $diff = $oldrep - $newrep;
        @sql_query("UPDATE users SET reputation = (reputation-{$diff}) WHERE id=".intval($r['userid']));
        $update['rep'] = ($User['reputation'] - $diff);
        $mc1->begin_transaction('MyUser_'.$r['userid']);
        $mc1->update_row(false, array(
            'reputation' => $update['rep']
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
        $mc1->begin_transaction('user'.$r['userid']);
        $mc1->update_row(false, array(
            'reputation' => $update['rep']
        ));
        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
        $mc1->delete_value('MyUser_'.$r['userid']);
        $mc1->delete_value('user'.$r['userid']);
    }
    redirect("staffpanel.php?tool=reputation_ad&amp;mode=list", "Saved Reputation #ID{$r['reputationid']} Successfully.", 5);
}
///////////////////////////////////////////////
//	Reputation output function
//	$msg -> string
//	$html -> string
///////////////////////////////////////////////
function html_out($html = "", $title = "")
{
    if (empty($html)) {
        stderr("Error", "Nothing to output");
    }
    echo stdhead($title).$html.stdfoot();
    exit();
}
function redirect($url, $text, $time = 2)
{
    global $INSTALLER09;
    $page_title = "Admin Rep Redirection";
    $page_detail = "<em>Redirecting...</em>";
    $html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='refresh' content=\"{$time}; url={$INSTALLER09['baseurl']}/{$url}\" />
		<title>Block Settings</title>
    <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
    </head>
    <body>
						  <div>
							<div>Redirecting</div>
							<div style='padding:8px'>
							 <div style='font-size:12px'>$text
							 <br />
							 <br />
							 <center><a href='{$INSTALLER09['baseurl']}/{$url}'>Click here if not redirected...</a></center>
							 </div>
							</div>
						   </div></body></html>";
    echo $html;
    exit;
}
/////////////////////////////
//	get_month worker function
/////////////////////////////
function get_month_dropdown($i = 0)
{
    global $now_date;
    $return = '';
    $month = array(
        '----',
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    );
    foreach ($month as $k => $m) {
        $return.= "\t<option value='".$k."'";
        $return.= (($k + $i) == $now_date['mon']) ? " selected='selected'" : "";
        $return.= ">".$m."</option>\n";
    }
    return $return;
}
/////////////////////////////
//	cache rep function
/////////////////////////////
function rep_cache()
{
    $query = @sql_query("SELECT * FROM reputationlevel");
    if (!mysqli_num_rows($query)) stderr('CACHE', 'No items to cache');
    $rep_cache_file = "{$INSTALLER09['baseurl']}/cache/rep_cache.php";
    $rep_out = "<"."?php\n\n\$reputations = array(\n";
    while ($row = mysqli_fetch_assoc($query)) {
        $rep_out.= "\t{$row['minimumreputation']} => '{$row['level']}',\n";
    }
    $rep_out.= "\n);\n\n?".">";
    clearstatcache($rep_cache_file);
    if (is_file($rep_cache_file) && is_writable($rep_cache_file)) {
        $filenum = fopen($rep_cache_file, 'w');
        ftruncate($filenum, 0);
        fwrite($filenum, $rep_out);
        fclose($filenum);
    }
}
?>
