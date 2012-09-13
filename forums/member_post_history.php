<?php
/**********************************************************
New 2010 forums that don't suck for TB based sites....
pretty much coded page by page, but coming from a 
history ot TBsourse and TBDev and the many many 
coders who helped develop them over time.
proper credits to follow :)

beta monday aug 25th 2010 v0.1
member post history

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
$colour = $post_status_image = '';
$ASC_DESC = ((isset($_GET['ASC_DESC']) && $_GET['ASC_DESC'] === 'ASC') ? 'ASC ' : 'DESC ');
$member_id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
if (!isset($member_id) || !is_valid_id($member_id)) {
    //=== search members
    $search = isset($_GET['search']) ? strip_tags(trim($_GET['search'])) : '';
    $class = isset($_GET['class']) ? $_GET['class'] : '-';
    $letter = '';
    $q = '';
    if ($class == '-' || !ctype_digit($class)) $class = '';
    if ($search != '' || $class) {
        $query = 'username LIKE '.sqlesc("%$search%").' AND status=\'confirmed\'';
        if ($search) $q = 'search='.htmlsafechars($search);
    } else {
        $letter = isset($_GET['letter']) ? trim((string)$_GET['letter']) : '';
        if (strlen($letter) > 1) die;
        if ($letter == '' || strpos('abcdefghijklmnopqrstuvwxyz0123456789', $letter) === false) $letter = '';
        $query = 'username LIKE '.sqlesc("$letter%").' AND status=\'confirmed\'';
        $q = 'letter='.$letter;
    }
    if (ctype_digit($class)) {
        $query.= ' AND class='.sqlesc($class);
        $q.= ($q ? '&amp;' : '').'class='.$class;
    }
    $HTMLOUT.= '<h1>Search members</h1>
			<form method="get" action="forums.php?">
			<input type="hidden" value="member_post_history" name="action" />
			<input type="text" size="30" name="search" value="'.$search.'" />
			<select name="class">
			<option value="-">(any class)</option>';
    for ($i = 0;; ++$i) {
        if ($c = get_user_class_name($i)) $option.= '<option value="'.$i.'"'.(ctype_digit($class) && $class == $i ? ' selected="selected"' : '').'>'.$c.'</option>';
        else break;
    }
    $HTMLOUT.= $option.'</select>
			<input type="submit" class="button" value="Search" onmouseover="this.className=\'button_hover\'" onmouseout="this.className=\'button\'" />
			</form>
			<br /><br />';
    $aa = range('0', '9');
    $bb = range('a', 'z');
    $cc = array_merge($aa, $bb);
    unset($aa, $bb);
    $HTMLOUT.= '<div align="center">';
    $count = 0;
    foreach ($cc as $L) {
        $next.= ($count == 10) ? '<br />' : '';
        if (!strcmp($L, $letter)) $next.= ' <span style="font-weight: bold;">'.strtoupper($L).'</span>';
        else $next.= ' <a class="altlink" href="forums.php?action=member_post_history&amp;letter='.$L.'">'.strtoupper($L).'</a>';
        $count++;
    }
    $HTMLOUT.= $next.'</div>';
    //=== get stuff for the pager
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
    $res_count = @sql_query('SELECT COUNT(id) FROM users WHERE '.$query) or sqlerr(__FILE__, __LINE__);
    $arr_count = mysqli_fetch_row($res_count);
    $count = ($arr_count[0] > 0 ? $arr_count[0] : 0);
    list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'forums.php?action=member_post_history');
    $HTMLOUT.= ($arr_count[0] > $perpage) ? '<p>'.$menu.'<br /></p>' : '<br />';
    if ($arr_count[0] > 0) {
        $res = sql_query('SELECT u.id, u.username, u.class, u.donor, u.suspended, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.added, u.last_access, c.name, c.flagpic 
								FROM users AS u FORCE INDEX (username) 
								LEFT JOIN countries AS c ON u.country = c.id WHERE '.$query.' 
								ORDER BY u.username '.$LIMIT) or sqlerr(__FILE__, __LINE__);
        $HTMLOUT.= '<table border="0" cellspacing="5" cellpadding="5">
			<tr><td class="forum_head_dark" align="left">Member</td>
			<td class="forum_head_dark">Registered</td>
			<td class="forum_head_dark">Last access</td>
			<td class="forum_head_dark" align="left">Class</td>
			<td class="forum_head_dark">Country</td>
			<td class="forum_head_dark" align="center">View</td></tr>';
        while ($row = mysqli_fetch_assoc($res)) {
            //=== change colors
            $colour = (++$colour) % 2;
            $class = ($colour == 0 ? 'one' : 'two');
            $country = ($row['name'] != NULL) ? '<td class="'.$class.'" align="center"><img src="pic/flag/'.$row['flagpic'].'" alt="'.htmlsafechars($row['name']).'" /></td>' : '<td class="'.$class.'" align="center">---</td>';
            $HTMLOUT.= '<tr>
		<td class="'.$class.'" align="left">'.print_user_stuff($row).'</td>
		<td class="'.$class.'">'.get_date($row['added'], '').'</td>
		<td class="'.$class.'">'.get_date($row['last_access'], '').'</td>
		<td class="'.$class.'" align="left">'.get_user_class_name($row['class']).'</td>
		'.$country.'
		<td class="'.$class.'" align="center"><a href="forums.php?action=member_post_history&amp;id='.(int)$row['id'].'" title="see this members post history" class="altlink">Post History</a></td>
	</tr>';
        }
        $HTMLOUT.= '</table>';
    } else $HTMLOUT.= 'sorry, no member was found';
    $HTMLOUT.= ($arr_count[0] > $perpage) ? '<br /><p>'.$menu.'</p>' : '<br /><br />';
    //echo stdfoot();
    die();
}
$res_count = sql_query('SELECT COUNT(p.id) AS count
								FROM posts AS p 
								LEFT JOIN topics AS t ON p.topic_id = t.id 
								LEFT JOIN forums AS f ON f.id = t.forum_id 
								WHERE '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')).'
								p.user_id = '.sqlesc($member_id).' AND f.min_class_read <= '.$CURUSER['class']);
$arr_count = mysqli_fetch_row($res_count);
$count = $arr_count[0];
//=== get stuff for the pager
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
$subscription_on_off = (isset($_GET['s']) ? ($_GET['s'] == 1 ? '<br /><div style="font-weight: bold;">Subscribed to topic <img src="pic/forums/subscribe.gif" alt=" " width="25"></div>' : '<br /><div style="font-weight: bold;">Unsubscribed from topic <img src="pic/forums/unsubscribe.gif" alt=" " width="25"></div>') : '');
list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'forums.php?action=member_post_history&amp;id='.$member_id.(isset($_GET['perpage']) ? '&amp;perpage='.$perpage : ''));
$res = sql_query('SELECT p.id AS post_id, p.topic_id, p.user_id, p.added, p.body, p.edited_by, p.edit_date, p.icon, p.post_title, p.bbcode, p.post_history, p.edit_reason, p.ip, p.status AS post_status,
t.id AS topic_id, t.topic_name, t.forum_id, t.sticky, t.locked, t.poll_id, t.status AS topic_status,
f.name AS forum_name, f.description
FROM posts AS p 
LEFT JOIN topics AS t ON p.topic_id = t.id 
LEFT JOIN forums AS f ON f.id = t.forum_id 
WHERE  '.($CURUSER['class'] < UC_STAFF ? 'p.status = \'ok\' AND t.status = \'ok\' AND' : ($CURUSER['class'] < $min_delete_view_class ? 'p.status != \'deleted\' AND t.status != \'deleted\'  AND' : '')).'
 p.user_id = '.sqlesc($member_id).' AND f.min_class_read <= '.$CURUSER['class'].'
ORDER BY p.id '.$ASC_DESC.$LIMIT);
//== get user info
$user_res = sql_query('SELECT id, username, class, donor, suspended, warned, enabled, chatpost, leechwarn, pirate, king, title, avatar, offensive_avatar FROM users WHERE id = '.sqlesc($member_id));
$user_arr = mysqli_fetch_assoc($user_res);
if ($count == 0) {
    stderr('Sorry', ($user_arr['username'] <> '' ? print_user_stuff($user_arr).' has no posts to look at!' : 'No member with that ID!'));
}
$links = '<span style="text-align: center;"><a class="altlink" href="forums.php">Main Forums</a> |  '.$mini_menu.'<br /><br /></span>';
$the_top_and_bottom = '<tr><td class="three" colspan="3" align="center">'.(($count > $perpage) ? $menu : '').'</td></tr>';
$HTMLOUT.= '<h1>'.$count.' Posts by '.print_user_stuff($user_arr).'</h1>'.$links.'
			<div><a class="altlink" href="forums.php?action=member_post_history&amp;id='.$member_id.'" title="view posts from newest to oldest">Sort by newest posts first</a> || 
			<a class="altlink" href="forums.php?action=member_post_history&amp;id='.$member_id.'&amp;ASC_DESC=ASC" title="view posts from oldest to newest">Sort by oldest posts first</a></div><br />';
$HTMLOUT.= '<a name="top"></a><table border="0" cellspacing="5" cellpadding="10" width="90%">'.$the_top_and_bottom;
//=== lets start the loop \o/
while ($arr = mysqli_fetch_assoc($res)) {
    //=== change colors
    $colour = (++$colour) % 2;
    $class = ($colour == 0 ? 'one' : 'two');
    $class_alt = ($colour == 0 ? 'two' : 'one');
    //=== topic status
    $topic_status = htmlsafechars($arr['topic_status']);
    switch ($topic_status) {
    case 'ok':
        $topic_status_image = '';
        break;

    case 'recycled':
        $topic_status_image = '<img src="pic/forums/recycle_bin.gif" alt="Recycled" title="this thread is currently in the recycle-bin" />';
        break;

    case 'deleted':
        $topic_status_image = '<img src="pic/forums/delete_icon.gif" alt="Deleted" title="this thread is currently deleted" />';
        break;
    }
    //=== post status
    $post_status = htmlsafechars($arr['post_status']);
    switch ($post_status) {
    case 'ok':
        $post_status = $class;
        $post_status_image = '';
        break;

    case 'recycled':
        $post_status = 'recycled';
        $post_status_image = ' <img src="pic/forums/recycle_bin.gif" alt="Recycled" title="This post is currently in the recycle-bin" width="24px" />';
        break;

    case 'deleted':
        $post_status = 'deleted';
        $post_status_image = ' <img src="pic/forums/delete_icon.gif" alt="Deleted" title="This post is currently deleted" width="24px" />';
        break;

    case 'postlocked':
        $post_status = 'postlocked';
        $post_status_image = ' <img src="pic/forums/thread_locked.gif" alt="Locked" title="This post is currently locked" width="24px" />';
        break;
    }
    $post_icon = ($arr['icon'] !== '' ? '<img src="pic/smilies/'.htmlsafechars($arr['icon']).'.gif" alt="icon" /> ' : '<img src="pic/forums/topic_normal.gif" alt="icon" /> ');
    $post_title = ($arr['post_title'] !== '' ? ' <span style="font-weight: bold; font-size: x-small;">'.htmlsafechars($arr['post_title'], ENT_QUOTES).'</span>' : 'Link to Post');
    $edited_by = '';
    if ($arr['edit_date'] > 0) {
        $res_edited = sql_query('SELECT username FROM users WHERE id='.$arr['edited_by']);
        $arr_edited = mysqli_fetch_assoc($res_edited);
        $edited_by = '<br /><br /><br /><span style="font-weight: bold; font-size: x-small;">Last edited by <a class="altlink" href="userdetails.php?id='.(int)$arr['edited_by'].'">'.htmlsafechars($arr_edited['username']).'</a>
				 at '.get_date($arr['edit_date'], '').' GMT '.($arr['edit_reason'] !== '' ? ' </span>[ Reason: '.htmlsafechars($arr['edit_reason']).' ] <span style="font-weight: bold; font-size: x-small;">' : '').'
				 '.(($CURUSER['class'] >= UC_STAFF && $arr['post_history'] !== '') ? ' <a class="altlink" href="forums.php?action=view_post_history&amp;post_id='.(int)$arr['post_id'].'&amp;forum_id='.(int)$arr['forum_id'].'&amp;topic_id='.(int)$arr['topic_id'].'">read post history</a></span><br />' : '');
    }
    $body = ($arr['bbcode'] == 'yes' ? format_comment($arr['body']) : format_comment_no_bbcode($arr['body']));
    $post_id = (int)$arr['post_id'];
    $HTMLOUT.= '<tr>
				<td class="forum_head_dark" colspan="3" align="left">Forum:  
				<a class="altlink" href="forums.php?action=view_forum&amp;forum_id='.$arr['forum_id'].'" title="Link to Forum">
				<span style="color: white;font-weight: bold;">'.htmlsafechars($arr['forum_name'], ENT_QUOTES).'</span></a>&nbsp;&nbsp;&nbsp;&nbsp;
				Topic: <a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.(int)$arr['topic_id'].'" title="Link to Forum">
				<span style="color: white;font-weight: bold;">'.htmlsafechars($arr['topic_name'], ENT_QUOTES).'</span></a>'.$topic_status_image.'</td>
			</tr>
			<tr>
				<td class="forum_head" align="left" width="100" valign="middle"><a name="'.$post_id.'"></a></td>
				<td class="forum_head" align="left" valign="middle"> <span style="white-space:nowrap;">'.$post_icon.'
				<a class="altlink" href="forums.php?action=view_topic&amp;topic_id='.(int)$arr['topic_id'].'&amp;page='.$page.'#'.(int)$arr['post_id'].'" title="Link to Post">
				'.$post_title.'</a>&nbsp;&nbsp;'.$post_status_image.' &nbsp;&nbsp; posted on: '.get_date($arr['added'], '').' ['.get_date($arr['added'], '', 0, 1).']</span></td>
				<td class="forum_head" align="right" valign="middle"><span style="white-space:nowrap;"> 
				<a href="forums.php?action=view_my_posts&amp;page='.$page.'#top"><img src="pic/forums/up.gif" alt="top" /></a> 
				<a href="forums.php?action=view_my_posts&amp;page='.$page.'#bottom"><img src="pic/forums/down.gif" alt="bottom" /></a></span></td>
			</tr>	
			<tr>
				<td class="'.$class_alt.'" align="center" width="100px" valign="top">'.avatar_stuff($user_arr).'<br />'.print_user_stuff($user_arr).($user_arr['title'] == '' ? '' : '<br /><span style=" font-size: xx-small;">['.htmlsafechars($user_arr['title']).']</span>').'<br />
				<span style="font-weight: bold;">'.get_user_class_name($user_arr['class']).'</span><br /></td>
				<td class="'.$post_status.'" align="left" valign="top" colspan="2">'.$body.$edited_by.'</td>
			</tr>
			<tr>
				<td class="'.$class_alt.'" align="right" valign="middle" colspan="3"></td>
			</tr>';
} //=== end while loop
$HTMLOUT.= $the_top_and_bottom.'</table><a name="bottom"></a><br />'.$links.'<br />';
?>
