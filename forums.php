<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/**********************************************************
New 2010 forums that don't suck for TB based sites....
mostly coded up either from scratch, or based on code from the following sources:
TBsource, BrokenStones, TBDev 
with inspiration from SMF forums, Google, php.net
Retro, System, CoLdFuSiOn, pdq, putyn, bigjoos, x0r, Laffin
and the many many other
coders who helped develop TB based sites and code over the years
and will be added when I have more of a brain :P
Beta Thurs Sept 9th 2010 v0.5
Powered by Bunnies!!!
***************************************************************/
define('BUNNY_FORUMS', TRUE);
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'function_rating.php');
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global'), load_language('forums') );

$stdhead = array(/** include css **/'css' => array('forums','jquery.lightbox-0.5','style','style2','bbcode','rating_style'));

$stdfoot = array(/** include js **/'js' => array('popup','jquery.lightbox-0.5.min','lightbox','sack'));

$over_forum_id = $count = $now_viewing = $child_boards = '';

if ($INSTALLER09['forums_online'] == 0 AND $CURUSER['class'] < UC_STAFF)
stderr('Information', 'The forums are currently offline for maintainance work');

if (function_exists('parked'))
parked();

$HTMLOUT='';
//=== update members last forums access 
sql_query('UPDATE users SET forum_access='.TIME_NOW.' WHERE id='.sqlesc($CURUSER['id']));
/*==============================
the following is 110% up to you...
you can set all the configuration stuff here in the forums.php main file,
or you can use the admin/forum_config.php.

hardcoding the vars here is a bit more secure, but some sites are administered 
without a coder being handy all the time, so I've added this option to the code :)

using the forum_config SQL and following the instructions will get you started with the defaults and the forums as I have them set up

the default is to use the config method... 
un-comment the next bit and set up the values below if you are gong to use hard coded method!
and comment out the following config DB method stuff
=====================================
IF you DON'T want to use the forum_config.php...

use the following and suit to your site:

	//=== Retros read post mod (sets all posts older then XX days to read, saves a huge bunch of DB space
	//=== I  just noticed that this is now a TBDEV global... I'll leave it here and in cleanup, as they are the only places it's used ***
	$readpost_expiry = 14 * 86400; //=== 14 days
	
	//=== stuff for file uploads
	$min_upload_class = UC_POWER_USER; 
	
	//=== if you change the following 2 lines,  you will need to change code in new_topic.php & post_reply.php & edit_post.php
	$accepted_file_extension  = array('.zip', '.rar'); 
	$accepted_file_types  = array('application/zip', 'application/rar');
	$max_file_size = 1024*1024*2; //=== 2 MB
	//=== name of your uploads folder must be writable chmod 777 will do
	$upload_folder = 'uploads/'; //===  you should rename this for security. or even beter put it outside your root dir :D	
	
============================================================ */
	//=== get config info from the DB (comment out and use hard coded if you prefer)
	$config_id = 13;
	$config_res = sql_query ('SELECT delete_for_real, min_delete_view_class, readpost_expiry, min_upload_class, accepted_file_extension, 
								accepted_file_types, max_file_size, upload_folder FROM forum_config WHERE id = '.sqlesc($config_id));
	$config_arr = mysqli_fetch_array($config_res);
	//=== all config stuff:
	$delete_for_real = ($config_arr['delete_for_real'] == 1 ? 1 : 0);
	$min_delete_view_class =  htmlsafechars($config_arr['min_delete_view_class']);
	$readpost_expiry = ((int)$config_arr['readpost_expiry'] * 86400);
	$min_upload_class = htmlsafechars($config_arr['min_upload_class']);
	$accepted_file_extension =  array($config_arr['accepted_file_extension']);
	$accepted_file_types =  array($config_arr['accepted_file_types']);
	$max_file_size = intval($config_arr['max_file_size']);
	$upload_folder =  htmlsafechars(trim($config_arr['upload_folder']));
	
	//=== post / get action posted so we know what to do :P
	$posted_action = strip_tags((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '')));

	//=== add all possible actions here and check them to be sure they are ok
	if ($CURUSER['class'] >= UC_STAFF)
	{
	$valid_actions = array('forum', 'view_forum', 'section_view', 'new_topic', 'view_topic', 'post_reply', 'delete_post', 'edit_post', 'subscriptions', 'delete_subscription', 'add_subscription', 'search', 'new_replies', 'view_unread_posts', 'view_my_posts', 'mark_all_as_read', 'clear_unread_post', 'download_attachment', 'poll', 'view_post_history', 'staff_actions', 'member_post_history', 'staff_lock');
	}
	else
	{
	$valid_actions = array('forum', 'view_forum', 'section_view', 'new_topic', 'view_topic', 'post_reply', 'delete_post', 'edit_post', 'subscriptions', 'delete_subscription', 'add_subscription', 'search', 'new_replies', 'view_unread_posts', 'view_my_posts', 'mark_all_as_read', 'clear_unread_post', 'download_attachment', 'poll', 'member_post_history');
	}

	//=== bubble tool tip
  function bubble($link, $text)
  {
  $bubble = '<a href="#" class="tt_f"><span class="tooltip_forum_bubble"><span class="top"></span><span class="middle">'.$text.'</span><span class="bottom"></span></span>'.$link.'</a>';
  return $bubble;
  }

  //=== tool tip
  function tool_tip($link, $text, $title = false)
  {
  $bubble = '<a href="#" class="tt_f2"><span class="tooltip_forum_tip"><span class="top">'.$title.'</span><span class="middle">'.$text.'</span></span>'.$link.'</a>';
  return $bubble;
  }
          
	
	//=== check posted action, and if no action was posted, show the default main forums page
	$action = (in_array($posted_action, $valid_actions) ? $posted_action : 'forum');
	
//=== some default global type stuff
//=== let admin and above delete shite
  if ($CURUSER['class'] >= UC_ADMINISTRATOR)
  {
  $HTMLOUT .="<script type='text/javascript'>
  /*<![CDATA[*/
  function confirm_delete(id)
  {
  if(confirm('Are you sure you want to delete this forum?'))
  {
  self.location.href='staffpanel.php?tool=forum_manage&action=delete&id='+id;
  }
  }
  /*]]>*/
  </script>";
  }
  $HTMLOUT .="<script type='text/javascript'>
	var e = new sack();
function do_rate(rate,id,what) {
		var box = document.getElementById('rate_'+id);
		e.setVar('rate',rate);
		e.setVar('id',id);
		e.setVar('ajax','1');
		e.setVar('what',what);
		e.requestFile = 'rating.php';
		e.method = 'GET';
		e.element = 'rate_'+id;
		e.onloading = function () {
			box.innerHTML = 'Loading ...'
		}
		e.onCompletion = function() {
			if(e.responseStatus)
				box.innerHTML = e.response();
		}
		e.onerror = function () {
			alert('That was something wrong with the request!');
		}
		e.runAJAX();
}
</script>";
//=== mini menu
$mini_menu = '<a class="altlink" href="forums.php?action=subscriptions">My Subscriptions</a> |
			<a class="altlink" href="forums.php?action=search">Search</a> |
			<a class="altlink" href="forums.php?action=view_unread_posts">Unread Posts</a> |
			<a class="altlink" href="forums.php?action=new_replies">New Replies</a> |
			<a class="altlink" href="forums.php?action=view_my_posts">My Posts</a> |
			<a class="altlink" href="forums.php?action=mark_all_as_read">Mark All As Read</a>';

$location_bar = '<h1><a class="altlink" href="index.php">'.$INSTALLER09['site_name'].'</a>  <img src="pic/forums/arrow_next.gif" alt="&#9658;" />
						<a class="altlink" href="forums.php">Forums</a></h1>
						<span style="text-align: center;">'.$mini_menu.'</span><br />'.(isset($_GET['m']) ? '<h1>All forums up to date.</h1>' : '<br />');

$legend = '<br /><!--<span style="text-align: center;">-->
    <table border="0" cellspacing="5" cellpadding="5" width="600" align="center">
		<tr>
		<td class="forum_head_dark" colspan="8" align="center">legend</td>
		</tr>
		<tr>
		<td class="one" align="center"><img src="pic/forums/unlockednew.gif" alt="unlockednew" title="Unlocked new"/></td>
		<td class="one">unread forum</td>
		<td class="one" align="center"><img src="pic/forums/unlocked.gif" alt="unlocked" title="Unlocked" /></td>
		<td class="one">read forum</td>
		<td class="one" align="center"><img src="pic/forums/topicnew.gif" alt="topicnew" title="New Topic" /></td>
		<td class="one">unread post</td>
		<td class="one" align="center"><img src="pic/forums/topic.gif" alt="topic" title="Topic" /></td>
		<td class="one">read post</td>
		</tr>
		<tr>
		<td class="one" align="center"><img src="pic/forums/hot_topic_new.gif" alt="hot_topic_new" title="Hot Topic New" /></td>
		<td class="one">hot topic un-read</td>
		<td class="one" align="center"><img src="pic/forums/hot_topic.gif" alt="hot_topic" title="Hot Topic" /></td>
		<td class="one">hot topic [more than 30 replies]<br /></td>
		<td class="one" align="center"><img src="pic/forums/lockednew.gif" alt="lockednew" title="Locked new"/></td>
		<td class="one">locked un-read</td>
		<td class="one" align="center"><img src="pic/forums/locked.gif" alt="locked" title="Locked" /></td>
		<td class="one">locked<br /></td>
		</tr>
		<tr>
		<td class="one" align="center"><img src="pic/forums/poll.gif" alt="poll" title="Poll" /></td>
		<td class="one">Poll</td>
		<td class="one" align="center"><img src="pic/forums/pinned.gif" alt="pinned" title="Pinned" /></td>
		<td class="one">Pinned<br /></td>
		<td class="one" align="center"><img src="pic/forums/subscriptions.gif" alt="Subscribed" title="Subscribed" /></td>
		<td class="one">Subscribed to thread</td>
		<td class="one" align="center"><img src="pic/forums/posted.gif" alt="posted" title="Posted" /></td>
		<td class="one">you have posted here<br /></td>
		</tr>
		<tr>
		<td class="one" align="center"><img src="pic/forums/mg.gif" height="20" alt="1st Post Preview" title="1st Post Preview" /></td>
		<td class="one">1st Post Preview<br /></td>
		<td class="one" align="center"><img src="pic/forums/last_post.gif" alt="last post" title="Last Post" /></td>
		<td class="one">Last Post</td>
		<td class="one" align="center"><img src="pic/forums/topic_normal.gif" alt="Thread Icon" title="Thread Icon" /></td>
		<td class="one">Thread Icon</td>
		<td class="one" align="center"></td>
		<td class="one"></td>
		</tr>
		</table><br />
		<h1 style="text-align: center;">
		<a href="http://btdev.net">
		<img src="pic/forums/powered_by_bunnies.gif" alt="These forums are powerd by bunnies!!!" title="These forums are powerd by bunnies!!!" /></a></h1><br /><!--</span>-->';


//=== more options poll & atachments
$poll_starts = (isset($_POST['poll_starts']) ? intval($_POST['poll_starts']) : 0);
$poll_ends = (isset($_POST['poll_ends']) ? intval($_POST['poll_ends']) : 1356048000);
$change_vote = ((isset($_POST['change_vote']) && $_POST['change_vote'] === 'yes') ? 'yes' : 'no');
$multi_options = (isset($_POST['multi_options']) ? intval($_POST['multi_options']) : 1);
//$can_add_poll = (isset($_GET['action']) && $_GET['action'] == 'new_topic' ? 1 : 0);

//=== options for amount of options lol
$options = '';
for($i = 2; $i < 21; $i++)
{
$options .='<option class="body" value="'.$i.'" '.($multi_options === $i ? 'selected="selected"' : '').'>'.$i.' options</option>';
}	

$more_options = '<div id="tools" '.((isset($_POST['poll_question']) && $_POST['poll_question'] !== '') ? '' : 'style="display:none"').' >
    	<br /><table border="0" cellspacing="0" cellpadding="5" width="800" align="center">
	<tr>
		<td  class="forum_head_dark" align="left" colspan="3">Additional Options...</td>
	</tr>
	'.($CURUSER['class'] < $min_upload_class ? '' : 
	'<tr>
		<td class="three" align="center" valign="top" width="10"><img src="pic/forums/attach.gif" alt=" " /></td>
		<td class="three" align="right" valign="top" width="20"><span style="white-space:nowrap;font-weight: bold;">Attachments:</span></td>
		<td class="three" align="left" valign="top">
		<input type="file" size="30" name="attachment[]" /> <a title="Add more attachments"  id="more" style="white-space:nowrap;font-weight:bold;cursor:pointer;">Add more attachments</a> 
		<img src="pic/forums/zip.gif" alt=" " width="18" style="vertical-align: middle;" />
		<img src="pic/forums/rar.gif" alt=" " width="18" style="vertical-align: middle;" /><br />
		<div id="attach_more" style="display:none">
		<input type="file" size="30" name="attachment[]" /><br /> 
		<input type="file" size="30" name="attachment[]" /><br /> 
		<input type="file" size="30" name="attachment[]" />
		</div>
		</td>
	</tr>').((isset($_GET['action']) && $_GET['action'] <> 'new_topic') ? '' :
	'<tr>
		<td class="three" align="center" valign="top" width="10"></td>
		<td class="three" align="right" valign="top" width="20"></td>
		<td class="three" align="left"><span style="white-space:nowrap;font-weight: bold;"> <img src="pic/forums/poll.gif" alt="" style="vertical-align: middle;" /> Add poll to topic</span>
		</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/question.png" alt=" " width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll question:</span></td>
		<td class="three" align="left"><input type="text" name="poll_question" class="text_default" value="'.(isset($_POST['poll_question']) ? strip_tags($_POST['poll_question']) : '').'" /></td>
	</tr>
	<tr>
		<td class="three" align="center" valign="top"><img src="pic/forums/options.gif" alt=" " width="24" style="vertical-align: middle;" /></td>
		<td class="three" align="right" valign="top"><span style="white-space:nowrap;font-weight: bold;">Poll answers:</span></td>
		<td class="three" align="left" valign="top"><textarea cols="30" rows="4" name="poll_answers" class="text_area_small">'.(isset($_POST['poll_answers']) ? strip_tags($_POST['poll_answers']) : '').'</textarea><br /> One option per line. There is a minimum of 2 options, and a maximun of 20 options. BBcode is enabled.</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/clock.png" alt=" " width="30" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll starts:</span></td>
		<td class="three" align="left"><select name="poll_starts">
											<option class="body" value="0" '.($poll_starts === 0 ? 'selected="selected"' : '').'>Start Now!</option>
											<option class="body" value="1" '.($poll_starts === 1 ? 'selected="selected"' : '').'>in 1 day</option>
											<option class="body" value="2" '.($poll_starts === 2 ? 'selected="selected"' : '').'>in 2 days</option>
											<option class="body" value="3" '.($poll_starts === 3 ? 'selected="selected"' : '').'>in 3 days</option>
											<option class="body" value="4" '.($poll_starts === 4 ? 'selected="selected"' : '').'>in 4 days</option>
											<option class="body" value="5" '.($poll_starts === 5 ? 'selected="selected"' : '').'>in 5 days</option>
											<option class="body" value="6" '.($poll_starts === 6 ? 'selected="selected"' : '').'>in 6 days</option>
											<option class="body" value="7" '.($poll_starts === 7? 'selected="selected"' : '').'>in 1 week</option>
											</select> When to start the poll. Default is "Start Now!"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/stop.png" alt=" " width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Poll ends:</span></td>
		<td class="three" align="left"><select name="poll_ends">
											<option class="body" value="1356048000" '.($poll_ends === 1356048000 ? 'selected="selected"' : '').'>Run Forever</option>
											<option class="body" value="1" '.($poll_ends === 1 ? 'selected="selected"' : '').'>in 1 day</option>
											<option class="body" value="2" '.($poll_ends === 2 ? 'selected="selected"' : '').'>in 2 days</option>
											<option class="body" value="3" '.($poll_ends === 3 ? 'selected="selected"' : '').'>in 3 days</option>
											<option class="body" value="4" '.($poll_ends === 4 ? 'selected="selected"' : '').'>in 4 days</option>
											<option class="body" value="5" '.($poll_ends === 5 ? 'selected="selected"' : '').'>in 5 days</option>
											<option class="body" value="6" '.($poll_ends === 6 ? 'selected="selected"' : '').'>in 6 days</option>
											<option class="body" value="7" '.($poll_ends === 7 ? 'selected="selected"' : '').'>in 1 week</option>
											<option class="body" value="14" '.($poll_ends === 14 ? 'selected="selected"' : '').'>in 2 weeks</option>
											<option class="body" value="21" '.($poll_ends === 21 ? 'selected="selected"' : '').'>in 3 weeks</option>
											<option class="body" value="28" '.($poll_ends === 28 ? 'selected="selected"' : '').'>in 1 month</option>
											<option class="body" value="56" '.($poll_ends === 56 ? 'selected="selected"' : '').'>in 2 months</option>
											<option class="body" value="84" '.($poll_ends === 84 ? 'selected="selected"' : '').'>in 3 months</option>
											</select> How long should this poll run? Default is "run forever"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"><img src="pic/forums/multi.gif" alt=" " width="20" style="vertical-align: middle;" /></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Multi options:</span></td>
		<td class="three" align="left"><select name="multi_options">
											<option class="body" value="1" '.($multi_options === 1 ? 'selected="selected"' : '').'>Single option!</option>
											'.$options.'
											</select> Allow members to have more then one selection? Default is "Single option!"</td>
	</tr>
	<tr>		
		<td class="three" align="center" valign="middle"></td>
		<td class="three" align="right"><span style="white-space:nowrap;font-weight: bold;">Change vote:</span></td>
		<td class="three" align="left"><input name="change_vote" value="yes" type="radio"'.($change_vote === 'yes' ? ' checked="checked"' : '').' />Yes 
													<input name="change_vote" value="no" type="radio"'.($change_vote === 'no' ? ' checked="checked"' : '').' />No   <br /> Allow members to change their vote? Default is "no"
	</td></tr>').'
	</table>
	</div>';
	
   $forum_id = (isset($_GET['forum_id']) ? intval($_GET['forum_id']) :  (isset($_POST['forum_id']) ? intval($_POST['forum_id']) :  0));
   
	 //=== print the bottom of the page  
    $the_bottom_of_the_page ='';
	 $the_bottom_of_the_page .=  insert_quick_jump_menu($forum_id).$legend;
	 $the_bottom_of_the_page .=  stdfoot($stdfoot);	
    //=== here we go with all the possibilities \\o\o/o//
    //=== will be sure to put these in order of most hit to make it a bit faster...
	switch ($action)
    {
    //=== view forum section
    case 'view_forum':
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'view_forum.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
	
    //=== view topic
    case 'view_topic':	
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'view_topic.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
      	
    //=== view  section
    case 'section_view':	
    require_once(FORUM_DIR.'section_view.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;	
		
    //===   poll stuff
    case 'poll':
    //require_once 'include/bbcode_functions.php';
    require_once(FORUM_DIR.'poll.php');
    break;
		
    //=== subscriptions add_subscription
    case 'subscriptions':      
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'subscriptions.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
      
    //===  add subscription 
    case 'add_subscription':      
    require_once(FORUM_DIR.'add_subscription.php');
    break;
	
    //===  add delete post
    case 'delete_post':      
    require_once(FORUM_DIR.'delete_post.php');
    break;
 
    //===  delete subscription
    case 'delete_subscription':      
    require_once(FORUM_DIR.'delete_subscription.php');
    break;
	  
    //=== new topic
    case 'new_topic':
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(FORUM_DIR.'new_topic.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
	
    //=== post reply
    case 'post_reply':
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(FORUM_DIR.'post_reply.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
	
    case 'search':
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'search.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;

    case 'view_unread_posts': 
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'view_unread_posts.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;

    case 'new_replies': 
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'new_replies.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;
	
    case 'view_my_posts': 
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'view_my_posts.php');
    $HTMLOUT .= $the_bottom_of_the_page;	
    break; 
	
    case 'member_post_history': 
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(INCL_DIR.'pager_new.php');
    require_once(FORUM_DIR.'member_post_history.php');
    $HTMLOUT .= $the_bottom_of_the_page;	
    break; 
	
    case 'mark_all_as_read': 
    require_once(FORUM_DIR.'mark_all_as_read.php');
    break;

    case 'download_attachment': 
    require_once(FORUM_DIR.'download_attachment.php');
    break;
	
    case 'clear_unread_post': 
    require_once(FORUM_DIR.'clear_unread_post.php');
    break;
	
    case 'edit_post':
    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(FORUM_DIR.'edit_post.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;

    case 'view_post_history':
	
    if ($CURUSER['class'] < UC_STAFF)
    {
    stderr('Error', 'No access for you Mr. Fancy-Pants.');
    }	

    require_once(INCL_DIR.'bbcode_functions.php');
    require_once(FORUM_DIR.'view_post_history.php');
    $HTMLOUT .= $the_bottom_of_the_page;
    break;

    case 'staff_actions':
	
    if ($CURUSER['class'] < UC_STAFF)
    {
    stderr('Error', 'No access for you Mr. Fancy-Pants.');
    }	

    require_once(FORUM_DIR.'staff_actions.php');
    break;
	
	 //===  staff lock
    case 'staff_lock':
    if ($CURUSER['class'] < UC_SYSOP)
    {
    stderr('Error', 'No access for you Mr. Fancy-Pants.');
    }	    
    require_once(FORUM_DIR.'stafflock_post.php');
    break;
	
    //=== default action / forums
    case 'forum':
/**********************************************************************
wanted to include this as another file, but keep geting errors lol...
it's the default forums.php page
If I can't figure out why it won't work as it's own file, then I'll move it to the top

***********************************************************************/
//=== some default stuff
//=== main huge query: 
$res_forums = sql_query('SELECT o_f.id AS over_forum_id, o_f.name AS over_forum_name, o_f.description AS over_forum_description, o_f.min_class_view AS over_forum_min_class_view, 
				f.id AS real_forum_id, f.name, f.description, f.post_count, f.topic_count,  f.forum_id
				FROM over_forums AS o_f JOIN forums AS f
				WHERE o_f.min_class_view <= '.$CURUSER['class'].'
				AND f.min_class_read <= '.$CURUSER['class'].' AND parent_forum = 0 
				ORDER BY o_f.sort, f.sort ASC');
  
	  $HTMLOUT .= $location_bar.'<br />
		<table border="0" cellspacing="0" cellpadding="5" width="95%">';

		//=== well... let's do the loop and make the damned page!
		while ($arr_forums = mysqli_fetch_assoc($res_forums))
		{
	//=== if it's a forums section print it, if not, list the fourm sections in it \o/
	$HTMLOUT .= ($arr_forums['over_forum_id'] != $over_forum_id ? 
	'<tr><td align="left" class="forum_head_dark" colspan="3">
	<a class="altlink" href="forums.php?action=section_view&amp;forum_id='.(int)$arr_forums['over_forum_id'].'" title="'.htmlsafechars($arr_forums['over_forum_description'], ENT_QUOTES).'"><span style="color: white;">'.htmlsafechars($arr_forums['over_forum_name'], ENT_QUOTES).'</span></a></td></tr>' : '');
			
		if ($arr_forums['forum_id'] == $arr_forums['over_forum_id'])
		{
		//=== change colors
		$count= (++$count)%2;
		$class = ($count == 0 ? 'one' : 'two');
		$forum_id = (int)$arr_forums['real_forum_id'];
		$forum_name = htmlsafechars($arr_forums['name'], ENT_QUOTES);
		$forum_description = htmlsafechars($arr_forums['description'], ENT_QUOTES);
		$topic_count = number_format($arr_forums['topic_count']);
		$post_count = number_format($arr_forums['post_count']);
		//=== Find last post ID
		$last_post_res = sql_query('SELECT t.id AS topic_id, t.topic_name, t.last_post, p.added, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.avatar_rights FROM topics AS t 
      LEFT JOIN posts AS p ON p.topic_id = t.id 
      RIGHT JOIN users AS u ON u.id = p.user_id 
      WHERE '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? ' t.status != \'deleted\' AND p.status != \'deleted\'  AND' : '')).' t.forum_id = '.sqlesc($forum_id).' ORDER BY p.id DESC LIMIT 1');
		$last_post_arr = mysqli_fetch_assoc($last_post_res);
		//=== only do more if there is a post there...
		if ($last_post_arr['last_post'] > 0)
		{
			$last_post_id = (int)$last_post_arr['last_post'];
			//=== get the last post read by CURUSER (with Retro's $readpost_expiry thingie)
  			$last_read_post_res = sql_query('SELECT last_post_read FROM read_posts WHERE user_id='.sqlesc($CURUSER['id']).' AND topic_id='.sqlesc($last_post_arr['topic_id']));
			$last_read_post_arr = mysqli_fetch_row($last_read_post_res);
      	$image_to_use = ($last_post_arr['added'] > (TIME_NOW - $readpost_expiry)) ? (!$last_read_post_arr OR $last_post_id > $last_read_post_arr[0]) : 0;
         $img = ($image_to_use ? 'unlockednew' : 'unlocked');
			$last_post = '<span style="white-space:nowrap;">Last Post by: '.($last_post_arr['username'] !== '' ? ''.print_user_stuff($last_post_arr).'' : 'Lost').' <span style="font-size: x-small;"> [ '.get_user_class_name($last_post_arr['class']).' ] </span><br />
			in &#9658; <a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.(int)$last_post_arr['topic_id'].'&amp;page='.$last_post_id.'#'.$last_post_id.'" title="'.htmlsafechars($last_post_arr['topic_name'], ENT_QUOTES).'">
			<span style="font-weight: bold;">'.CutName(htmlsafechars($last_post_arr['topic_name'], ENT_QUOTES), 30).'</span></a><br />
			'.get_date($last_post_arr['added'],'').'<br /></span>';
				//=== get child boards if any
				$child_boards_res = sql_query('SELECT name, id FROM forums WHERE parent_forum = '.sqlesc($arr_forums['real_forum_id']).' AND min_class_read <= '.sqlesc($CURUSER['class']).' ORDER BY sort ASC');
				$child_boards = '';
				while ($child_boards_arr = mysqli_fetch_assoc($child_boards_res))
				{
			  	if ($child_boards != '')
			  	$child_boards .= ', ';
			  	$child_boards .= '<a href="forums.php?action=view_forum&amp;forum_id='.(int)$child_boards_arr['id'].'" title="click to view!" class="altlink">'.htmlsafechars($child_boards_arr['name'], ENT_QUOTES).'</a>';
			  	}
			   if ($child_boards != '')
			   {
			   $child_boards = '<hr /><span style="font-size: xx-small;">child boards:</span> '.$child_boards;
			   }		  
				//=== now_viewing
				 $now_viewing_res = sql_query('SELECT n_v.user_id, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.avatar_rights FROM now_viewing AS n_v LEFT JOIN users AS u ON n_v.user_id = u.id WHERE forum_id = '.sqlesc($arr_forums['real_forum_id']));
				//=== let's see whos lookng in here...
				$now_viewing = '';
				while ($now_viewing_arr = mysqli_fetch_assoc($now_viewing_res))
				{
				$now_viewing .= print_user_stuff($now_viewing_arr);
			  	}

				if ($now_viewing != '')
				{
				$now_viewing = '<hr /><span style="font-size: xx-small;">now viewing:</span>'.$now_viewing;
				}
				$now_viewing = '';
		      } //=== end of only do more if there is a post there...
		else
		{
		$img = 'unlocked';
		$now_viewing = '';
		$last_post = 'N/A';
		}
		
		$HTMLOUT .= '<tr>
		<td align="left" class="'.$class.'">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td class="'.$class.'" align="left" width="30"><img src="pic/forums/'.$img.'.gif" alt="'.$img.'" title="Unlocked" /></td>
		<td width="100%" class="'.$class.'" align="left">
		'.bubble('<span style="font-weight: bold;"><a class="altlink" href="?action=view_forum&amp;forum_id='.(int)$arr_forums['real_forum_id'].'">
		'.$forum_name.'</a></span>', '<span style="font-size: x-small;">'.$forum_name.'</span>
		'.$forum_description).($CURUSER['class'] >= UC_ADMINISTRATOR ? '<span style="font-size: x-small;"> 
		[<a class="altlink" href="staffpanel.php?tool=forum_manage&amp;action=forum_manage&amp;action2=edit_forum_page&amp;id='.$forum_id.'">Edit</a>] 
		[<a class="altlink" href="javascript:confirm_delete(\''.$forum_id.'\');">Delete</a>]</span>' : '').'<br />
		<span style="font-size: x-small;"> '.$forum_description.'</span>'.$child_boards.$now_viewing.'</td>
		</tr>
		</table>
		</td>
		<td class="'.$class.'" align="center" width="80"><span style="font-size: x-small;">'.$post_count.' Posts<br />'.$topic_count.' Topics</span></td>
		<td class="'.$class.'" align="left" width="140"><span style="font-size: x-small;">'.$last_post.'</span></td>
		</tr>';	
	   } //== end of sectioon	
			
		$over_forum_id = (int)$arr_forums['over_forum_id'];
		$child_boards = '';
		} //=== end while loop!	
		
		$HTMLOUT .= '</table><br />'.$location_bar.insert_quick_jump_menu().'<br />';
		
	   //=== members active in forums
	   $active_members_res = sql_query('SELECT n_v.user_id, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.avatar_rights FROM now_viewing AS n_v LEFT JOIN users AS u ON n_v.user_id = u.id');	
	   //=== let's see whos lookng in here...
	   $now_viewing = '';
	   while ($active_members_arr = mysqli_fetch_assoc($active_members_res))
	   {
	   $now_viewing .= print_user_stuff($active_members_arr);
	   }

      $HTMLOUT .= '<table border="0" cellspacing="5" cellpadding="5" style="max-width:80%;min-width:600px;" align="center">
		<tr>
	   <td class="forum_head_dark" align="center">Members currently active</td>
		</tr>
		<tr>
		<td class="three" align="center">'.$now_viewing.'</td>
		</tr>
		</table><br />'.$legend.stdfoot($stdfoot);
	   break;
	   } //=== end switch


//=== all functions

	//=== search string highlighting by fusion found at stackoverflow.com :D
    function highlightWords($text, $words) {
        preg_match_all('~\w+~', $words, $m);
        if(!$m)
            return $text;
        $re = '~\\b(' . implode('|', $m[0]) . ')~i';
        $string = preg_replace($re, '<span style="color: black; background-color: yellow;font-weight: bold;">$0</span>', $text);
        return $string;
    }
	
//=== not currently used, but most likely will delete and go with a jquery based thing
function ratingpic_forums($num) 
{
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return '<img src="pic/forums/rating/'.$r.'.gif" alt="rating: '.$num.' / 5" />';
}

//=== Inserts a quick jump menu ......UPDATED!  now used for staff stuff too \o/
    function insert_quick_jump_menu($current_forum = 0, $staff = false)
    {
    global $CURUSER;
    $switch='';
    $quick_jump_menu = ($staff === false ? '
				<table><tr><td>
				<form method="get" action="forums.php" name="jump">
				<span style="text-align: center;font-weight: bold;">
				<input type="hidden" name="action" value="view_forum" />Quick jump: 
				<select name="forum_id" onchange="if(this.options[this.selectedIndex].value != -1){ forms[\'jump\'].submit() }">
				<option class="head" value="0"> Select a forum to jump to</option>' : '');
    $res = sql_query('SELECT f.id, f.name, f.parent_forum, f.min_class_read, of.name AS over_forum_name FROM forums AS f LEFT JOIN over_forums AS of ON f.forum_id = of.id ORDER BY of.sort, f.parent_forum, f.sort ASC');
    if (mysqli_num_rows($res) > 0)
    {
    while ($arr = mysqli_fetch_array($res))
    {
     
      if ($CURUSER['class'] >= $arr['min_class_read'])
      {
	    if ($switch !== $arr['over_forum_name'])
	    {
	    $quick_jump_menu .= '<option class="head" value="-1"> '.$arr['over_forum_name'] . ' </option>';
	    }
		  $switch = $arr['over_forum_name'];
      $quick_jump_menu .= '<option class="body" value="'.(int)$arr['id'].'">'.($arr['parent_forum'] != 0 ? '&#176; '.htmlsafechars($arr['name']).' [ child-board ]' : htmlsafechars($arr['name'])).'</option>';
      }
    }
    }
    $quick_jump_menu .= ($staff === false ? '</select></span></form></td></tr></table><br />' : '');
    return $quick_jump_menu;
    }

echo stdhead($INSTALLER09['site_name'].' Forums', true, $stdhead) . $HTMLOUT;
?>
