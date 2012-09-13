<?php
//=== testing concept of "share ratio"
if (($cache_share_ratio = $mc1->get_value('share_ratio_'.$id)) === false) {
    $cache_share_ratio = mysqli_fetch_assoc(sql_query("SELECT SUM(seedtime) AS seed_time_total, COUNT(id) AS total_number FROM snatched WHERE seedtime > '0' AND userid =".sqlesc($user['id'])));
    $cache_share_ratio['total_number'] = (int)$cache_share_ratio['total_number'];
    $cache_share_ratio['seed_time_total'] = (int)$cache_share_ratio['seed_time_total'];
    $mc1->cache_value('share_ratio_'.$id, $cache_share_ratio, $INSTALLER09['expires']['share_ratio']);
}
//=== get times per class
switch (true) {
    //===  member
    
case ($user['class'] == UC_USER):
    $days = 2;
    break;
    //=== Member +
    
case ($user['class'] == UC_POWER_USER):
    $days = 1.5;
    break;
    //=== Member ++
    
case ($user['class'] == UC_VIP || $user['class'] == UC_UPLOADER || $user['class'] == UC_STAFF || $user['class'] == UC_ADMINISTRATOR || $user['class'] == UC_SYSOP):
    $days = 0.5;
    break;
}
if ($cache_share_ratio['seed_time_total'] > 0 && $cache_share_ratio['total_number'] > 0) {
    $avg_time_ratio = (($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']) / 86400 / $days);
    $avg_time_seeding = mkprettytime($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']);
    if ($user["id"] == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF) {
        $HTMLOUT.= '<tr><td class="clearalt5" align="right"><b>Time Ratio:</b></td><td align="left" class="clearalt5">'.(($user_stats['downloaded'] > 0 || $user_stats['uploaded'] > 2147483648) ? '<font color="'.get_ratio_color(number_format($avg_time_ratio, 3)).'">'.number_format($avg_time_ratio, 3).'</font>     '.ratio_image_machine(number_format($avg_time_ratio, 3)).'     [<font color="'.get_ratio_color(number_format($avg_time_ratio, 3)).'"> '.$avg_time_seeding.'</font> per torrent average ]  Ratio based on the assumption that all torrents were New. ' : 'Inf.').'</td></tr>';
    }
}
//==end
// End Class
// End File
