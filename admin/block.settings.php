<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
/*Block settings by elephant*/
if (!defined('IN_INSTALLER09_ADMIN')) {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
    echo $HTMLOUT;
    exit();
}
$stdfoot = array(
    /** include js **/
    'js' => array(
        'custom-form-elements'
    )
);
$stdhead = array(
    /** include css **/
    'css' => array(
        'global_blocks'
    )
);
$block_set_cache = CACHE_DIR.'block_settings_cache.php';
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    unset($_POST['submit']);
    block_cache();
    exit;
}
/////////////////////////////
//	cache block function
/////////////////////////////
function block_cache()
{
    global $block_set_cache;
    $block_out = "<"."?php\n\n\$BLOCKS = array(\n";
    foreach ($_POST as $k => $v) {
        $block_out.= ($k == 'block_undefined') ? "\t'{$k}' => '".htmlsafechars($v)."',\n" : "\t'{$k}' => ".intval($v).",\n";
    }
    $block_out.= "\n);\n\n?".">";
    if (is_file($block_set_cache) && is_writable(pathinfo($block_set_cache, PATHINFO_DIRNAME))) {
        $filenum = fopen($block_set_cache, 'w');
        ftruncate($filenum, 0);
        fwrite($filenum, $block_out);
        fclose($filenum);
    }
    redirect('staffpanel.php?tool=block.settings&amp;action=block.settings', 'Block Settings Have Been Updated!', 3);
}
function get_cache_array()
{
    return array(
        'ie_user_alert' => 1,
        'active_users_on' => 1,
        'active_24h_users_on' => 1,
        'active_irc_users_on' => 1,
        'active_birthday_users_on' => 1,
        'disclaimer_on' => 1,
        'shoutbox_on' => 1,
        'staff_shoutbox_on' => 1,
        'news_on' => 1,
        'stats_on' => 1,
        'latest_user_on' => 1,
        'forum_posts_on' => 1,
        'latest_torrents_on' => 1,
        'latest_torrents_scroll_on' => 1,
        'announcement' => 1,
        'donation_progress_on' => 1,
        'ads_on' => 1,
        'radio_on' => 1,
        'torrentfreak_on' => 1,
        'xmas_gift_on' => 1,
        'active_poll_on' => 1,
        'movie_ofthe_week_on' => 1,
        'global_demotion_on' => 1,
        'global_staff_warn_on' => 1,
        'global_message_on' => 1,
        'global_staff_uploadapp_on' => 1,
        'global_staff_report_on' => 1,
        'global_freeleech_on' => 1,
        'global_happyhour_on' => 1,
        'global_crazyhour_on' => 1,
        'global_bug_message_on' => 1,
        'userdetails_login_link_on' => 1,
        'userdetails_flush_on' => 1,
        'userdetails_joined_on' => 1,
        'userdetails_online_time_on' => 1,
        'userdetails_browser_on' => 1,
        'userdetails_reputation_on' => 1,
        'userdetails_profile_hits_on' => 1,
        'userdetails_birthday_on' => 1,
        'userdetails_contact_info_on' => 1,
        'userdetails_iphistory_on' => 1,
        'userdetails_traffic_on' => 1,
        'userdetails_share_ratio_on' => 1,
        'userdetails_seedtime_ratio_on' => 1,
        'userdetails_seedbonus_on' => 1,
        'userdetails_irc_stats_on' => 1,
        'userdetails_connectable_port_on' => 1,
        'userdetails_avatar_on' => 1,
        'userdetails_userclass_on' => 1,
        'userdetails_gender_on' => 1,
        'userdetails_freestuffs_on' => 1,
        'userdetails_comments_on' => 1,
        'userdetails_forumposts_on' => 1,
        'userdetails_invitedby_on' => 1,
        'userdetails_torrents_block_on' => 1,
        'userdetails_completed_on' => 1,
        'userdetails_snatched_staff_on' => 1,
        'userdetails_userinfo_on' => 1,
        'userdetails_showpm_on' => 1,
        'userdetails_report_user_on' => 1,
        'userdetails_user_status_on' => 1,
        'userdetails_user_comments_on' => 1
    );
}
if (!is_file($block_set_cache)) {
    $BLOCKS = get_cache_array();
} else {
    require_once $block_set_cache;
    if (!is_array($BLOCKS)) {
        $BLOCKS = get_cache_array();
    }
}
$HTMLOUT = '';
$HTMLOUT.= '
    <div>Global Block Settings</div><br />
    <div><br />
    <form action="staffpanel.php?tool=block.settings&amp;action=block.settings" method="post">
    <div><h1>Index Display Settings</h1></div>
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable IE alert?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the IE user alert.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#ie_user_alert#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable News?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the News Block.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#news_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Shoutbox?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Shoutbox.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#shoutbox_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Staff Shoutbox?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Staff Shoutbox.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#staff_shoutbox_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Users?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Active Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Users Over 24hours?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Active Users visited over 24hours.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_24h_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Active Irc Users?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Active Irc Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_irc_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Birthday Users?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Active Birthday Users.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_birthday_users_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Site Stats?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Stats.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#stats_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Disclaimer?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable Disclaimer.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#disclaimer_on#></div></td>
    </tr></table>  
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest User?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable Latest User.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_user_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest Forum Posts?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable latest Forum Posts.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#forum_posts_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest torrents?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable latest torrents.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_torrents_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Latest torrents scroll?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable latest torrents marquee.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#latest_torrents_scroll_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Announcement?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Announcement Block.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#announcement_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Donation Progress?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Donation Progress.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#donation_progress_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Advertisements?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Advertisements.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#ads_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Radio?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the site radio.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#radio_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Torrent Freak?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the torrent freak news.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#torrentfreak_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Xmas Gift?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Christmas Gift.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#xmas_gift_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Poll?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the Active Poll.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#active_poll_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Enable Movie of the week?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Set this option to "Yes" if you want to enable the movie of the week.</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#movie_ofthe_week_on#></div></td>
    </tr></table>
    
    <div><h1>Stdhead Display Settings</h1></div>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Freeleech?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable "freeleech mark" in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_freeleech_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Demotion</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable the global demotion alert block</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_demotion_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Message block?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable message alert block</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_message_on#></div></td>
    </tr></table>

    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Staff Warning?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Shows a warning if there is a new message for staff</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_warn_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Staff Reports?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable reports alert in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_report_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Upload App Alert?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable upload application alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_staff_uploadapp_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Happyhour?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable happy hour alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_happyhour_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>CrazyHour?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable crazyhour alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_crazyhour_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Bug Message Alert?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable Bug message alerts in stdhead</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#global_bug_message_on#></div></td>
    </tr></table>
    
    <div><h1>Userdetails Display Settings</h1></div>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Quick Login Link?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable quick login link</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_login_link_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Flush Torrents?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable flush torrents</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_flush_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Joined date?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable join date</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_joined_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>User online time?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable user online time</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_online_time_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Browser and OS detect?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable browser and os detection</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_browser_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Reputation?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable reputation link</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_reputation_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Userhits?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable user hits</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_profile_hits_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Birthday?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable birthday display</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_birthday_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Contact info?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable contact info</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_contact_info_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>IP history?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable quick login link</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_iphistory_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Traffic?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable uploaded and downloaded</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_traffic_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Share ratio?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable share ratio</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_share_ratio_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Seed time ratio?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable seedtime ratio</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_seedtime_ratio_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Seedbonus?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable seedbonus</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_seedbonus_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>IRC stats?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable irc stats</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_irc_stats_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Connectable and port?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable connectable and port</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_connectable_port_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Avatar?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable avatar</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_avatar_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Userclass?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable userclass</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_userclass_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Gender?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable gender</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_gender_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Freeslots and Freeleech?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable feeslots and freeleech</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_freestuffs_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Torrent comments?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable torrent comments history</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_comments_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Forum posts?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable forum posts history</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_forumposts_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Invited by?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable invited by</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_invitedby_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Torrent info?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable seeding, leeching, snatched, uploaded torrents</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_torrents_block_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Completed?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable completed torrents</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_completed_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Staff snatched?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable staff snatched torrents</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_snatched_staff_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>User info?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable user info</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_userinfo_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Show pm?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable send message button</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_showpm_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Report?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable report user</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_report_user_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>User status?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable user status</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_user_status_on#></div></td>
    </tr></table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
    <td width="60%">
    <b>Usercomments?</b><div><hr style="color:#A83838;" size="1" /></div>
    <div>Enable usercomments</div></td>
    <td width="40%"><div style="width: auto;" align="right"><#userdetails_user_comments_on#></div></td>
    </tr></table>
    <input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" />
    </form>
    </div>';
$HTMLOUT = preg_replace_callback("|<#(.*?)#>|", "template_out", $HTMLOUT);
echo stdhead("Block Settings", true, $stdhead) , $HTMLOUT, stdfoot($stdfoot);
function template_out($matches)
{
    global $BLOCKS;
    return 'Yes &nbsp; <input name="'.$matches[1].'" value="1" '.($BLOCKS[$matches[1]] == 1 ? 'checked="checked"' : "").' type="radio" />&nbsp;&nbsp;&nbsp;<input name="'.$matches[1].'" value="0" '.($BLOCKS[$matches[1]] == 1 ? "" : 'checked="checked"').' type="radio" /> &nbsp; No';
}
function redirect($url, $text, $time = 2)
{
    global $INSTALLER09;
    $page_title = "Admin Blocks Redirection";
    $page_detail = "<em>Redirecting...</em>";
    $html = "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='refresh' content=\"{$time}; url={$INSTALLER09['baseurl']}/{$url}\" />
		<title>Block Settings</title>
    <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
    </head>
    <body>
    <div>
	  <div>Redirecting</div>
		<div style='padding:8px'>
		<div style='font-size:12px'>$text
		<br />
		<br />
		<a href='{$INSTALLER09['baseurl']}/{$url}'>Click here if not redirected...</a>
		</div>
		</div>
		</div></body></html>";
    echo $html;
    exit;
}
?>
