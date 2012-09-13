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
require_once (CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
$params = array_merge($_GET, $_POST);
$params['mode'] = isset($params['mode']) ? $params['mode'] : '';
$INSTALLER09['max_poll_questions'] = 2;
$INSTALLER09['max_poll_choices_per_question'] = 20;
switch ($params['mode']) {
case 'delete':
    delete_poll();
    break;

case 'edit':
    edit_poll_form();
    break;

case 'new':
    show_poll_form();
    break;

case 'poll_new':
    insert_new_poll();
    break;

case 'poll_update':
    update_poll();
    break;

default:
    show_poll_archive();
    break;
}
function delete_poll()
{
    global $INSTALLER09, $CURUSER, $mc1;
    $total_votes = 0;
    if (!isset($_GET['pid']) OR !is_valid_id($_GET['pid'])) stderr('USER ERROR', 'There is no poll with that ID!');
    $pid = intval($_GET['pid']);
    if (!isset($_GET['sure'])) stderr('USER WARNING', "<h2>You are about to delete a poll forever!</h2>
      <a href='javascript:history.back()' title='Cancel this operation!' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_delete.gif' alt='Go Back' />Go Back</span></a>&nbsp;<a href=staffpanel.php?tool=polls_manager&amp;'action=polls_manager&amp;mode=delete&amp;pid={$pid}&amp;sure=1' title='Delete this poll, there is no going back!' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_tick.gif' alt='Delete' />Delete Sure?</span></a>");
    sql_query("DELETE FROM polls WHERE pid = $pid");
    sql_query("DELETE FROM poll_voters WHERE poll_id = $pid");
    $mc1->delete_value('poll_data_'.$CURUSER['id']);
    show_poll_archive();
}
function update_poll()
{
    global $INSTALLER09, $CURUSER, $mc1;
    $total_votes = 0;
    if (!isset($_POST['pid']) OR !is_valid_id($_POST['pid'])) stderr('USER ERROR', 'There is no poll with that ID!');
    $pid = intval($_POST['pid']);
    if (!isset($_POST['poll_question']) OR empty($_POST['poll_question'])) stderr('USER ERROR', 'There is no title defined!');
    $poll_title = sqlesc(htmlsafechars(strip_tags($_POST['poll_question']) , ENT_QUOTES));
    //get the main crux of the poll data
    $poll_data = makepoll();
    $total_votes = isset($poll_data['total_votes']) ? intval($poll_data['total_votes']) : 0;
    unset($poll_data['total_votes']);
    if (!is_array($poll_data) OR !count($poll_data)) stderr('SYSTEM ERROR', 'There was no data sent');
    //all ok, serialize
    $poll_data = sqlesc(serialize($poll_data));
    $username = sqlesc($CURUSER['username']);
    sql_query("UPDATE polls SET choices=$poll_data, starter_id={$CURUSER['id']}, starter_name=$username, votes=$total_votes, poll_question=$poll_title WHERE pid=$pid") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('poll_data_'.$CURUSER['id']);
    if (-1 == mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $msg = "<h2>An Error Occured!</h2>
      <a href='javascript:history.back()' title='Go back and fix the error' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_delete.gif' alt='Go Back' />Go Back</span></a>";
    } else {
        $msg = "<h2>Groovy, everything went hunky dory!</h2>
      <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager' title='Return to Polls Manager' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_tick.gif' alt='Success' />Success</span></a>";
    }
    echo stdhead('Poll Manager::Add New Poll').$msg.stdfoot();
}
function insert_new_poll()
{
    global $INSTALLER09, $CURUSER, $mc1;
    if (!isset($_POST['poll_question']) OR empty($_POST['poll_question'])) stderr('USER ERROR', 'There is no title defined!');
    $poll_title = sqlesc(htmlsafechars(strip_tags($_POST['poll_question']) , ENT_QUOTES));
    //get the main crux of the poll data
    $poll_data = makepoll();
    if (!is_array($poll_data) OR !count($poll_data)) stderr('SYSTEM ERROR', 'There was no data sent');
    //all ok, serialize
    $poll_data = sqlesc(serialize($poll_data));
    $username = sqlesc($CURUSER['username']);
    $time = TIME_NOW;
    sql_query("INSERT INTO polls (start_date, choices, starter_id, starter_name, votes, poll_question)VALUES($time, $poll_data, {$CURUSER['id']}, $username, 0, $poll_title)") or sqlerr(__FILE__, __LINE__);
    $mc1->delete_value('poll_data_'.$CURUSER['id']);
    if (false == ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res)) {
        $msg = "<h2>An Error Occured!</h2>
      <a href='javascript:history.back()' title='Go back and fix the error' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_delete.gif' alt='Go Back' />Go Back</span></a>";
    } else {
        $msg = "<h2>Groovy, everything went hunky dory!</h2>
      <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager' title='Return to Polls Manager' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_tick.gif' alt='Success' />Success</span></a>";
    }
    echo stdhead('Poll Manager::Add New Poll').$msg.stdfoot();
}
function show_poll_form()
{
    global $INSTALLER09;
    $poll_box = poll_box($INSTALLER09['max_poll_questions'], $INSTALLER09['max_poll_choices_per_question'], 'poll_new');
    echo stdhead('Poll Manager::Add New Poll').$poll_box.stdfoot();
}
function edit_poll_form()
{
    global $INSTALLER09;
    $poll_questions = '';
    $poll_multi = '';
    $poll_choices = '';
    $poll_votes = '';
    $query = sql_query("SELECT * FROM polls WHERE pid = ".intval($_GET['pid']));
    if (false == mysqli_num_rows($query)) return 'No poll with that ID';
    $poll_data = mysqli_fetch_assoc($query);
    $poll_answers = $poll_data['choices'] ? unserialize(stripslashes($poll_data['choices'])) : array();
    foreach ($poll_answers as $question_id => $data) {
        $poll_questions.= "\t{$question_id} : '".str_replace("'", '&#39;', $data['question'])."',\n";
        $data['multi'] = isset($data['multi']) ? intval($data['multi']) : 0;
        $poll_multi.= "\t{$question_id} : '".$data['multi']."',\n";
        foreach ($data['choice'] as $choice_id => $text) {
            $choice = $text;
            $votes = intval($data['votes'][$choice_id]);
            $poll_choices.= "\t'{$question_id}_{$choice_id}' : '".str_replace("'", '&#39;', $choice)."',\n";
            $poll_votes.= "\t'{$question_id}_{$choice_id}' : '".$votes."',\n";
        }
    }
    $poll_questions = preg_replace("#,(\n)?$#", "\\1", $poll_questions);
    $poll_choices = preg_replace("#,(\n)?$#", "\\1", $poll_choices);
    $poll_multi = preg_replace("#,(\n)?$#", "\\1", $poll_multi);
    $poll_votes = preg_replace("#,(\n)?$#", "\\1", $poll_votes);
    $poll_question = $poll_data['poll_question'];
    $show_open = $poll_data['choices'] ? 1 : 0;
    $poll_box = poll_box($INSTALLER09['max_poll_questions'], $INSTALLER09['max_poll_choices_per_question'], 'poll_update', $poll_questions, $poll_choices, $poll_votes, $show_open, $poll_question, $poll_multi);
    echo stdhead('Poll Manager::Edit Poll').$poll_box.stdfoot();
}
function show_poll_archive()
{
    global $INSTALLER09;
    $HTMLOUT = '';
    $query = sql_query("SELECT * FROM polls ORDER BY start_date DESC");
    if (false == mysqli_num_rows($query)) {
        $HTMLOUT = "<h2>No polls defined</h2>
      <br />
      <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager&amp;mode=new'><span class='btn' style='padding:3px;' title='Add a new poll'><img style='vertical-align:top;' src='{$INSTALLER09['pic_base_url']}/polls/p_add.gif' alt='Add New' />&nbsp;Add New Poll</span></a>";
    } else {
        $HTMLOUT.= "<h2>Manage Polls</h2>
      <br /><br />
      <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager&amp;mode=new'><span class='btn' style='padding:3px;' title='Add a new poll'><img style='vertical-align:top;' src='{$INSTALLER09['pic_base_url']}/polls/p_add.gif' alt='Add New' />&nbsp;Add New Poll</span></a>
      <br /><br />
      <table cellpadding='5'>
      <tr>
        <td>ID</td>
        <td>Question</td>
        <td>No. Votes</td>
        <td>Date</td>
        <td>Starter</td>
        <td>&nbsp;</td>
      </tr>";
        while ($row = mysqli_fetch_assoc($query)) {
            $row['start_date'] = get_date($row['start_date'], 'DATE');
            $HTMLOUT.= "<tr>
          <td>{$row['pid']}</td>
          <td>{$row['poll_question']}</td>
          <td>{$row['votes']}</td>
          <td>{$row['start_date']}</td>
          <td><a href='userdetails.php?id={$row['starter_id']}'>{$row['starter_name']}</a></td>
          <td><a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager&amp;mode=edit&amp;pid={$row['pid']}'><span class='btn' title='Edit poll'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_edit.gif' alt='Edit' />&nbsp;Edit</span></a>&nbsp;
          <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager&amp;mode=delete&amp;pid={$row['pid']}'><span class='btn' title='Delete poll'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_delete.gif' alt='Delete' />&nbsp;Delete</span></a></td>
        </tr>";
        }
        $HTMLOUT.= "</table><br />";
    }
    echo stdhead('Poll manager::Poll Archive').$HTMLOUT.stdfoot();
}
function poll_box($max_poll_questions = "", $max_poll_choices = "", $form_type = '', $poll_questions = "", $poll_choices = "", $poll_votes = "", $show_open = "", $poll_question = "", $poll_multi = "")
{
    global $INSTALLER09;
    $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
    $form_type = ($form_type != '' ? $form_type : 'poll_update');
    $HTMLOUT = "";
    $HTMLOUT.= "
    <script type=\"text/javascript\" src=\"scripts/polls.js\"></script>
     <script type=\"text/javascript\">
     //<![CDATA[

      var showfullonload = parseInt(\"{$show_open}\");
      
      // Questions
      var poll_questions = {{$poll_questions}};
      
      var poll_choices = {{$poll_choices}};
      
      var poll_votes = {{$poll_votes}};
      var poll_multi = {{$poll_multi}};
      
      // Setting elements
      var max_poll_questions = parseInt(\"{$max_poll_questions}\");
      var max_poll_choices   = parseInt(\"{$max_poll_choices}\");
      
      // HTML elements
      var html_add_question = \"<a href='#' title='Add Poll Question' style='color:green;font-weight:bold' onclick='return poll_add_question()'><span class='btn' style='padding:3px;'><img style='vertical-align:-30%;' src='{$INSTALLER09['pic_base_url']}/polls/p_plus.gif' alt='Add Poll Question' />Add Poll Question</span></a>\";
      
      var html_add_choice = \"<li>&nbsp;<a href='#' title='Add Poll Choice' style='color:green;font-weight:bold' onclick='return poll_add_choice(\"+'\"'+'<%1>'+'\"'+\")'><span class='btn' style='padding:3px;'><img style='vertical-align:-30%;' src='{$INSTALLER09['pic_base_url']}/polls/p_plus.gif' alt='Add Poll Choice' />Add Poll Choice</span></a></li>\";
      
      var html_question_box = \"<input type='text' id='question_<%1>' name='question[<%1>]' size='50' class='input' value='<%2>' /> <a href='#' title='Remove Question' style='color:red;font-weight:bold' onclick='return poll_remove_question(\"+'\"'+'<%1>'+'\"'+\")'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_minus.gif' alt='Add New' /></a><br /><input class='checkbox' type='checkbox' id='multi_<%1>' name='multi[<%1>]' value='1' <%3> /><span>Multiple choice question? (Allows users to select more than one choice) </span>\";
      
      var html_votes_box = \"&nbsp;<input type='text' id='votes_<%1>_<%2>' name='votes[<%1>_<%2>]' size='5' class='input' value='<%3>' />\";
      
      var html_choice_box = \"<li><input type='text' id='choice_<%1>_<%2>' name='choice[<%1>_<%2>]' size='35' class='input' value='<%3>' /><%4> <a href='#' title='Remove Choice' style='color:red;font-weight:bold' onclick='return poll_remove_choice(\"+'\"'+'<%1>_<%2>'+'\"'+\")'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_minus.gif' alt='Add New' /></a></li>\";
      
      var html_choice_wrap = \"<ol><%1></ol>\";
      var html_question_wrap = \"<div><%1></div>\";
      var html_stat_wrap = \"<br /><div><%1></div>\";
      
      // Lang elements
      var js_lang_confirm = \"Please confirm this action\";
      var poll_stat_lang = \"You are allowed <%1> more question(s) with <%2> choices per question.\";
      
      //]]>
     </script>
     
     
     <h2>Editing Poll</h2>
     <br />
     <a href='staffpanel.php?tool=polls_manager&amp;action=polls_manager' title='Cancel' style='color:green;font-weight:bold'><span class='btn' style='padding:3px;'><img style='vertical-align:middle;' src='{$INSTALLER09['pic_base_url']}/polls/p_delete.gif' alt='Cancel' />Cancel</span></a>
     <br /><br />
     <form id='postingform' action='staffpanel.php?tool=polls_manager&amp;action=polls_manager' method='post' name='inputform' enctype='multipart/form-data'>
     <input type='hidden' name='mode' value='{$form_type}' />
     <input type='hidden' name='pid' value='$pid' />
     
     <div style='text-align:left; width:650px; border: 1px solid black; padding:5px;'>
        <fieldset>
         <legend><strong>Poll Title</strong></legend>
         <input type='text' size='40' class='input' name='poll_question' value='{$poll_question}' />
        </fieldset>

        <fieldset>
         <legend><strong>Poll Content</strong></legend>
          <div id='poll-box-main'>
        
          </div>
        </fieldset>
        
        <fieldset>
         <legend><strong>Poll Info</strong></legend>
          <div id='poll-box-stat'></div>
        </fieldset>
        <input type='submit' name='submit' value='Post Poll' class='btn' />
     </div>
     
    </form>  
     
     <script type='text/javascript'>
      poll_init_state();
     </script>";
    return $HTMLOUT;
}
function makepoll()
{
    global $INSTALLER09, $CURUSER;
    $questions = array();
    $choices_count = 0;
    $poll_total_votes = 0;
    if (isset($_POST['question']) AND is_array($_POST['question']) and count($_POST['question'])) {
        foreach ($_POST['question'] as $id => $q) {
            if (!$q OR !$id) {
                continue;
            }
            $questions[$id]['question'] = htmlsafechars(strip_tags($q) , ENT_QUOTES);
        }
    }
    if (isset($_POST['multi']) AND is_array($_POST['multi']) and count($_POST['multi'])) {
        foreach ($_POST['multi'] as $id => $q) {
            if (!$q OR !$id) {
                continue;
            }
            $questions[$id]['multi'] = intval($q);
        }
    }
    if (isset($_POST['choice']) AND is_array($_POST['choice']) and count($_POST['choice'])) {
        foreach ($_POST['choice'] as $mainid => $choice) {
            list($question_id, $choice_id) = explode("_", $mainid);
            $question_id = intval($question_id);
            $choice_id = intval($choice_id);
            if (!$question_id OR !isset($choice_id)) {
                continue;
            }
            if (!$questions[$question_id]['question']) {
                continue;
            }
            $questions[$question_id]['choice'][$choice_id] = htmlsafechars(strip_tags($choice) , ENT_QUOTES);
            $_POST['votes'] = isset($_POST['votes']) ? $_POST['votes'] : 0;
            $questions[$question_id]['votes'][$choice_id] = intval($_POST['votes'][$question_id.'_'.$choice_id]);
            $poll_total_votes+= $questions[$question_id]['votes'][$choice_id];
        }
    }
    foreach ($questions as $id => $data) {
        if (!is_array($data['choice']) OR !count($data['choice'])) {
            unset($questions[$id]);
        } else {
            $choices_count+= intval(count($data['choice']));
        }
    }
    if (count($questions) > $INSTALLER09['max_poll_questions']) {
        exit('poll_to_many');
    }
    if (count($choices_count) > ($INSTALLER09['max_poll_questions'] * $INSTALLER09['max_poll_choices_per_question'])) {
        exit('poll_to_many');
    }
    if (isset($_POST['mode']) AND $_POST['mode'] == 'poll_update') $questions['total_votes'] = $poll_total_votes;
    return $questions;
}
?>
