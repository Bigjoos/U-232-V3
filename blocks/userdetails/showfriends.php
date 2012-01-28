<?php
//== Users friends list
    $dt = TIME_NOW - 180;
    $keys['user_friends'] = 'user_friends_'.$id;
    if(($users_friends = $mc1->get_value($keys['user_friends'])) === false) {                    
    $fr = sql_query("SELECT f.friendid as uid, f.userid AS userid, u.last_access, u.id, u.ip, u.avatar, u.username, u.class, u.donor, u.title, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king, u.downloaded, u.uploaded FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$id ORDER BY username ASC LIMIT 100") or  sqlerr(__file__, __line__);
    while($user_friends = mysqli_fetch_assoc($fr))
    $users_friends[] = $user_friends;
    $mc1->cache_value($keys['user_friends'] , $users_friends, 0);
    }
    if (count($users_friends) > 0)
    {
    $user_friends = "<table width='100%' class='main' border='1' cellspacing='0' cellpadding='5'>\n" .
    "<tr><td class='colhead' width='20'>Avatar</td><td class='colhead'>Username".($CURUSER['class'] >= UC_STAFF ? "/Ip" : "")."</td><td class='colhead' align='center'>Uploaded</td><td class='colhead' align='center'>Downloaded</td><td class='colhead' align='center'>Ratio</td><td class='colhead' align='center'>Status</td></tr>\n";
    if ($users_friends)
    {
    foreach($users_friends as $a) {
    $avatar = ($user['avatars'] == 'yes' ? ($a['avatar'] == '' ? '<img src="'.$INSTALLER09['pic_base_url'].'default_avatar.gif"  width="40" alt="default avatar" />' : '<img src="'.htmlspecialchars($a['avatar']).'" alt="avatar"  width="40" />') : '');
    $status = "<img style='vertical-align: middle;' src='{$INSTALLER09['pic_base_url']}".($a['last_access'] > $dt ? "online.png" : "offline.png")."' border='0' alt='' />";
    $user_stuff = $a;
    $user_stuff['id'] = (int)$a['id'];
    $user_friends .= "<tr><td class='one' style='padding: 0px; border: none' width='40px'>".$avatar."</td><td class='one'>".format_username($user_stuff)."<br />".($CURUSER['class'] >= UC_STAFF ? "".htmlentities($a['ip'])."" : "")."</td><td class='one' style='padding: 1px' align='center'>".mksize($a['uploaded'])."</td><td class='one' style='padding: 1px' align='center'>".mksize($a['downloaded'])."</td><td class='one' style='padding: 1px' align='center'>".member_ratio($a['uploaded'], $a['downloaded'])."</td><td class='one' style='padding: 1px' align='center'>".$status."</td></tr>\n";
    }
    $user_friends .= "</table>";
    $HTMLOUT .="<tr><td class='rowhead' width='1%'>Friends&nbsp</td><td align='left' width='99%'><a href=\"javascript: klappe_news('a6')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica6".(int)$a['uid']."\" alt=\"[Hide/Show]\" title=\"[Hide/Show]\" /></a><div id=\"ka6\" style=\"display: none;\"><br />$user_friends</div></td></tr>";
    } else {
    if (empty($users_friends))
    $HTMLOUT .= "<tr><td colspan='2'>No Friends yet.</td></tr>";
    }
    }
    //== thee end
//==end
// End Class

// End File
