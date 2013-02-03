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
require_once (INCL_DIR . 'user_functions.php');
require_once (INCL_DIR . 'pager_functions.php');
$params = array_merge($_GET, $_POST);
$params['mode'] = isset($params['mode']) ? $params['mode'] : '';
switch ($params['mode']) {
case 'unlock':
    cleanup_take_unlock();
    break;

case 'delete':
    cleanup_take_delete();
    break;

case 'takenew':
    cleanup_take_new();
    break;

case 'new':
    cleanup_show_new();
    break;

case 'takeedit':
    cleanup_take_edit();
    break;

case 'edit':
    cleanup_show_edit();
    break;

case 'run':
    manualclean();
    break;

default:
    cleanup_show_main();
    break;
}
function manualclean()
{
    global $params;
    if (function_exists('docleanup')) {
        stderr('ERROR', 'Another cleanup operation is already in progress. Refresh to try again.');
    }
    $opts = array(
        'options' => array(
            'min_range' => 1
        )
    );
    $params['cid'] = filter_var($params['cid'], FILTER_VALIDATE_INT, $opts);
    if (!is_numeric($params['cid'])) stderr('Error', "Bad you!");
    $params['cid'] = sqlesc($params['cid']);
    $sql = sql_query("SELECT * FROM cleanup WHERE clean_id = {$params['cid']}") or sqlerr(__file__, __line__);
    $row = mysqli_fetch_assoc($sql);
    if ($row['clean_id']) {
        $next_clean = intval(TIME_NOW + ($row['clean_increment'] ? $row['clean_increment'] : 15 * 60));
        sql_query("UPDATE cleanup SET clean_time = $next_clean WHERE clean_id = {$row['clean_id']}") or sqlerr(__file__, __line__);
        if (is_file(CLEAN_DIR . '' . $row['clean_file'])) {
            require_once (CLEAN_DIR . '' . $row['clean_file']);
        }
        if (function_exists('docleanup')) {
            docleanup($row);
        }
    }
    cleanup_show_main(); //instead of header() so can see queries in footer (using sql_query())
    exit();
}
function cleanup_show_main()
{
    $count1 = get_row_count('cleanup');
    $perpage = 15;
    $pager = pager($perpage, $count1, 'staffpanel.php?tool=cleanup_manager&amp;');
    $htmlout = "<h2>Current Cleanup Tasks</h2>
    <table class='torrenttable' bgcolor='#333333' border='1' cellpadding='5px' width='80%'>
    <tr>
      <td class='colhead'>Cleanup Title &amp; Description</td>
      <td class='colhead' width='150px'>Runs every</td>
      <td class='colhead' width='150px'>Next Clean Time</td>
      <td class='colhead' width='40px'>Edit</td>
      <td class='colhead' width='40px'>Delete</td>
      <td class='colhead' width='40px'>Off/On</td>
      <td class='colhead' style='width: 40px;'>Run&nbsp;now</td>
    </tr>";
    $sql = sql_query("SELECT * FROM cleanup ORDER BY clean_time ASC " . $pager['limit']) or sqlerr(__FILE__, __LINE__);
    if (!mysqli_num_rows($sql)) stderr('Error', 'Fucking panic now!');
    while ($row = mysqli_fetch_assoc($sql)) {
        $row['_clean_time'] = get_date($row['clean_time'], 'LONG');
        $row['clean_increment'] = $row['clean_increment'];
        $row['_class'] = $row['clean_on'] != 1 ? " style='color:red'" : '';
        $row['_title'] = $row['clean_on'] != 1 ? " (Locked)" : '';
        $row['_clean_time'] = $row['clean_on'] != 1 ? "<span style='color:red'>{$row['_clean_time']}</span>" : $row['_clean_time'];
    
        $htmlout.= "<tr>
          <td{$row['_class']}><strong>{$row['clean_title']}{$row['_title']}</strong><br />{$row['clean_desc']}</td>
          <td>" . mkprettytime($row['clean_increment']) . "</td>
          <td>{$row['_clean_time']}</td>
          <td align='center'><a href='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager&amp;mode=edit&amp;cid={$row['clean_id']}'>
            <img src='./pic/aff_tick.gif' alt='Edit Cleanup' title='Edit' border='0' height='12' width='12' /></a></td>

          <td align='center'><a href='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager&amp;mode=delete&amp;cid={$row['clean_id']}'>
            <img src='./pic/aff_cross.gif' alt='Delete Cleanup' title='Delete' border='0' height='12' width='12' /></a></td>
          <td align='center'><a href='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager&amp;mode=unlock&amp;cid={$row['clean_id']}&amp;clean_on={$row['clean_on']}'>
            <img src='./pic/warned.png' alt='On/Off Cleanup' title='on/off' border='0' height='12' width='12' /></a></td>
<td align='center'><a href='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager&amp;mode=run&amp;cid={$row['clean_id']}'>Run it now</a></td>
 </tr>";
    }
    $htmlout.= "</table>";
    if ($count1 > $perpage) $htmlout .= $pager['pagerbottom'];
    $htmlout.= "<br />
                <span class='btn'><a href='./staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager&amp;mode=new'>Add New</a></span>";
    echo stdhead('Cleanup Manager - View') . $htmlout . stdfoot();
}
function cleanup_show_edit()
{
    global $params;
    if (!isset($params['cid']) OR empty($params['cid']) OR !is_valid_id($params['cid'])) {
        cleanup_show_main();
        exit;
    }
    $cid = intval($params['cid']);
    $sql = sql_query("SELECT * FROM cleanup WHERE clean_id = $cid");
    if (!mysqli_num_rows($sql)) stderr('Error', 'Why me?');
    $row = mysqli_fetch_assoc($sql);
    $row['clean_title'] = htmlsafechars($row['clean_title'], ENT_QUOTES);
    $row['clean_desc'] = htmlsafechars($row['clean_desc'], ENT_QUOTES);
    $row['clean_file'] = htmlsafechars($row['clean_file'], ENT_QUOTES);
    $row['clean_title'] = htmlsafechars($row['clean_title'], ENT_QUOTES);
    $logyes = $row['clean_log'] ? 'checked="checked"' : '';
    $logno = !$row['clean_log'] ? 'checked="checked"' : '';
    $cleanon = $row['clean_on'] ? 'checked="checked"' : '';
    $cleanoff = !$row['clean_on'] ? 'checked="checked"' : '';
    $htmlout = '';
    $htmlout = "<h2>Editing cleanup: {$row['clean_title']}</h2>
    <div style='width: 800px; text-align: left; padding: 10px; margin: 0 auto;border-style: solid; border-color: #333333; border-width: 5px 2px;'>
    <form name='inputform' method='post' action='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager'>
    <input type='hidden' name='mode' value='takeedit' />
    <input type='hidden' name='cid' value='{$row['clean_id']}' />
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Title</label>
    <input type='text' value='{$row['clean_title']}' name='clean_title' style='width:250px;' /></div>
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Description</label>
    <input type='text' value='{$row['clean_desc']}' name='clean_desc' style='width:380px;' />
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup File Name</label>
    <input type='text' value='{$row['clean_file']}' name='clean_file' style='width:380px;' />
    
    </div>
    
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup Interval</label>
    <input type='text' value='{$row['clean_increment']}' name='clean_increment' style='width:380px;' />
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup Log</label>
    Yes &nbsp; <input name='clean_log' value='1' $logyes type='radio' />&nbsp;&nbsp;&nbsp;<input name='clean_log' value='0' $logno type='radio' /> &nbsp; No
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>cleanup On or Off?</label>
    Yes &nbsp; <input name='clean_on' value='1' $cleanon type='radio' />&nbsp;&nbsp;&nbsp;<input name='clean_on' value='0' $cleanoff type='radio' /> &nbsp; No
    </div>
    
    <div style='text-align:center;'><input type='submit' name='submit' value='Edit' class='button' />&nbsp;<input type='button' value='Cancel' onclick='javascript: history.back()' /></div>
    </form>
    </div>";
    echo stdhead('Cleanup Manager - Edit') . $htmlout . stdfoot();
}
function cleanup_take_edit()
{
    global $params;
    //ints
    foreach (array(
        'cid',
        'clean_increment',
        'clean_log',
        'clean_on'
    ) as $x) {
        unset($opts);
        if ($x == 'cid' OR $x == 'clean_increment') {
            $opts = array(
                'options' => array(
                    'min_range' => 1
                )
            );
        } else {
            $opts = array(
                'options' => array(
                    'min_range' => 0,
                    'max_range' => 1
                )
            );
        }
        $params[$x] = filter_var($params[$x], FILTER_VALIDATE_INT, $opts);
        if (!is_numeric($params[$x])) stderr('Error', "Don't leave any field blank $x");
    }
    unset($opts);
    // strings
    foreach (array(
        'clean_title',
        'clean_desc',
        'clean_file'
    ) as $x) {
        $opts = array(
            'flags' => FILTER_FLAG_STRIP_LOW,
            FILTER_FLAG_STRIP_HIGH
        );
        $params[$x] = filter_var($params[$x], FILTER_SANITIZE_STRING, $opts);
        if (empty($params[$x])) stderr('Error', "Don't leave any field blank");
    }
    $params['clean_file'] = preg_replace('#\.{1,}#s', '.', $params['clean_file']);
    if (!file_exists(CLEAN_DIR . "{$params['clean_file']}")) {
        stderr('Error', "You need to upload the cleanup file first!");
    }
    // new clean time =
    $params['clean_time'] = intval(TIME_NOW + $params['clean_increment']);
    //one more time around! LoL
    foreach ($params as $k => $v) {
        $params[$k] = sqlesc($v);
    }
    sql_query("UPDATE cleanup SET clean_title = {$params['clean_title']}, clean_desc = {$params['clean_desc']}, clean_file = {$params['clean_file']}, clean_time = {$params['clean_time']}, clean_increment = {$params['clean_increment']}, clean_log = {$params['clean_log']}, clean_on = {$params['clean_on']} WHERE clean_id = {$params['cid']}");
    cleanup_show_main();
    exit();
}
function cleanup_show_new()
{
    $htmlout = "<h2>Add a new cleanup task</h2>
    <div style='width: 800px; text-align: left; padding: 10px; margin: 0 auto;border-style: solid; border-color: #333333; border-width: 5px 2px;'>
    <form name='inputform' method='post' action='staffpanel.php?tool=cleanup_manager&amp;action=cleanup_manager'>
    <input type='hidden' name='mode' value='takenew' />
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Title</label>
    <input type='text' value='' name='clean_title' style='width:350px;' />
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Description</label>
    <input type='text' value='' name='clean_desc' style='width:350px;' />
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup File Name</label>
    <input type='text' value='' name='clean_file' style='width:350px;' />
    </div>
    
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup Interval</label>
    <input type='text' value='' name='clean_increment' style='width:350px;' />
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>Cleanup Log</label>
    Yes &nbsp; <input name='clean_log' value='1' type='radio' />&nbsp;&nbsp;&nbsp;<input name='clean_log' value='0' checked='checked' type='radio' /> &nbsp; No
    </div>
    
    <div style='margin-bottom:5px;'>
    <label style='float:left;width:200px;'>cleanup On or Off?</label>
    Yes &nbsp; <input name='clean_on' value='1' type='radio' />&nbsp;&nbsp;&nbsp;<input name='clean_on' value='0' checked='checked' type='radio' /> &nbsp; No
    </div>
    
    <div style='text-align:center;'><input type='submit' name='submit' value='Add' class='button' />&nbsp;<input type='button' value='Cancel' onclick='javascript: history.back()' /></div>
    </form>
    </div>";
    echo stdhead('Cleanup Manager - Add New') . $htmlout . stdfoot();
}
function cleanup_take_new()
{
    global $params;
    //ints
    foreach (array(
        'clean_increment',
        'clean_log',
        'clean_on'
    ) as $x) {
        unset($opts);
        if ($x == 'clean_increment') {
            $opts = array(
                'options' => array(
                    'min_range' => 1
                )
            );
        } else {
            $opts = array(
                'options' => array(
                    'min_range' => 0,
                    'max_range' => 1
                )
            );
        }
        $params[$x] = filter_var($params[$x], FILTER_VALIDATE_INT, $opts);
        if (!is_numeric($params[$x])) stderr('Error', "Don't leave any field blank $x");
    }
    unset($opts);
    // strings
    foreach (array(
        'clean_title',
        'clean_desc',
        'clean_file'
    ) as $x) {
        $opts = array(
            'flags' => FILTER_FLAG_STRIP_LOW,
            FILTER_FLAG_STRIP_HIGH
        );
        $params[$x] = filter_var($params[$x], FILTER_SANITIZE_STRING, $opts);
        if (empty($params[$x])) stderr('Error', "Don't leave any field blank");
    }
    $params['clean_file'] = preg_replace('#\.{1,}#s', '.', $params['clean_file']);
    if (!file_exists(CLEAN_DIR . "{$params['clean_file']}")) {
        stderr('Error', "You need to upload the cleanup file first!");
    }
    // new clean time =
    $params['clean_time'] = intval(time() + $params['clean_increment']);
    $params['clean_cron_key'] = md5(uniqid()); // just for now.
    //one more time around! LoL
    foreach ($params as $k => $v) {
        $params[$k] = sqlesc($v);
    }
    sql_query("INSERT INTO cleanup (clean_title, clean_desc, clean_file, clean_time, clean_increment, clean_cron_key, clean_log, clean_on) VALUES ({$params['clean_title']}, {$params['clean_desc']}, {$params['clean_file']}, {$params['clean_time']}, {$params['clean_increment']}, {$params['clean_cron_key']}, {$params['clean_log']}, {$params['clean_on']})");
    if (((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res)) {
        stderr('Info', "Success, new cleanup task added!");
    } else {
        stderr('Error', "Something went horridly wrong");
    }
    exit();
}
function cleanup_take_delete()
{
    global $params;
    $opts = array(
        'options' => array(
            'min_range' => 1
        )
    );
    $params['cid'] = filter_var($params['cid'], FILTER_VALIDATE_INT, $opts);
    if (!is_numeric($params['cid'])) stderr('Error', "Bad you!");
    $params['cid'] = sqlesc($params['cid']);
    sql_query("DELETE FROM cleanup WHERE clean_id = {$params['cid']}");
    if (1 === mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        stderr('Info', "Success, cleanup task deleted!");
    } else {
        stderr('Error', "Something went horridly wrong");
    }
    exit();
}
function cleanup_take_unlock()
{
    global $params;
    foreach (array(
        'cid',
        'clean_on'
    ) as $x) {
        unset($opts);
        if ($x == 'cid') {
            $opts = array(
                'options' => array(
                    'min_range' => 1
                )
            );
        } else {
            $opts = array(
                'options' => array(
                    'min_range' => 0,
                    'max_range' => 1
                )
            );
        }
        $params[$x] = filter_var($params[$x], FILTER_VALIDATE_INT, $opts);
        if (!is_numeric($params[$x])) stderr('Error', "Don't leave any field blank $x");
    }
    unset($opts);
    $params['cid'] = sqlesc($params['cid']);
    $params['clean_on'] = ($params['clean_on'] === 1 ? sqlesc($params['clean_on'] - 1) : sqlesc($params['clean_on'] + 1));
    sql_query("UPDATE cleanup SET clean_on = {$params['clean_on']} WHERE clean_id = {$params['cid']}");
    if (1 === mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        cleanup_show_main(); // this go bye bye later
        
    } else {
        stderr('Error', "Something went horridly wrong");
    }
    exit();
}
?>
