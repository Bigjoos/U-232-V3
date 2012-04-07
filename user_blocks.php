<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/*
+------------------------------------------------
|   $Date$ 10022011
|   $Revision$ 1.0
|   $Author$ pdq,Bigjoos
|   $User block system
|   
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'html_functions.php');
require_once(INCL_DIR.'user_functions.php');

dbconn(false);
loggedinorreturn();

$stdfoot = array(/** include js **/'js' => array('custom-form-elements'));
$stdhead = array(/** include css **/'css' => array('user_blocks'));

$lang = load_language('global');

$id = (isset($_GET['id']) ? $_GET['id'] : $CURUSER['id']);
if (!is_valid_id($id) || $CURUSER['class'] < UC_STAFF)
    $id = $CURUSER['id'];

if ($CURUSER['got_blocks'] == 'no'){
stderr("Error", "Time shall unfold what plighted cunning hides\n\nWho cover faults, at last shame them derides.... Go to your Karma bonus page and buy this unlock before trying to access it.");
die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $updateset = array();
    $setbits_index_page = $clrbits_index_page = $setbits_global_stdhead = $clrbits_global_stdhead  = $setbits_userdetails_page = $clrbits_userdetails_page = 0;
    
    //==Index
    if (isset($_POST['ie_alert']))
    	$setbits_index_page |= block_index::IE_ALERT;
    else
    	$clrbits_index_page |= block_index::IE_ALERT;
    
    if (isset($_POST['news']))
    	$setbits_index_page |= block_index::NEWS;
    else
    	$clrbits_index_page |= block_index::NEWS;
    
    if (isset($_POST['shoutbox']))
    	$setbits_index_page |= block_index::SHOUTBOX;
    else
    	$clrbits_index_page |= block_index::SHOUTBOX;
    
    if (isset($_POST['active_users']))
    	$setbits_index_page |= block_index::ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::ACTIVE_USERS;
    
    if (isset($_POST['last_24_active_users']))
    	$setbits_index_page |= block_index::LAST_24_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::LAST_24_ACTIVE_USERS;
    
    if (isset($_POST['irc_active_users']))
    	$setbits_index_page |= block_index::IRC_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::IRC_ACTIVE_USERS;
    
    if (isset($_POST['birthday_active_users']))
    	$setbits_index_page |= block_index::BIRTHDAY_ACTIVE_USERS;
    else
    	$clrbits_index_page |= block_index::BIRTHDAY_ACTIVE_USERS;
    
    if (isset($_POST['stats']))
    	$setbits_index_page |= block_index::STATS;
    else
    	$clrbits_index_page |= block_index::STATS;
    
    if (isset($_POST['disclaimer']))
    	$setbits_index_page |= block_index::DISCLAIMER;
    else
    	$clrbits_index_page |= block_index::DISCLAIMER;
    
    if (isset($_POST['latest_user']))
    	$setbits_index_page |= block_index::LATEST_USER;
    else
    	$clrbits_index_page |= block_index::LATEST_USER;
    
    if (isset($_POST['forumposts']))
    	$setbits_index_page |= block_index::FORUMPOSTS;
    else
    	$clrbits_index_page |= block_index::FORUMPOSTS;
    
    if (isset($_POST['latest_torrents']))
    	$setbits_index_page |= block_index::LATEST_TORRENTS;
    else
    	$clrbits_index_page |= block_index::LATEST_TORRENTS;
    
    if (isset($_POST['latest_torrents_scroll']))
    	$setbits_index_page |= block_index::LATEST_TORRENTS_SCROLL;
    else
    	$clrbits_index_page |= block_index::LATEST_TORRENTS_SCROLL;
    
    if (isset($_POST['announcement']))
    	$setbits_index_page |= block_index::ANNOUNCEMENT;
    else
    	$clrbits_index_page |= block_index::ANNOUNCEMENT;
    
    if (isset($_POST['donation_progress']))
    	$setbits_index_page |= block_index::DONATION_PROGRESS;
    else
    	$clrbits_index_page |= block_index::DONATION_PROGRESS;
    
    if (isset($_POST['advertisements']))
    	$setbits_index_page |= block_index::ADVERTISEMENTS;
    else
    	$clrbits_index_page |= block_index::ADVERTISEMENTS;
    
    if (isset($_POST['radio']))
    	$setbits_index_page |= block_index::RADIO;
    else
    	$clrbits_index_page |= block_index::RADIO;
    
    if (isset($_POST['torrentfreak']))
    	$setbits_index_page |= block_index::TORRENTFREAK;
    else
    	$clrbits_index_page |= block_index::TORRENTFREAK;
    
    if (isset($_POST['xmas_gift']))
    	$setbits_index_page |= block_index::XMAS_GIFT;
    else
    	$clrbits_index_page |= block_index::XMAS_GIFT;
    
    if (isset($_POST['active_poll']))
    	$setbits_index_page |= block_index::ACTIVE_POLL;
    else
    	$clrbits_index_page |= block_index::ACTIVE_POLL;

   if (isset($_POST['staff_shoutbox']))
    	$setbits_index_page |= block_index::STAFF_SHOUT;
    else
    	$clrbits_index_page |= block_index::STAFF_SHOUT;

   if (isset($_POST['movie_ofthe_week']))
      $setbits_index_page |= block_index::MOVIEOFWEEK;
     else
      $clrbits_index_page |= block_index::MOVIEOFWEEK;
    
    //==Stdhead
    if (isset($_POST['stdhead_freeleech']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_FREELEECH;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_FREELEECH;
    
    if (isset($_POST['stdhead_demotion']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_DEMOTION;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_DEMOTION;
    
    if (isset($_POST['stdhead_newpm']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_NEWPM;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_NEWPM;
    
    if (isset($_POST['stdhead_staff_message']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_STAFF_MESSAGE;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_STAFF_MESSAGE;
    
    if (isset($_POST['stdhead_reports']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_REPORTS;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_REPORTS;
    
    if (isset($_POST['stdhead_uploadapp']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_UPLOADAPP;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_UPLOADAPP;
    
    if (isset($_POST['stdhead_happyhour']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_HAPPYHOUR;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_HAPPYHOUR;
    
    if (isset($_POST['stdhead_crazyhour']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_CRAZYHOUR;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_CRAZYHOUR;
      
    if (isset($_POST['stdhead_bugmessage']))
      $setbits_global_stdhead |= block_stdhead::STDHEAD_BUG_MESSAGE;
    else
      $clrbits_global_stdhead |= block_stdhead::STDHEAD_BUG_MESSAGE;

    
    //==Userdetails
    if (isset($_POST['userdetails_login_link']))
    	$setbits_userdetails_page |= block_userdetails::LOGIN_LINK;
    else
    	$clrbits_userdetails_page |= block_userdetails::LOGIN_LINK;
    
    if (isset($_POST['userdetails_flush']))
    	$setbits_userdetails_page |= block_userdetails::FLUSH;
    else
    	$clrbits_userdetails_page |= block_userdetails::FLUSH;
    
    if (isset($_POST['userdetails_joined']))
    	$setbits_userdetails_page |= block_userdetails::JOINED;
    else
    	$clrbits_userdetails_page |= block_userdetails::JOINED;
    
    if (isset($_POST['userdetails_online_time']))
    	$setbits_userdetails_page |= block_userdetails::ONLINETIME;
    else
    	$clrbits_userdetails_page |= block_userdetails::ONLINETIME;
    
    if (isset($_POST['userdetails_browser']))
    	$setbits_userdetails_page |= block_userdetails::BROWSER;
    else
    	$clrbits_userdetails_page |= block_userdetails::BROWSER;
    
    if (isset($_POST['userdetails_reputation']))
    	$setbits_userdetails_page |= block_userdetails::REPUTATION;
    else
    	$clrbits_userdetails_page |= block_userdetails::REPUTATION;
    
    if (isset($_POST['userdetails_user_hits']))
    	$setbits_userdetails_page |= block_userdetails::PROFILE_HITS;
    else
    	$clrbits_userdetails_page |= block_userdetails::PROFILE_HITS;
    
    if (isset($_POST['userdetails_birthday']))
    	$setbits_userdetails_page |= block_userdetails::BIRTHDAY;
    else
    	$clrbits_userdetails_page |= block_userdetails::BIRTHDAY;
    
    if (isset($_POST['userdetails_birthday']))
    	$setbits_userdetails_page |= block_userdetails::BIRTHDAY;
    else
    	$clrbits_userdetails_page |= block_userdetails::BIRTHDAY;
    
    if (isset($_POST['userdetails_contact_info']))
    	$setbits_userdetails_page |= block_userdetails::CONTACT_INFO;
    else
    	$clrbits_userdetails_page |= block_userdetails::CONTACT_INFO;
    
    if (isset($_POST['userdetails_iphistory']))
    	$setbits_userdetails_page |= block_userdetails::IPHISTORY;
    else
    	$clrbits_userdetails_page |= block_userdetails::IPHISTORY;
    
    if (isset($_POST['userdetails_traffic']))
    	$setbits_userdetails_page |= block_userdetails::TRAFFIC;
    else
    	$clrbits_userdetails_page |= block_userdetails::TRAFFIC;
    
    if (isset($_POST['userdetails_share_ratio']))
    	$setbits_userdetails_page |= block_userdetails::SHARE_RATIO;
    else
    	$clrbits_userdetails_page |= block_userdetails::SHARE_RATIO;
    
    if (isset($_POST['userdetails_seedtime_ratio']))
    	$setbits_userdetails_page |= block_userdetails::SEEDTIME_RATIO;
    else
    	$clrbits_userdetails_page |= block_userdetails::SEEDTIME_RATIO;
    
    if (isset($_POST['userdetails_seedbonus']))
    	$setbits_userdetails_page |= block_userdetails::SEEDBONUS;
    else
    	$clrbits_userdetails_page |= block_userdetails::SEEDBONUS;
    
    if (isset($_POST['userdetails_irc_stats']))
    	$setbits_userdetails_page |= block_userdetails::IRC_STATS;
    else
    	$clrbits_userdetails_page |= block_userdetails::IRC_STATS;
    	
    if (isset($_POST['userdetails_connectable_port']))
    	$setbits_userdetails_page |= block_userdetails::CONNECTABLE_PORT;
    else
    	$clrbits_userdetails_page |= block_userdetails::CONNECTABLE_PORT;
    
    if (isset($_POST['userdetails_avatar']))
    	$setbits_userdetails_page |= block_userdetails::AVATAR;
    else
    	$clrbits_userdetails_page |= block_userdetails::AVATAR;
    	
    if (isset($_POST['userdetails_userclass']))
    	$setbits_userdetails_page |= block_userdetails::USERCLASS;
    else
    	$clrbits_userdetails_page |= block_userdetails::USERCLASS;
    	
    if (isset($_POST['userdetails_gender']))
    	$setbits_userdetails_page |= block_userdetails::GENDER;
    else
    	$clrbits_userdetails_page |= block_userdetails::GENDER;
    
    if (isset($_POST['userdetails_freestuffs']))
    	$setbits_userdetails_page |= block_userdetails::FREESTUFFS;
    else
    	$clrbits_userdetails_page |= block_userdetails::FREESTUFFS;
    
    if (isset($_POST['userdetails_comments']))
    	$setbits_userdetails_page |= block_userdetails::COMMENTS;
    else
    	$clrbits_userdetails_page |= block_userdetails::COMMENTS;
    
    if (isset($_POST['userdetails_forumposts']))
    	$setbits_userdetails_page |= block_userdetails::FORUMPOSTS;
    else
    	$clrbits_userdetails_page |= block_userdetails::FORUMPOSTS;
    
    if (isset($_POST['userdetails_invitedby']))
    	$setbits_userdetails_page |= block_userdetails::INVITEDBY;
    else
    	$clrbits_userdetails_page |= block_userdetails::INVITEDBY;
    
    if (isset($_POST['userdetails_torrents_block']))
    	$setbits_userdetails_page |= block_userdetails::TORRENTS_BLOCK;
    else
    	$clrbits_userdetails_page |= block_userdetails::TORRENTS_BLOCK;
    
    if (isset($_POST['userdetails_completed']))
    	$setbits_userdetails_page |= block_userdetails::COMPLETED;
    else
    	$clrbits_userdetails_page |= block_userdetails::COMPLETED;
    
    if (isset($_POST['userdetails_snatched_staff']))
    	$setbits_userdetails_page |= block_userdetails::SNATCHED_STAFF;
    else
    	$clrbits_userdetails_page |= block_userdetails::SNATCHED_STAFF;
    
    if (isset($_POST['userdetails_userinfo']))
    	$setbits_userdetails_page |= block_userdetails::USERINFO;
    else
    	$clrbits_userdetails_page |= block_userdetails::USERINFO;
    
    if (isset($_POST['userdetails_showpm']))
    	$setbits_userdetails_page |= block_userdetails::SHOWPM;
    else
    	$clrbits_userdetails_page |= block_userdetails::SHOWPM;
    
    if (isset($_POST['userdetails_report_user']))
    	$setbits_userdetails_page |= block_userdetails::REPORT_USER;
    else
    	$clrbits_userdetails_page |= block_userdetails::REPORT_USER;
    
    if (isset($_POST['userdetails_user_status']))
    	$setbits_userdetails_page |= block_userdetails::USERSTATUS;
    else
    	$clrbits_userdetails_page |= block_userdetails::USERSTATUS;
    
    if (isset($_POST['userdetails_user_comments']))
    	$setbits_userdetails_page |= block_userdetails::USERCOMMENTS;
    else
    	$clrbits_userdetails_page |= block_userdetails::USERCOMMENTS;
  
    //== set n clear
    if ($setbits_index_page)
      $updateset[] = 'index_page = (index_page | '.$setbits_index_page.')';
    
    if ($clrbits_index_page)
      $updateset[] = 'index_page = (index_page & ~'.$clrbits_index_page.')';
      
    if ($setbits_global_stdhead)
      $updateset[] = 'global_stdhead = (global_stdhead | '.$setbits_global_stdhead.')';
    
    if ($clrbits_global_stdhead)
      $updateset[] = 'global_stdhead = (global_stdhead & ~'.$clrbits_global_stdhead.')';
      
    if ($setbits_userdetails_page)
      $updateset[] = 'userdetails_page = (userdetails_page | '.$setbits_userdetails_page.')';
    
    if ($clrbits_userdetails_page)
      $updateset[] = 'userdetails_page = (userdetails_page & ~'.$clrbits_userdetails_page.')';
    
    if (count($updateset))
      sql_query('UPDATE user_blocks SET '.implode(',', $updateset).' WHERE userid = '.sqlesc($id)) or sqlerr(__FILE__, __LINE__);
      $mc1->delete_value('blocks::'.$id);
      header('Location: '.$INSTALLER09['baseurl'].'/user_blocks.php');
      exit();
    }
    
        //==Index
        $checkbox_index_ie_alert = ((curuser::$blocks['index_page'] & block_index::IE_ALERT) ? ' checked="checked"' : '');
        $checkbox_index_news = ((curuser::$blocks['index_page'] & block_index::NEWS) ? ' checked="checked"' : '');
        $checkbox_index_shoutbox = ((curuser::$blocks['index_page'] & block_index::SHOUTBOX) ? ' checked="checked"' : '');
        $checkbox_index_active_users = ((curuser::$blocks['index_page'] & block_index::ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_index_active_24h_users = ((curuser::$blocks['index_page'] & block_index::LAST_24_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_index_active_irc_users = ((curuser::$blocks['index_page'] & block_index::IRC_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_index_active_birthday_users = ((curuser::$blocks['index_page'] & block_index::BIRTHDAY_ACTIVE_USERS) ? ' checked="checked"' : '');
        $checkbox_index_stats = ((curuser::$blocks['index_page'] & block_index::STATS) ? ' checked="checked"' : '');
        $checkbox_index_disclaimer = ((curuser::$blocks['index_page'] & block_index::DISCLAIMER) ? ' checked="checked"' : '');
        $checkbox_index_latest_user = ((curuser::$blocks['index_page'] & block_index::LATEST_USER) ? ' checked="checked"' : '');
        $checkbox_index_latest_forumposts = ((curuser::$blocks['index_page'] & block_index::FORUMPOSTS) ? ' checked="checked"' : '');
        $checkbox_index_latest_torrents = ((curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS) ? ' checked="checked"' : '');
        $checkbox_index_latest_torrents_scroll = ((curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS_SCROLL) ? ' checked="checked"' : '');
        $checkbox_index_announcement = ((curuser::$blocks['index_page'] & block_index::ANNOUNCEMENT) ? ' checked="checked"' : '');
        $checkbox_index_donation_progress = ((curuser::$blocks['index_page'] & block_index::DONATION_PROGRESS) ? ' checked="checked"' : '');
        $checkbox_index_ads = ((curuser::$blocks['index_page'] & block_index::ADVERTISEMENTS) ? ' checked="checked"' : '');
        $checkbox_index_radio = ((curuser::$blocks['index_page'] & block_index::RADIO) ? ' checked="checked"' : '');
        $checkbox_index_torrentfreak = ((curuser::$blocks['index_page'] & block_index::TORRENTFREAK) ? ' checked="checked"' : '');
        $checkbox_index_xmasgift = ((curuser::$blocks['index_page'] & block_index::XMAS_GIFT) ? ' checked="checked"' : '');
        $checkbox_index_active_poll = ((curuser::$blocks['index_page'] & block_index::ACTIVE_POLL) ? ' checked="checked"' : '');
        $checkbox_index_staffshoutbox = ((curuser::$blocks['index_page'] & block_index::STAFF_SHOUT) ? ' checked="checked"' : '');
        $checkbox_index_mow = ((curuser::$blocks['index_page'] & block_index::MOVIEOFWEEK) ? ' checked="checked"' : '');
        //==Stdhead
        $checkbox_global_freeleech = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_FREELEECH) ? ' checked="checked"' : '');
        $checkbox_global_demotion = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_DEMOTION) ? ' checked="checked"' : '');
        $checkbox_global_message_alert = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_NEWPM) ? ' checked="checked"' : '');
        $checkbox_global_staff_message_alert = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_STAFF_MESSAGE) ? ' checked="checked"' : '');
        $checkbox_global_staff_report = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_REPORTS) ? ' checked="checked"' : '');
        $checkbox_global_staff_uploadapp = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_UPLOADAPP) ? ' checked="checked"' : '');
        $checkbox_global_happyhour = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_HAPPYHOUR) ? ' checked="checked"' : '');
        $checkbox_global_crazyhour = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_CRAZYHOUR) ? ' checked="checked"' : '');
        $checkbox_global_bugmessage = ((curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_BUG_MESSAGE) ? ' checked="checked"' : '');
        //==Userdetails
        $checkbox_userdetails_login_link = ((curuser::$blocks['userdetails_page'] & block_userdetails::LOGIN_LINK) ? ' checked="checked"' : '');
        $checkbox_userdetails_flush = ((curuser::$blocks['userdetails_page'] & block_userdetails::FLUSH) ? ' checked="checked"' : '');
        $checkbox_userdetails_joined = ((curuser::$blocks['userdetails_page'] & block_userdetails::JOINED) ? ' checked="checked"' : '');
        $checkbox_userdetails_onlinetime = ((curuser::$blocks['userdetails_page'] & block_userdetails::ONLINETIME) ? ' checked="checked"' : '');
        $checkbox_userdetails_browser = ((curuser::$blocks['userdetails_page'] & block_userdetails::BROWSER) ? ' checked="checked"' : '');
        $checkbox_userdetails_reputation = ((curuser::$blocks['userdetails_page'] & block_userdetails::REPUTATION) ? ' checked="checked"' : '');
        $checkbox_userdetails_userhits = ((curuser::$blocks['userdetails_page'] & block_userdetails::PROFILE_HITS) ? ' checked="checked"' : '');
        $checkbox_userdetails_birthday = ((curuser::$blocks['userdetails_page'] & block_userdetails::BIRTHDAY) ? ' checked="checked"' : '');
        $checkbox_userdetails_contact_info = ((curuser::$blocks['userdetails_page'] & block_userdetails::CONTACT_INFO) ? ' checked="checked"' : '');
        $checkbox_userdetails_iphistory = ((curuser::$blocks['userdetails_page'] & block_userdetails::IPHISTORY) ? ' checked="checked"' : '');
        $checkbox_userdetails_traffic = ((curuser::$blocks['userdetails_page'] & block_userdetails::TRAFFIC) ? ' checked="checked"' : '');
        $checkbox_userdetails_shareratio = ((curuser::$blocks['userdetails_page'] & block_userdetails::SHARE_RATIO) ? ' checked="checked"' : '');
        $checkbox_userdetails_seedtime_ratio = ((curuser::$blocks['userdetails_page'] & block_userdetails::SEEDTIME_RATIO) ? ' checked="checked"' : '');
        $checkbox_userdetails_seedbonus = ((curuser::$blocks['userdetails_page'] & block_userdetails::SEEDBONUS) ? ' checked="checked"' : '');
        $checkbox_userdetails_irc_stats = ((curuser::$blocks['userdetails_page'] & block_userdetails::IRC_STATS) ? ' checked="checked"' : '');
        $checkbox_userdetails_connectable = ((curuser::$blocks['userdetails_page'] & block_userdetails::CONNECTABLE_PORT) ? ' checked="checked"' : '');
        $checkbox_userdetails_avatar = ((curuser::$blocks['userdetails_page'] & block_userdetails::AVATAR) ? ' checked="checked"' : '');
        $checkbox_userdetails_userclass = ((curuser::$blocks['userdetails_page'] & block_userdetails::USERCLASS) ? ' checked="checked"' : '');
        $checkbox_userdetails_gender = ((curuser::$blocks['userdetails_page'] & block_userdetails::GENDER) ? ' checked="checked"' : '');
        $checkbox_userdetails_freestuffs = ((curuser::$blocks['userdetails_page'] & block_userdetails::FREESTUFFS) ? ' checked="checked"' : '');
        $checkbox_userdetails_torrent_comments = ((curuser::$blocks['userdetails_page'] & block_userdetails::COMMENTS) ? ' checked="checked"' : '');
        $checkbox_userdetails_forumposts = ((curuser::$blocks['userdetails_page'] & block_userdetails::FORUMPOSTS) ? ' checked="checked"' : '');
        $checkbox_userdetails_invitedby = ((curuser::$blocks['userdetails_page'] & block_userdetails::INVITEDBY) ? ' checked="checked"' : '');
        $checkbox_userdetails_torrents_block = ((curuser::$blocks['userdetails_page'] & block_userdetails::TORRENTS_BLOCK) ? ' checked="checked"' : '');
        $checkbox_userdetails_completed = ((curuser::$blocks['userdetails_page'] & block_userdetails::COMPLETED) ? ' checked="checked"' : '');
        $checkbox_userdetails_snatched_staff = ((curuser::$blocks['userdetails_page'] & block_userdetails::SNATCHED_STAFF) ? ' checked="checked"' : '');
        $checkbox_userdetails_userinfo = ((curuser::$blocks['userdetails_page'] & block_userdetails::USERINFO) ? ' checked="checked"' : '');
        $checkbox_userdetails_showpm = ((curuser::$blocks['userdetails_page'] & block_userdetails::SHOWPM) ? ' checked="checked"' : '');
        $checkbox_userdetails_report = ((curuser::$blocks['userdetails_page'] & block_userdetails::REPORT_USER) ? ' checked="checked"' : '');
        $checkbox_userdetails_userstatus = ((curuser::$blocks['userdetails_page'] & block_userdetails::USERSTATUS) ? ' checked="checked"' : '');
        $checkbox_userdetails_usercomments = ((curuser::$blocks['userdetails_page'] & block_userdetails::USERCOMMENTS) ? ' checked="checked"' : '');       
        $HTMLOUT='';
        $HTMLOUT .= begin_frame();

        $HTMLOUT .= '
        <form action="" method="post">
        <div><h1>Index Display Settings</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable IE alert?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the IE user alert.</div></td>
        <td width="40%"><div style="width: auto;" align="right">
        <input type="checkbox" name="ie_alert" value="yes"'.$checkbox_index_ie_alert.' /></div></td>
        </tr></table>

        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable News?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the News Block.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="news" value="yes"'.$checkbox_index_news.' /></div></td>
        </tr></table>

        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Shoutbox?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Shoutbox.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="shoutbox" value="yes"'.$checkbox_index_shoutbox.' /></div></td>
        </tr></table>';

        if($CURUSER['class'] >= UC_STAFF) {
        $HTMLOUT .='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Staff Shoutbox?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Staff Shoutbox.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="staff_shoutbox" value="yes"'.$checkbox_index_staffshoutbox.' /></div></td>
        </tr></table>';
        }

        $HTMLOUT .='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Users?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Active Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="active_users" value="yes"'.$checkbox_index_active_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Users Over 24hours?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Active Users visited over 24hours.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="last_24_active_users" value="yes"'.$checkbox_index_active_24h_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Active Irc Users?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Active Irc Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="irc_active_users" value="yes"'.$checkbox_index_active_irc_users.' /></div></td>
        </tr></table>
      
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Birthday Users?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Active Birthday Users.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="birthday_active_users" value="yes"'.$checkbox_index_active_birthday_users.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Site Stats?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Stats.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stats" value="yes"'.$checkbox_index_stats.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Disclaimer?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable Disclaimer.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="disclaimer" value="yes"'.$checkbox_index_disclaimer.' /></div></td>
        </tr></table>  
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest User?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable Latest User.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_user" value="yes"'.$checkbox_index_latest_user.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest Forum Posts?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable latest Forum Posts.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="forumposts" value="yes"'.$checkbox_index_latest_forumposts.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest torrents?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable latest torrents.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_torrents" value="yes"'.$checkbox_index_latest_torrents.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Latest torrents scroll?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable latest torrents marquee.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="latest_torrents_scroll" value="yes"'.$checkbox_index_latest_torrents_scroll.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Announcement?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Announcement Block.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="announcement" value="yes"'.$checkbox_index_announcement.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Donation Progress?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Donation Progress.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="donation_progress" value="yes"'.$checkbox_index_donation_progress.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Advertisements?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Advertisements.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="advertisements" value="yes"'.$checkbox_index_ads.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Radio?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the site radio.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="radio" value="yes"'.$checkbox_index_radio.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Torrent Freak?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the torrent freak news.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="torrentfreak" value="yes"'.$checkbox_index_torrentfreak.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Xmas Gift?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Christmas Gift.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="xmas_gift" value="yes"'.$checkbox_index_xmasgift.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Poll?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the Active Poll.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="active_poll" value="yes"'.$checkbox_index_active_poll.' /></div></td>
        </tr></table>

        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Enable Movie of the week?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Check this option if you want to enable the MOvie of the week.</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="movie_ofthe_week" value="yes"'.$checkbox_index_mow.' /></div></td>
        </tr></table>
    
        <div><h1>Stdhead Display Settings</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Freeleech?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable "freeleech mark" in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_freeleech" value="yes"'.$checkbox_global_freeleech.' /></div></td>
        </tr></table>';
        
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Demotion</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable the global demotion alert block</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_demotion" value="yes"'.$checkbox_global_demotion.' /></div></td>
        </tr></table>';
        }
        
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Message block?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable message alert block</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_newpm" value="yes"'.$checkbox_global_message_alert.' /></div></td>
        </tr></table>';
        
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Staff Warning?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Shows a warning if there is a new message for staff</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_staff_message" value="yes"'.$checkbox_global_staff_message_alert.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Staff Reports?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable reports alert in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_reports" value="yes"'.$checkbox_global_staff_report.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Upload App Alert?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable upload application alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_uploadapp" value="yes"'.$checkbox_global_staff_uploadapp.' /></div></td>
        </tr></table>';
        }
    
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Happyhour?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable happy hour alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_happyhour" value="yes"'.$checkbox_global_happyhour.' /></div></td>
        </tr></table>
    
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>CrazyHour?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable crazyhour alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_crazyhour" value="yes" '.$checkbox_global_crazyhour.' /></div></td>
        </tr></table>';
        
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Bug Alert Message?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable Bug Message alerts in stdhead</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="stdhead_bugmessage" value="yes" '.$checkbox_global_bugmessage.' /></div></td>
        </tr></table>';
        }
         
        $HTMLOUT.='<div><h1>Userdetails Display Settings</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Login link?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable quick login link</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_login_link" value="yes" '.$checkbox_userdetails_login_link.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Flush torrents?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable flush torrents</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_flush" value="yes" '.$checkbox_userdetails_flush.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Join date?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable join date</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_joined" value="yes" '.$checkbox_userdetails_joined.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Online time?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable online time</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_online_time" value="yes" '.$checkbox_userdetails_onlinetime.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Broswer?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable browser and os detection</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_browser" value="yes" '.$checkbox_userdetails_browser.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Reputation?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable add reputation link</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_reputation" value="yes" '.$checkbox_userdetails_reputation.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Profile hits?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable user hits</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_user_hits" value="yes" '.$checkbox_userdetails_userhits.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Birthday?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable birthdate and age</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_birthday" value="yes" '.$checkbox_userdetails_birthday.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Contact?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable contact infos</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_contact_info" value="yes" '.$checkbox_userdetails_contact_info.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>IP history?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable ip history lists</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_iphistory" value="yes" '.$checkbox_userdetails_iphistory.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>User traffic?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable uploaded and download</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_traffic" value="yes" '.$checkbox_userdetails_traffic.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Share ratio?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable share ratio</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_share_ratio" value="yes" '.$checkbox_userdetails_shareratio.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Seed time ratio?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable seed time per torrent average ratio</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_seedtime_ratio" value="yes" '.$checkbox_userdetails_seedtime_ratio.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Seedbonus?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable seed bonus</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_seedbonus" value="yes" '.$checkbox_userdetails_seedbonus.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>IRC stats?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable irc online stats</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_irc_stats" value="yes" '.$checkbox_userdetails_irc_stats.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Connectable?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable connectable and port</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_connectable_port" value="yes" '.$checkbox_userdetails_connectable.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Avatar?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable avatar</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_avatar" value="yes" '.$checkbox_userdetails_avatar.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Userclass?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable userclass</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_userclass" value="yes" '.$checkbox_userdetails_userclass.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Gender?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable gender</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_gender" value="yes" '.$checkbox_userdetails_gender.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Free stuffs?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable freeslots and freeleech status</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_freestuffs" value="yes" '.$checkbox_userdetails_freestuffs.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Comments?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable torrent comments history</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_comments" value="yes" '.$checkbox_userdetails_torrent_comments.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Forumposts?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable forum posts history</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_forumposts" value="yes" '.$checkbox_userdetails_forumposts.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Invited by?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable invited by list</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_invitedby" value="yes" '.$checkbox_userdetails_invitedby.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Torrents blocks?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable seeding, leeching, snatched and uploaded torrents</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_torrents_block" value="yes" '.$checkbox_userdetails_torrents_block.' /></div></td>
        </tr></table>';
        
        if($CURUSER['class'] >= UC_STAFF){
        $HTMLOUT.='
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Completed?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable completed torrents</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_completed" value="yes" '.$checkbox_userdetails_completed.' /></div></td>
        </tr></table>';
        }
        $HTMLOUT.='<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Staff snatched?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable staff snatchlist</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_snatched_staff" value="yes" '.$checkbox_userdetails_snatched_staff.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>User info?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable user info</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_userinfo" value="yes" '.$checkbox_userdetails_userinfo.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Show pm?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable send message button</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_showpm" value="yes" '.$checkbox_userdetails_showpm.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>Report user?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable report users button</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_report_user" value="yes" '.$checkbox_userdetails_report.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>User status?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable user status</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_user_status" value="yes" '.$checkbox_userdetails_userstatus.' /></div></td>
        </tr></table>
        
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="60%">
        <b>User comments?</b><div><hr style="color:#A83838;" size="1" /></div>
        <div style="color: lightgrey;">Enable user comments</div></td>
        <td width="40%"><div style="width: auto;" align="right"><input type="checkbox" name="userdetails_user_comments" value="yes" '.$checkbox_userdetails_usercomments.' /></div></td>
        </tr></table>';
        $HTMLOUT.='<input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" /></form>';
        $HTMLOUT .= end_frame();
    
echo stdhead("User Blocks Config", true, $stdhead) . $HTMLOUT . stdfoot($stdfoot);
?>
