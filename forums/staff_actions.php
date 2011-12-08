<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsource and TBDev and the many many 
coders who helped develope them over time.
proper credits to follow :)
beta sun aug 1st 2010 v0.1
Staff actions
should I add this to the admin folder?
Powered by Bunnies!!!
***************************************************************/

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

	//=== post  action posted so we know what to do :P
	$posted_staff_action = strip_tags((isset($_POST['action_2']) ? $_POST['action_2'] : ''));
	
	//=== add all possible actions here and check them to be sure they are ok
	$valid_staff_actions = array('delete_posts', 'un_delete_posts', 'split_topic', 'merge_posts', 'append_posts', 'send_to_recycle_bin', 'send_pm', 'set_pinned', 'set_locked', 'move_topic', 'rename_topic', 'change_topic_desc', 'merge_topic', 'move_to_recycle_bin', 'remove_from_recycle_bin', 'delete_topic', 'un_delete_topic');
	
	//=== check posted action, and if no match, kill it
	$staff_action = (in_array($posted_staff_action, $valid_staff_actions) ? $posted_staff_action : 1);
	
	if ($CURUSER['class'] < UC_STAFF)
	{
	stderr('Error', 'No access for you Mr. Fancy-Pants...');
	}	
	if ($staff_action == 1)
	{
	stderr('Error', 'No action selected!');
	}
	
	$post_id = (isset($_POST['post_id']) ? intval($_POST['post_id']) :  0);
	$topic_id = (isset($_POST['topic_id']) ? intval($_POST['topic_id']) :  0);
	$forum_id = (isset($_POST['forum_id']) ? intval($_POST['forum_id']) :  0);
	
	//=== stop any rogue staff tomfoolery  
	if ($topic_id > 0)
	{
	$res_check = sql_query('SELECT f.min_class_read FROM forums AS f LEFT JOIN topics AS t ON t.forum_id = f.id WHERE f.id = t.forum_id AND t.id = '.$topic_id);
	$arr_check = mysqli_fetch_row($res_check);
		
		if ($CURUSER['class'] < $arr_check[0])
		{
		stderr('Error', 'Bad ID.');
		}
	}
	
	

switch ($staff_action)
{
	//=== with selected
	case 'delete_posts':

if (isset($_POST['post_to_mess_with']))
{
   $_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
   $post_to_mess_with = array();    
    foreach ($_POST['post_to_mess_with'] as $var)
        $post_to_mess_with[] = intval($var);
        
    $post_to_mess_with = array_unique($post_to_mess_with);
    $posts_count = count($post_to_mess_with);
       
    if ($posts_count > 0)  
	{
		//=== if you want the un-delete option (only admin and up can see "deleted" posts)
		if ($delete_for_real  < 1)
		{
		sql_query('UPDATE posts SET status = \'deleted\' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
		}
		else
		{
		//=== if you just want the damned things deleted
		sql_query('DELETE FROM posts WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
	  $mc1->delete_value('last_posts_'.$CURUSER['class']);
		//=== re-do that last post thing ;)
		$res = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_id.' ORDER BY p.id DESC LIMIT 1');
		$arr = mysqli_fetch_assoc($res);
	
		sql_query('UPDATE topics SET last_post = '.$arr['id'].', post_count = post_count - '.$posts_count.' WHERE id = '.$topic_id);
		sql_query('UPDATE forums SET post_count = post_count - '.$posts_count.' WHERE id = '.$arr['forum_id']);
		}
    }
	else
	{
	stderr('Error', 'Nothing deleted!');
	}
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
}

	break;	
	
	case 'un_delete_posts': //=== only if you don't actuall delete posts in delete_posts
	
if (isset($_POST['post_to_mess_with']))
{
   $_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
   $post_to_mess_with = array();    
    foreach ($_POST['post_to_mess_with'] as $var)
        $post_to_mess_with[] = intval($var);
        
    $post_to_mess_with = array_unique($post_to_mess_with);
    $posts_count = count($post_to_mess_with);
       
    if ($posts_count > 0)  
	{
	sql_query('UPDATE posts SET status = \'ok\' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
  $mc1->delete_value('last_posts_'.$CURUSER['class']);
  }
	else
	{
	stderr('Error', 'Nothing removed from the trash!');
	}
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
}

	break;
	
	case 'split_topic':
	
    if (!is_valid_id($topic_id) || !is_valid_id($forum_id))
    {
	stderr('Error', 'Bad ID.');
    }	

	$new_topic_name = strip_tags((isset($_POST['new_topic_name']) ? trim($_POST['new_topic_name']) : ''));
	$new_topic_desc = strip_tags((isset($_POST['new_topic_desc']) ? trim($_POST['new_topic_desc']) : ''));
	
	if ($new_topic_name === '')
	{
	stderr('Error', 'To split this topic, you must supply a name for the new topic!');
	} 
	
if (isset($_POST['post_to_mess_with']))
{
	//=== make the new topic:
	sql_query('INSERT INTO topics (topic_name, forum_id, topic_desc) VALUES ('.sqlesc($new_topic_name).', '.$forum_id.', '.sqlesc($new_topic_desc).')');
    $new_topic_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	
   $_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
   $post_to_mess_with = array();    
    foreach ($_POST['post_to_mess_with'] as $var)
        $post_to_mess_with[] = intval($var);
        
    $post_to_mess_with = array_unique($post_to_mess_with);
    $posts_count = count($post_to_mess_with);
       
    if ($posts_count > 0)  
	{
	//=== move posts to new topic
	sql_query('UPDATE posts SET topic_id = '.$new_topic_id.' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);

	//=== update post counts... topic split FROM
	$res_split_from = sql_query('SELECT p.id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_id.' ORDER BY p.id DESC LIMIT 1');
	$arr_split_from = mysqli_fetch_row($res_split_from);
	
	sql_query('UPDATE topics SET last_post = '.$arr_split_from[0].', post_count = post_count - '.$posts_count.' WHERE id = '.$topic_id);
			
	//=== update post counts... new topic from split
	$res_split_to = sql_query('SELECT p.id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$new_topic_id.' ORDER BY p.id DESC LIMIT 1');
	$arr_split_to = mysqli_fetch_row($res_split_to);
	
	//=== get topic owner for new split topic based on first poster in new topic
	$res_owner = sql_query('SELECT p.user_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$new_topic_id.' ORDER BY p.id ASC LIMIT 1');
	$arr_owner = mysqli_fetch_row($res_owner);
	
	sql_query('UPDATE topics SET last_post = '.$arr_split_to[0].', post_count = '.$posts_count.', user_id = '.$arr_owner[0].' WHERE id = '.$new_topic_id);
	
    }
	else
	{
	stderr('Error', 'Topic not split!');
	}
	header('Location: forums.php?action=view_topic&topic_id='.$new_topic_id); 
	die();	
}

	break;
	
	case 'merge_posts':
	
	$topic_to_merge_with = (isset($_POST['new_topic']) ? intval($_POST['new_topic']) :  0);

	//=== make sure there is a topic to merge with
	$topic_res = sql_query('SELECT id  FROM topics WHERE id = '.$topic_to_merge_with);
	$topic_arr = mysqli_fetch_row($topic_res);
	
    if (!is_valid_id($topic_arr[0]))
    {
	stderr('Error', 'Bad ID.');
    }
	
		if (isset($_POST['post_to_mess_with']))
		{
		$_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
			$post_to_mess_with = array();    
			foreach ($_POST['post_to_mess_with'] as $var)
				$post_to_mess_with[] = intval($var);
        
			$post_to_mess_with = array_unique($post_to_mess_with);
			$posts_count = count($post_to_mess_with);
       
			if ($posts_count > 0)  
			{
			sql_query('UPDATE posts SET topic_id = '.$topic_to_merge_with.' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
			
			//=== update post counts... topic merged FROM
			$res_from = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_id.' ORDER BY p.id DESC LIMIT 1');
			$arr_from = mysqli_fetch_assoc($res_from);
	
			sql_query('UPDATE topics SET last_post = '.$arr_from['id'].', post_count = post_count - '.$posts_count.' WHERE id = '.$topic_id);
			sql_query('UPDATE forums SET post_count = post_count - '.$posts_count.' WHERE id = '.$arr_from['forum_id']);
			
			//=== update post counts... topic merged INTO
			$res_to = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_to_merge_with.' ORDER BY p.id DESC LIMIT 1');
			$arr_to = mysqli_fetch_assoc($res_to);
	
			sql_query('UPDATE topics SET last_post = '.$arr_to['id'].', post_count = post_count + '.$posts_count.' WHERE id = '.$topic_to_merge_with);
			sql_query('UPDATE forums SET post_count = post_count + '.$posts_count.' WHERE id = '.$arr_to['forum_id']);
			}
			else
			{
			stderr('Error', 'Posts were NOT merged!');
			}
		header('Location: forums.php?action=view_topic&topic_id='.$topic_to_merge_with); 
		die();	
		}
	
	break;
	
	case 'append_posts':

	$topic_to_append_to = (isset($_POST['new_topic']) ? intval($_POST['new_topic']) :  0);
	
	//=== make sure there is a topic to append to
	$topic_res = sql_query('SELECT id  FROM topics WHERE id = '.$topic_to_append_to);
	$topic_arr = mysqli_fetch_row($topic_res);
	
    if (!is_valid_id($topic_arr[0]))
    {
	stderr('Error', 'Bad ID.');
    }
	
		if (isset($_POST['post_to_mess_with']))
		{
		$_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
		
			$post_to_mess_with = array();   
			$count = 0;
			foreach ($_POST['post_to_mess_with'] as $var)
			{
			$post_to_mess_with = intval($var);
			
			//=== get current post info
			$post_res = sql_query('SELECT * FROM posts WHERE id = '.$post_to_mess_with);
			$post_arr = mysqli_fetch_array($post_res);
			
			sql_query('INSERT INTO posts (`topic_id`, `user_id`, `added`, `body`, `edited_by`, `edit_date`, `icon`, `post_title`, `bbcode`, `post_history`, `edit_reason`, `ip`, `status`) VALUES 
						('.$topic_to_append_to.', '.$post_arr['user_id'].', '.$post_arr['added'].', '.sqlesc($post_arr['body']).', '.$post_arr['edited_by'].', '.$post_arr['edit_date'].', 
						'.sqlesc($post_arr['icon']).', '.sqlesc($post_arr['post_title']).', '.sqlesc($post_arr['bbcode']).', '.sqlesc($post_arr['post_history']).', 
						'.sqlesc($post_arr['edit_reason']).', '.sqlesc($post_arr['ip']).', '.sqlesc($post_arr['status']).')');
						
			$count = $count + 1;
			sql_query('DELETE FROM posts  WHERE id = '.$post_to_mess_with.' AND topic_id = '.$topic_id);
			}
			
			//=== and delete post and update counts and boum! done \o/
			if ($count > 0)
			{
			//=== update post counts... topic apended from
			$res_from = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_id.' ORDER BY p.id DESC LIMIT 1');
			$arr_from = mysqli_fetch_assoc($res_from);
			
			sql_query('UPDATE topics SET last_post = '.$arr_from['id'].', post_count = post_count - '.$count.' WHERE id = '.$topic_id);
			sql_query('UPDATE forums SET post_count = post_count - '.$count.' WHERE id = '.$arr_from['forum_id']);
			
			//=== update post counts... topic apended to
			$res_to = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_to_append_to.' ORDER BY p.id DESC LIMIT 1');
			$arr_to = mysqli_fetch_assoc($res_to);
	
			sql_query('UPDATE topics SET last_post = '.$arr_to['id'].', post_count = post_count + '.$count.' WHERE id = '.$topic_to_append_to);
			sql_query('UPDATE forums SET post_count = post_count + '.$count.' WHERE id = '.$arr_to['forum_id']);
	}

	header('Location: forums.php?action=view_topic&topic_id='.$topic_to_append_to); 
	die();
	}
	
	break;
	
	case 'send_to_recycle_bin':
	
if (isset($_POST['post_to_mess_with']))
{
   $_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
   $post_to_mess_with = array();    
    foreach ($_POST['post_to_mess_with'] as $var)
        $post_to_mess_with[] = intval($var);
        
    $post_to_mess_with = array_unique($post_to_mess_with);
    $posts_count = count($post_to_mess_with);
       
    if ($posts_count > 0)  
	{
	sql_query('UPDATE posts SET status = \'recycled\' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
    }
	else
	{
	stderr('Error', 'Nothing sent to recycle bin!');
	}
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
}

	break;
	
	case 'remove_from_recycle_bin':
	
if (isset($_POST['post_to_mess_with']))
{
   $_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
   $post_to_mess_with = array();    
    foreach ($_POST['post_to_mess_with'] as $var)
        $post_to_mess_with[] = intval($var);
        
    $post_to_mess_with = array_unique($post_to_mess_with);
    $posts_count = count($post_to_mess_with);
       
    if ($posts_count > 0)  
	{
	sql_query('UPDATE posts SET status = \'ok\' WHERE id IN ('.implode(', ', $post_to_mess_with).') AND topic_id = '.$topic_id);
    }
	else
	{
	stderr('Error', 'Nothing removed from the recycle bin!');
	}
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
}

	break;
	
//=== send_pm
	case 'send_pm':

	if (!is_valid_id($topic_id))
    {
	stderr('Error', 'Bad ID.');
    }

	$subject = strip_tags(isset($_POST['subject']) ? trim($_POST['subject']) : '');
    $message = (isset($_POST['message']) ? $_POST['message'] : '');
	$from = ((isset($_POST['pm_from']) && $_POST['pm_from'] == 0) ? 0 : $CURUSER['id']);

	if ($subject == '' || $message == '')
    {
	stderr('Error', 'You must enter both a subject and message.');
    }

		if (isset($_POST['post_to_mess_with']))
		{
		$_POST['post_to_mess_with'] = (isset($_POST['post_to_mess_with']) ? $_POST['post_to_mess_with'] : '');    
		
			$post_to_mess_with = array();   
			$count = 0;
			foreach ($_POST['post_to_mess_with'] as $var)
			{
			$post_to_mess_with = intval($var);
			
				//=== get user id to send to
				$post_res = sql_query('SELECT user_id FROM posts WHERE id = '.$post_to_mess_with);
				$post_arr = mysqli_fetch_row($post_res);

				sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, location, poster) 
								VALUES ('.$from.', '.$post_arr[0].', '.TIME_NOW.', '.sqlesc($message).', '.sqlesc($subject).', 1, '.$from.')');
				$count = $count + 1;
			}	
			
		}

	header('Location: forums.php?action=view_topic&topic_id='.$topic_id.'&count='.$count); 
	die();
	
	break;	
	
//=== Set Pinned
	case 'set_pinned':
	
	if (!is_valid_id($topic_id))
    {
	stderr('Error', 'Bad ID.');
    }
	
	sql_query('UPDATE topics SET sticky = \''.($_POST['pinned'] === 'yes' ? 'yes' : 'no').'\' WHERE id = '.$topic_id);
	
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();
	
	break;	

//=== Set Locked
	case 'set_locked':
	
	if (!is_valid_id($topic_id))
    {
	stderr('Error', 'Bad ID.');
    }
	
	sql_query('UPDATE topics SET locked = \''.($_POST['locked'] === 'yes' ? 'yes' : 'no').'\' WHERE id = '.$topic_id);
	
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();
	
	break;

//=== move topic
	case 'move_topic':
	
	//=== make sure there is a forum to move it to
	$res = sql_query('SELECT id FROM forums WHERE id = '.$forum_id);
	$arr = mysqli_fetch_row($res);
	
	if (!is_valid_id($arr[0]))
    {
	stderr('Error', 'Bad ID.');
    }	
	
	sql_query('UPDATE topics SET forum_id = '.$forum_id.' WHERE id = '.$topic_id);
	
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
	
	break;

//=== rename topic
	case 'rename_topic':
	
	$new_topic_name = strip_tags((isset($_POST['new_topic_name']) ? trim($_POST['new_topic_name']) : ''));
	
	if ($new_topic_name == '')
	{
	stderr('Error', 'If you want to rename the topic, you must supply a name!');
	} 
	
	sql_query('UPDATE topics SET topic_name = '.sqlesc($new_topic_name).' WHERE id = '.$topic_id);
	$mc1->delete_value('last_posts_'.$CURUSER['class']);
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
	
	break;
	
//===  change topic desc
	case 'change_topic_desc':
	
	$new_topic_desc = strip_tags((isset($_POST['new_topic_desc']) ? trim($_POST['new_topic_desc']) : ''));
	
	sql_query('UPDATE topics SET topic_desc = '.sqlesc($new_topic_desc).' WHERE id = '.$topic_id);
	$mc1->delete_value('last_posts_'.$CURUSER['class']);
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();
	
	break;	

//=== Merge topic
	case 'merge_topic':
	
	$topic_to_merge_with = (isset($_POST['topic_to_merge_with']) ? intval($_POST['topic_to_merge_with']) :  0);

	//=== make sure there is a topic to merge with & get post count
	$topic_res = sql_query('SELECT COUNT(p.id) AS count, t.id, t.forum_id FROM posts AS p LEFT JOIN topics AS t ON p.topic_id = t.id WHERE t.id = '.$topic_id.' GROUP BY p.topic_id');
	$topic_arr = mysqli_fetch_assoc($topic_res);
	$count = $topic_arr['count'];
	
    if (!is_valid_id($topic_arr['id']))
    {
	stderr('Error', 'Bad ID.');
    }
	
	//=== change all posts to new topic
	sql_query('UPDATE posts SET topic_id = '.$topic_to_merge_with.' WHERE topic_id = '.$topic_id);
	//=== change any subscriptions to the new topic
	sql_query('UPDATE subscriptions SET topic_id = '.$topic_to_merge_with.' WHERE topic_id = '.$topic_id);
	
	//=== update post counts / last post
	$res = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_to_merge_with.' ORDER BY p.id DESC LIMIT 1');
	$arr = mysqli_fetch_assoc($res);
	
	sql_query('UPDATE topics SET last_post = '.$arr['id'].', post_count = post_count + '.$count.' WHERE id = '.$topic_to_merge_with);
	
	//=== if topic merged with a topic in another forum
	if($topic_arr['forum_id'] != $arr['forum_id'])
	{
	sql_query('UPDATE forums SET post_count = post_count + '.$count.' WHERE id = '.$arr['forum_id']);
	sql_query('UPDATE forums SET post_count = post_count - '.$count.', topic_count = topic_count -1 WHERE id = '.$topic_arr['forum_id']);
	}
	else
	{
	sql_query('UPDATE forums SET topic_count = topic_count -1 WHERE id = '.$arr['forum_id']);
	}

	//=== delete the old topic
	sql_query('DELETE FROM topics  WHERE id = '.$topic_id);

	header('Location: forums.php?action=view_topic&topic_id='.$topic_to_merge_with); 
	die();
	
	break;

//=== move to recylebin
	case 'move_to_recycle_bin':
	
	$status = ($_POST['status'] == 'yes' ? 'recycled' : 'ok');
	
	sql_query('UPDATE topics SET status = \''.$status.'\' WHERE id = '.$topic_id);
	sql_query('DELETE FROM subscriptions WHERE topic_id = '.$topic_id);
	$mc1->delete_value('last_posts_'.$CURUSER['class']);
	//=== perhaps redirect to the bin lol
	header('Location: forums.php'.($_POST['status'] == 'yes' ? '?action=view_forum&forum_id='.$forum_id : '?action=view_topic&topic_id='.$topic_id)); 
	die();
	
	break;

//=== delete topic
	case 'delete_topic':
	
	//=== depending on settings, the topic can be set to  not really be deleted, OR they can just be deleted...
	
	//=== sanity check
	if (!isset($_POST['sanity_check']))
	{
	stderr('Sanity Check!', 'Are you sure you want to delete this topic? If you are sure, click the delete button.<br />
	<form action="forums.php?action=staff_actions" method="post">
	<input type="hidden" name="action_2" value="delete_topic" />
	<input type="hidden" name="sanity_check" value="1" />
	<input type="hidden" name="topic_id" value="'.$topic_id.'" />
	<input type="submit" name="button" class="button" value="Delete Topic" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
	</form>');
	}
	
	//=== if you want the un-delete option (only admin and up can see "deleted" posts)
	if ($delete_for_real  < 1)
	{
	sql_query('UPDATE topics SET status = \'deleted\' WHERE id = '.$topic_id);
	header('Location: forums.php'); 
	die();
	}
	else
	{
	//=== if you just want the damned things deleted
		
	//=== get post count of topic
	$res_count = sql_query('SELECT post_count, forum_id, poll_id FROM topics WHERE id = '.$topic_id);
	$arr_count = mysqli_fetch_assoc($res_count);
	
	//=== delete all the stuff
	sql_query('DELETE FROM subscriptions WHERE topic_id = '.$topic_id);
	sql_query('DELETE FROM forum_poll WHERE id = '.$arr_count['poll_id']);
	sql_query('DELETE FROM forum_poll_votes WHERE poll_id = '.$arr_count['poll_id']);
	sql_query('DELETE FROM topics WHERE id = '.$topic_id);
	sql_query('DELETE FROM posts WHERE topic_id = '.$topic_id);
	$mc1->delete_value('last_posts_'.$CURUSER['class']);
	//=== should I delete attachments? or let the members have a management page? or do it in cleanup?
	sql_query('UPDATE forums SET post_count = post_count - '.$arr_count['post_count'].', topic_count = topic_count - 1 WHERE id = '.$arr_count['forum_id']);
	
	header('Location: forums.php'); 
	die();
	}

	break;
	
//=== un_delete_topic
	case 'un_delete_topic':
	
	sql_query('UPDATE topics SET status = \'ok\' WHERE id = '.$topic_id);
	
	//=== get post count of topic
	$res_count = sql_query('SELECT post_count FROM topics WHERE id = '.$topic_id);
	$arr_count = mysqli_fetch_row($res_count);
	
	//=== should I delete attachments? or let the members have a management page? or do it in cleanup?
	sql_query('UPDATE forums SET post_count = post_count + '.$arr_count[0].', topic_count = topic_count + 1 WHERE id = '.$arr_count['forum_id']);
	$mc1->delete_value('last_posts_'.$CURUSER['class']);
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();
	
	break;
	
	}//=== ends switch
?>