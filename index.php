<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once INCL_DIR.'user_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once ROOT_DIR.'polls.php';
dbconn(true);
loggedinorreturn();

   $stdhead = array(/** include css **/'css' => array('bbcode'));
   $stdfoot = array(/** include js **/'js' => array('shout','java_klappe'));
   $lang = array_merge( load_language('global'), load_language('index') );
   $HTMLOUT = '';
   //==Global blocks by elephant2
   //==Curuser blocks by pdq
   if (curuser::$blocks['index_page'] & block_index::IE_ALERT && $BLOCKS['ie_user_alert']){
   require_once(BLOCK_DIR.'index/ie_user.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::ANNOUNCEMENT && $BLOCKS['announcement_on']){
   require_once(BLOCK_DIR.'index/announcement.php');
   }
   
   if ($CURUSER['class'] >= UC_STAFF && curuser::$blocks['index_page'] & block_index::STAFF_SHOUT && $BLOCKS['staff_shoutbox_on']){
   require_once(BLOCK_DIR.'index/staff_shoutbox.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::SHOUTBOX && $BLOCKS['shoutbox_on']){
   require_once(BLOCK_DIR.'index/shoutbox.php');
   }

   if (curuser::$blocks['index_page'] & block_index::NEWS && $BLOCKS['news_on']){
   require_once(BLOCK_DIR.'index/news.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::ADVERTISEMENTS && $BLOCKS['ads_on']){
   require_once(BLOCK_DIR.'index/advertise.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::FORUMPOSTS && $BLOCKS['forum_posts_on']){
   require_once(BLOCK_DIR.'index/forum_posts.php');
   }

   if (curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS && $BLOCKS['latest_torrents_on']){
   require_once(BLOCK_DIR.'index/latest_torrents.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::LATEST_TORRENTS_SCROLL && $BLOCKS['latest_torrents_scroll_on']){
   require_once(BLOCK_DIR.'index/latest_torrents_scroll.php');
   }
        
   if (curuser::$blocks['index_page'] & block_index::STATS && $BLOCKS['stats_on']){
   require_once(BLOCK_DIR.'index/stats.php');
   }

   if (curuser::$blocks['index_page'] & block_index::ACTIVE_USERS && $BLOCKS['active_users_on']){
   require_once(BLOCK_DIR.'index/active_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::IRC_ACTIVE_USERS && $BLOCKS['active_irc_users_on']){
   require_once(BLOCK_DIR.'index/active_irc_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::LAST_24_ACTIVE_USERS && $BLOCKS['active_24h_users_on']){
   require_once(BLOCK_DIR.'index/active_24h_users.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::BIRTHDAY_ACTIVE_USERS && $BLOCKS['active_birthday_users_on']){
   require_once(BLOCK_DIR.'index/active_birthday_users.php');
   }

   if (curuser::$blocks['index_page'] & block_index::LATEST_USER && $BLOCKS['latest_user_on']){
   require_once(BLOCK_DIR.'index/latest_user.php');
   }

   if (curuser::$blocks['index_page'] & block_index::ACTIVE_POLL && $BLOCKS['active_poll_on']){
   require_once(BLOCK_DIR.'index/poll.php');
   }

   if (curuser::$blocks['index_page'] & block_index::DONATION_PROGRESS && $BLOCKS['donation_progress_on']){
   require_once(BLOCK_DIR.'index/donations.php');
   }

   if (curuser::$blocks['index_page'] & block_index::XMAS_GIFT && $BLOCKS['xmas_gift_on']){
   require_once(BLOCK_DIR.'index/gift.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::RADIO && $BLOCKS['radio_on']){
   require_once(BLOCK_DIR.'index/radio.php');
   }

   if (curuser::$blocks['index_page'] & block_index::TORRENTFREAK && $BLOCKS['torrentfreak_on']){
   require_once(BLOCK_DIR.'index/torrentfreak.php');
   }
   
   if (curuser::$blocks['index_page'] & block_index::DISCLAIMER && $BLOCKS['disclaimer_on']){
   require_once(BLOCK_DIR.'index/disclaimer.php');
   }

echo stdhead('Home', true, $stdhead) . $HTMLOUT . stdfoot($stdfoot);
?>
