<?php
//==Start activeusers - pdq
$keys['activeusers'] = 'activeusers';
if (($active_users_cache = $mc1->get_value($keys['activeusers'])) === false) {
    $dt = $_SERVER['REQUEST_TIME'] - 180;
    $activeusers = '';
    $active_users_cache = array();
    $res = sql_query('SELECT id, username, class, donor, title, warned, enabled, chatpost, leechwarn, pirate, king '.'FROM users WHERE last_access >= '.$dt.' AND perms < '.bt_options::PERMS_STEALTH.' ORDER BY username ASC') or sqlerr(__FILE__, __LINE__);
    $actcount = mysqli_num_rows($res);
    while ($arr = mysqli_fetch_assoc($res)) {
        if ($activeusers) $activeusers.= ",\n";
        $activeusers.= '<b>'.format_username($arr).'</b>';
    }
    $active_users_cache['activeusers'] = $activeusers;
    $active_users_cache['actcount'] = $actcount;
    $mc1->cache_value($keys['activeusers'], $active_users_cache, $INSTALLER09['expires']['activeusers']);
}
if (!$active_users_cache['activeusers']) $active_users_cache['activeusers'] = 'There have been no active users in the last 15 minutes.';
$active_users = '<div class="headline">'.$lang['index_active'].'&nbsp;('.$active_users_cache['actcount'].')</div>
     <div class="headbody">
     <!--<a href=\'javascript: klappe_news("a1")\'><img border=\'0\' src=\'pic/plus.gif\' id=\'pica1\' alt=\'[Hide/Show]\' /></a><div id=\'ka1\' style=\'display: none;\'>-->
     <!--<a class="altlink"  title="Click for more info" id="div_open1" style="font-weight:bold;cursor:pointer;"><img border=\'0\' src=\'pic/plus.gif\' alt=\'[Hide/Show]\' /></a>
     <div id="div_info1" style="display:none;background-color:#FEFEF4;max-width:940px;padding: 5px 5px 5px 10px;">-->
     '.$active_users_cache['activeusers'].'
     </div><!--</div>--><br />';
$HTMLOUT.= $active_users;
//== end activeusers
// End Class
// End File
