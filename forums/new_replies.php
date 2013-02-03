<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsource and TBDev and the many many 
coders who helped develope them over time.
proper credits to follow :)
beta mon aug 2 2010 v0.1
view replies to your posts
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
//=== start page
$colour = $topicpoll = '';
$links = '<span style="text-align: center;"><a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php">Main Forums</a> |  ' . $mini_menu . '<br /><br /></span>';
$HTMLOUT.= '<h1>New replies to threads you\'ve posted in</h1>' . $links;
//$time = $readpost_expiry;
$res_count = sql_query('SELECT t.id, t.last_post FROM topics AS t LEFT JOIN posts AS p ON t.last_post = p.id LEFT JOIN forums as f ON f.id = t.forum_id WHERE ' . ($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')) . ' f.min_class_read <= ' . $CURUSER['class']);
//=== lets do the loop / Check if post is read / get count there must be a beter way to do this lol
$count = 0;
while ($arr_count = mysqli_fetch_assoc($res_count)) {
    $res_post_read = sql_query('SELECT last_post_read FROM read_posts WHERE user_id=' . sqlesc($CURUSER['id']) . ' AND topic_id=' . sqlesc($arr_count['id']));
    $arr_post_read = mysqli_fetch_row($res_post_read);
    $did_i_post_here = sql_query('SELECT user_id FROM posts WHERE user_id=' . sqlesc($CURUSER['id']) . ' AND topic_id=' . sqlesc($arr_count['id']));
    $posted = (mysqli_num_rows($did_i_post_here) > 0 ? 1 : 0);
    if ($arr_post_read[0] < $arr_count['last_post'] && $posted) {
        ++$count;
    }
}
//=== nothing here? kill the page
if ($count == 0) {
    $HTMLOUT.= '<br /><br /><table border="0" cellspacing="10" cellpadding="10" width="400px">
   <tr><td class="three"align="center">
   <h1>No unread posts!</h1>You are up to date on all topics you\'ve posted in.<br /><br />
	</td></tr></table><br /><br />';
    $HTMLOUT.= $links . '<br />';
} else {
    //=== get stuff for the pager
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
    list($menu, $LIMIT) = pager_new($count, $perpage, $page, $INSTALLER09['baseurl'] . '/forums.php?action=view_unread_posts' . (isset($_GET['perpage']) ? '&amp;perpage=' . $perpage : ''));
    //=== top and bottom stuff
    $the_top_and_bottom = '<br /><table border="0" cellspacing="0" cellpadding="0" width="90%">
   <tr><td class="three" align="center" valign="middle">' . (($count > $perpage) ? $menu : '') . '</td>
	</tr></table>';
    //=== main huge query:
    $res_unread = sql_query('SELECT t.id AS topic_id, t.topic_name AS topic_name, t.last_post, t.post_count, 
   t.views, t.topic_desc, t.locked, t.sticky, t.poll_id, t.forum_id, t.rating_sum, t.num_ratings, t.anonymous AS tan,
   f.name AS forum_name, f.description AS forum_desc, p.post_title, p.body, p.icon, p.user_id, p.anonymous AS pan,
   u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king
   FROM topics AS t
   LEFT JOIN posts AS p ON t.last_post = p.id
   LEFT JOIN forums as f ON f.id = t.forum_id
   LEFT JOIN users AS u on u.id = t.user_id
   WHERE ' . ($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')) . ' f.min_class_read <= ' . $CURUSER['class'] . ' 
   ORDER BY t.last_post DESC ' . $LIMIT);
    $HTMLOUT.= $the_top_and_bottom . '<table border="0" cellspacing="5" cellpadding="10" width="90%">
	<tr>
	<td align="center" valign="middle" class="forum_head_dark" width="10"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/topic.gif" alt="topic" title="topic" /></td>
	<td align="center" valign="middle" class="forum_head_dark" width="10"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/topic_normal.gif" alt="Thread Icon" title="Thread Icon" /></td>
	<td align="left" class="forum_head_dark">New Posts!</td>
	<td class="forum_head_dark" align="center" width="10">Replies</td>
	<td class="forum_head_dark" align="center" width="10">Views</td>
	<td align="center" class="forum_head_dark">Started By</td>
	</tr>';
    //=== ok let's show the posts...
    while ($arr_unread = mysqli_fetch_assoc($res_unread)) {
        $res_post_read = sql_query('SELECT last_post_read FROM read_posts WHERE user_id=' . sqlesc($CURUSER['id']) . ' AND topic_id=' . sqlesc($arr_unread['topic_id']));
        $arr_post_read = mysqli_fetch_row($res_post_read);
        $did_i_post_here = sql_query('SELECT user_id FROM posts WHERE user_id=' . sqlesc($CURUSER['id']) . ' AND topic_id=' . sqlesc($arr_unread['topic_id']));
        $posted = (mysqli_num_rows($did_i_post_here) > 0 ? 1 : 0);
        if ($arr_post_read[0] < $arr_unread['last_post'] && $posted) {
            //=== change colors
            $colour = (++$colour) % 2;
            $class = ($colour == 0 ? 'one' : 'two');
            $locked = $arr_unread['locked'] == 'yes';
            $sticky = $arr_unread['sticky'] == 'yes';
            $topic_poll = $arr_unread['poll_id'] > 0;
            $first_unread_poster = sql_query('SELECT added FROM posts WHERE status = \'ok\'  AND topic_id=' . sqlesc($arr_unread['topic_id']) . ' ORDER BY id ASC LIMIT 1');
            $first_unread_poster_arr = mysqli_fetch_row($first_unread_poster);
            if ($arr_unread['tan'] == 'yes') {
                if ($CURUSER['class'] < UC_STAFF && $arr_unread['user_id'] != $CURUSER['id']) $thread_starter = ($arr_unread['username'] !== '' ? '<i>Anonymous</i>' : 'Lost [' . $arr_unread['id'] . ']') . '<br />' . get_date($first_unread_poster_arr[0], '');
                else $thread_starter = ($arr_unread['username'] !== '' ? '<i>Anonymous</i> [' . print_user_stuff($arr_unread) . ']' : 'Lost [' . $arr_unread['id'] . ']') . '<br />' . get_date($first_unread_poster_arr[0], '');
            } else {
                $thread_starter = ($arr_unread['username'] !== '' ? print_user_stuff($arr_unread) : 'Lost [' . $arr_unread['id'] . ']') . '<br />' . get_date($first_unread_poster_arr[0], '');
            }
            $topicpic = ($arr_unread['post_count'] < 30 ? ($locked ? 'lockednew' : 'topicnew') : ($locked ? 'lockednew' : 'hot_topic_new'));
            $rpic = ($arr_unread['num_ratings'] != 0 ? ratingpic_forums(ROUND($arr_unread['rating_sum'] / $arr_unread['num_ratings'], 1)) : '');
            $sub = sql_query('SELECT user_id FROM subscriptions WHERE user_id=' . sqlesc($CURUSER['id']) . ' AND topic_id=' . sqlesc($arr_unread['topic_id']));
            $subscriptions = (mysqli_num_rows($sub) > 0 ? 1 : 0);
            $icon = ($arr_unread['icon'] == '' ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/topic_normal.gif" alt="Topic" title="Topic" />' : '<img src="pic/smilies/' . htmlsafechars($arr_unread['icon']) . '.gif" alt="' . htmlsafechars($arr_unread['icon']) . '" title="' . htmlsafechars($arr_unread['icon']) . '" />');
            $first_post_text = tool_tip(' <img src="' . $INSTALLER09['pic_base_url'] . 'forums/mg.gif" height="14" alt="Preview" title="Preview" />', format_comment($arr_unread['body'], true, false, false) , 'Last Post Preview');
            $topic_name = ($sticky ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/pinned.gif" alt="Pinned" title="Pinned" /> ' : ' ') . ($topicpoll ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/poll.gif" alt="Poll" title="Poll" /> ' : ' ') . ' <a class="altlink" href="?action=view_topic&amp;topic_id=' . (int)$arr_unread['topic_id'] . '" title="First post in thread">' . htmlsafechars($arr_unread['topic_name'], ENT_QUOTES) . '</a><a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_topic&amp;topic_id=' . (int)$arr_unread['topic_id'] . '&amp;page=0#' . (int)$arr_post_read[0] . '" title="First unread post in this thread"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/last_post.gif" alt="First unread post" title="First unread post" /></a>' . ($posted ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/posted.gif" alt="Posted" title="Posted" /> ' : ' ') . ($subscriptions ? '<img src="' . $INSTALLER09['pic_base_url'] . 'forums/subscriptions.gif" alt="subscribed" title="subscribed" /> ' : ' ') . ' <img src="' . $INSTALLER09['pic_base_url'] . 'forums/new.gif" alt="New post in topic!" title="New post in topic!" />';
            //=== print here
            $HTMLOUT.= '<tr>
		<td class="' . $class . '" align="center"><img src="' . $INSTALLER09['pic_base_url'] . 'forums/' . $topicpic . '.gif" alt="topic" title="topic" /></td>
		<td class="' . $class . '" align="center">' . $icon . '</td>
		<td align="left" valign="middle" class="' . $class . '">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td  class="' . $class . '" align="left">' . $topic_name . $first_post_text . ' </td>
		<td class="' . $class . '" align="right">' . $rpic . '</td>
		</tr>
		</table>
		' . ($arr_unread['topic_desc'] !== '' ? '&#9658; <span style="font-size: x-small;">' . htmlsafechars($arr_unread['topic_desc'], ENT_QUOTES) . '</span>' : '') . '  
		<hr />in: <a class="altlink" href="' . $INSTALLER09['baseurl'] . '/forums.php?action=view_forum&amp;forum_id=' . (int)$arr_unread['forum_id'] . '">' . htmlsafechars($arr_unread['forum_name'], ENT_QUOTES) . '</a>
		' . ($arr_unread['topic_desc'] !== '' ? ' [ <span style="font-size: x-small;">' . htmlsafechars($arr_unread['topic_desc'], ENT_QUOTES) . '</span> ]' : '') . '</td>
		<td align="center" class="' . $class . '">' . number_format($arr_unread['post_count'] - 1) . '</td>
		<td align="center" class="' . $class . '">' . number_format($arr_unread['views']) . '</td>
		<td align="center" class="' . $class . '">' . $thread_starter . '</td>
		</tr>';
        }
    }
    $HTMLOUT.= '</table>' . $the_top_and_bottom . '<br /><br />' . $links . '<br />';
}
?>
