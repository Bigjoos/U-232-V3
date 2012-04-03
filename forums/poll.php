<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
this one is from scratch lol

Forum Polls: beta tues july 20 2010 v0.1

STILL TO DO:
- perhaps change all options to switch statement with vote on the top :)
- add some sort of admin page / option to list voters with IP and member names (to find cheaters / multi votes from same IP etc

Powered by Bunnies!!!
**********************************************************/
if (!defined('BUNNY_FORUMS')) 
{
	$HTMLOUT ='';
	$HTMLOUT .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <title>ERROR</title>
        </head><body>
        <h1 style="text-align:center;">ERROR</h1>
        <p style="text-align:center;">How did you get here? silly rabbit Trix are for kids!.</p>
        </body></html>';
	echo $HTMLOUT;
	exit();
}
   $topic_id = (isset($_GET['topic_id']) ? intval($_GET['topic_id']) :  (isset($_POST['topic_id']) ? intval($_POST['topic_id']) :  0));

    if (!is_valid_id($topic_id))
    {
	stderr('Error', 'Bad ID.');
    }

//=== sue me I got lazy :P but I still think  is_numeric is crappy
function is_valid_poll_vote($vote)
{
return is_numeric($vote) && ($vote >= 0) && (floor($vote) == $vote);
}
	
	$success = 0; //=== used for errors
	
	//=== lets do that action 2 thing \\o\o/o//
	$posted_action = strip_tags((isset($_GET['action_2']) ? $_GET['action_2'] : (isset($_POST['action_2']) ? $_POST['action_2'] : '')));
	//=== add all possible actions here and check them to be sure they are ok
	$valid_actions = array('poll_vote', 'poll_add', 'poll_delete', 'poll_reset', 'poll_close', 'poll_open', 'poll_edit', 'reset_vote');
	
	//=== check posted action, and if no match, kill it
	$action = (in_array($posted_action, $valid_actions) ? $posted_action : 1);
	
	if ($action == 1)
	{
	stderr('Error', 'Thy sin\'s not accidental, but a trade!');
	}
//=== casting a vote(s) ===========================================================================================//
	switch($action)
	{
		case 'poll_vote':
		//=== Get poll info
		$res_poll = sql_query('SELECT t.poll_id, t.locked, f.min_class_write, f.min_class_read, p.poll_starts, p.poll_ends, p.change_vote, p.multi_options, p.poll_closed
										FROM topics AS t LEFT JOIN forum_poll AS p ON t.poll_id = p.id LEFT JOIN forums AS f ON t.forum_id = f.id  WHERE t.id = '.sqlesc($topic_id));
		$arr_poll = mysqli_fetch_assoc($res_poll);

		//=== did they vote yet
		$res_poll_did_they_vote = sql_query('SELECT COUNT(id) FROM forum_poll_votes WHERE poll_id = '.sqlesc($arr_poll['poll_id']).' AND user_id = '.sqlesc($CURUSER['id']));
		$row = mysqli_fetch_row($res_poll_did_they_vote);
		$vote_count = number_format($row[0]);
		$post_vote = (isset($_POST['vote']) ? $_POST['vote'] : '');   
		
		//=== let's do all the possible errors
		switch (true)
		{
		case (!is_valid_id($arr_poll['poll_id']) || COUNT($post_vote) > $arr_poll['multi_options']): //=== no poll or trying to vote with too many options 
				stderr('Error', 'Bad ID. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case($arr_poll['poll_closed'] === 'yes'): //=== poll closed
				stderr('Error', 'Poll is closed, you cannot vote. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($arr_poll['poll_starts'] > TIME_NOW):  //=== poll hasn't started yet
				stderr('Error', 'Poll hasn\'t started yet. The Poll starts: '.get_date($arr_poll['poll_starts'],'').'. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($vote_count > 0 && $arr_poll['change_vote'] == 'no'): //=== already voted and change vote set to no
				stderr('Error', 'You have already voted, you cannot change your vote. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($CURUSER['class'] < $arr_poll['min_class_read']): //=== shouldn't be here!
				stderr('Error', 'Bad ID.');
			break;
		case ($CURUSER['class'] < $arr_poll['min_class_write'] || $CURUSER['forum_post'] == 'no' || $CURUSER['suspended'] == 'yes'): //=== not alowed to post
				stderr('Error', 'You are not permitted to vote here.  <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>');
			break;			
		case ($arr_poll['locked'] == 'yes'): //=== topic locked
				stderr('Error', 'This topic is locked.  <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>');
			break;
		}
	
	//=== ok, all is good, lets enter the vote(s) into the DB
	$ip = sqlesc(($CURUSER['ip'] == '' ? htmlsafechars($_SERVER['REMOTE_ADDR']) : $CURUSER['ip']));
	$added = TIME_NOW;
	//=== if they selected "I just want to see the results!" only enter that one... 666 is reserved for that :)
	if (in_array('666', $post_vote))
	{
		sql_query('INSERT INTO forum_poll_votes (`poll_id`, `user_id`, `option`, `ip`, `added`) VALUES ('.sqlesc($arr_poll['poll_id']).', '.sqlesc($CURUSER['id']).', 666, '.sqlesc($ip).', '.$added.')');
		
		//=== all went well, send them back!
		header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
		die();	
	}
	else
	{
	//=== if single vote (not array)
		if (is_valid_poll_vote($post_vote))
		{
		sql_query('INSERT INTO forum_poll_votes (`poll_id`, `user_id`, `option`, `ip`, `added`) VALUES('.sqlesc($arr_poll['poll_id']).', '.sqlesc($CURUSER['id']).', '.sqlesc($post_vote).', '.sqlesc($ip).', '.$added.')');
		$success = 1;
		}
		else
		{
			foreach ($post_vote as $votes)
			{
				$vote = 0 + $votes;
					if (is_valid_poll_vote($vote))
					{
					sql_query('INSERT INTO forum_poll_votes (`poll_id`, `user_id`, `option`, `ip`, `added`) VALUES('.sqlesc($arr_poll['poll_id']).', '.sqlesc($CURUSER['id']).', '.sqlesc($vote).', '.sqlesc($ip).', '.$added.')');
					$success = 1;
					}
			}
		}

	//=== did it work?
		if ($success != 1)
		{
		stderr('Error', 'Something went wrong, your vote was not counted!. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
		} 

	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	} //=== end of else
	
	break; //=== end casting a vote(s)

//=== resetting vote ============================================================================================//	
	case 'reset_vote':

		//=== Get poll info
		$res_poll = sql_query('SELECT t.poll_id, t.locked, f.min_class_write, f.min_class_read, p.poll_starts, p.poll_ends, p.change_vote, p.multi_options, p.poll_closed
										FROM topics AS t LEFT JOIN forum_poll AS p ON t.poll_id = p.id LEFT JOIN forums AS f ON t.forum_id = f.id  WHERE t.id = '.sqlesc($topic_id));
		$arr_poll = mysqli_fetch_assoc($res_poll);

		//=== did they vote yet
		$res_poll_did_they_vote = sql_query('SELECT COUNT(id) FROM forum_poll_votes WHERE poll_id = '.sqlesc($arr_poll['poll_id']).' AND user_id = '.sqlesc($CURUSER['id']));
		$row = mysqli_fetch_row($res_poll_did_they_vote);
		$vote_count = number_format($row[0]);
		
		//=== let's do all the possible errors
		switch (true)
		{
		case (!is_valid_id($arr_poll['poll_id'])): //=== no poll 
				stderr('Error', 'Bad ID. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case($arr_poll['poll_closed'] === 'yes'): //=== poll closed
				stderr('Error', 'Poll is closed, you cannot vote. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($arr_poll['poll_starts'] > TIME_NOW):  //=== poll hasn't started yet
				stderr('Error', 'Poll hasn\'t started yet. The Poll starts: '.get_date($arr_poll['poll_starts'],'').'. <a href="forums.php?action=view_topic&topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($arr_poll['change_vote'] == 'no'): //===  vote set to no changes
				stderr('Error', 'You have already voted, you cannot change your vote. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
			break;
		case ($CURUSER['class'] < $arr_poll['min_class_read']): //=== shouldn't be here!
				stderr('Error', 'Bad ID.');
			break;
		case ($CURUSER['class'] < $arr_poll['min_class_write'] || $CURUSER['forum_post'] == 'no' || $CURUSER['suspended'] == 'yes'): //=== not alowed to vote
				stderr('Error', 'You are not permitted to vote here.  <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>');
			break;			
		case ($arr_poll['locked'] == 'yes'): //=== topic locked
				stderr('Error', 'This topic is locked.  <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>');
			break;
		}	
		
		//=== ok all is well, let then change their votes :)
		sql_query('DELETE FROM forum_poll_votes WHERE poll_id = '.sqlesc($arr_poll['poll_id']).' AND user_id = '.sqlesc($CURUSER['id'])000);
		
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	
	break;
	
	
//=== adding a poll ============================================================================================//	
	case 'poll_add':
	//=== be sure there is no poll yet :P
    $res_poll = sql_query('SELECT poll_id, user_id, topic_name FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_assoc($res_poll);
	$poll_id = (int)$arr_poll['poll_id'];
	$user_id = (int)$arr_poll['user_id'];
	
		if (is_valid_id($poll_id))
		{
		stderr('Error', 'There can only be one poll per topic. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		if ($user_id != $CURUSER['id'] && $CURUSER['class'] < UC_STAFF)
		{
		stderr('Error', 'Only the topic owner or staff can start a poll. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		
		//=== enter it into the DB \o/
		if (isset($_POST['add_the_poll']) && $_POST['add_the_poll'] == 1)
		{	
		//=== post stuff
		$poll_question = (isset($_POST['poll_question']) ? htmlsafechars($_POST['poll_question']) : '');
		$poll_answers = (isset($_POST['poll_answers']) ? htmlsafechars($_POST['poll_answers']) : '');
		$poll_ends = ((isset($_POST['poll_ends']) && $_POST['poll_ends'] > 168) ? 1356048000 : (TIME_NOW + $_POST['poll_ends'] * 86400));
		$poll_starts = ((isset($_POST['poll_starts']) && $_POST['poll_starts'] === 0) ? TIME_NOW : (TIME_NOW + $_POST['poll_starts'] * 86400));
		$poll_starts = ($poll_starts > ($poll_ends + 1) ? TIME_NOW : $poll_starts);
		$change_vote = ((isset($_POST['change_vote']) && $_POST['change_vote'] === 'yes') ? 'yes' : 'no');
	
		if ($poll_answers == '' && $poll_question == '')
		{
		stderr('Error', 'Be sure to fill in the question, and at least two options (max 20).');
		}

		//=== make it an array with a max of 20 options
		$break_down_poll_options = explode("\n", $poll_answers); 

			//=== be sure there are no blank options
			for($i = 0; $i < count($break_down_poll_options); $i++){

				if (strlen($break_down_poll_options[$i]) < 2)
				{
				stderr('Error', 'No blank lines in the poll, each option should be on it\'s own line, one line, one option.');
				}
			}
		
		if ($i > 20 || $i < 2)
		{
		stderr('Error', 'There is a minimum of 2 options, and a maximun of 20 options. you have entered '.$i.'.');
		}

	$multi_options = ((isset($_POST['multi_options']) && $_POST['multi_options'] <= $i) ? intval($_POST['multi_options']) : 1);
	
	//=== serialize it and slap it in the DB allready!
	$poll_options = serialize($break_down_poll_options); 

      sql_query('INSERT INTO `forum_poll` (`user_id` ,`question` ,`poll_answers` ,`number_of_options` ,`poll_starts` ,`poll_ends` ,`change_vote` ,`multi_options`)
					VALUES ('.sqlesc($CURUSER['id']).', '.sqlesc($poll_question).', '.sqlesc($poll_options).', '.$i.', '.$poll_starts.', '.$poll_ends.', \''.$change_vote.'\', '.$multi_options.')');
		$poll_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

		if (is_valid_id($poll_id))
		{
		sql_query('UPDATE `topics` SET poll_id = '.sqlesc($poll_id).' WHERE id='.sqlesc($topic_id));
		}
		else
		{
		stderr('Error', 'Something went wrong, the poll was not added.');
		}
	
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	} //=== end of posting poll to DB
		
//=== ok looks like they can be here
//=== options for amount of options lol
for($i = 2; $i < 21; $i++)
{
$options .='<option class="body" value="'.$i.'">'.$i.' options</option>';
}	
 
	$HTMLOUT .= '<table class="main" width="750px" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="embedded" align="center">
		<h1 style="text-align: center;">Add poll in "<a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'">'.htmlsafechars($arr_poll['topic_name'], ENT_QUOTES).'</a>"</h1>
		
	<form action="forums.php?action=poll" method="post" name="poll">
		<input type="hidden" name="topic_id" value="'.$topic_id.'" />
		<input type="hidden" name="action_2" value="poll_add" />
		<input type="hidden" name="add_the_poll" value="1" />
	<table border="0" cellspacing="0" cellpadding="5" width="800" align="center">
	<tr>
		<td class="forum_head_dark" colspan="3"><span style="color: white; font-weight: bold;"><img src="pic/forums/poll.gif" alt="Poll" title="Poll" style="vertical-align: middle;" /> Add poll to topic!</span></td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/question.png" alt="Question" title="Question" width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll question:</span></td>
		<td class="three" align="left"><input type="text" name="poll_question" class="text_default" value="" /></td>
	</tr>
	<tr>
		<td class="three" align="center" valign="top"><img src="pic/forums/options.gif" alt="Options" title="Options" width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right" valign="top"><span style="white-space:nowrap;font-weight: bold;">Poll answers:</span></td>
		<td class="three" align="left" valign="top"><textarea cols="30" rows="4" name="poll_answers" class="text_area_small"></textarea>
		<br /> One option per line. There is a minimum of 2 options, and a maximun of 20 options. BBcode is enabled.</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/clock.png" alt="Clock" title="Clock" width="30" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll starts:</span></td>
		<td class="three" align="left"><select name="poll_starts">
											<option class="body" value="0">Start Now!</option>
											<option class="body" value="1">in 1 day</option>
											<option class="body" value="2">in 2 days</option>
											<option class="body" value="3">in 3 days</option>
											<option class="body" value="4">in 4 days</option>
											<option class="body" value="5">in 5 days</option>
											<option class="body" value="6">in 6 days</option>
											<option class="body" value="7">in 1 week</option>
											</select> When to start the poll. Default is "Start Now!"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/stop.png" alt="Stop" title="Stop" width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll ends:</span></td>
		<td class="three" align="left"><select name="poll_ends">
											<option class="body" value="1356048000">Run Forever</option>
											<option class="body" value="1">in 1 day</option>
											<option class="body" value="2">in 2 days</option>
											<option class="body" value="3">in 3 days</option>
											<option class="body" value="4">in 4 days</option>
											<option class="body" value="5">in 5 days</option>
											<option class="body" value="6">in 6 days</option>
											<option class="body" value="7">in 1 week</option>
											<option class="body" value="14">in 2 weeks</option>
											<option class="body" value="21">in 3 weeks</option>
											<option class="body" value="28">in 1 month</option>
											<option class="body" value="56">in 2 months</option>
											<option class="body" value="84">in 3 months</option>
											</select> How long should this poll run? Default is "run forever"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/multi.gif" alt="Multi" title="Multi" width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Multi options:</span></td>
		<td class="three" align="left"><select name="multi_options">
											<option class="body" value="1">Single option!</option>
											'.$options.'
											</select> Allow members to have more then one selection? Default is "Single option!"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Change vote:</span></td>
		<td class="three" align="left"><input name="change_vote" value="yes" type="radio"'.($change_vote === 'yes' ? ' checked="checked"' : '').' />Yes 
													<input name="change_vote" value="no" type="radio"'.($change_vote === 'no' ? ' checked="checked"' : '').' />No   <br /> Allow members to change their vote? Default is "no"
	</td>
	</tr>
	<tr>
		<td class="forum_head_dark" colspan="3" align="center">
		<input type="submit" name="button" class="button" value="Add Poll!" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
	</tr>
	</table></form><br /></td>
	</tr>
	</table>';
	
	 $HTMLOUT .= $the_bottom_of_the_page;
	
	break; //=== end add poll
	
//=== deleting a poll ============================================================================================//
	case 'poll_delete':
	
	if ($CURUSER['class'] < UC_STAFF)
	{	
	stderr('Error', 'Wherein [art thou] good, but to taste sack and drink it? Wherein neat and cleanly, but to carve a capon and eat it? 
	Wherein cunning, but in craft? Wherein crafty but in villainy? Wherein villainous, but in all things? Wherein worthy but in nothing?');
	}
	
	//=== be sure there is a poll to delete :P
    $res_poll = sql_query('SELECT poll_id FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_row($res_poll);
	$poll_id = $arr_poll[0];
	
		if (!is_valid_id($poll_id))
		{
		stderr('Error', 'Bad ID... <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		else
		{
		//=== delete the poll
		sql_query('DELETE FROM forum_poll WHERE id = '.sqlesc($poll_id));
		//=== delete the votes
		sql_query('DELETE FROM forum_poll_votes WHERE poll_id = '.sqlesc($poll_id));
		//=== remove poll refrence from topic
		sql_query('UPDATE topics SET `poll_id` = 0 WHERE id = '.sqlesc($topic_id));
		$success = 1;
		}
		
		//=== did it work?
		if ($success != 1)
		{
		stderr('Error', 'Something went wrong, the poll was not deleted!. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
		} 
	
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();	
	
	break; //=== end delete poll
	
//=== reseting a poll ============================================================================================//
	case 'poll_reset':
	
	if ($CURUSER['class'] < UC_STAFF)
	{	
	stderr('Error', 'Thou hath more hair than wit, and more faults than hairs, and more wealth than faults.');
	}
	
	//=== be sure there is a poll to reset :P
    $res_poll = sql_query('SELECT poll_id FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_row($res_poll);
	$poll_id = $arr_poll[0];
	
		if (!is_valid_id($poll_id))
		{
		stderr('Error', 'Bad ID... <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		else
		{
		//=== delete the votes
		sql_query('DELETE FROM forum_poll_votes WHERE poll_id = '.sqlesc($poll_id));
		$success = 1;
		}
		
		//=== did it work?
		if ($success != 1)
		{
		stderr('Error', 'Something went wrong, the poll was not reset!. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
		} 
	
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();	
	
	break; //=== end reset poll	
	
//=== closing a poll ============================================================================================//
	case 'poll_close':
	if ($CURUSER['class'] < UC_STAFF)
	{
	stderr('Error', 'A weasel hath not such a deal of spleen as you are toss\'d with.');
	}
		
	//=== be sure there is a poll to close :P
    $res_poll = sql_query('SELECT poll_id FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_row($res_poll);
	$poll_id = $arr_poll[0];
	
		if (!is_valid_id($poll_id))
		{
		stderr('Error', 'Bad ID... <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		else
		{
		//=== close the poll
		sql_query('UPDATE forum_poll SET `poll_closed` = \'yes\', poll_ends = '.TIME_NOW.' WHERE id = '.sqlesc($poll_id));
		$success = 1;
		}
		
		//=== did it work?
		if ($success != 1)
		{
		stderr('Error', 'Something went wrong, the poll was not closed!. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
		} 
	
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	
	break; //=== end of poll close
	
//=== opening a poll  (either after it was closed, or timed out) ===============================================================================//
	case 'poll_open':
	
	if ($CURUSER['class'] < UC_STAFF)
	{
	stderr('Error', 'Thou bootless toad-spotted ratsbane!');
	}
		
	//=== be sure there is a poll to open :P
    $res_poll = sql_query('SELECT poll_id FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_row($res_poll);
	$poll_id = $arr_poll[0];
	
		if (!is_valid_id($poll_id))
		{
		stderr('Error', 'Bad ID... <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');	
		}
		else
		{
		//=== open the poll
		sql_query('UPDATE forum_poll SET `poll_closed` = \'no\', poll_ends = \'1356048000\' WHERE id = '.sqlesc($poll_id));
		$success = 1;
		}
		
		//=== did it work?
		if ($success != 1)
		{
		stderr('Error', 'Something went wrong, the poll was not opened!. <a href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'" class="altlink">Back To Topic</a>.');
		} 
	
	//=== all went well, send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	
	break; //=== end of open poll
	
//=== edit a poll ============================================================================================//	

	case 'poll_edit':

		if ($CURUSER['class'] < UC_STAFF)
		{
		stderr('Error', 'Confusion now hath made his masterpiece!');	
		}
		
	//=== be sure there is a poll to edit :P
    $res_poll = sql_query('SELECT poll_id, topic_name FROM topics WHERE id = '.sqlesc($topic_id));
    $arr_poll = mysqli_fetch_assoc($res_poll);
	$poll_id = (int)$arr_poll['poll_id'];
	
		if (!is_valid_id($poll_id))
		{
		stderr('Error', 'Bad ID.');	
		}


	//=== enter it into the DB \o/
	if (isset($_POST['do_poll_edit']) && $_POST['do_poll_edit'] == 1)
	{	
	//=== post stuff
	$poll_question = (isset($_POST['poll_question']) ? htmlsafechars($_POST['poll_question']) : '');
	$poll_answers = (isset($_POST['poll_answers']) ? htmlsafechars($_POST['poll_answers']) : '');
	$poll_ends = ((isset($_POST['poll_ends']) && $_POST['poll_ends'] == 1356048000) ? 1356048000 : (TIME_NOW + $_POST['poll_ends'] * 86400));
	$poll_starts = ((isset($_POST['poll_starts']) && $_POST['poll_starts'] == 0) ? TIME_NOW : (TIME_NOW + $_POST['poll_starts'] * 86400));
	$poll_starts = ($poll_starts > ($poll_ends + 1) ? TIME_NOW : $poll_starts);
	$change_vote = ((isset($_POST['change_vote']) && $_POST['change_vote'] == 'yes') ? 'yes' : 'no');
	
	if ($poll_answers == '' || $poll_question == '')
	{
	stderr('Error', 'Be sure to fill in the question, and at least two options (max 20).');
	}
	//=== make it an array with a max of 20 options
	$break_down_poll_options = explode("\n", $poll_answers); 

		//=== be sure there are no blank options
		for($i = 0; $i < count($break_down_poll_options); $i++){

			if (strlen($break_down_poll_options[$i]) < 2)
			{
			stderr('Error', 'No blank lines in the poll, each option should be on it\'s own line, one line, one option.');
			}
		}
		
		if ($i > 20 || $i < 2)
		{
		stderr('Error', 'There is a minimum of 2 options, and a maximun of 20 options. you have entered '.$i.'.');
		}

	$multi_options = ((isset($_POST['multi_options']) && $_POST['multi_options'] <= $i) ? intval($_POST['multi_options']) : 1);
	
	//=== serialize it and slap it in the DB FFS!
	$poll_options = serialize($break_down_poll_options); 

      sql_query('UPDATE forum_poll  SET question = '.sqlesc($poll_question).', poll_answers = '.sqlesc($poll_options).', number_of_options = '.$i.' , poll_starts =  '.$poll_starts.' , poll_ends = '.$poll_ends.', change_vote = \''.$change_vote.'\', multi_options = '.$multi_options.', poll_closed = \'no\' WHERE id = '.sqlesc($poll_id);
	
		//=== delete the votes
		sql_query('DELETE FROM forum_poll_votes WHERE poll_id = '.sqlesc($poll_id));
	
	//=== send them back!
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id);   
	die();
	} //=== end of posting poll to DB

	//=== get poll stuff to edit
	$res_edit = sql_query('SELECT * FROM forum_poll WHERE id = '.sqlesc($poll_id));
    $arr_edit = mysqli_fetch_assoc($res_edit);
	
	$poll_question = strip_tags($arr_edit['question']);
	$poll_answers = unserialize($arr_edit['poll_answers']);
	$number_of_options = $arr_edit['number_of_options'];
	$poll_starts = (int)$arr_edit['poll_starts'];
	$poll_ends = (int)$arr_edit['poll_ends'];
	$change_vote = htmlsafechars($arr_edit['change_vote']);
	$multi_options = htmlsafechars($arr_edit['multi_options']);
	
	//=== make the answers all readable
	$poll_answers = implode("\n", $poll_answers); 

//=== options for amount of options lol
for($i = 2; $i < 21; $i++)
{
$options .='<option class="body" value="'.$i.'" '.($multi_options == $i ? 'selected="selected"' : '').'>'.$i.' options</option>';
}	

//=== ok looks like they can be here
	$HTMLOUT .= '
	<form action="forums.php?action=poll" method="post" name="poll">
	<table class="main" width="750px" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="embedded" align="center">
		<h1 style="text-align: center;">Edit poll in "<a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'">'.htmlsafechars($arr_poll['topic_name'], ENT_QUOTES).'</a>"</h1>
		<input type="hidden" name="topic_id" value="'.$topic_id.'" />
		<input type="hidden" name="action_2" value="poll_edit" />
		<input type="hidden" name="do_poll_edit" value="1" />

	<table border="0" cellspacing="0" cellpadding="5" width="800" align="center">
	<tr>
		<td class="forum_head_dark" colspan="3"><span style="color: white; font-weight: bold;"><img src="pic/forums/poll.gif" alt="Poll" title="Poll" style="vertical-align: middle;" /> Add poll to topic!</span>  
		        Editing the poll will re-set all the votes (ie: delete them).</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/question.png" alt="Question" title="Question" width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll question:</span></td>
		<td class="three" align="left"><input type="text" name="poll_question" class="text_default" value="'.$poll_question.'" /></td>
	</tr>
	<tr>
		<td class="three" align="center" valign="top"><img src="pic/forums/options.gif" alt="Options" title="Options" width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right" valign="top"><span style="white-space:nowrap;font-weight: bold;">Poll answers:</span></td>
		<td class="three" align="left" valign="top"><textarea cols="30" rows="4" name="poll_answers" class="text_area_small">'.strip_tags($poll_answers).'</textarea><br /> 
		One option per line. There is a minimum of 2 options, and a maximun of 20 options. BBcode is enabled.</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/clock.png" alt="Clock" title="Clock" width="30" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll starts:</span></td>
		<td class="three" align="left"><select name="poll_starts">
											<option class="body" value="0">Start Now!</option>
											<option class="body" value="1">in 1 day</option>
											<option class="body" value="2">in 2 days</option>
											<option class="body" value="3">in 3 days</option>
											<option class="body" value="4">in 4 days</option>
											<option class="body" value="5">in 5 days</option>
											<option class="body" value="6">in 6 days</option>
											<option class="body" value="7">in 1 week</option>
											</select> When to start the poll. Default is "Start Now!"<br />
											Poll set to start: '.get_date($poll_starts,'').'</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/stop.png" alt="Stop" title="Stop" width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll ends:</span></td>
		<td class="three" align="left"><select name="poll_ends">
											<option class="body" value="1356048000">Run Forever</option>
											<option class="body" value="1">in 1 day</option>
											<option class="body" value="2">in 2 days</option>
											<option class="body" value="3">in 3 days</option>
											<option class="body" value="4">in 4 days</option>
											<option class="body" value="5">in 5 days</option>
											<option class="body" value="6">in 6 days</option>
											<option class="body" value="7">in 1 week</option>
											<option class="body" value="14">in 2 weeks</option>
											<option class="body" value="21">in 3 weeks</option>
											<option class="body" value="28">in 1 month</option>
											<option class="body" value="56">in 2 months</option>
											<option class="body" value="84">in 3 months</option>
											<option class="body" value="168">in 6 months</option>
											</select> How long should this poll run? Default is "run forever"<br />
											Poll set to end: '.($poll_ends === 1356048000 ? 'Run Forever' : get_date($poll_ends,'')).'</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/multi.gif" alt="Multi" title="Multi" width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Multi options:</span></td>
		<td class="three" align="left"><select name="multi_options">
											<option class="body" value="1" '.($multi_options == 1 ? 'selected="selected"' : '').'>Single option!</option>
											'.$options.'
											</select> Allow members to have more then one selection? Default is "Single option!"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Change vote:</span></td>
		<td class="three" align="left"><input name="change_vote" value="yes" type="radio"'.($change_vote === 'yes' ? ' checked="checked"' : '').' />Yes 
													<input name="change_vote" value="no" type="radio"'.($change_vote == 'no' ? ' checked="checked"' : '').' />No   <br /> Allow members to change their vote? Default is "no"</td>
	</tr>
	<tr>
	<td class="forum_head_dark" colspan="3" align="center">
	<input type="submit" name="button" class="button" value="Edit Poll!" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
	</tr>
	</table><br /></td>
	</tr>
	</table></form>';
	
	$HTMLOUT .= $the_bottom_of_the_page;
	
	break; //=== end edit poll
	default :
		//=== at the end of the day, if they are messing about doing what they shouldn't, let's give then what for!
		stderr('Error', 'O teach me how I should forget to think.');
		die();
	}//=== end switch all actions

?>
