<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsourse and TBDev and the many many 
coders who helped develop them over time.
proper credits to follow :)
beta tues july 20 2010 v0.1
update tue 11 aug added the rest of the staff tools (multi select ones)
 "View Topic" with Forum Polls
STILL TO DO:
fix getting to last post... I seem to have messed it up
Powered by Bunnies!!!
**********************************************************/
if (!defined('BUNNY_FORUMS')) {
    $HTMLOUT = '';
    $HTMLOUT.= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
$colour = $class = $attachments = $members_votes = $status = $topic_poll = $stafflocked = $child = $parent_forum_name = $math_image = $math_text = $staff_tools = $staff_link = $now_viewing = '';
$topic_id = (isset($_GET['topic_id']) ? intval($_GET['topic_id']) : (isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0));
if (!is_valid_id($topic_id)) {
    stderr('Error', 'Bad ID.');
}
//=== get errors
$upload_errors_size = (isset($_GET['se']) ? intval($_GET['se']) : 0);
$upload_errors_type = (isset($_GET['ee']) ? intval($_GET['ee']) : 0);
//=== Get topic info
$res = sql_query('SELECT t.id AS topic_id, t.user_id, t.topic_name, t.locked, t.last_post, t.sticky, t.status, t.views, t.poll_id, t.num_ratings, t.rating_sum, t.topic_desc, t.forum_id, t.anonymous, f.name AS forum_name, f.min_class_read, f.min_class_write, f.parent_forum FROM topics AS t LEFT JOIN forums AS f ON t.forum_id = f.id WHERE  ' . ($CURUSER['class'] < UC_STAFF ? 't.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? ' t.status != \'deleted\'  AND' : '')) . ' t.id =' . sqlesc($topic_id));
$arr = mysqli_fetch_assoc($res);
//=== stop them, they shouldn't be here lol
if ($CURUSER['class'] < $arr['min_class_read'] || !is_valid_id($arr['topic_id']) || $CURUSER['class'] < $min_delete_view_class && $status == 'deleted' || $CURUSER['class'] < UC_STAFF && $status == 'recycled') {
    stderr('Error', 'Bad ID.'); //=== why tell them there is a forum here...
    
}
//=== topic status
$status = htmlsafechars($arr['status']);
switch ($status) {
case 'ok':
    $status = '';
    $status_image = '';
    break;

case 'recycled':
    $status = 'recycled';
    $status_image = '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/recycle_bin.gif" alt="Recycled" title="This thread is currently in the recycle-bin" />';
    break;

case 'deleted':
    $status = 'deleted';
    $status_image = '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete_icon.gif" alt="Deleted" title="This thread is currently deleted" />';
    break;
}
//=== topics stuff
$forum_id = (int)$arr['forum_id'];
$topic_owner = (int)$arr['user_id'];
$topic_name = htmlsafechars($arr['topic_name'], ENT_QUOTES);
$topic_desc1 = htmlsafechars($arr['topic_desc'], ENT_QUOTES);
//=== poll stuff
$members_votes = array();
if ($arr['poll_id'] > 0) {
    //=== get the poll info
    $res_poll = sql_query('SELECT * FROM forum_poll WHERE id = ' . sqlesc($arr['poll_id']));
    $arr_poll = mysqli_fetch_assoc($res_poll);
    //=== get the stuff for just staff
    if ($CURUSER['class'] >= UC_STAFF) {
        $res_poll_voted = sql_query('SELECT DISTINCT fpv.user_id, fpv.ip, fpv.added, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king FROM forum_poll_votes AS fpv LEFT JOIN users AS u ON u.id = fpv.user_id WHERE u.id > 0 AND poll_id = ' . sqlesc($arr['poll_id']));
        //=== let's see who's voted will add IP and time later :P
        $who_voted = (mysqli_num_rows($res_poll_voted) > 0 ? '<hr />' : 'no votes yet');
        while ($arr_poll_voted = mysqli_fetch_assoc($res_poll_voted)) {
            $who_voted.= print_user_stuff($arr_poll_voted);
        }
    }
    //=== see if they voted yet
    $res_did_they_vote_yet = sql_query('SELECT `option` FROM `forum_poll_votes` WHERE `poll_id` = ' . sqlesc($arr['poll_id']) . ' AND `user_id` = ' . sqlesc($CURUSER['id']));
    $voted = 0;
    $members_vote = 1000;
    if (mysqli_num_rows($res_did_they_vote_yet) > 0) {
        $voted = 1;
        while ($members_vote = mysqli_fetch_assoc($res_did_they_vote_yet)) {
            $members_votes[] = $members_vote['option'];
        }
    }
    $change_vote = ($arr_poll['change_vote'] === 'no' ? 0 : 1);
    $poll_open = (($arr_poll['poll_closed'] === 'yes' || $arr_poll['poll_starts'] > TIME_NOW || $arr_poll['poll_ends'] < TIME_NOW) ? 0 : 1);
    $poll_options = unserialize($arr_poll['poll_answers']);
    $multi_options = $arr_poll['multi_options'];
    $total_votes_res = sql_query('SELECT COUNT(id) FROM forum_poll_votes WHERE `option` < 21 AND poll_id = ' . sqlesc($arr['poll_id']));
    $total_votes_arr = mysqli_fetch_row($total_votes_res);
    $total_votes = $total_votes_arr[0];
    $res_non_votes = sql_query('SELECT COUNT(id) FROM `forum_poll_votes` WHERE `option` > 20 AND `poll_id` = ' . sqlesc($arr['poll_id']));
    $arr_non_votes = mysqli_fetch_row($res_non_votes);
    $num_non_votes = $arr_non_votes[0];
    $total_non_votes = ($num_non_votes > 0 ? ' [ ' . number_format($num_non_votes) . ' member' . ($num_non_votes == 1 ? '' : 's') . ' just wanted to see the results ]' : '');
    //=== if they voted show them the resaults, if not, let them vote
    $topic_poll.= (($voted === 1 || $poll_open === 0) ? '<br /><br />' : '<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll" method="post" name="poll">
	<fieldset class="poll_select">
	<input type="hidden" name="topic_id" value="' . $topic_id . '" />
	<input type="hidden" name="action_2" value="poll_vote" />') . '
	<table border="0" cellspacing="5" cellpadding="5" style="max-width:80%;" align="center">
	<tr>
	<td class="forum_head_dark" colspan="2" align="left"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/poll.gif" alt="" /><span style="font-weight: bold;">Poll
	' . ($arr_poll['poll_closed'] === 'yes' ? 'closed</span>' : ($arr_poll['poll_starts'] > TIME_NOW ? 'starts:</span> ' . get_date($arr_poll['poll_starts'], '') : ($arr_poll['poll_ends'] == 1356048000 ? '</span>' : ($arr_poll['poll_ends'] > TIME_NOW ? ' ends:</span> ' . get_date($arr_poll['poll_ends'], '', 0, 1) : '</span>')))) . '</td>
	<td class="forum_head_dark" colspan="3" align="right">' . ($CURUSER['class'] < UC_STAFF ? '' : '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_edit&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/modify.gif" alt="" width="20px" /> edit</a>  
	<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_reset&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/stop_watch.png" alt=" " width="20px" /> reset</a> 
	' . (($arr_poll['poll_ends'] > TIME_NOW || $arr_poll['poll_closed'] === 'no') ? '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_close&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/clock.png" alt="" width="20px" /> close</a>' : '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_open&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/clock.png" alt="" width="20px" /> start</a>') . '
	<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_delete&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete.gif" alt="" width="20px" /> delete</a>') . '</td>
	</tr>
	<tr>
	<td class="three" width="5px" align="center"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/poll_question.png" alt="" width="25px" /></td>
	<td class="three" align="left" valign="top" colspan="4"><br />' . format_comment($arr_poll['question']) . '<br /><br /></td>
	</tr>
	<tr>
	<td class="three" colspan="5" align="center">' . (($voted === 1 || $poll_open === 0) ? '' : '<p>you may select up to <span style="font-weight: bold;">' . $multi_options . ' </span>option' . ($multi_options == 1 ? '' : 's') . '.</p>') . '</td>
	</tr>';
    $number_of_options = (int)$arr_poll['number_of_options'];
    for ($i = 0; $i < $number_of_options; $i++) {
        //=== change colors
        $colour = (++$colour) % 2;
        $class = ($colour == 0 ? 'two' : 'one');
        //=== if they have voted
        if ($voted === 1) {
            //=== do the math for the votes
            $math_res = sql_query('SELECT COUNT(id) FROM `forum_poll_votes` WHERE poll_id = ' . sqlesc($arr['poll_id']) . ' AND `option` = ' . sqlesc($i));
            $math_row = mysqli_fetch_row($math_res);
            $vote_count = $math_row[0];
            $math = $vote_count > 0 ? round(($vote_count / $total_votes) * 100) : 0;
            $math_text = $math . '% with ' . $vote_count . ' vote' . ($vote_count == 1 ? '' : 's');
            $math_image = '<table border="0" width="200px">
		<tr>
		<td style="padding: 0px; background-image: url(' . $INSTALLER09['pic_base_url'] . 'forums/vote_img_bg.gif); background-repeat: repeat-x">
	   <img src="' . $INSTALLER09['pic_base_url'] . 'forums/vote_img.gif" width="' . $math . '%" height="8" alt="' . $math_text . '" title="' . $math_text . '"  /></td>
	   </tr></table>';
        }
        $topic_poll.= '<tr><td class="' . $class . '" width="5px" align="center">' . (($voted === 1 || $poll_open === 0) ? '<span style="font-weight: bold;">' . ($i + 1) . '.</span>' : ($multi_options == 1 ? '<input type="radio" name="vote" value="' . $i . '" />' : '<input type="checkbox" name="vote[]" id="vote[]" value="' . $i . '" />')) . '</td>
		<td class="' . $class . '" align="left" valign="middle">' . format_comment($poll_options[$i]) . '</td>
		<td class="' . $class . '" align="left">' . $math_image . '</td>
		<td class="' . $class . '" align="center"><span style="white-space:nowrap;">' . $math_text . '</span></td>
		<td class="' . $class . '" align="center">' . (in_array($i, $members_votes) ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/check.gif" width="20px" alt=" " /> <span style="font-weight: bold;">Your vote!</span>' : '') . '</td></tr>';
    }
    $class = ($class == 'one' ? 'two' : 'one');
    $topic_poll.= (($change_vote === 1 && $voted === 1) ? '<tr><td class="three" colspan="5" align="center">
			<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=reset_vote&amp;topic_id=' . $topic_id . '" class="altlink"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/stop_watch.png" alt="" width="20px" /> Reset Your Vote!</a> 
			</td></tr>' : '') . ($voted === 1 ? '
	     <tr>
			<td class="three" colspan="5" align="center">Total votes: ' . number_format($total_votes) . $total_non_votes . ($CURUSER['class'] < UC_STAFF ? '' : '<br />
			<a class="altlink"  title="List voters" id="toggle_voters" style="font-weight:bold;cursor:pointer;">List voters</a>
			<div id="voters" style="display:none">' . $who_voted . '</div>') . '</td>
	</tr>
</table><br />' : ($poll_open === 0 ? '' : '<tr>
			<td class="' . $class . '" width="5px" align="center">' . ($multi_options == 1 ? '<input type="radio" name="vote" value="666" />' : '<input type="checkbox" name="vote[]" id="vote[]" value="666" />') . '</td>
			<td class="' . $class . '" align="left" valign="middle" colspan="4"><span style="font-weight: bold;">I just want to see the results!</span></td>
		</tr>') . (($voted === 1 || $poll_open === 0) ? '</table><br />' : '<tr><td class="three" colspan="5" align="center">
			<input type="submit" name="button" class="button" value="Vote!" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
		</tr></table></fieldset></form>'));
}
if (isset($_GET['search'])) {
    $search = htmlsafechars($_GET['search']);
    $topic_name = highlightWords($topic_name, $search);
}
$forum_desc = ($arr['topic_desc'] !== '' ? '<span style="font-weight: bold;">' . htmlsafechars($arr['topic_desc'], ENT_QUOTES) . '</span><br /><br />' : '');
$locked = ($arr['locked'] === 'yes' ? 'yes' : 'no');
$sticky = ($arr['sticky'] === 'yes' ? 'yes' : 'no');
$views = number_format($arr['views']);
//=== forums stuff
$forum_name = htmlsafechars($arr['forum_name'], ENT_QUOTES);
//=== staff options
if ($CURUSER['class'] >= UC_STAFF) {
    $staff_link = '<a class="altlink"  title="Staff Tools" id="tool_open" style="font-weight:bold;cursor:pointer;">Staff Tools</a>';
}
//=== rate topic \o/
if ($arr['num_ratings'] != 0) $rating = ROUND($arr['rating_sum'] / $arr['num_ratings'], 1);
//=== see if member is subscribed to topic
$res_subscriptions = sql_query('SELECT id FROM subscriptions WHERE topic_id=' . sqlesc($topic_id) . ' AND user_id=' . sqlesc($CURUSER['id']));
$row_subscriptions = mysqli_fetch_row($res_subscriptions);
$subscriptions = ($row_subscriptions[0] > 0 ? ' <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=delete_subscription&amp;topic_id=' . $topic_id . '"> 
		<img src="' . $INSTALLER09['pic_base_url'] . 'forums/unsubscribe.gif" alt="+" title="+" width="12" /> Unsubscribe from this topic</a>' : '<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=add_subscription&amp;forum_id=' . $forum_id . '&amp;topic_id=' . $topic_id . '">
		<img src="' . $INSTALLER09['pic_base_url'] . 'forums/subscribe.gif" alt="+" title="+" width="12" /> Subscribe to this topic</a>');
//=== who is here
sql_query('DELETE FROM now_viewing WHERE user_id =' . sqlesc($CURUSER['id']));
sql_query('INSERT INTO now_viewing (user_id, forum_id, topic_id, added) VALUES(' . sqlesc($CURUSER['id']) . ', ' . sqlesc($forum_id) . ', ' . sqlesc($topic_id) . ', ' . TIME_NOW . ')');
//=== now_viewing
$keys['now_viewing'] = 'now_viewing_topic';
if (($topic_users_cache = $mc1->get_value($keys['now_viewing'])) === false) {
    $topicusers = '';
    $topic_users_cache = array();
    $res = sql_query('SELECT n_v.user_id, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.perms FROM now_viewing AS n_v LEFT JOIN users AS u ON n_v.user_id = u.id WHERE topic_id = ' . sqlesc($topic_id)) or sqlerr(__FILE__, __LINE__);
    $actcount = mysqli_num_rows($res);
    while ($arr = mysqli_fetch_assoc($res)) {
        if ($topicusers) $topicusers.= ",\n";
        $topicusers.= ($arr['perms'] & bt_options::PERMS_STEALTH ? '<i>UnKn0wn</i>' : format_username($arr));
    }
    $topic_users_cache['topic_users'] = $topicusers;
    $topic_users_cache['actcount'] = $actcount;
    $mc1->cache_value($keys['now_viewing'], $topic_users_cache, $INSTALLER09['expires']['forum_users']);
}
if (!$topic_users_cache['topic_users']) $topic_users_cache['topic_users'] = 'There have been no active users in the last 15 minutes.';
//$forum_users = '&nbsp;('.$forum_users_cache['actcount'].')';
$topic_users = $topic_users_cache['topic_users'];
if ($topic_users != '') {
    $topic_users = 'Currently viewing this topic: ' . $topic_users;
}
//=== Update views column
sql_query('UPDATE topics SET views = views + 1 WHERE id=' . sqlesc($topic_id));
//=== must get count for pager... mini query
$res_count = sql_query('SELECT COUNT(id) AS count FROM posts WHERE ' . ($CURUSER['class'] < UC_STAFF ? 'status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'status != \'deleted\' AND' : '')) . ' topic_id=' . sqlesc($topic_id));
$arr_count = mysqli_fetch_row($res_count);
$count = $arr_count[0];
//=== get stuff for the pager
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$perpage = isset($_GET['perpage']) ? intval($_GET['perpage']) : 15;
$subscription_on_off = (isset($_GET['s']) ? ($_GET['s'] == 1 ? '<br /><div style="font-weight: bold;">Subscribed to topic <img src="' . $INSTALLER09['pic_base_url'] . 'forums/subscribe.gif" alt="Subscribed" title="Subscribed"  width="25" /></div>' : '<br /><div style="font-weight: bold;">Unsubscribed from topic <img src="' . $INSTALLER09['pic_base_url'] . 'forums/unsubscribe.gif" alt="Un-subscribe" title="Un-subscribe" width="25" /></div>') : '');
list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'forums.php?action=view_topic&amp;topic_id=' . $topic_id . (isset($_GET['perpage']) ? '&amp;perpage=' . $perpage : ''));
$res = sql_query('SELECT p.id AS post_id, p.topic_id, p.user_id, p.staff_lock, p.added, p.body, p.edited_by, p.edit_date, p.icon, p.post_title, p.bbcode, p.post_history, p.edit_reason, p.ip, p.status AS post_status, p.anonymous, u.seedbonus, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.chatpost, u.leechwarn, u.pirate, u.king, u.enabled, u.email, u.website, u.icq, u.msn, u.aim, u.yahoo, u.last_access, u.show_email, u.paranoia, u.hit_and_run_total, u.avatar, u.title, u.uploaded, u.downloaded, u.signature, u.google_talk, u.icq, u.msn, u.aim, u.yahoo, u.website, u.mood, u.perms, u.reputation FROM posts AS p LEFT JOIN users AS u ON p.user_id = u.id WHERE ' . ($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND' : '')) . ' topic_id=' . sqlesc($topic_id) . ' ORDER BY p.id ASC ' . $LIMIT) or sqlerr(__FILE__, __LINE__);
//=== make sure they can reply here
$may_post = ($CURUSER['class'] >= $arr['min_class_write'] && $CURUSER['forum_post'] == 'yes' && $CURUSER['suspended'] == 'no');
//=== reply button
$locked_or_reply_button = ($locked === 'yes' ? '<span style="font-weight: bold; font-size: x-small;"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/thread_locked.gif" alt="Thread Locked" title="Thread Locked" width="22" />This topic is locked, you may not post in this thread.</span>' : ($CURUSER['forum_post'] == 'no' ? '<span style="font-weight: bold; font-size: x-small;">Your posting rights have been removed. You may not post.</span>' : '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=post_reply&amp;topic_id=' . $topic_id . '" class="btn">Add Reply</a>'));
/*
    $quick_reply ="<table style='border:1px solid #000000;' align='center'>
      <tr>
		<td style='padding:10px;text-align:center;'>
		<b>Quick Reply</b>
		<form name='compose' method='post' action='forums.php?action=post_reply'>
		<input type='hidden' name='topic_id' value='".$topic_id."' />
		<textarea name='body' rows='4' cols='70'></textarea><br />
		<input type='submit' class='btn' value='Submit' /><br />
		<!--Anonymous<input type='checkbox' name='anonymous' value='yes' ".($CURUSER['anonymous'] == 'yes' ? "checked='checked'":'')." />-->
		</form></td></tr></table>";
*/
if ($arr['parent_forum'] > 0) {
    //=== now we need the parent forums stuff
    $parent_forum_res = sql_query('SELECT name AS parent_forum_name FROM forums WHERE id=' . sqlesc($arr['parent_forum']));
    $parent_forum_arr = mysqli_fetch_row($parent_forum_res);
    $child = ($arr['parent_forum'] > 0 ? '<span style="font-size: x-small;"> [ child-board ]</span>' : '');
    $parent_forum_name = '<img src="' . $INSTALLER09['pic_base_url'] . 'arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
		<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_forum&amp;forum_id=' . $forum_id . '">' . htmlsafechars($parent_forum_arr[0], ENT_QUOTES) . '</a>';
}
//=== top and bottom stuff
$the_top_and_bottom = '<tr><td class="three" width="33%" align="left" valign="middle">&nbsp;&nbsp;' . $subscriptions . '</td>
		<td class="three" width="33%" align="center">' . (($count > $perpage) ? $menu : '') . '</td>
		<td class="three" align="right">' . ($may_post ? $locked_or_reply_button : '<span style="font-weight: bold; font-size: x-small;">
		You are not permitted to post in this thread.</span>') . '</td></tr>';
$location_bar = '<a name="top"></a>' . $status_image . ' <a class="altlink" href="index.php">' . $INSTALLER09['site_name'] . '</a>  <img src="' . $INSTALLER09['pic_base_url'] . 'arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
			<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php">Forums</a> ' . $parent_forum_name . ' 
			<img src="' . $INSTALLER09['pic_base_url'] . 'arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
			<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_forum&amp;forum_id=' . $forum_id . '">' . $forum_name . $child . '</a>
			<img src="' . $INSTALLER09['pic_base_url'] . 'arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
			<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_topic&amp;topic_id=' . $topic_id . '">' . $topic_name . '</a> ' . $status_image . '<br />' . $forum_desc . '
			<span style="text-align: center;">' . $mini_menu . (($topic_owner == $CURUSER['id'] && $arr['poll_id'] == 0 || $CURUSER['class'] >= UC_STAFF && $arr['poll_id'] == 0) ? '  |<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=poll&amp;action_2=poll_add&amp;topic_id=' . $topic_id . '" class="altlink">&nbsp;Add Poll</a>' : '') . '</span><br /><br />';
$HTMLOUT.= ($upload_errors_size > 0 ? ($upload_errors_size === 1 ? '<div style="text-align: center;">One file was not uploaded. The maximum file size allowed is. ' . mksize($max_file_size) . '.</div>' : '<div style="text-align: center;">' . $upload_errors_size . ' file were not uploaded. The maximum file size allowed is. ' . mksize($max_file_size) . '.</div>') : '') . ($upload_errors_type > 0 ? ($upload_errors_type === 1 ? '<div style="text-align: center;">One file was not uploaded. The accepted formats are zip and rar.</div>' : '<div style="text-align: center;">' . $upload_errors_type . ' files were not uploaded. The accepted formats are zip and rar.</div>') : '') . $location_bar . $topic_poll . '<br />' . $subscription_on_off . '<br />
		' . ($CURUSER['class'] < UC_STAFF ? '' : '<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post" name="checkme" onsubmit="return SetChecked(this,\'post_to_mess_with\')" enctype="multipart/form-data">') . (isset($_GET['count']) ? '
		<div style="text-align: center;">' . intval($_GET['count']) . ' PMs Sent</div>' : '') . '
		<!--<table border="0" cellspacing="5" cellpadding="10" width="100%">-->
		<table border="0" cellspacing="0" cellpadding="4" width="100%">
		' . $the_top_and_bottom . '
		<tr><td class="forum_head_dark" align="left" width="100"> <img src="' . $INSTALLER09['pic_base_url'] . 'forums/topic_normal.gif" alt="Topic" title="Topic" />&nbsp;&nbsp;Author</td>
		<td class="forum_head_dark" align="left" colspan="2">&nbsp;&nbsp;Topic: ' . $topic_name . '  [ Read ' . $views . ' times ] </td></tr>
		<tr><td class="three" align="left" colspan="3">Topic rating: ' . (getRate($topic_id, "topic")) . '</td></tr>
      <tr><td class="three" align="left" colspan="3">' . $topic_users . '</td></tr>';
//=== lets start the loop \o/
while ($arr = mysqli_fetch_assoc($res)) {
    //=== change colors
    $colour = (++$colour) % 2;
    $class = ($colour == 0 ? 'one' : 'two');
    $class_alt = ($colour == 0 ? 'two' : 'one');
    $moodname = (isset($mood['name'][$arr['mood']]) ? htmlsafechars($mood['name'][$arr['mood']]) : 'is feeling neutral');
    $moodpic = (isset($mood['image'][$arr['mood']]) ? htmlsafechars($mood['image'][$arr['mood']]) : 'noexpression.gif');
    $post_icon = ($arr['icon'] !== '' ? '<img src="' . $INSTALLER09['pic_base_url'] . 'smilies/' . htmlsafechars($arr['icon']) . '.gif" alt="icon" title="icon" /> ' : '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/topic_normal.gif" alt="icon" title="icon" /> ');
    $post_title = ($arr['post_title'] !== '' ? ' <span style="font-weight: bold; font-size: x-small;">' . htmlsafechars($arr['post_title'], ENT_QUOTES) . '</span>' : '');
    $stafflocked = ( /*$CURUSER['class'] == UC_SYSOP && */
    $arr["staff_lock"] == 1 ? "<img src='{$INSTALLER09['pic_base_url']}locked.gif' border='0' alt='Post Locked' title='Post Locked' />" : "");
    $member_reputation = $arr['username'] != '' ? get_reputation($arr, 'posts') : '';
    $edited_by = '';
    if ($arr['edit_date'] > 0) {
        $res_edited = sql_query('SELECT username FROM users WHERE id=' . sqlesc($arr['edited_by']));
        $arr_edited = mysqli_fetch_assoc($res_edited);
        //== Anonymous
        if ($arr['anonymous'] == 'yes') {
            if ($CURUSER['class'] < UC_STAFF && $arr['user_id'] != $CURUSER['id']) $edited_by = '<br /><br /><br /><span style="font-weight: bold; font-size: x-small;">Last edited by Anonymous
				 at ' . get_date($arr['edit_date'], '') . ' GMT ' . ($arr['edit_reason'] !== '' ? ' </span>[ Reason: ' . htmlsafechars($arr['edit_reason']) . ' ] <span style="font-weight: bold; font-size: x-small;">' : '') . '
				 ' . (($CURUSER['class'] >= UC_STAFF && $arr['post_history'] !== '') ? ' <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_post_history&amp;post_id=' . (int)$arr['post_id'] . '&amp;forum_id=' . $forum_id . '&amp;topic_id=' . $topic_id . '">read post history</a></span><br />' : '</span>');
            else $edited_by = '<br /><br /><br /><span style="font-weight: bold; font-size: x-small;">Last edited by Anonymous [<a class="altlink" href="userdetails.php?id=' . (int)$arr['edited_by'] . '">' . htmlsafechars($arr_edited['username']) . '</a>]
				 at ' . get_date($arr['edit_date'], '') . ' GMT ' . ($arr['edit_reason'] !== '' ? ' </span>[ Reason: ' . htmlsafechars($arr['edit_reason']) . ' ] <span style="font-weight: bold; font-size: x-small;">' : '') . '
				 ' . (($CURUSER['class'] >= UC_STAFF && $arr['post_history'] !== '') ? ' <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_post_history&amp;post_id=' . (int)$arr['post_id'] . '&amp;forum_id=' . $forum_id . '&amp;topic_id=' . $topic_id . '">read post history</a></span><br />' : '</span>');
        } else {
            $edited_by = '<br /><br /><br /><span style="font-weight: bold; font-size: x-small;">Last edited by <a class="altlink" href="userdetails.php?id=' . (int)$arr['edited_by'] . '">' . htmlsafechars($arr_edited['username']) . '</a>
				 at ' . get_date($arr['edit_date'], '') . ' GMT ' . ($arr['edit_reason'] !== '' ? ' </span>[ Reason: ' . htmlsafechars($arr['edit_reason']) . ' ] <span style="font-weight: bold; font-size: x-small;">' : '') . '
				 ' . (($CURUSER['class'] >= UC_STAFF && $arr['post_history'] !== '') ? ' <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_post_history&amp;post_id=' . (int)$arr['post_id'] . '&amp;forum_id=' . $forum_id . '&amp;topic_id=' . $topic_id . '">read post history</a></span><br />' : '</span>');
        }
        //==
        
    }
    //==== highlight for search
    $body = ($arr['bbcode'] == 'yes' ? format_comment($arr['body']) : format_comment_no_bbcode($arr['body']));
    if (isset($_GET['search'])) {
        $body = highlightWords($body, $search);
        $post_title = highlightWords($post_title, $search);
    }
    $post_id = (int)$arr['post_id'];
    //=== if there are attachments, let's get them!
    $attachments_res = sql_query('SELECT id, file_name, extension, size FROM attachments WHERE post_id =' . sqlesc($post_id) . ' AND user_id = ' . sqlesc($arr['id']));
    if (mysqli_num_rows($attachments_res) > 0) {
        $attachments = '<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td class="' . $class . '" align="left"><span style="font-weight: bold;">Attachments:</span><hr />';
        while ($attachments_arr = mysqli_fetch_assoc($attachments_res)) {
            $attachments.= '<span style="white-space:nowrap;">' . ($attachments_arr['extension'] === 'zip' ? ' <img src="' . $INSTALLER09['pic_base_url'] . 'forums/zip.gif" alt="Zip" title="Zip" width="18" style="vertical-align: middle;" /> ' : ' <img src="' . $INSTALLER09['pic_base_url'] . 'forums/rar.gif" alt="Rar" title="Rar" width="18" /> ') . ' 
					<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=download_attachment&amp;id=' . (int)$attachments_arr['id'] . '" title="Download Attachment" target="_blank">
					' . htmlsafechars($attachments_arr['file_name']) . '</a> <span style="font-weight: bold; font-size: xx-small;">[' . mksize($attachments_arr['size']) . ']</span>&nbsp;&nbsp;</span>';
        }
        $attachments.= '</td></tr></table>';
    }
    $width = 300;
    $height = 100;
    //=== signature stuff
    $signature = ($CURUSER['signatures'] == 'no' ? '' : ($arr['signature'] == '' ? '' : ($arr['anonymous'] == 'yes' || $arr['perms'] & bt_options::PERMS_STEALTH ? '<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td class="' . $class . '" align="left"><hr /><img style="max-width:' . $width . 'px;max-height:' . $height . 'px;" src="' . $INSTALLER09['pic_base_url'] . 'anonymous_2.jpg" alt="Signature" /></td></tr></table>' : '<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td class="' . $class . '" align="left"><hr />' . format_comment($arr['signature']) . '</td></tr></table>')));
    //=== post status
    $post_status = htmlsafechars($arr['post_status']);
    switch ($post_status) {
    case 'ok':
        $post_status = $class;
        break;

    case 'recycled':
        $post_status = 'recycled';
        break;

    case 'deleted':
        $post_status = 'deleted';
        break;

    case 'postlocked':
        $post_status = 'postlocked';
        break;
    }
    $width = 100;
    $HTMLOUT.= '<tr><td class="' . $class . '" align="left" valign="top" colspan="3"><table border="0" cellspacing="5" cellpadding="10" width="100%"><tr><td class="forum_head" align="left" width="100" valign="middle">
			<span style="white-space:nowrap;"><a name="' . $post_id . '"></a>
			' . ($CURUSER['class'] >= UC_STAFF ? '<input type="checkbox" name="post_to_mess_with[]" value="' . $post_id . '" />' : '') . '
			<a href="javascript:window.alert(\'Direct link to this post:\n ' . $INSTALLER09['baseurl'] . '/forums.php?action=view_topic&amp;topic_id=' . $topic_id . '&amp;page=' . $page . '#' . $post_id . '\');">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/link.gif" alt="Direct link to this post" title="Direct link to this post" width="12px" /></a>
			<span style="font-weight: bold;">' . ($arr['anonymous'] == 'yes' ? '<i>Anonymous</i>' : '' . htmlsafechars($arr['username']) . '') . '&nbsp;</span>
			<!-- Mood -->
         <span class="tool"><a href="javascript:;" onclick="PopUp(\'usermood.php\',\'Mood\',530,500,1,1);"><img src="' . $INSTALLER09['pic_base_url'] . 'smilies/' . $moodpic . '" alt="' . $moodname . '" border="0" />
      <span class="tip">' . ($arr['anonymous'] == 'yes' ? '<i>Anonymous</i>' : htmlsafechars($arr['username'])) . ' ' . $moodname . ' !</span></a>&nbsp;</span>
			' . (($arr['paranoia'] >= 2 && $CURUSER['class'] < UC_STAFF) ? '<img src="' . $INSTALLER09['pic_base_url'] . 'smilies/tinfoilhat.gif" alt="I wear a tin-foil hat!" title="I wear a tin-foil hat!" />' : get_user_ratio_image($arr['uploaded'], ($INSTALLER09['ratio_free'] ? "0" : $arr['downloaded']))) . '</span>
			</td>
			<td class="forum_head" align="left" valign="middle"><span style="white-space:nowrap;">' . $post_icon . $post_title . '&nbsp;&nbsp;&nbsp;&nbsp; posted on: ' . get_date($arr['added'], '') . ' [' . get_date($arr['added'], '', 0, 1) . ']</span></td>
			<td class="forum_head" align="right" valign="middle"><span style="white-space:nowrap;"> 
			<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=post_reply&amp;topic_id=' . $topic_id . '&amp;quote_post=' . $post_id . '&amp;key=' . $arr['added'] . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/quote.gif" alt="Quote" title="Quote" /> Quote</a>
			' . (($CURUSER['class'] >= UC_STAFF || $CURUSER['id'] == $arr['id']) ? ' <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=edit_post&amp;post_id=' . $post_id . '&amp;topic_id=' . $topic_id . '&amp;page=' . $page . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/modify.gif" alt="Modify" title="Modify" /> Modify</a> 
			 <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=delete_post&amp;post_id=' . $post_id . '&amp;topic_id=' . $topic_id . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete.gif" alt="Delete" title="Delete" /> Remove</a>' : '') . '
			 <!--<a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=report_post&amp;topic_id=' . $topic_id . '&amp;post_id=' . $post_id . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/report.gif" alt="Report" title="Report" width="22" /> Report</a>-->
			 <a href="' . $INSTALLER09['baseurl'] . '/report.php?type=Post&amp;id=' . $post_id . '&amp;id_2=' . $topic_id . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/report.gif" alt="Report" title="Report" width="22" /> Report</a>
	     ' . ($CURUSER['class'] == UC_MAX && $arr['staff_lock'] == 1 ? '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_lock&amp;mode=unlock&amp;post_id=' . $post_id . '&amp;topic_id=' . $topic_id . '"><img src="' . $INSTALLER09['pic_base_url'] . 'key.gif" alt="Un Lock" title="Un Lock" /> UnLock post</a>&nbsp;' : '') . '
			 ' . ($CURUSER['class'] == UC_MAX && $arr['staff_lock'] == 0 ? '<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_lock&amp;mode=lock&amp;post_id=' . $post_id . '&amp;topic_id=' . $topic_id . '"><img src="' . $INSTALLER09['pic_base_url'] . 'key.gif" alt="Lock" title="Lock" /> Lock post</a>' : '') . $stafflocked . '
			<a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_topic&amp;topic_id=' . $topic_id . '&amp;page=' . $page . '#top"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/up.gif" alt="top" title="Top" /></a> 
		  <a href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_topic&amp;topic_id=' . $topic_id . '&amp;page=' . $page . '#bottom"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/down.gif" alt="bottom" title="Bottom" /></a> 
			</span></td>
			</tr>	
			<tr>
         <td class="' . $class_alt . '" align="center" valign="top">' . ($arr['anonymous'] == 'yes' ? '<img style="max-width:' . $width . 'px;" src="' . $INSTALLER09['pic_base_url'] . 'anonymous_1.jpg" alt="avatar" />' : avatar_stuff($arr)) . '<br />
			' . ($arr['anonymous'] == 'yes' ? '<i>Anonymous</i>' : print_user_stuff($arr)) . ($arr['anonymous'] == 'yes' || $arr['title'] == '' ? '' : '<br /><span style=" font-size: xx-small;">[' . htmlsafechars($arr['title']) . ']</span>') . '<br />
			<span style="font-weight: bold;">' . ($arr['anonymous'] == 'yes' ? '' : get_user_class_name($arr['class'])) . '</span><br />
			' . ($arr['last_access'] > (TIME_NOW - 300) && $arr['perms'] < bt_options::PERMS_STEALTH ? ' <img src="' . $INSTALLER09['pic_base_url'] . 'online.gif" alt="Online" title="Online" border="0" /> Online' : ' <img src="' . $INSTALLER09['pic_base_url'] . 'offline.gif" border="0" alt="Offline" title="Offline" /> Offline') . '<br />
			Karma: ' . number_format($arr['seedbonus']) . '<br /><br />' . $member_reputation . '<br />' . ($arr['google_talk'] !== '' ? ' <a href="http://talkgadget.google.com/talkgadget/popout?member=' . htmlsafechars($arr['google_talk']) . '" title="click for google talk gadget"  target="_blank"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/google_talk.gif" alt="google_talk" /></a> ' : '') . ($arr['icq'] !== '' ? ' <a href="http://people.icq.com/people/&amp;uin=' . htmlsafechars($arr['icq']) . '" title="click to open icq page" target="_blank"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/icq.gif" alt="icq" /></a> ' : '') . ($arr['msn'] !== '' ? ' <a href="http://members.msn.com/' . htmlsafechars($arr['msn']) . '" target="_blank" title="click to see msn details"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/msn.gif" alt="msn" /></a> ' : '') . ($arr['aim'] !== '' ? ' <a href="http://aim.search.aol.com/aol/search?s_it=searchbox.webhome&amp;q=' . htmlsafechars($arr['aim']) . '" target="_blank" title="click to search on aim... you will need to have an AIM account!"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/aim.gif" alt="AIM" /></a> ' : '') . ($arr['yahoo'] !== '' ? ' <a href="http://webmessenger.yahoo.com/?im=' . htmlsafechars($arr['yahoo']) . '" target="_blank" title="click to open yahoo"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/yahoo.gif" alt="yahoo" /></a> ' : '') . '<br /><br />' . ($arr['website'] !== '' ? ' <a href="' . htmlsafechars($arr['website']) . '" target="_blank" title="click to go to website"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/website.gif" alt="website" /></a> ' : '') . ($arr['show_email'] == 'yes' ? ' <a href="mailto:' . htmlsafechars($arr['email']) . '"  title="click to email" target="_blank"><img src="' . $INSTALLER09['pic_base_url'] . 'email.gif" alt="email" width="25" /> </a>' : '') . '<br /><br />
			' . ($CURUSER['class'] >= UC_STAFF ? '   
			<ul class="makeMenu">
				<li>' . htmlsafechars($arr['ip']) . '
					<ul>
					<li><a href="https://ws.arin.net/whois/?queryinput=' . htmlsafechars($arr['ip']) . '" title="whois to find ISP info" target="_blank">IP whois</a></li>
					<li><a href="http://www.infosniper.net/index.php?ip_address=' . htmlsafechars($arr['ip']) . '" title="IP to map using InfoSniper!" target="_blank">IP to Map</a></li>
				</ul>
				</li>
			</ul>' : '') . '
			</td>
			<td class="' . $post_status . '" align="left" valign="top" colspan="2">' . $body . $edited_by . '</td></tr>
			<tr><td class="' . $class_alt . '" width="100"></td><td class="' . $class . '" align="left" valign="top" colspan="2">' . $signature . '</td></tr>
			<tr><td class="' . $class_alt . '" width="100"></td><td class="' . $class . '" align="left" valign="top" colspan="2">' . $attachments . '</td></tr>
			<tr><td class="' . $class_alt . '" align="right" valign="middle" colspan="3">' . (($arr['paranoia'] >= 1 && $CURUSER['class'] < UC_STAFF) ? '' : '
			<span style="color: green;"><img src="' . $INSTALLER09['pic_base_url'] . 'up.png" alt="uploaded" title="uploaded" /> ' . mksize($arr['uploaded']) . '</span>&nbsp;&nbsp;  
			' . ($INSTALLER09['ratio_free'] ? '' : '<span style="color: red;"><img src="' . $INSTALLER09['pic_base_url'] . 'dl.png" alt="downloaded" title="downloaded" /> ' . mksize($arr['downloaded']) . '</span>') . '&nbsp;&nbsp;') . (($arr['paranoia'] >= 2 && $CURUSER['class'] < UC_STAFF) ? '' : 'Ratio: ' . member_ratio($arr['uploaded'], $INSTALLER09['ratio_free'] ? '0' : $arr['downloaded']) . '&nbsp;&nbsp;
			' . ($arr['hit_and_run_total'] == 0 ? '<img src="' . $INSTALLER09['pic_base_url'] . 'no_hit_and_runs2.gif" width="22" alt="' . ($arr['anonymous'] == 'yes' ? 'Anonymous' : htmlsafechars($arr['username'])) . ' has never hit &amp; ran!" title="' . ($arr['anonymous'] == 'yes' ? 'Anonymous' : htmlsafechars($arr['username'])) . ' has never hit &amp; ran!" />' : '') . '
			&nbsp;&nbsp;&nbsp;&nbsp;') . '
			<a class="altlink" href="pm_system.php?action=send_message&amp;receiver=' . $arr['id'] . '&amp;returnto=' . urlencode($_SERVER['REQUEST_URI']) . '"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/send_pm.png" alt="Send Pm" title="Send Pm" width="18" /> Send Message</a></td></tr></table></td></tr>';
    $attachments = '';
} //=== end while loop
//=== update the last post read by CURUSER
sql_query('DELETE FROM `read_posts` WHERE user_id =' . sqlesc($CURUSER['id']) . ' AND `topic_id` = ' . sqlesc($topic_id));
sql_query('INSERT INTO `read_posts` (`user_id` ,`topic_id` ,`last_post_read`) VALUES (' . sqlesc($CURUSER['id']) . ', ' . sqlesc($topic_id) . ', ' . sqlesc($post_id) . ')');
$mc1->delete_value('last_read_post_' . $topic_id . '_' . $CURUSER['id']);
$mc1->delete_value('sv_last_read_post_' . $topic_id . '_' . $CURUSER['id']);
//=== set up jquery show hide here
//$HTMLOUT .= $the_top_and_bottom.'</table>'.$quick_reply.'
$HTMLOUT.= $the_top_and_bottom . '</table>
    <span style="text-align: center;">' . $location_bar . '</span><a name="bottom"></a>
    <br />' . ($CURUSER['class'] >= UC_STAFF ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/tools.png" alt="Tools" title="Tools" width="22" /> ' . $staff_link . ' <img src="' . $INSTALLER09['pic_base_url'] . 'forums/tools.png" alt="Tools" title="Tools" width="22" /><br /><br />
	 <div id="tools" style="display:none">
    <br />
    <table border="0" cellspacing="5" cellpadding="5" width="800" align="center">
	 <tr>
    <td class="forum_head_dark" colspan="4" align="center">Staff Tools</td>
		  </tr>
		  <tr>
			<td class="two" align="left" colspan="3">
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="hidden" name="forum_id" value="' . $forum_id . '" />
      <table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		  <tr>
			<td class="two" align="center" valign="middle" width="18">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/recycle_bin.gif" alt="Recycle" title="Recycle" width="22" /></td>
			<td class="two" align="left" valign="middle">
			<input type="radio" name="action_2" value="send_to_recycle_bin" />Send to Recycle Bin  <br />
			<input type="radio" name="action_2" value="remove_from_recycle_bin" />Remove from Recycle Bin 
			</td>
			<td class="two" align="center" valign="middle" width="18"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete.gif" alt="Delete" title="Delete" /></td>
			<td class="two" align="left" valign="middle">
			<input type="radio" name="action_2" value="delete_posts" />Delete
			' . ($CURUSER['class'] < $min_delete_view_class ? '' : '<br />
			<input type="radio" name="action_2" value="un_delete_posts" /><span style="font-weight:bold;color:red;">*</span>Un-Delete') . '
			</td>
			<td class="two" align="center" valign="middle" width="18">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/merge.gif" alt="Merge" title="Merge" /></td>
			<td class="two" align="left" valign="middle">
			<input type="radio" name="action_2" value="merge_posts" />Merge With<br />
			<input type="radio" name="action_2" value="append_posts" />Append To
			</td>
			<td class="two" align="left" valign="middle">
			Topic:<input type="text" size="2" name="new_topic" value="' . $topic_id . '" />
		  </td>
		  </tr>
	    </table>
      <table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		  <tr>
			<td class="two" align="center" valign="middle" width="18">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/split.gif" alt="Split" title="Split" width="18" /></td>
			<td class="two" align="left" valign="middle">
			<input type="radio" name="action_2" value="split_topic" />Split Topic
			</td>
			<td class="two" align="left" valign="middle">
			New Topic Name:<input type="text" size="20" maxlength="120" name="new_topic_name" value="' . ($topic_name !== '' ? $topic_name : '') . '" /> [required]<br />
			New Topic Desc:<input type="text" size="20" maxlength="120" name="new_topic_desc" value="" />
			</td>
			<td class="two" align="center" valign="middle" width="18"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/send_pm.png" alt="Send Pm" title="Send Pm" width="18" /></td>
			<td class="two" align="center" valign="middle">
			<a class="altlink"  title="Send PM to Selected Members - click" id="pm_open" style="font-weight:bold;cursor:pointer;">Send PM </a><br />[click]
			</td>
		  </tr>
	    </table>
      <div id="pm" style="display:none"><br />
      <table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		  <tr>
			<td class="forum_head_dark" align="left" colspan="2">Send Pm to Selected Members</td>
		  </tr>
		  <tr>
			<td class="three" align="right" valign="top">
		  <span style="font-weight: bold;">Subject:</span>
			</td>
			<td class="three" align="left" valign="top">
			<input type="text" size="20" maxlength="120" class="text_default" name="subject" value="" />
			<input type="radio" name="action_2" value="send_pm" />
			<span style="font-weight: bold;">Select to send.</span> 
			</td>
		  </tr>
		  <tr>
			<td class="three" align="right" valign="top">
			<span style="font-weight: bold;">Message:</span>
			</td>
			<td class="three" align="left" valign="top">
			<textarea cols="30" rows="4" name="message" class="text_area_small"></textarea>
			</td>
		  </tr>
		  <tr>
			<td class="three" align="right" valign="top">
			<span style="font-weight: bold;">From:</span>
			</td>
			<td class="three" align="left" valign="top">
			<input type="radio" name="pm_from" value="0" checked="checked" /> System  
			<input type="radio" name="pm_from" value="1" /> ' . print_user_stuff($CURUSER) . '
			</td>
      </tr>
      </table>
      </div>
      <hr /></td>
			<td class="two" align="center">
			<a class="altlink" href="javascript:SetChecked(1,\'post_to_mess_with[]\')" title="Select all posts and use the following options"> Select All</a> <br />
			<a class="altlink" href="javascript:SetChecked(0,\'post_to_mess_with[]\')" title="Un-select all posts">Un-Select All</a><br />
			<input type="submit" name="button" class="button" value="With Selected" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form></td>
      </tr>
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/pinned.gif" alt="Pinned" title="Pinned" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Pin Topic:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="set_pinned" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="radio" name="pinned" value="yes" ' . ($sticky === 'yes' ? 'checked="checked"' : '') . ' /> Yes  
			<input type="radio" name="pinned" value="no" ' . ($sticky === 'no' ? 'checked="checked"' : '') . ' /> No</td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Set Pinned" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form></td>
      </tr>
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/thread_locked.gif" alt="Locked" title="Locked" width="22" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Lock Topic:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="set_locked" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="radio" name="locked" value="yes" ' . ($locked === 'yes' ? 'checked="checked"' : '') . ' /> Yes  
			<input type="radio" name="locked" value="no" ' . ($locked === 'no' ? 'checked="checked"' : '') . ' /> No</td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Lock Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form></td>
      </tr>
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/move.gif" alt="Move" title="Move" width="22" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Move Topic:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="move_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<select name="forum_id">
			' . insert_quick_jump_menu($forum_id, $staff = true) . '</select></td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Move Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/modify.gif" alt="Modify" title="Modify" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Rename Topic:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="rename_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="text" size="40" maxlength="120" name="new_topic_name" value="' . ($topic_name !== '' ? $topic_name : '') . '" /></td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Rename Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/modify.gif" alt="Modify" title="Modify" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Change Topic Desc:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="change_topic_desc" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="text" size="40" maxlength="120" name="new_topic_desc" value="' . ($topic_desc1 !== '' ? $topic_desc1 : '') . '" /></td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Change Desc" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/merge.gif" alt="Merge" title="Merge" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Merge Topic:</span></td>
			<td class="two" align="left" valign="top">With topic # 
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="merge_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="text" size="4" name="topic_to_merge_with" value="' . $topic_id . '" /><br />
			Enter the destination  Topic Id to merge into<br />
			Topic ID can be found in the address bar above... the topic id for this thread is: ' . $topic_id . '<br />
			[This option will mix the two topics together, keeping dates and post numbers preserved.]</td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Merge Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/merge.gif" alt="Merge" title="Merge" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Append Topic:</span></td>
			<td class="two" align="left" valign="top">With topic # 
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="append_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="text" size="4" name="topic_to_append_into" value="' . $topic_id . '" /><br />
			Enter the destination  Topic Id to append to.<br />
			Topic ID can be found in the address bar above... the topic id for this thread is: ' . $topic_id . '<br />
			[This option will append this topic to the end of the new topic. The dates will be preserved, but the posts will be added after the last post in the appended to thread.]</td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Append Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28" valign="top">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/recycle_bin.gif" alt="Recycle" title="Recycle" width="22" /></td>
			<td class="two" align="right" valign="top">
			<span style="font-weight: bold;white-space:nowrap;">Move to Recycle Bin:</span></td>
			<td class="two" align="left" valign="top">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="move_to_recycle_bin" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="hidden" name="forum_id" value="' . $forum_id . '" />
			<input type="radio" name="status" value="yes" ' . ($status === 'recycled' ? 'checked="checked"' : '') . ' /> Yes  
			<input type="radio" name="status" value="no" ' . ($status !== 'recycled' ? 'checked="checked"' : '') . ' /> No<br />
			This option will send this thread to the hidden recycle bin for other staff to view it.<br />
			All subscriptions to this thread will be deleted!</td>
			<td class="two" align="center">
			<input type="submit" name="button" class="button" value="Recycle It" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>	
      <tr>
			<td class="two" align="center" width="28">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete.gif" alt="Delete" title="Delete" /></td>
			<td class="two" align="right">
			<span style="font-weight: bold;white-space:nowrap;">Delete Topic:</span></td>
			<td class="two" align="left">Are you really sure you want to delete this topic, and not just move it or merge it?</td>
			<td class="two" align="center">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="delete_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="submit" name="button" class="button" value="Delete Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>			
			' . ($CURUSER['class'] < $min_delete_view_class ? '' : '
      <tr>
			<td class="two" align="center" width="28">
			<img src="' . $INSTALLER09['pic_base_url'] . 'forums/delete.gif" alt="Delete" title="Delete" /></td>
			<td class="two" align="right">
			<span style="font-weight: bold;white-space:nowrap;"><span style="font-weight:bold;color:red;">*</span>Un-Delete Topic:</span></td>
			<td class="two" align="left"></td>
			<td class="two" align="center">
			<form action="' . $INSTALLER09['baseurl'] . '/forums.php?action=staff_actions" method="post">
			<input type="hidden" name="action_2" value="un_delete_topic" />
			<input type="hidden" name="topic_id" value="' . $topic_id . '" />
			<input type="submit" name="button" class="button" value="Un-Delete Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			</td>
      </tr>
      <tr>
			<td class="two" align="center" colspan="4"><span style="font-weight:bold;color:red;">*</span>
			only <span style="font-weight:bold;">' . get_user_class_name($min_delete_view_class) . '</span> and above can see these options!</td>
      </tr>') . '
      </table><br /></div>
      <script type="text/javascript" src="scripts/check_selected.js"></script>
      <script src="scripts/jquery.trilemma.js" type="text/javascript"></script>
      <script type="text/javascript">
      /*<![CDATA[*/
      $(function(){
      jQuery(\'.poll_select\').trilemma({max:' . $multi_options . ',disablelabels:true});
      });
      /*]]>*/
      </script>
      <script type="text/javascript">
      /*<![CDATA[*/
      $(document).ready(function()	{
      //=== show hide staff tools
      $("#tool_open").click(function() {
      $("#tools").slideToggle("slow", function() {
      });
      });
      //=== show hide voters
      $("#toggle_voters").click(function() {
      $("#voters").slideToggle("slow", function() {
      });
      });
      });
      //=== show hide send PM
      $("#pm_open").click(function() {
      $("#pm").slideToggle("slow", function() {
      });
      });
      /*]]>*/
      </script>
      ' : '');
?>
