<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
subscriptions mod based on my subscriptions mod  for TBDev 
with some code from TBsourse & TBdev

beta fri june 11th 2010 v0.1

thanks to pdq & elephant for suggestions :D

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


$posts = $lppostid = $topicpoll = $colour = $rpic = $content = '';
$links = '<span style="text-align: center;"><a class="altlink" href="forums.php">Main Forums</a> |  '.$mini_menu.'<br /><br /></span>';

	$HTMLOUT .= '<h1>Subscribed Forums for '.print_user_stuff($CURUSER).'</h1>'.$links;

	//=== Get count 
	$res = sql_query('SELECT COUNT(id) FROM subscriptions WHERE user_id='.$CURUSER['id']);
	$row = mysqli_fetch_row($res);
	$count = $row[0];
	
	//=== nothing here? kill the page
	if ($count == 0)
	{
	$HTMLOUT .='<br /><br /><table border="0" cellspacing="10" cellpadding="10" width="400px">
		<tr><td class="three"align="center">
		<h1>No Subscriptions Found!</h1>You are not yet subscribed to any forums... To subscribe to a forum, click the 
		<span style="font-weight: bold;font-style: italic;">Subscribe to this Forum</span> link on the thread page.<br /><br />
		To be notified via PM when there is a new post, go to your <a class="altlink" href="my.php">profile</a> 
		page and set <span style="font-weight: bold;">PM on Subscriptions</span> to yes.<br /><br />
		</td></tr></table><br /><br />';
	
	$HTMLOUT .= $links.'<br />';	
	}	
		
	 //=== get stuff for the pager
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$perpage = (isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20);

	list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'forums.php?action=subscriptions'.(isset($_GET['perpage']) ? '&amp;perpage='.$perpage : '')); 

	
	//=== top and bottom stuff
	$the_top_and_bottom =  '<table border="0" cellspacing="0" cellpadding="0" width="90%">
		<tr><td class="three" align="center" valign="middle">'.(($count > $perpage) ? $menu : '').'</td>
		</tr></table>';
			
//=== get the info
$res = sql_query('SELECT s.id AS subscribed_id, t.id AS topic_id, t.topic_name, t.topic_desc, t.last_post,  t.views, t.post_count, t.locked, t.sticky, t.poll_id, t.user_id,
p.id AS post_id, p.added,
u.username, u.id, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king
FROM subscriptions AS s 
LEFT JOIN topics as t ON s.topic_id = t.id 
LEFT JOIN posts as p ON t.last_post = p.id 
LEFT JOIN forums AS f ON f.id = t.forum_id 
LEFT JOIN users AS u ON u.id = p.user_id 
WHERE '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : 
($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')).' 
s.user_id = '.$CURUSER['id'].' AND f.min_class_read < '.$CURUSER['class'].' AND s.user_id = '.$CURUSER['id'].'  ORDER BY t.id DESC '.$LIMIT);

	while ($topic_arr = mysqli_fetch_assoc($res))
	{

		$topic_id = $topic_arr['topic_id'];
        $locked = $topic_arr['locked'] == 'yes';
        $sticky = $topic_arr['sticky'] == 'yes';
        $topic_poll = $topic_arr['poll_id'] > 0;
	
		$last_post_username = ($topic_arr['username'] !== '' ? print_user_stuff($topic_arr) : 'Lost ['.$topic_arr['id'].']');
        $last_post_id = $topic_arr['last_post'];

//=== Get author / first post info
$first_post_res = sql_query('SELECT p.added, p.icon, p.body,
u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king
FROM posts AS p 
LEFT JOIN users AS u ON p.user_id = u.id 
WHERE '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND' : 
($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND' : '')).' topic_id='.$topic_id.'  
ORDER BY id DESC LIMIT 1');

        $first_post_arr = mysqli_fetch_assoc($first_post_res);
		
        $thread_starter = ($first_post_arr['username'] !== '' ? print_user_stuff($first_post_arr) : 'Lost ['.$first_post_arr['id'].']').'<br />'.get_date($first_post_arr['added'],'');
		$icon = ($first_post_arr['icon'] == '' ? '<img src="pic/forums/topic_normal.gif" alt="Topic" title="Topic" />' : '<img src="pic/smilies/'.htmlspecialchars($first_post_arr['icon']).'.gif" alt="'.htmlspecialchars($first_post_arr['icon']).'" title="'.htmlspecialchars($first_post_arr['icon']).'" />');
        $first_post_text = tool_tip(' <img src="pic/forums/mg.gif" height="14" alt="Preview" title="Preview" />', format_comment($first_post_arr['body'], true, false, false), 'First Post Preview');
	  
	//=== last post read in topic
        $last_unread_post_res = sql_query('SELECT last_post_read FROM read_posts WHERE user_id='.$CURUSER['id'].' AND topic_id='.$topic_id);
        $last_unread_post_arr = mysqli_fetch_row($last_unread_post_res);
	
		$did_i_post_here = sql_query('SELECT user_id FROM posts WHERE user_id='.$CURUSER['id'].' AND topic_id='.$topic_id);       
		$posted = (mysqli_num_rows($did_i_post_here) > 0 ? 1 : 0);
        
      
	         //=== make the multi pages thing...
	        $total_pages = floor($posts / $perpage);

			switch (true)
			{
			case ($total_pages == 0):
			$multi_pages = '';
			break;
			
			case ($total_pages > 11):
				$multi_pages = ' <span style="font-size: xx-small;"> <img src="pic/forums/multipage.gif" alt="+" title="+" />';
					for ($i = 1; $i < 5; ++$i)
           	 			{
   	         			$multi_pages .= ' <a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'&amp;page='.$i.'">'.$i.'</a>';
        	    			}
					$multi_pages .= ' ... ';
					for ($i = ($total_pages - 2); $i <= $total_pages; ++$i)
        	    			{
        	    			$multi_pages .= ' <a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'&amp;page='.$i.'">'.$i.'</a>';
        	    			}
        	    		$multi_pages .= '</span>';
			break;
			
			case ($total_pages < 11):
				$multi_pages = ' <span style="font-size: xx-small;"> <img src="pic/forums/multipage.gif" alt="+" title="+" />';
					for ($i = 1; $i < $total_pages; ++$i)
					{
            				$multi_pages .= ' <a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'&amp;page='.$i.'">'.$i.'</a>';
            				}
            			$multi_pages .= '</span>';
			break;
			}


        $new = ($topic_arr['added'] > (time() - $readpost_expiry)) ? (!$last_unread_post_arr || $lppostid > $last_unread_post_arr[0]) : 0;
		
        $topicpic = ($posts < 30 ? ($locked ? ($new ? 'lockednew' : 'locked') : ($new ? 'topicnew' : 'topic')) : ($locked ? ($new ? 'lockednew' : 'locked') : ($new ? 'hot_topic_new' : 'hot_topic')));
        
        $topic_name = ($sticky ? '<img src="pic/forums/pinned2.gif" alt="Pinned" title="Pinned" /> ' : ' ').($topicpoll ? '<img src="pic/forums/poll.gif" alt="Poll" title="Poll" /> ' : ' '). '
        		<a class="altlink" href="?action=view_topic&amp;topic_id='.$topic_id.'">'.htmlentities($topic_arr['topic_name'], ENT_QUOTES).'</a> '.$multi_pages;
	   
		//=== change colors
		$colour= (++$colour)%2;
		$class = ($colour == 0 ? 'one' : 'two');

	$content .=  '<tr>
		<td class="'.$class.'" align="center"><img src="pic/forums/'.$topicpic.'.gif" alt="topic" title="topic" /></td>
		<td class="'.$class.'" align="center">'.$icon.'</td>
		<td align="left" valign="middle" class="'.$class.'">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td  class="'.$class.'" align="left">'.$topic_name.$first_post_text.($new ? ' <img src="pic/forums/new.gif" alt="New post in topic!" title="New post in topic!" />' : '').'</td>
		<td class="'.$class.'" align="right">'.$rpic.'</td>
		</tr>
		</table>
		'.($topic_arr ['topic_desc'] !== '' ? '&#9658; <span style="font-size: x-small;">'.htmlentities($topic_arr ['topic_desc'], ENT_QUOTES).'</span>' : '').'</td>
		<td align="center" class="'.$class.'">'.$thread_starter.'</td>
		<td align="center" class="'.$class.'">'.number_format($topic_arr ['post_count'] - 1).'</td>
		<td align="center" class="'.$class.'">'.number_format($topic_arr ['views']).'</td>
		<td align="center" class="'.$class.'"><span style="white-space:nowrap;">'.get_date($topic_arr['added'],'').'</span><br />by&nbsp;'.$last_post_username.'</td>
		<td align="center" class="'.$class.'"><a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.$topic_id.'&amp;page=p'.$last_post_id.'#'.$last_post_id.'" title="last post in this thread">
		<img src="pic/forums/last_post.gif" alt="Last post" title="Last post" /></a></td>
		<td align="center" class="'.$class.'"><input type="checkbox" name="remove[]" value="'.$topic_arr['subscribed_id'].'" /></td>
		</tr>';

	}

$HTMLOUT .= $the_top_and_bottom.'<form action="forums.php?action=delete_subscription" method="post" name="checkme">
		<table border="0" cellspacing="0" cellpadding="5" width="90%">
		<tr>
		<td align="center" valign="middle" class="forum_head_dark" width="10"><img src="pic/forums/topic.gif" alt="Topic" title="Topic" /></td>
		<td align="center" valign="middle" class="forum_head_dark" width="10"><img src="pic/forums/topic_normal.gif" alt="Thread Icon" title="Thread Icon" /></td>
		<td align="left" class="forum_head_dark">Topic</td>
		<td align="center" class="forum_head_dark">Started By</td>
		<td class="forum_head_dark" align="center" width="10">Replies</td>
		<td class="forum_head_dark" align="center" width="10">Views</td>
		<td align="center" class="forum_head_dark" width="140">Last Post</td>
		<td align="center" valign="middle" class="forum_head_dark" width="10"><img src="pic/forums/last_post.gif" alt="Last post" title="Last post" /></td>
		<td align="center" valign="middle" class="forum_head_dark" width="10"></td>
		</tr>'.$content.'
		<tr>
		<td align="center" valign="middle" class="forum_head_dark" colspan="9">
		<a class="altlink" href="javascript:SetChecked(1,\'remove[]\')"> <span style="color: black;">select all</span></a> - 
		<a class="altlink" href="javascript:SetChecked(0,\'remove[]\')"><span style="color: black;">un-select all</span></a>  
		<input type="submit" name="button" class="button" value="Remove Selected" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" /></td>
		</tr></table></form><script type="text/javascript" src="scripts/check_selected.js"></script>
		'.$the_top_and_bottom.'<br /><br />'.$links.'<br />';
     
?>
