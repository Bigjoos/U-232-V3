<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsourse and TBDev and the many many 
coders who helped develop them over time.
proper credits to follow :)

beta fri june 11th 2010 v0.1
delete post... thinking of changing this...

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

	$post_id = (isset($_GET['post_id']) ? intval($_GET['post_id']) :  (isset($_POST['post_id']) ? intval($_POST['post_id']) :  0));
	$topic_id = (isset($_GET['topic_id']) ? intval($_GET['topic_id']) :  (isset($_POST['topic_id']) ? intval($_POST['topic_id']) :  0));
	$sanity_check = (isset($_GET['sanity_check']) ? intval($_GET['sanity_check']) :  0);
   
    if (!is_valid_id($post_id) || !is_valid_id($topic_id))
    {
	stderr('Error', 'Bad ID.');
    }	

	//=== make sure it's their post or they are staff... this may change
	$res_post = sql_query('SELECT p.user_id, p.staff_lock, u.id, u.class, u.suspended, t.locked, t.user_id AS owner_id, t.first_post, f.min_class_read, f.min_class_write, f.id AS forum_id 
							FROM posts AS p LEFT JOIN users AS u ON p.user_id = u.id LEFT JOIN topics AS t ON t.id = p.topic_id LEFT JOIN forums AS f ON t.forum_id = f.id WHERE p.id='.$post_id);
	$arr_post = mysqli_fetch_assoc($res_post);

	//=== if staff or post owner let them delete post
	$can_delete = ($arr_post['user_id'] === $CURUSER['id'] || $CURUSER['class'] >= UC_STAFF);
	    
		//=== stop them, they shouldn't be here lol
		//=== this is kinda long, but seems like a switch thing would be pointless, as you have to check them all...
		if ($CURUSER['class'] < $arr_post['min_class_read'] ||$CURUSER['class'] < $arr_post['min_class_write'])
		{
		stderr('Error', 'Topic not found.');
		}
		if ($CURUSER['forum_post'] == 'no' || $CURUSER['suspended'] == 'yes')
		{
		stderr('Error', 'Your posting rights have been suspended.');
		}
		if (!$can_delete)
		{
		stderr('Error', 'This is not your post to delete.');
		}
		if ($arr_post['locked'] == 'yes')
		{
		stderr('Error', 'This topic is locked.');
		}
		
		if ($arr_post['staff_lock'] == 1)
		{
		stderr('Error', 'This post staff is locked my friend, deleting the evidence you wont be.');
		}
		
		if ($arr_post['first_post'] == $post_id && $CURUSER['class'] < UC_STAFF)
		{
		stderr('Error', 'This is the first post in the topic, only Staff can delete topics.');
		}
		if ($arr_post['first_post'] == $post_id && $CURUSER['class'] >= UC_STAFF)
		{
		stderr('Error', 'This is the first post in the topic, you must use <a class="altlink" href="forums.php?action=forums_admin&amp;action_2=delete_topic&amp;topic_id='.$topic_id.'">Delete Topic</a>.');
		}
		
	//=== ok... they made it this far, so let's delete the damned post!
	if($sanity_check > 0)
	{
		//=== if you want the un-delete option (only admin and up can see "deleted" posts)
		if ($delete_for_real  === 1)
		{
		//=== re-do that last post thing ;)
		$res = sql_query('SELECT p.id, t.forum_id FROM posts AS p LEFT JOIN topics as t ON p.topic_id = t.id WHERE p.topic_id = '.$topic_id.' ORDER BY id DESC LIMIT 1');
		$arr = mysqli_fetch_assoc($res);
		
		sql_query('UPDATE topics SET last_post = '.$arr['id'].', post_count = post_count - 1 WHERE id = '.$topic_id);
		sql_query('UPDATE forums SET post_count = post_count - 1 WHERE id = '.$arr['forum_id']);	
		sql_query('DELETE FROM posts WHERE id = '.$post_id);
		$mc1->delete_value('last_posts_'.$CURUSER['class']);
		}
		else
		{
		sql_query('UPDATE posts SET status = \'deleted\'  WHERE id = '.$post_id.' AND topic_id = '.$topic_id);
		}
	//=== ok, all done here, send them back! \o/
	header('Location: forums.php?action=view_topic&topic_id='.$topic_id); 
	die();	
	}
	else 
	{
	stderr('Sanity Check!', 'Are you sure you want to delete this post? If so, click 
	<a class="altlink" href="forums.php?action=delete_post&amp;post_id='.$post_id.'&amp;topic_id='.$topic_id.'&amp;sanity_check=1">Here</a>.');
	}

?>