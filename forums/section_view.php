<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsourse and TBDev and the many many 
coders who helped develop them over time.
proper credits to follow :)

beta monday aug 2nd 2010 v0.1
section view (looking at an over forums section)

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
   $colour = $child_boards = $now_viewing = $colour = '';
   $forum_id = (isset($_GET['forum_id']) ? intval($_GET['forum_id']) :  (isset($_POST['forum_id']) ? intval($_POST['forum_id']) :  0));

    if (!is_valid_id($forum_id))
    {
	stderr('Error', 'Bad ID.');
    }

	//=== stupid query just to get overforum name :'(
	$over_forums_res = sql_query('SELECT name, min_class_view FROM over_forums WHERE id ='.$forum_id);
	$over_forums_arr = mysqli_fetch_assoc($over_forums_res);
	      
	//=== make sure they can be here
	if ($CURUSER['class'] < $over_forums_arr['min_class_view'])
	{
	stderr('Error', 'Bad ID.');
	}

$location_bar = '<h1><a class="altlink" href="index.php">'.$INSTALLER09['site_name'].'</a>  <img src="pic/arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
			<a class="altlink" href="forums.php">Forums</a> <img src="pic/arrow_next.gif" alt="&#9658;" title="&#9658;" /> 
			<a class="altlink" href="forums.php?action=section_view&amp;forum_id='.$forum_id.'">'.htmlentities($over_forums_arr['name'], ENT_QUOTES).'</a></h1>
			'.$mini_menu.'<br /><br />';

	$HTMLOUT .= $location_bar;
			
	//=== top and bottom stuff
	$HTMLOUT .= '<br /><table border="0" cellspacing="0" cellpadding="5" width="90%">
		<tr>
		<td class="forum_head_dark" align="left" colspan="4"><span style="color: white;">Section View for '.htmlentities($over_forums_arr['name'], ENT_QUOTES).'</span></td>
		</tr>';

    //=== basic query
    $forums_res = sql_query('SELECT name AS forum_name, description AS forum_description, id AS forum_id, post_count, topic_count FROM forums WHERE min_class_read < '.$CURUSER['class'].' AND forum_id='.$forum_id.' AND parent_forum = 0 ORDER BY sort');
    			
		//=== lets start the loop \o/
		while ($forums_arr = mysqli_fetch_assoc($forums_res))
		{
		//=== change colors
		$colour = (++$colour)%2;
		$class = ($colour == 0 ? 'one' : 'two');
		
    //=== Get last post info
    $res = sql_query('SELECT t.last_post, t.topic_name, t.id AS topic_id, p.user_id, p.added,  u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king
							FROM topics AS t LEFT JOIN posts AS p ON t.last_post = p.id LEFT JOIN users AS u ON p.user_id = u.id 
							WHERE '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : 
							($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')).'
							forum_id='.$forums_arr ['forum_id'].' ORDER BY last_post DESC LIMIT 1');
    $arr = mysqli_fetch_assoc($res);
	
		//=== only do more if there is a stuff here...
		if ($arr['last_post'] > 0)
		{

		//=== get the last post read by CURUSER
		$last_read_post_res = sql_query('SELECT last_post_read FROM read_posts WHERE user_id='.$CURUSER['id'].' AND topic_id='.$arr['topic_id']);
      		$last_read_post_arr = mysqli_fetch_row($last_read_post_res);
      		
			$image_and_link = ($arr['added'] > (time() - $readpost_expiry)) ? (!$last_read_post_arr || $arr['last_post'] > $last_read_post_arr[0]) : 0;
		$img = ($image_and_link ? 'unlockednew' : 'unlocked');
      			
      			//=== get child boards if any
				$child_boards_res = sql_query('SELECT name, id FROM forums WHERE parent_forum = '.$forums_arr ['forum_id'].' ORDER BY sort ASC');
				
				while ($child_boards_arr = mysqli_fetch_assoc($child_boards_res))
				{
			  	if ($child_boards !== '')
			  	$child_boards .= ', ';
			  	$child_boards .= '<a href="forums.php?action=view_forum&amp;forum_id='.$child_boards_arr['id'].'" title="click to view!" class="altlink">'.htmlentities($child_boards_arr['name'], ENT_QUOTES).'</a>';
			  	}

					if ($child_boards !== '')
					{
					$child_boards = '<hr /><span style="font-size: xx-small;">child boards:</span> '.$child_boards;
					}
								  	
			  	//=== now_viewing
				$now_viewing_res = sql_query('SELECT n_v.user_id, u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king 
									FROM now_viewing AS n_v LEFT JOIN users AS u ON n_v.user_id = u.id WHERE forum_id = '.$forums_arr ['forum_id']);

				//=== let's see whos lookng in here...
				while ($now_viewing_arr = mysqli_fetch_assoc($now_viewing_res))
				{
				if ($now_viewing !== '')
				$now_viewing .= ', ';
				$now_viewing .= print_user_stuff($now_viewing_arr);
			  	}

					if ($now_viewing !== '')
					{
					$now_viewing = '<hr /><span style="font-size: xx-small;">now viewing:</span>'.$now_viewing;
					}
					
		$last_post = 'Last Post by: '.print_user_stuff($arr).' <span style="font-size: x-small;"> [ '.get_user_class_name($arr['class']).' ] </span><br />
		in &#9658; <a class="altlink" href="?action=view_topic&amp;topic_id='.$arr['topic_id'].'&amp;page=p'.$arr['last_post'].'#'.$arr['last_post'].'" title="'.htmlentities($arr['topic_name'], ENT_QUOTES).'">
		<span style="font-weight: bold;">'.CutName(htmlentities($arr['topic_name'], ENT_QUOTES), 30).'</span></a><br />
		'.get_date($arr['added'],'').'<br />';

		} //=== end of only do more if there is a post there...
		else
		{
		$img = 'unlocked';
		$now_viewing = '';
		$last_post = 'N/A';
		}
		
		$HTMLOUT .= '<tr>
		<td class="'.$class.'" align="center" valign="middle" width="30"><img src="pic/forums/'.$img.'.gif" alt="pic/forums/'.$img.'.gif" title="pic/forums/'.$img.'.gif" /></td>
		<td class="'.$class.'" align="left">
		<a class="altlink" href="forums.php?action=view_forum&amp;forum_id='.$forums_arr['forum_id'].'">'.htmlentities($forums_arr['forum_name'], ENT_QUOTES).'</a>
		<br />'.htmlentities($forums_arr['forum_description'], ENT_QUOTES).$child_boards.$now_viewing.'</td>
		<td class="'.$class.'" align="center" width="80">'.number_format($forums_arr['post_count']).' Posts<br />'.number_format($forums_arr['topic_count']).' Topics</td>
		<td class="'.$class.'" align="left" width="140">
		<span style="white-space:nowrap;">'.$last_post.'</span>
		</td>
		</tr>';		

		}	
	
$HTMLOUT .= '</table><br />'.$location_bar;

?>