<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once CLASS_DIR.'page_verify.php';
require_once(INCL_DIR.'user_functions.php');
require_once INCL_DIR.'bbcode_functions.php';
require_once INCL_DIR.'html_functions.php';
require_once(INCL_DIR.'function_onlinetime.php');
dbconn(false);
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('userdetails') );

if (function_exists('parked'))
parked();

$newpage = new page_verify(); 
$newpage->create('mdk1@@9'); 

$stdfoot = array(/** include js **/'js' => array('popup','java_klappe','flip_box','flush_torrents'));
 
    $id = 0 + $_GET["id"];
    if (!is_valid_id($id))
    stderr("Error", "{$lang['userdetails_bad_id']}");
    
    $user_fields = 'id, username, passhash, secret, passkey, email, status, added, '.
                     'last_login, last_access, curr_ann_last_check, curr_ann_id, editsecret, privacy, stylesheet, '.
                     'info, acceptpms, ip, class, override_class, language, avatar, av_w, av_h, '.
                     'title, country, notifs, enabled, donor, warned, torrentsperpage, topicsperpage, '.
                     'postsperpage, deletepms, savepms, reputation, time_offset, dst_in_use, auto_correct_dst, '.
                     'show_shout, shoutboxbg, chatpost, smile_until, vip_added, vip_until, '.
                     'freeslots, free_switch, invites, invitedby, invite_rights, anonymous, uploadpos, forumpost, '.
                     'downloadpos, immunity, leechwarn, disable_reason, clear_new_tag_manually, last_browse, sig_w, '.
                     'sig_h, signatures, signature, forum_access, highspeed, hnrwarn, hit_and_run_total, donoruntil, '.
                     'donated, total_donated, vipclass_before, parked, passhint, hintanswer, avatarpos, support, '.
                     'supportfor, sendpmpos, invitedate, invitees, invite_on, subscription_pm, gender,  anonymous_until, '.
                     'viewscloud, tenpercent, avatars, offavatar, pirate, king, hidecur, ssluse, signature_post, forum_post, '.
                     'avatar_rights, offensive_avatar, view_offensive_avatar, paranoia, google_talk, msn, aim, yahoo, website, '.
                     'icq, show_email, parked_until, gotgift, hash1, suspended, bjwins, bjlosses, warn_reason, onirc, irctotal, '.
                     'birthday, got_blocks, last_access_numb, onlinetime, pm_on_delete, commentpm, split, browser, hits, '.
                     'comments, categorie_icon, reputation, perms, mood, got_moods, pms_per_page, show_pm_avatar, watched_user, watched_user_reason, staff_notes, game_access';

    if(($user = $mc1->get_value('user'.$id)) === false) {
    $r1 = sql_query("SELECT ".$user_fields." FROM users WHERE id=".sqlesc($id)."") or sqlerr(__FILE__,__LINE__);
    $user = mysqli_fetch_assoc($r1) or stderr("Error", "{$lang['userdetails_no_user']}");
    $user['id'] = (int)$user['id'];
      $user['added'] = (int)$user['added'];
      $user['last_login'] = (int)$user['last_login'];
      $user['last_access'] = (int)$user['last_access'];
      $user['curr_ann_last_check'] = (int)$user['curr_ann_last_check'];
      $user['curr_ann_id'] = (int)$user['curr_ann_id'];
      $user['stylesheet'] = (int)$user['stylesheet'];
      $user['class'] = (int)$user['class'];
      $user['override_class'] = (int)$user['override_class'];
      $user['av_w'] = (int)$user['av_w'];
      $user['av_h'] = (int)$user['av_h'];
      $user['country'] = (int)$user['country'];
      $user['warned'] = (int)$user['warned'];
      $user['torrentsperpage'] = (int)$user['torrentsperpage'];
      $user['topicsperpage'] = (int)$user['topicsperpage'];
      $user['postsperpage'] = (int)$user['postsperpage'];
      $user['reputation'] = (int)$user['reputation'];
      $user['time_offset'] = (float)$user['time_offset'];
      $user['dst_in_use'] = (int)$user['dst_in_use'];
      $user['auto_correct_dst'] = (int)$user['auto_correct_dst'];
      $user['chatpost'] = (int)$user['chatpost'];
      $user['smile_until'] = (int)$user['smile_until'];
      $user['vip_until'] = (int)$user['vip_until'];
      $user['freeslots'] = (int)$user['freeslots'];
      $user['free_switch'] = (int)$user['free_switch'];
      $user['invites'] = (int)$user['invites'];
      $user['invitedby'] = (int)$user['invitedby'];
      $user['anonymous'] = $user['anonymous'];
      $user['uploadpos'] = (int)$user['uploadpos'];
      $user['forumpost'] = (int)$user['forumpost'];
      $user['downloadpos'] = (int)$user['downloadpos'];
      $user['immunity'] = (int)$user['immunity'];
      $user['leechwarn'] = (int)$user['leechwarn'];
      $user['last_browse'] = (int)$user['last_browse'];
      $user['sig_w'] = (int)$user['sig_w'];
      $user['sig_h'] = (int)$user['sig_h'];
      $user['forum_access'] = (int)$user['forum_access'];
      $user['hit_and_run_total'] = (int)$user['hit_and_run_total'];
      $user['donoruntil'] = (int)$user['donoruntil'];
      $user['donated'] = (int)$user['donated'];
      $user['total_donated'] = (float)$user['total_donated'];
      $user['vipclass_before'] = (int)$user['vipclass_before'];
      $user['passhint'] = (int)$user['passhint'];
      $user['avatarpos'] = (int)$user['avatarpos'];
      $user['sendpmpos'] = (int)$user['sendpmpos'];
      $user['invitedate'] = (int)$user['invitedate'];
      $user['anonymous_until'] = (int)$user['anonymous_until'];
      $user['pirate'] = (int)$user['pirate'];
      $user['king'] = (int)$user['king'];
      $user['ssluse'] = (int)$user['ssluse'];
      $user['paranoia'] = (int)$user['paranoia'];
      $user['parked_until'] = (int)$user['parked_until'];
      $user['bjwins'] = (int)$user['bjwins'];
      $user['bjlosses'] = (int)$user['bjlosses'];
      $user['irctotal'] = (int)$user['irctotal'];
      $user['last_access_numb'] = (int)$user['last_access_numb'];
      $user['onlinetime'] = (int)$user['onlinetime'];
      $user['categorie_icon'] = (int)$user['categorie_icon'];
      $user['perms'] = (int)$user['perms'];
      $user['mood'] = (int)$user['mood'];
      $user['watched_user'] = (int)$user['watched_user'];
      $user['pms_per_page'] = (int)$user['pms_per_page'];
      $user['game_access'] = (int)$user['game_access'];
    $mc1->cache_value('user'.$id, $user, $INSTALLER09['expires']['user_cache']);
    }
    
    if ($user["status"] == "pending") 
    stderr("Error","User is still pending.");

    // user stats
    if(($user_stats = $mc1->get_value('user_stats_'.$id)) === false) {
      $sql_1 = sql_query('SELECT uploaded, downloaded, seedbonus, bonuscomment, modcomment FROM users WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);
      $user_stats = mysqli_fetch_assoc($sql_1);
      $user_stats['seedbonus'] = (float)$user_stats['seedbonus'];
      $user_stats['uploaded'] = (float)$user_stats['uploaded'];
      $user_stats['downloaded'] = (float)$user_stats['downloaded'];
      $user_stats['bonuscomment'] = $user_stats['bonuscomment'];
      $user_stats['modcomment'] = $user_stats['modcomment'];
      $mc1->cache_value('user_stats_'.$id, $user_stats, $INSTALLER09['expires']['user_stats']); // 5 mins
    }
    
    if(($user_status = $mc1->get_value('user_status_'.$id)) === false) { 
       $sql_2 = sql_query('SELECT * FROM ustatus WHERE userid = '.$id);
       if (mysqli_num_rows($sql_2))
           $user_status = mysqli_fetch_assoc($sql_2);
       else
         $user_status=array('last_status'=>'','last_update'=>0,'archive'=>'');
         $mc1->add_value('user_status_'.$id, $user_status, $INSTALLER09['expires']['user_status']); // 30 days
    }

    //===  paranoid settings
    if ($user['paranoia'] == 3 && $CURUSER['class'] < UC_STAFF && $CURUSER['id'] <> $id) {
    stderr('Error!','<span style="font-weight: bold; text-align: center;"><img src="pic/smilies/tinfoilhat.gif" alt="I wear a tin-foil hat!" title="I wear a tin-foil hat!" /> 
    This members paranoia settings are at tinfoil hat levels!!! <img src="pic/smilies/tinfoilhat.gif" alt="I wear a tin-foil hat!" title="I wear a tin-foil hat!" /></span>');
    die();
    }

    //=== delete H&R
    if(isset($_GET['delete_hit_and_run']) && $CURUSER['class'] >= UC_STAFF)
    {
		$delete_me = isset($_GET['delete_hit_and_run']) ? intval($_GET['delete_hit_and_run']) : 0;
			if (!is_valid_id($delete_me))
				stderr('Error!','Bad ID');

    sql_query('UPDATE snatched SET hit_and_run = \'0\', mark_of_cain = \'no\' WHERE id = '.$delete_me) or sqlerr(__FILE__,__LINE__);
		if (@mysqli_affected_rows($GLOBALS["___mysqli_ston"]) === 0)
		{
		stderr('Error!','H&R not deleted!');
		}

		header('Location: ?id='.$id.'&completed=1');
    die();
    }
    
    $r = sql_query("SELECT t.id, t.name, t.seeders, t.leechers, c.name AS cname, c.image FROM torrents t LEFT JOIN categories c ON t.category = c.id WHERE t.owner = $id ORDER BY t.name") or sqlerr(__FILE__,__LINE__);
    if (mysqli_num_rows($r) > 0)
    {
      $torrents = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n" .
        "<tr><td class='colhead'>{$lang['userdetails_type']}</td><td class='colhead'>{$lang['userdetails_name']}</td><td class='colhead'>{$lang['userdetails_seeders']}</td><td class='colhead'>{$lang['userdetails_leechers']}</td></tr>\n";
      while ($a = mysqli_fetch_assoc($r))
      {
        $cat = "<img src=\"". htmlspecialchars("{$INSTALLER09['pic_base_url']}/caticons/{$CURUSER['categorie_icon']}/{$a['image']}") ."\" title=\"{$a['cname']}\" alt=\"{$a['cname']}\" />";
          $torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href='details.php?id=" . (int)$a['id'] . "&amp;hit=1'><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
            "<td align='right'>".(int)$a['seeders']."</td><td align='right'>".(int)$a['leechers']."</td></tr>\n";
      }
      $torrents .= "</table>";
    }
    
    if ($user['ip'] && ($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']))
    {
        $dom = @gethostbyaddr($user['ip']);
        $addr = ($dom == $user['ip'] || @gethostbyname($dom) != $user['ip']) ? $user['ip'] : $user['ip'].' ('.$dom.')';
    }

    if ($user['added'] == 0)
      $joindate = "{$lang['userdetails_na']}";
    else
      $joindate = get_date( $user['added'],'');
    $lastseen = $user["last_access"];
    if ($lastseen == 0)
      $lastseen = "{$lang['userdetails_never']}";
    else
    {
      $lastseen = get_date( $user['last_access'],'',0,1);
    }

    /** #$^$&%$&@ invincible! NO IP LOGGING..pdq **/
    if ((($user['class'] == UC_MAX && $user['id'] == $CURUSER['id']) || ($user['class'] < UC_MAX) && $CURUSER['class'] == UC_MAX) && isset($_GET['invincible'])) { 
    require_once(INCL_DIR.'invincible.php');
    if ($_GET['invincible'] == 'yes')
    $HTMLOUT .= invincible($id);
    elseif ($_GET['invincible'] == 'remove_bypass')
    $HTMLOUT .= invincible($id, true, false);
    else
    $HTMLOUT .= invincible($id, false);
    }// End

    //==country by pdq
    function countries() {
    global $mc1, $INSTALLER09;
    if(($ret = $mc1->get_value('countries::arr')) === false) {
        $res = sql_query("SELECT id, name, flagpic FROM countries ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
        while ($row = mysqli_fetch_assoc($res))
            $ret[] = $row;
        $mc1->cache_value('countries::arr', $ret, $INSTALLER09['expires']['user_flag']);
    }
    return $ret;
    }
    
    $country = '';
    $countries = countries();
    foreach ($countries as $cntry)
    if ($cntry['id'] == $user['country']) {
    $country = "<img src=\"{$INSTALLER09['pic_base_url']}flag/{$cntry['flagpic']}\" alt=\"". htmlspecialchars($cntry['name']) ."\" style='margin-left: 8pt' />";
    break;
    }

    $res = sql_query("SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name as torrentname, t.size, t.category, t.seeders, t.leechers, c.name as catname, c.image FROM peers p LEFT JOIN torrents t ON p.torrent = t.id LEFT JOIN categories c ON t.category = c.id WHERE p.userid=$id") or sqlerr();
    while ($arr = mysqli_fetch_assoc($res))
    {
        if ($arr['seeder'] == 'yes')
            $seeding[] = $arr;
        else
            $leeching[] = $arr;
    }
    
    //==userhits update by pdq
    if (!(isset($_GET["hit"])) && $CURUSER["id"] <> $user["id"]) {
    $res = sql_query("SELECT added FROM userhits WHERE userid ={$CURUSER['id']} AND hitid = ".sqlesc($id)." LIMIT 1") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_row($res);
    if (!($row[0] > TIME_NOW - 3600)) {
        $hitnumber = $user['hits'] + 1;
        sql_query("UPDATE users SET hits = hits + 1 WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
        // do update hits userdetails cache
        $update['user_hits'] = ($user['hits'] + 1);
        $mc1->begin_transaction('MyUser_'.$id);
        $mc1->update_row(false, array('hits' => $update['user_hits']));
        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
        $mc1->begin_transaction('user'.$id);
        $mc1->update_row(false, array('hits' => $update['user_hits']));
        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
        sql_query("INSERT INTO userhits (userid, hitid, number, added) VALUES(".sqlesc($CURUSER['id']).", ".sqlesc($id).", ".sqlesc($hitnumber).", ".sqlesc(TIME_NOW).")") or sqlerr(__FILE__, __LINE__);
    }
    }
  
    $HTMLOUT = $perms = $suspended = $watched_user = $h1_thingie = '';
    if ($user['anonymous'] == 'yes' && ($CURUSER['class'] < UC_STAFF && $user["id"] != $CURUSER["id"]))
    {
	  $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5' class='main'>";
	  $HTMLOUT .= "<tr><td colspan='2' align='center'>{$lang['userdetails_anonymous']}</td></tr>";
	  if ($user["avatar"])
	  $HTMLOUT .= "<tr><td colspan='2' align='center'><img src='" . htmlspecialchars($user["avatar"]) . "'></td></tr>\n";
	  if ($user["info"])
	  $HTMLOUT .= "<tr valign='top'><td align='left' colspan='2' class=text bgcolor='#F4F4F0'>'" . format_comment($user["info"]) . "'</td></tr>\n";
    $HTMLOUT .= "<tr><td colspan='2' align='center'><form method='get' action='{$INSTALLER09['baseurl']}/sendmessage.php'><input type='hidden' name='receiver' value='".(int)$user["id"]."' /><input type='submit' value='{$lang['userdetails_sendmess']}' style='height: 23px' /></form>";
	  if ($CURUSER['class'] < UC_STAFF && $user["id"] != $CURUSER["id"])
	  {
	  $HTMLOUT .= end_main_frame();
	  echo stdhead('Anonymous user') . $HTMLOUT . stdfoot();
    die;
  	}
    $HTMLOUT .= "</td></tr></table><br />";
    }
    $h1_thingie = ((isset($_GET['sn']) || isset($_GET['wu'])) ? '<h1>Member Updated</h1>' : '');
    $suspended .= ($user['suspended'] == 'yes' ? '&nbsp;&nbsp;<img src="'.$INSTALLER09['pic_base_url'].'smilies/excl.gif" alt="Suspended" title="Suspended" />&nbsp;<b>This account has been suspended</b>&nbsp;<img src="'.$INSTALLER09['pic_base_url'].'smilies/excl.gif" alt="Suspended" title="Suspended" />' : '');
    $watched_user .=  ($user['watched_user'] == 0 ? '' : '&nbsp;&nbsp;<img src="'.$INSTALLER09['pic_base_url'].'smilies/excl.gif" align="middle" alt="Watched User" title="Watched User" /> <b>This account is currently on the <a href="staffpanel.php?tool=watched_users" >watched user list</a></b> <img src="'.$INSTALLER09['pic_base_url'].'smilies/excl.gif" align="middle" alt="Watched User" title="Watched User" />');
    $perms .= (($user['perms'] & bt_options::PERMS_NO_IP) ? '&nbsp;&nbsp;<img src="'.$INSTALLER09['pic_base_url'].'smilies/super.gif" alt="Invincible!"  title="Invincible!" />' : '');
    $enabled = $user["enabled"] == 'yes';
    $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='0'>".
    "<tr><td class='embedded'><h1 style='margin:0px'>" . format_username($user, true) . "</h1>$country$perms$watched_user$suspended$h1_thingie</td></tr></table>\n";
   
     if ($user["parked"] == 'yes')
 	  $HTMLOUT .= "<p><b>{$lang['userdetails_parked']}</b></p>\n";
    
      if (!$enabled)
      $HTMLOUT .= "<p><b>{$lang['userdetails_disabled']}</b></p>\n";
      
      elseif ($CURUSER["id"] <> $user["id"])
      {
      if(($friends = $mc1->get_value('Friends_'.$id)) === false) {
      $r3 = sql_query("SELECT id FROM friends WHERE userid={$CURUSER['id']} AND friendid=$id") or sqlerr(__FILE__, __LINE__);
      $friends = mysqli_num_rows($r3);
      $mc1->cache_value('Friends_'.$id, $friends, $INSTALLER09['expires']['user_friends']);
      }
      
      if(($blocks = $mc1->get_value('Blocks_'.$id)) === false) {
      $r4 = sql_query("SELECT id FROM blocks WHERE userid={$CURUSER['id']} AND blockid=$id") or sqlerr(__FILE__, __LINE__);
      $blocks = mysqli_num_rows($r4);
      $mc1->cache_value('Blocks_'.$id, $blocks, $INSTALLER09['expires']['user_blocks']);
      }
       
      if ($friends > 0)
      $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_remove_friends']}</a>)</p>\n";
      else
      $HTMLOUT .= "<p>(<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_add_friends']}</a>)</p>";
      
      if($blocks > 0)
      $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>{$lang['userdetails_remove_blocks']}</a>)</p>\n";
      else
      $HTMLOUT .= "<p>(<a href='friends.php?action=add&amp;type=block&amp;targetid=$id'>{$lang['userdetails_add_blocks']}</a>)</p>\n";
      }
    
    //== 09 Shitlist by Sir_Snuggles
    if ($CURUSER['class'] >= UC_STAFF){
    $shitty = '';
    if(($shit_list = $mc1->get_value('shit_list_'.$id)) === false) {
    $check_if_theyre_shitty = sql_query("SELECT suspect FROM shit_list WHERE userid=".sqlesc($CURUSER['id'])." AND suspect=".$id) or sqlerr(__FILE__, __LINE__);
    list($shit_list) = mysqli_fetch_row($check_if_theyre_shitty); 
    $mc1->cache_value('shit_list_'.$id, $shit_list, $INSTALLER09['expires']['shit_list']);
    }
    
    if ($shit_list > 0){
    $shitty = "<img src='pic/smilies/shit.gif' alt='Shit' title='Shit' />";
    $HTMLOUT .="<br /><b>".$shitty."&nbsp;This member is on your shit list click <a class='altlink' href='staffpanel.php?tool=shit_list&amp;action=shit_list'>HERE</a> to see your shit list&nbsp;".$shitty."</b>";
    }
    elseif ($CURUSER["id"] <> $user["id"]){
    $HTMLOUT .="<br /><a class='altlink' href='staffpanel.php?tool=shit_list&amp;action=shit_list&amp;action2=new&amp;shit_list_id=".$id."&amp;return_to=userdetails.php?id=".$id."'><b>Add member to your shit list</b></a>";
    }
    }
   // ===donor count down
   if ($user["donor"] && $CURUSER["id"] == $user["id"] || $CURUSER["class"] == UC_SYSOP) {
   $donoruntil = htmlspecialchars($user['donoruntil']);
   if ($donoruntil == '0')
   $HTMLOUT.= "";
   else {
   $HTMLOUT.= "<br /><b>Donated Status Until - ".get_date($user['donoruntil'], 'DATE'). "";
   $HTMLOUT.=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go...</b><font size=\"-2\"> To re-new your donation click <a class='altlink' href='{$INSTALLER09['baseurl']}/donate.php'>Here</a>.</font><br /><br />\n";
   }
   }
    
    if ($CURUSER['id'] == $user['id'])
    $HTMLOUT.="<h1><a href='{$INSTALLER09['baseurl']}/usercp.php?action=default'>Edit My Profile</a></h1>
 	  <h1><a href='{$INSTALLER09['baseurl']}/view_announce_history.php'>View My Announcements</a></h1>";
    
    if ($CURUSER['class'] >= UC_STAFF)
	  $HTMLOUT .= "<h1><a href='{$INSTALLER09['baseurl']}/userimages.php?user=".htmlspecialchars($user['username'])."'>{$lang['userdetails_viewimages']}</a></h1>";
    
    if ($CURUSER['id'] != $user['id'])
    $HTMLOUT .="<h1><a href='{$INSTALLER09['baseurl']}/sharemarks.php?id=$id'>View sharemarks</a></h1>\n";
    
    //==invincible no iplogging and ban bypass by pdq
    $invincible = $mc1->get_value('display_'.$CURUSER['id']);
    if ($invincible)
    $HTMLOUT .= '<h1>'.$user['username'].' is '.$invincible.' invincible!</h1>';
    //== links to make invincible method 1(PERMS_NO_IP/ no log ip) and 2(PERMS_BYPASS_BAN/cannot be banned)
    $HTMLOUT .= ($CURUSER['class'] === UC_MAX ? (($user['perms'] & bt_options::PERMS_NO_IP) ? ' - (<a title='.
            "\n".'"Invincible means do not log IP. IP is set to localhost and user is logged out and all '.
            "\n".'IP history is deleted." href="userdetails.php?id='.$id.'&amp;invincible=no">'.
            "\n".'Remove Invincible</a>)'.(($user['perms'] & bt_options::PERMS_BYPASS_BAN) ? ' - '.
            "\n".'(<a title="Invincible means do not log IP. IP is set to localhost and user is logged out.'.
            "\n".' and all IP history is deleted. Immune to ban checks." href="userdetails.php?id='.$id.'&amp;'.
            "\n".'invincible=remove_bypass">Remove Bypass Bans</a>)' : ' - (<a title="Invincible means do not '.
            "\n".'log IP. IP is set to'."\n".' localhost and user is logged out and all IP history is deleted. '.
            "\n".'Not immune to ban checks." href="userdetails.php?id='.$id.'&amp;invincible=yes">'.
            "\n".'Add Bypass Bans</a>)') : ' - (<a title="Invincible means do not log IP. IP is set to localhost'."
            \n".' and user is logged out and all IP history is deleted. Immune to ban checks." '.
            "\n".'href="userdetails.php?id='.$id.'&amp;invincible=yes">Make Invincible</a>)') : '');
    
    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>";
    $moodname = (isset($mood['name'][$user['mood']]) ? htmlspecialchars($mood['name'][$user['mood']]) : 'is feeling neutral');
    $moodpic  = (isset($mood['image'][$user['mood']]) ? htmlspecialchars($mood['image'][$user['mood']]) : 'noexpression.gif');
    $HTMLOUT .='<tr><td class="rowhead">Current Mood</td><td align="left"><span class="tool">
    <a href="javascript:;" onclick="PopUp(\'usermood.php\',\'Mood\',530,500,1,1);">
    <img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$moodpic.'" alt="'.$moodname.'" border="0" />
    <span class="tip">'.htmlspecialchars($user['username']).' '.$moodname.' !</span></a></span></td></tr>';
   // === make sure prople can't see their own naughty history by snuggles
    if (($CURUSER['id'] !== $user['id']) && ($CURUSER['class'] >= UC_STAFF)) 
    {
    //=== watched user stuff
    $the_flip_box = '[ <a name="watched_user"></a><a class="altlink" href="#watched_user" onclick="javascript:flipBox(\'3\')" title="Add - Edit - View Watched User">'.($user['watched_user'] > 0 ? 'Add - Edit - View ' : 'Add - View ').'<img onclick="javascript:flipBox(\'3\')" src="pic/panel_on.gif" name="b_3" style="vertical-align:middle;"   width="8" height="8" alt="Add - Edit - View Watched User" title="Add - Edit - View Watched User" /></a> ]';
      $HTMLOUT .= '<tr><td class="rowhead">Watched User</td>
			<td align="left">'.($user['watched_user'] > 0 ? 'Currently being watched since  '.get_date( $user['watched_user'],'').' ' : ' Not currently being watched ').
			$the_flip_box.'
			<div align="left" id="box_3" style="display:none">
			<form method="post" action="member_input.php" name="notes_for_staff">
			<input name="id" type="hidden" value="'.$id.'" />
			<input type="hidden" value="watched_user" name="action" />
			Add to watched users? 			
			<input type="radio" value="yes" name="add_to_watched_users"'.($user['watched_user'] > 0 ? ' checked="checked"' : '').' /> yes 
			<input type="radio" value="no" name="add_to_watched_users"'.($user['watched_user'] == 0 ? ' checked="checked"' : '').' /> no <br />
			<span id="desc_text" style="color:red;font-size: xx-small;">* you must select yes or no if you wish to change the watched user status!<br />
			you may add, edit or delete the text below without changing their status.</span><br />
			<textarea id="watched_reason" cols="50" rows="6" name="watched_reason">'.htmlspecialchars($user['watched_user_reason']).'</textarea><br />
			<input id="watched_user_button" type="submit" value="Submit!" class="btn" name="watched_user_button" />
			</form></div> </td></tr>';
         //=== staff Notes
      $the_flip_box_4 = '[ <a name="staff_notes"></a><a class="altlink" href="#staff_notes" onclick="javascript:flipBox(\'4\')" name="b_4" title="Open / Close Staff Notes">view <img onclick="javascript:flipBox(\'4\')" src="pic/panel_on.gif" name="b_4" style="vertical-align:middle;" width="8" height="8" alt="Open / Close Staff Notes" title="Open / Close Staff Notes" /></a> ]';
      $HTMLOUT .= '<tr><td class="rowhead">Staff Notes</td><td align="left">		
			<a class="altlink" href="#staff_notes" onclick="javascript:flipBox(\'6\')" name="b_6" title="Add - Edit - View staff note">'.($user['staff_notes'] !== '' ? 'View - Add - Edit ' : 'Add ').'<img onclick="javascript:flipBox(\'6\')" src="pic/panel_on.gif" name="b_6" style="vertical-align:middle;" width="8" height="8" alt="Add - Edit - View staff note" title="Add - Edit - View staff note" /></a>
			<div align="left" id="box_6" style="display:none">
			<form method="post" action="member_input.php" name="notes_for_staff">
			<input name="id" type="hidden" value="'.(int)$user['id'].'" />
			<input type="hidden" value="staff_notes" name="action" id="action" />
			<textarea id="new_staff_note" cols="50" rows="6" name="new_staff_note">'.htmlspecialchars($user['staff_notes']).'</textarea>
			<br /><input id="staff_notes_button" type="submit" value="Submit!" class="btn" name="staff_notes_button"/>
			</form>
			</div> </td></tr>';
      //=== system comments
      $the_flip_box_7 = '[ <a name="system_comments"></a><a class="altlink" href="#system_comments" onclick="javascript:flipBox(\'7\')"  name="b_7" title="Open / Close System Comments">view <img onclick="javascript:flipBox(\'7\')" src="pic/panel_on.gif" name="b_7" style="vertical-align:middle;" width="8" height="8" alt="Open / Close System Comments" title="Open / System Comments" /></a> ]';
      if(!empty($user_stats['modcomment']))
      $HTMLOUT .= "<tr><td class='rowhead'>System Comments</td><td align='left'>".($user_stats['modcomment'] != '' ? $the_flip_box_7.'<div align="left" id="box_7" style="display:none"><hr />'.format_comment($user_stats['modcomment']).'</div>' : '')."</td></tr>\n"; 
     }   
     //==Begin blocks
     if (curuser::$blocks['userdetails_page'] & block_userdetails::SHOWFRIENDS && $BLOCKS['userdetails_showfriends_on']){
	  require_once(BLOCK_DIR.'userdetails/showfriends.php');
	  }

     if (curuser::$blocks['userdetails_page'] & block_userdetails::LOGIN_LINK && $BLOCKS['userdetails_login_link_on']){
	  require_once(BLOCK_DIR.'userdetails/loginlink.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::FLUSH && $BLOCKS['userdetails_flush_on']){
	  require_once(BLOCK_DIR.'userdetails/flush.php');
	  }
    
    if (curuser::$blocks['userdetails_page'] & block_userdetails::JOINED && $BLOCKS['userdetails_joined_on']){
	  require_once(BLOCK_DIR.'userdetails/joined.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::ONLINETIME && $BLOCKS['userdetails_online_time_on']){
	  require_once(BLOCK_DIR.'userdetails/onlinetime.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::BROWSER && $BLOCKS['userdetails_browser_on']){
	  require_once(BLOCK_DIR.'userdetails/browser.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::REPUTATION && $BLOCKS['userdetails_reputation_on']){
	  require_once(BLOCK_DIR.'userdetails/reputation.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::PROFILE_HITS && $BLOCKS['userdetails_profile_hits_on']){
	  require_once(BLOCK_DIR.'userdetails/userhits.php');
	  }
	
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::BIRTHDAY && $BLOCKS['userdetails_birthday_on']){
	  require_once(BLOCK_DIR.'userdetails/birthday.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::CONTACT_INFO && $BLOCKS['userdetails_contact_info_on']){
	  require_once(BLOCK_DIR.'userdetails/contactinfo.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::IPHISTORY && $BLOCKS['userdetails_iphistory_on']){
	  require_once(BLOCK_DIR.'userdetails/iphistory.php');
	  }
	 
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::TRAFFIC && $BLOCKS['userdetails_traffic_on']){
	  require_once(BLOCK_DIR.'userdetails/traffic.php');
	  }
	 
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::SHARE_RATIO && $BLOCKS['userdetails_share_ratio_on']){
	  require_once(BLOCK_DIR.'userdetails/shareratio.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::SEEDTIME_RATIO && $BLOCKS['userdetails_seedtime_ratio_on']){
	  require_once(BLOCK_DIR.'userdetails/seedtimeratio.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::SEEDBONUS && $BLOCKS['userdetails_seedbonus_on']){
	  require_once(BLOCK_DIR.'userdetails/seedbonus.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::IRC_STATS && $BLOCKS['userdetails_irc_stats_on']){
	  require_once(BLOCK_DIR.'userdetails/irc.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::CONNECTABLE_PORT && $BLOCKS['userdetails_connectable_port_on']){
	  require_once(BLOCK_DIR.'userdetails/connectable.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::AVATAR && $BLOCKS['userdetails_avatar_on']){
	  require_once(BLOCK_DIR.'userdetails/avatar.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::USERCLASS && $BLOCKS['userdetails_userclass_on']){
	  require_once(BLOCK_DIR.'userdetails/userclass.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::GENDER && $BLOCKS['userdetails_gender_on']){
	  require_once(BLOCK_DIR.'userdetails/gender.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::FREESTUFFS && $BLOCKS['userdetails_freestuffs_on']){
	  require_once(BLOCK_DIR.'userdetails/freestuffs.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::COMMENTS && $BLOCKS['userdetails_comments_on']){
	  require_once(BLOCK_DIR.'userdetails/comments.php');
	  }
    
     if (curuser::$blocks['userdetails_page'] & block_userdetails::FORUMPOSTS && $BLOCKS['userdetails_forumposts_on']){
	  require_once(BLOCK_DIR.'userdetails/forumposts.php');
	  }
   
     if (curuser::$blocks['userdetails_page'] & block_userdetails::INVITEDBY && $BLOCKS['userdetails_invitedby_on']){
	  require_once(BLOCK_DIR.'userdetails/invitedby.php');
	  }
    
     if (curuser::$blocks['userdetails_page'] & block_userdetails::TORRENTS_BLOCK && $BLOCKS['userdetails_torrents_block_on']){
	  require_once(BLOCK_DIR.'userdetails/torrents_block.php');
	  }
    
     if (curuser::$blocks['userdetails_page'] & block_userdetails::COMPLETED && $BLOCKS['userdetails_completed_on']){
	  require_once(BLOCK_DIR.'userdetails/completed.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::SNATCHED_STAFF && $BLOCKS['userdetails_snatched_staff_on']){
	  require_once(BLOCK_DIR.'userdetails/snatched_staff.php');
	  }
	  
     if (curuser::$blocks['userdetails_page'] & block_userdetails::USERINFO && $BLOCKS['userdetails_userinfo_on']){
	  require_once(BLOCK_DIR.'userdetails/userinfo.php');
	  }
	  
     if (curuser::$blocks['userdetails_page'] & block_userdetails::SHOWPM && $BLOCKS['userdetails_showpm_on']){
	  require_once(BLOCK_DIR.'userdetails/showpm.php');
	  }

     if (curuser::$blocks['userdetails_page'] & block_userdetails::REPORT_USER && $BLOCKS['userdetails_report_user_on']){
	  require_once(BLOCK_DIR.'userdetails/report.php');
	  }
	  
	  if (curuser::$blocks['userdetails_page'] & block_userdetails::USERSTATUS && $BLOCKS['userdetails_user_status_on']){
	  require_once(BLOCK_DIR.'userdetails/userstatus.php');
	  }
     
     $HTMLOUT .= "</table>\n";
 
     if (curuser::$blocks['userdetails_page'] & block_userdetails::USERCOMMENTS && $BLOCKS['userdetails_user_comments_on']){
	  require_once(BLOCK_DIR.'userdetails/usercomments.php');
	  }

     //==end blocks
    $HTMLOUT .="<script type='text/javascript'>
    /*<![CDATA[*/
    function togglepic(bu, picid, formid){
	  var pic = document.getElementById(picid);
	  var form = document.getElementById(formid);
	
	  if(pic.src == bu + '/pic/plus.gif')	{
		pic.src = bu + '/pic/minus.gif';
		form.value = 'minus';
	  }else{
		pic.src = bu + '/pic/plus.gif';
		form.value = 'plus';
	  }
    }
    /*]]>*/
    </script>";

    if ($CURUSER['class'] >= UC_STAFF && $user["class"] < $CURUSER['class'])
    {
      $HTMLOUT .= begin_frame("Edit User", true);
      $HTMLOUT .= "<form method='post' action='staffpanel.php?tool=modtask'>\n";
      require_once CLASS_DIR.'validator.php';
      $HTMLOUT .= validatorForm('ModTask_'.$user['id']);
      $HTMLOUT .= "<input type='hidden' name='action' value='edituser' />\n";
      $HTMLOUT .= "<input type='hidden' name='userid' value='$id' />\n";
      $HTMLOUT .= "<input type='hidden' name='returnto' value='userdetails.php?id=$id' />\n";
      $HTMLOUT .= "
      <table class='main' border='1' cellspacing='0' cellpadding='5'>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_title']}</td><td colspan='2' align='left'><input type='text' size='60' name='title' value='" . htmlspecialchars($user['title']) . "' /></td></tr>\n";
      $avatar = htmlspecialchars($user["avatar"]);
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_avatar_url']}</td><td colspan='2' align='left'><input type='text' size='60' name='avatar' value='$avatar' /></td></tr>\n";
      $HTMLOUT .="<tr>
		  <td class='rowhead'>Signature Rights</td>
		  <td colspan='2' align='left'><input name='signature_post' value='yes' type='radio'".($user['signature_post'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='signature_post' value='no' type='radio'".($user['signature_post'] == "no" ? " checked='checked'" : "")." />No Disable this members signature rights.</td>
	    </tr>
	    <tr>
		  <td class='rowhead'>View Signatures</td>
		  <td colspan='2' align='left'><input name='signatures' value='yes' type='radio'".($user['signatures'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='signatures' value='no' type='radio'".($user['signatures'] == "no" ? " checked='checked'" : "")." /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Signature</td>
		  <td colspan='2' align='left'><textarea cols='60' rows='2' name='signature'>".htmlspecialchars($user['signature'])."</textarea></td>
	    </tr>

	    <tr>
		  <td class='rowhead'>Google Talk</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='google_talk' value='".htmlspecialchars($user['google_talk'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>MSN</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='msn' value='".htmlspecialchars($user['msn'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>AIM</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='aim' value='".htmlspecialchars($user['aim'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Yahoo</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='yahoo' value='".htmlspecialchars($user['yahoo'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>ICQ</td>
	 	  <td colspan='2' align='left'><input type='text' size='60' name='icq' value='".htmlspecialchars($user['icq'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Website</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='website' value='".htmlspecialchars($user['website'])."' /></td>
	    </tr>";
      //== we do not want mods to be able to change user classes or amount donated...
      // === Donor mod time based by snuggles
     if ($CURUSER["class"] == UC_MAX) {
     $donor = $user["donor"] == "yes";
     $HTMLOUT .="<tr><td class='rowhead' align='right'><b>{$lang['userdetails_donor']}</b></td><td colspan='2' align='center'>";
     if ($donor) {
     $donoruntil = (int)$user['donoruntil'];
     if ($donoruntil == '0')
     $HTMLOUT .="Arbitrary duration";
     else {
     $HTMLOUT .="<b>".$lang['userdetails_donor2']."</b> ".get_date($user['donoruntil'], 'DATE'). " ";
     $HTMLOUT .=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go\n";
     }
     } else {
     $HTMLOUT .="{$lang['userdetails_dfor']}<select name='donorlength'><option value='0'>------</option><option value='4'>1 month</option>" .
     "<option value='6'>6 weeks</option><option value='8'>2 months</option><option value='10'>10 weeks</option>" .
     "<option value='12'>3 months</option><option value='255'>Unlimited</option></select>\n";
     }
     $HTMLOUT .="<br /><b>{$lang['userdetails_cdonation']}</b><input type='text' size='6' name='donated' value=\"" .htmlspecialchars($user["donated"]) . "\" />" . "<b>{$lang['userdetails_tdonations']}</b>" . htmlspecialchars($user["total_donated"]) . "";
     if ($donor) {
     $HTMLOUT .="<br /><b>{$lang['userdetails_adonor']}</b> <select name='donorlengthadd'><option value='0'>------</option><option value='4'>1 month</option>" .
     "<option value='6'>6 weeks</option><option value='8'>2 months</option><option value='10'>10 weeks</option>" .
     "<option value='12'>3 months</option><option value='255'>Unlimited</option></select>\n";
     $HTMLOUT .="<br /><b>{$lang['userdetails_rdonor']}</b><input name='donor' value='no' type='checkbox' /> [ If they were bad ]";
     }
     $HTMLOUT .="</td></tr>\n";
     }
     // ====End
     
      if ($CURUSER['class'] == UC_STAFF && $user["class"] > UC_VIP)
        $HTMLOUT .= "<input type='hidden' name='class' value='{$user['class']}' />\n";
      else
      {
        $HTMLOUT .= "<tr><td class='rowhead'>Class</td><td colspan='2' align='left'><select name='class'>\n";
        if ($CURUSER['class'] == UC_STAFF)
          $maxclass = UC_VIP;
        else
          $maxclass = $CURUSER['class'] - 1;
        for ($i = 0; $i <= $maxclass; ++$i)
          $HTMLOUT .= "<option value='$i'" . ($user["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n";
        $HTMLOUT .= "</select></td></tr>\n";
      }
      $supportfor = htmlspecialchars($user["supportfor"]);
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_support']}</td><td colspan='2' align='left'><input type='radio' name='support' value='yes'" .($user["support"] == "yes" ? " checked='checked'" : "")." />{$lang['userdetails_yes']}<input type='radio' name='support' value='no'" .($user["support"] == "no" ? " checked='checked'" : "")." />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_supportfor']}</td><td colspan='2' align='left'><textarea cols='60' rows='2' name='supportfor'>{$supportfor}</textarea></td></tr>\n";

      $modcomment = htmlspecialchars($user_stats["modcomment"]);
      if ($CURUSER["class"] < UC_SYSOP) {
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment' readonly='readonly'>$modcomment</textarea></td></tr>\n";
      }
      else {
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment'>$modcomment</textarea></td></tr>\n";
      }
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_add_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='2' name='addcomment'></textarea></td></tr>\n";
      //=== bonus comment 
      $bonuscomment = htmlspecialchars($user_stats["bonuscomment"]);
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_bonus_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='bonuscomment' readonly='readonly' style='background:purple;color:yellow;'>$bonuscomment</textarea></td></tr>\n";
      //==end
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_enabled']}</td><td colspan='2' align='left'><input name='enabled' value='yes' type='radio'" . ($enabled ? " checked='checked'" : "") . " />{$lang['userdetails_yes']} <input name='enabled' value='no' type='radio'" . (!$enabled ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>Freeleech Slots:</td><td colspan='2' align='left'>
      <input type='text' size='6' name='freeslots' value='".(int)$user['freeslots']."' /></td></tr>";
      if ($CURUSER['class'] >= UC_ADMINISTRATOR) {
	    $free_switch = $user['free_switch'] != 0;
      $HTMLOUT .= "<tr><td class='rowhead'".(!$free_switch ? ' rowspan="2"' : '').">Freeleech Status</td>
 	    <td align='left' width='20%'>".($free_switch ?
      "<input name='free_switch' value='42' type='radio' />Remove Freeleech Status" :
      "No Freeleech Status Set")."</td>\n";
      if ($free_switch)
      {
      if ($user['free_switch'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['free_switch'], 'DATE'). " (". mkprettytime($user['free_switch'] - TIME_NOW). " to go)</td></tr>";
      } else
      {
      $HTMLOUT .= '<td>Freeleech for <select name="free_switch">
      <option value="0">------</option>
      <option value="1">1 week</option>
      <option value="2">2 weeks</option>
      <option value="4">4 weeks</option>
      <option value="8">8 weeks</option>
      <option value="255">Unlimited</option>
      </select></td></tr>
      <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="free_pm" /></td></tr>';
      }
      }
     //==Download disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $downloadpos = $user['downloadpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$downloadpos ? ' rowspan="2"' : '').">{$lang['userdetails_dpos']}</td>
 	   <td align='left' width='20%'>".($downloadpos ? "<input name='downloadpos' value='42' type='radio' />Remove download disablement" : "No disablement Status Set")."</td>\n";

     if ($downloadpos)
     {
     if ($user['downloadpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['downloadpos'], 'DATE'). " (".mkprettytime($user['downloadpos'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="downloadpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="disable_pm" /></td></tr>';
     }
     }
     //==Upload disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $uploadpos = $user['uploadpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$uploadpos ? ' rowspan="2"' : '').">{$lang['userdetails_upos']}</td>
 	   <td align='left' width='20%'>".($uploadpos ? "<input name='uploadpos' value='42' type='radio' />Remove upload disablement" : "No disablement Status Set")."</td>\n";

     if ($uploadpos)
     {
     if ($user['uploadpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['uploadpos'], 'DATE'). " (".mkprettytime($user['uploadpos'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="uploadpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="updisable_pm" /></td></tr>';
     }
     }
     //==Pm disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $sendpmpos = $user['sendpmpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$sendpmpos ? ' rowspan="2"' : '').">{$lang['userdetails_pmpos']}</td>
 	   <td align='left' width='20%'>".($sendpmpos ? "<input name='sendpmpos' value='42' type='radio' />Remove pm disablement" : "No disablement Status Set")."</td>\n";

     if ($sendpmpos)
     {
     if ($user['sendpmpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['sendpmpos'], 'DATE'). " (".mkprettytime($user['sendpmpos'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="sendpmpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="pmdisable_pm" /></td></tr>';
     }
     }
     //==Shoutbox disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $chatpost = $user['chatpost'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$chatpost ? ' rowspan="2"' : '').">{$lang['userdetails_chatpos']}</td>
 	   <td align='left' width='20%'>".($chatpost ? "<input name='chatpost' value='42' type='radio' />Remove Shout disablement" : "No disablement Status Set")."</td>\n";

     if ($chatpost)
     {
     if ($user['chatpost'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['chatpost'], 'DATE'). " (".mkprettytime($user['chatpost'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="chatpost">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="chatdisable_pm" /></td></tr>';
     }
     }
     //==Avatar disable
     if ($CURUSER['class'] >= UC_STAFF) {
     $avatarpos = $user['avatarpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$avatarpos ? ' rowspan="2"' : '').">{$lang['userdetails_avatarpos']}</td>
       <td align='left' width='20%'>".($avatarpos ? "<input name='avatarpos' value='42' type='radio' />Remove Avatar disablement" : "No disablement Status Set")."</td>\n";

     if ($avatarpos)
     {
     if ($user['avatarpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['avatarpos'], 'DATE'). " (".mkprettytime($user['avatarpos'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="avatarpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="avatardisable_pm" /></td></tr>';
     }
     }
     //==Immunity
     if ($CURUSER['class'] >= UC_STAFF) {
	   $immunity = $user['immunity'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$immunity ? ' rowspan="2"' : '').">{$lang['userdetails_immunity']}</td>
 	   <td align='left' width='20%'>".($immunity ? "<input name='immunity' value='42' type='radio' />Remove immune Status" : "No immunity Status Set")."</td>\n";

      if ($immunity)
      {
      if ($user['immunity'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['immunity'], 'DATE'). " (".
            mkprettytime($user['immunity'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Immunity for <select name="immunity">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="immunity_pm" /></td></tr>';
     }
     }
     //==End
     //==Leech Warnings
     if ($CURUSER['class'] >= UC_STAFF) {
	   $leechwarn = $user['leechwarn'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$leechwarn ? ' rowspan="2"' : '').">{$lang['userdetails_leechwarn']}</td>
 	   <td align='left' width='20%'>".($leechwarn ? "<input name='leechwarn' value='42' type='radio' />Remove Leechwarn Status" : "No leech warning Status Set")."</td>\n";

      if ($leechwarn)
      {
      if ($user['leechwarn'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['leechwarn'], 'DATE'). " (".
            mkprettytime($user['leechwarn'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>leechwarn for <select name="leechwarn">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="leechwarn_pm" /></td></tr>';
     }
     }
     //==End
     //==Warnings
     if ($CURUSER['class'] >= UC_STAFF) {
	   $warned = $user['warned'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$warned ? ' rowspan="2"' : '').">{$lang['userdetails_warned']}</td>
 	   <td align='left' width='20%'>".($warned ? "<input name='warned' value='42' type='radio' />Remove warned Status" : "No warning Status Set")."</td>\n";

      if ($warned)
      {
      if ($user['warned'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['warned'], 'DATE'). " (".
            mkprettytime($user['warned'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>'.$lang['userdetails_warn_for'].'<select name="warned">
     <option value="0">'.$lang['userdetails_warn0'].'</option>
     <option value="1">'.$lang['userdetails_warn1'].'</option>
     <option value="2">'.$lang['userdetails_warn2'].'</option>
     <option value="4">'.$lang['userdetails_warn4'].'</option>
     <option value="8">'.$lang['userdetails_warn8'].'</option>
     <option value="255">'.$lang['userdetails_warninf'].'</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">'.$lang['userdetails_pm_comm'].'<input type="text" size="60" name="warned_pm" /></td></tr>';
     }
     }
     //==End
     //==Games disable
     if ($CURUSER['class'] >= UC_STAFF) {
       $game_access = $user['game_access'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$game_access ? ' rowspan="2"' : '').">{$lang['userdetails_games']}</td>
        <td align='left' width='20%'>".($game_access ? "<input name='game_access' value='42' type='radio' />Remove games disablement" : "No disablement Status Set")."</td>\n";

     if ($game_access)
     {
     if ($user['game_access'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['game_access'], 'DATE'). " (".mkprettytime($user['game_access'] - TIME_NOW). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="game_access">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="game_disable_pm" /></td></tr>';
     }
     }   
      //==High speed
      if ($CURUSER["class"] == UC_MAX) {
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_highspeed']}</td><td class='row' colspan='2' align='left'><input type='radio' name='highspeed' value='yes' " .($user["highspeed"] == "yes" ? " checked='checked'" : "") ." />Yes <input type='radio' name='highspeed' value='no' " . ($user["highspeed"] == "no" ? " checked='checked'" : "") . " />No</td></tr>\n";
      }
     $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_park']}</td><td colspan='2' align='left'><input name='parked' value='yes' type='radio'" .
	   ($user["parked"] == "yes" ? " checked='checked'" : "") . " />{$lang['userdetails_yes']} <input name='parked' value='no' type='radio'" .
	   ($user["parked"] == "no" ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_reset']}</td><td colspan='2'><input type='checkbox' name='resetpasskey' value='1' /><font class='small'>{$lang['userdetails_pass_msg']}</font></td></tr>";
      // == seedbonus
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_bonus_points']}</td><td colspan='2' align='left'><input type='text' size='6' name='seedbonus' value='".(int)$user_stats['seedbonus']."' /></td></tr>";
      // ==end
      // == rep
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_rep_points']}</td><td colspan='2' align='left'><input type='text' size='6' name='reputation' value='".(int)$user['reputation']."' /></td></tr>";
      // ==end
      //==Invites
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_invright']}</td><td colspan='2' align='left'><input type='radio' name='invite_on' value='yes'" .($user["invite_on"]=="yes" ? " checked='checked'" : "") . " />{$lang['userdetails_yes']}<input type='radio' name='invite_on' value='no'" .($user["invite_on"]=="no" ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .= "<tr><td class='rowhead'><b>{$lang['userdetails_invites']}</b></td><td colspan='2' align='left'><input type='text' size='3' name='invites' value='" . htmlspecialchars($user['invites']) . "' /></td></tr>\n";
      
      $HTMLOUT.="<tr>
		  <td class='rowhead'>Avatar Rights</td>
		  <td colspan='2' align='left'><input name='view_offensive_avatar' value='yes' type='radio'".($user['view_offensive_avatar'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='view_offensive_avatar' value='no' type='radio'".($user['view_offensive_avatar'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>	
	    <tr>
		  <td class='rowhead'>Offensive Avatar</td>
		  <td colspan='2' align='left'><input name='offensive_avatar' value='yes' type='radio'".($user['offensive_avatar'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='offensive_avatar' value='no' type='radio'".($user['offensive_avatar'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>
	    <tr>
		  <td class='rowhead'>View Offensive Avatars</td>
		  <td colspan='2' align='left'><input name='avatar_rights' value='yes' type='radio'".($user['avatar_rights'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='avatar_rights' value='no' type='radio'".($user['avatar_rights'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>";
      $HTMLOUT .= 
	    '<tr>
		  <td class="rowhead">Hit and Runs</td>
		  <td colspan="2" align="left"><input type="text" size="3" name="hit_and_run_total" value="'.$user['hit_and_run_total'].'" /></td>
	    </tr>
	    <tr>
		  <td class="rowhead">Suspended</td>
		  <td colspan="2" align="left"><input name="suspended" value="yes" type="radio"'.($user['suspended'] == 'yes' ? ' checked="checked"' : '').' />Yes 
		  <input name="suspended" value="no" type="radio"'.($user['suspended'] == 'no' ? ' checked="checked"' : '').' />No 
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please enter the reason, it will be PMed to them<br />
		  <input type="text" size="60" name="suspended_reason" /></td>
	    </tr>';
      $HTMLOUT .="<tr>
		  <td class='rowhead'>Paranoia</td>
		  <td colspan='2' align='left'>
		  <select name='paranoia'>
		  <option value='0'".($user['paranoia'] == 0 ? " selected='selected'" : "").">Totally relaxed</option>
		  <option value='1'".($user['paranoia'] == 1 ? " selected='selected'" : "").">Sort of relaxed</option>
		  <option value='2'".($user['paranoia'] == 2 ? " selected='selected'" : "").">Paranoid</option>
		  <option value='3'".($user['paranoia'] == 3 ? " selected='selected'" : "").">Wears a tin-foil hat</option>
		  </select></td>
	    </tr> 
	    <tr>
		  <td class='rowhead'>Forum Rights</td>
		  <td colspan='2' align='left'><input name='forum_post' value='yes' type='radio'".($user['forum_post'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='forum_post' value='no' type='radio'".($user['forum_post'] == "no" ? " checked='checked'" : "")." />No Disable this members forum rights.</td>
	    </tr>
	    ";
      //Adjust up/down
      if ($CURUSER['class']>= UC_ADMINISTRATOR){
      $HTMLOUT .="<tr>
      <td class='rowhead'>{$lang['userdetails_addupload']}</td>
      <td align='center'>
      <img src='{$INSTALLER09['pic_base_url']}plus.gif' alt='Change Ratio' title='Change Ratio !' id='uppic' onclick=\"togglepic('{$INSTALLER09['baseurl']}', 'uppic','upchange')\" /> 
      <input type='text' name='amountup' size='10' />
      </td>
      <td>
      <select name='formatup'>\n
      <option value='mb'>{$lang['userdetails_MB']}</option>\n
      <option value='gb'>{$lang['userdetails_GB']}</option></select>\n
      <input type='hidden' id='upchange' name='upchange' value='plus' />
      </td>
      </tr>
      <tr>
      <td class='rowhead'>{$lang['userdetails_adddownload']}</td>
      <td align='center'>
      <img src='{$INSTALLER09['pic_base_url']}plus.gif' alt='Change Ratio' title='Change Ratio !' id='downpic' onclick=\"togglepic('{$INSTALLER09['baseurl']}','downpic','downchange')\" /> 
      <input type='text' name='amountdown' size='10' />
      </td>
      <td>
      <select name='formatdown'>\n
      <option value='mb'>{$lang['userdetails_MB']}</option>\n
      <option value='gb'>{$lang['userdetails_GB']}</option></select>\n
      <input type='hidden' id='downchange' name='downchange' value='plus' />
      </td></tr>";
      }
      $HTMLOUT .= "<tr><td colspan='3' align='center'><input type='submit' class='btn' value='{$lang['userdetails_okay']}' /></td></tr>\n";
      $HTMLOUT .= "</table>\n";
      $HTMLOUT .= "</form>\n";
      $HTMLOUT .= end_frame();
      }
      $HTMLOUT .= end_main_frame();

echo stdhead("{$lang['userdetails_details']} " . $user["username"]) . $HTMLOUT . stdfoot($stdfoot);
?>
