<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//==Start execution time
$start = microtime(true);
//==End
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');
require_once(CACHE_DIR.'free_cache.php');
//==Start memcache
require_once(CLASS_DIR.'class_cache.php');
//== tpl
//require_once(INCL_DIR.'tpl_functions.php');
$mc1 = NEW CACHE();
//$mc1->MemcachePrefix = '09source_1_';
//==Block class
class curuser {
public static $blocks  = array();
}
$CURBLOCK = & curuser::$blocks;
require_once CLASS_DIR.'class_blocks_index.php';
require_once CLASS_DIR.'class_blocks_stdhead.php';
require_once CLASS_DIR.'class_blocks_userdetails.php';
require_once CLASS_DIR.'class_bt_options.php';
require_once CACHE_DIR.'block_settings_cache.php';
// ///////Strip slashes by system//////////
function cleanquotes(&$in)
{
    if (is_array($in)) return array_walk($in, 'cleanquotes');
    return $in = stripslashes($in);
}
if (get_magic_quotes_gpc()) {
    array_walk($_GET, 'cleanquotes');
    array_walk($_POST, 'cleanquotes');
    array_walk($_COOKIE, 'cleanquotes');
    array_walk($_REQUEST, 'cleanquotes');
}
/**** validip/getip courtesy of manolete <manolete@myway.com> ****/
// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','0.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

//=== new and faster get IP function by Pandora
function getip()
{
   $ip = $_SERVER['REMOTE_ADDR'];

   if (isset($_SERVER['HTTP_VIA']))
   {
   $forwarded_for = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? (string) $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

      if ($forwarded_for != $ip)
      {
      $ip = $forwarded_for;
      $nums = sscanf($ip, '%d.%d.%d.%d');
      if ($nums[0] === null || $nums[1] === null || $nums[2] === null || $nums[3] === null || $nums[0] == 10 || ($nums[0] == 172 && $nums[1] >= 16 && $nums[1] <= 31) || ($nums[0] == 192 && $nums[1] == 168) || $nums[0] == 239 || $nums[0] == 0 || $nums[0] == 127)
      $ip = $_SERVER['REMOTE_ADDR'];
      }
   }

return $ip;
}

function dbconn($autoclean = false)
{
    global $INSTALLER09;

    if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($INSTALLER09['mysql_host'],  $INSTALLER09['mysql_user'],  $INSTALLER09['mysql_pass'])))
    {
	  switch (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)))
	  {
		case 1040:
		case 2002:
			if ($_SERVER['REQUEST_METHOD'] == "GET")
				die("<html><head><meta http-equiv='refresh' content=\"5 $_SERVER[REQUEST_URI]\"></head><body><table border='0' width='100%' height='100%'><tr><td><h3 align='center'>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
			else
				die("Too many users. Please press the Refresh button in your browser to retry.");
        default:
    	    die("[" . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . "] dbconn: mysql_connect: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
      }
    }
    ((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE {$INSTALLER09['mysql_db']}"))
        or die('dbconn: mysql_select_db: ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
    userlogin();
    if ($autoclean)
        register_shutdown_function("autoclean");
}

function status_change($id) {
sql_query('UPDATE announcement_process SET status = 0 WHERE user_id = '.sqlesc($id).' AND status = 1');
}

function hashit($var,$addtext="")
{
return md5("Th15T3xt".$addtext.$var.$addtext."is5add3dto66uddy6he@water...");
}

//== check bans by djGrrr <3 pdq
function check_bans($ip, &$reason = '') {
        global $INSTALLER09, $mc1;
        $key = 'bans:::'.$ip;
        if(($ban = $mc1->get_value($key)) === false) {
        $nip = ip2long($ip);
        $ban_sql = sql_query('SELECT comment FROM bans WHERE (first <= '.$nip.' AND last >= '.$nip.') LIMIT 1');
        if (mysqli_num_rows($ban_sql)) {
        $comment = mysqli_fetch_row($ban_sql);
        $reason = 'Manual Ban ('.$comment[0].')';
        $mc1->cache_value($key, $reason, 86400); // 86400 // banned
        return true;
        }
        ((mysqli_free_result($ban_sql) || (is_object($ban_sql) && (get_class($ban_sql) == "mysqli_result"))) ? true : false);
        $mc1->cache_value($key, 0, 86400); // 86400 // not banned
        return false;
        }
        elseif (!$ban)
        return false;
        else {
        $reason = $ban;
        return true;
        }
        }

   function userlogin() {
   global $INSTALLER09, $mc1, $CURBLOCK, $mood, $whereis;
   unset($GLOBALS["CURUSER"]);
   $dt = TIME_NOW;
   $ip = getip();
   $nip = ip2long($ip);
   $ipf = $_SERVER['REMOTE_ADDR'];
   if (isset($CURUSER))
      return;
 
   if (!$INSTALLER09['site_online'] || !get_mycookie('uid') || !get_mycookie('pass')|| !get_mycookie('hashv') )
      return;
   $id = 0 + get_mycookie('uid');
   if (!$id OR (strlen( get_mycookie('pass') ) != 32) OR (get_mycookie('hashv') != hashit($id,get_mycookie('pass'))))
      return;
   // let's cache $CURUSER - pdq
   if(($row = $mc1->get_value('MyUser_'.$id)) === false) { // $row not found
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
                     'supportfor, sendpmpos, invitedate, invitees, invite_on, subscription_pm, gender, anonymous_until, '.
                     'viewscloud, tenpercent, avatars, offavatar, pirate, king, hidecur, ssluse, signature_post, forum_post, '.
                     'avatar_rights, offensive_avatar, view_offensive_avatar, paranoia, google_talk, msn, aim, yahoo, website, '.
                     'icq, show_email, parked_until, gotgift, hash1, suspended, bjwins, bjlosses, warn_reason, onirc, irctotal, '.
                     'birthday, got_blocks, last_access_numb, onlinetime, pm_on_delete, commentpm, split, browser, hits, '.
                     'comments, categorie_icon, reputation, perms, mood, got_moods, pms_per_page, show_pm_avatar, watched_user, game_access';
      
      $res = sql_query("SELECT ".$user_fields." ".
                       "FROM users ".
                       "WHERE id = $id ".
                       "AND enabled='yes' ".
                       "AND status = 'confirmed'") or sqlerr(__FILE__, __LINE__);
      
      if (mysqli_num_rows($res) == 0) {
         logoutcookie();
         return;
      }
      $row = mysqli_fetch_assoc($res);
      // Do all ints and floats
      $row['id'] = (int)$row['id'];
      $row['added'] = (int)$row['added'];
      $row['last_login'] = (int)$row['last_login'];
      $row['last_access'] = (int)$row['last_access'];
      $row['curr_ann_last_check'] = (int)$row['curr_ann_last_check'];
      $row['curr_ann_id'] = (int)$row['curr_ann_id'];
      $row['stylesheet'] = (int)$row['stylesheet'];
      $row['class'] = (int)$row['class'];
      $row['override_class'] = (int)$row['override_class'];
      $row['av_w'] = (int)$row['av_w'];
      $row['av_h'] = (int)$row['av_h'];
      $row['country'] = (int)$row['country'];
      $row['warned'] = (int)$row['warned'];
      $row['torrentsperpage'] = (int)$row['torrentsperpage'];
      $row['topicsperpage'] = (int)$row['topicsperpage'];
      $row['postsperpage'] = (int)$row['postsperpage'];
      $row['reputation'] = (int)$row['reputation'];
      $row['time_offset'] = (float)$row['time_offset'];
      $row['dst_in_use'] = (int)$row['dst_in_use'];
      $row['auto_correct_dst'] = (int)$row['auto_correct_dst'];
      $row['chatpost'] = (int)$row['chatpost'];
      $row['smile_until'] = (int)$row['smile_until'];
      $row['vip_until'] = (int)$row['vip_until'];
      $row['freeslots'] = (int)$row['freeslots'];
      $row['free_switch'] = (int)$row['free_switch'];
      $row['invites'] = (int)$row['invites'];
      $row['invitedby'] = (int)$row['invitedby'];
      $row['anonymous'] = $row['anonymous'];
      $row['uploadpos'] = (int)$row['uploadpos'];
      $row['forumpost'] = (int)$row['forumpost'];
      $row['downloadpos'] = (int)$row['downloadpos'];
      $row['immunity'] = (int)$row['immunity'];
      $row['leechwarn'] = (int)$row['leechwarn'];
      $row['last_browse'] = (int)$row['last_browse'];
      $row['sig_w'] = (int)$row['sig_w'];
      $row['sig_h'] = (int)$row['sig_h'];
      $row['forum_access'] = (int)$row['forum_access'];
      $row['hit_and_run_total'] = (int)$row['hit_and_run_total'];
      $row['donoruntil'] = (int)$row['donoruntil'];
      $row['donated'] = (int)$row['donated'];
      $row['total_donated'] = (float)$row['total_donated'];
      $row['vipclass_before'] = (int)$row['vipclass_before'];
      $row['passhint'] = (int)$row['passhint'];
      $row['avatarpos'] = (int)$row['avatarpos'];
      $row['sendpmpos'] = (int)$row['sendpmpos'];
      $row['invitedate'] = (int)$row['invitedate'];
      $row['anonymous_until'] = (int)$row['anonymous_until'];
      $row['pirate'] = (int)$row['pirate'];
      $row['king'] = (int)$row['king'];
      $row['ssluse'] = (int)$row['ssluse'];
      $row['paranoia'] = (int)$row['paranoia'];
      $row['parked_until'] = (int)$row['parked_until'];
      $row['bjwins'] = (int)$row['bjwins'];
      $row['bjlosses'] = (int)$row['bjlosses'];
      $row['irctotal'] = (int)$row['irctotal'];
      $row['last_access_numb'] = (int)$row['last_access_numb'];
      $row['onlinetime'] = (int)$row['onlinetime'];
      $row['categorie_icon'] = (int)$row['categorie_icon'];
      $row['perms'] = (int)$row['perms'];
      $row['mood'] = (int)$row['mood'];
      $row['watched_user'] = (int)$row['watched_user'];
      $row['pms_per_page'] = (int)$row['pms_per_page'];
      $row['game_access'] = (int)$row['game_access'];
      $row['rep'] = get_reputation($row);
      $mc1->cache_value('MyUser_'.$id, $row, $INSTALLER09['expires']['curuser']);
      unset($res);
   }
   //==
   if (get_mycookie('pass') !== md5($row["passhash"].$_SERVER["REMOTE_ADDR"])){ 
   logoutcookie(); 
   return; 
   }
   // bans by djGrrr <3 pdq
   if (!isset($row['perms']) || (!($row['perms'] & bt_options::PERMS_BYPASS_BAN))) {
   $banned = false;
   if (check_bans($ip, $reason))
      $banned = true;
   else {
      if ($ip != $ipf) {
         if (check_bans($ipf, $reason))
            $banned = true;
      }
   }
   if ($banned) {
      header('Content-Type: text/html; charset=utf-8');
      echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
      <title>Forbidden</title>
      </head><body>
      <h1>403 Forbidden</h1>Unauthorized IP address!
      <p>Reason: <strong>'.htmlspecialchars($reason).'</strong></p>
      </body></html>';
      die;
   }
   }
   // Allowed staff
   if ($row["class"] >= UC_STAFF) {
      $allowed_ID = $INSTALLER09['allowed_staff']['id'];
      if (!in_array(((int)$row["id"]), $allowed_ID, true)) {
         $msg = "Fake Account Detected: Username: ".htmlspecialchars($row["username"])." - UserID: ".(int)$row["id"]." - UserIP : ".getip();
         // Demote and disable
         sql_query("UPDATE users SET enabled = 'no', class = 0 WHERE id =".sqlesc($row["id"])."") or sqlerr(__file__, __line__);
         $mc1->begin_transaction('MyUser_'.$row['id']);
         $mc1->update_row(false, array('enabled' => 'no', 'class' => 0));
         $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
         $mc1->begin_transaction('user'.$row['id']);
         $mc1->update_row(false, array('enabled' => 'no', 'class' => 0));
         $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
         write_log($msg);
         logoutcookie();
      }
   }
   // user stats
   if(($stats = $mc1->get_value('userstats_'.$id)) === false) {
      $sql = sql_query('SELECT uploaded, downloaded, seedbonus FROM users WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);
      $stats = mysqli_fetch_assoc($sql);
      $stats['seedbonus'] = (float)$stats['seedbonus'];
      $stats['uploaded'] = (float)$stats['uploaded'];
      $stats['downloaded'] = (float)$stats['downloaded'];
      $ratio = ($stats['downloaded'] > 0 ? $stats['uploaded'] / $stats['downloaded'] : 0);
      $stats['ratio'] = number_format($ratio, 2);
      $mc1->cache_value('userstats_'.$id, $stats, $INSTALLER09['expires']['u_stats']); // 5 mins
   }
   $row['seedbonus'] = $stats['seedbonus'];
   $row['uploaded'] = $stats['uploaded'];
   $row['downloaded'] = $stats['downloaded'];
   $row['ratio'] = $stats['ratio'];
   //==
   if(($ustatus = $mc1->get_value('userstatus_'.$id)) === false) {
       $sql2 = sql_query('SELECT * FROM ustatus WHERE userid = '.$id);
       if (mysqli_num_rows($sql2))
           $ustatus = mysqli_fetch_assoc($sql2);
       else
         $ustatus=array('last_status'=>'','last_update'=>0,'archive'=>'');
         $mc1->add_value('userstatus_'.$id, $ustatus, $INSTALLER09['expires']['u_status']); // 30 days
   }
   $row['last_status'] = $ustatus['last_status'];
   $row['last_update'] = $ustatus['last_update'];
   $row['archive'] = $ustatus['archive']; 
   //==
   if ($row['ssluse'] > 1 && !isset($_SERVER['HTTPS']) && !defined('NO_FORCE_SSL')) {
      $INSTALLER09['baseurl'] = str_replace('http','https',$INSTALLER09['baseurl']);
      header('Location: '.$INSTALLER09['baseurl'].$_SERVER['REQUEST_URI']);
      exit();
   }
   // bitwise curuser bloks by pdq
   $blocks_key = 'blocks::'.$row['id'];
   if(($CURBLOCK = $mc1->get_value($blocks_key)) === false) {
      $c_sql = sql_query('SELECT * FROM user_blocks WHERE userid = '.$row['id']) or sqlerr(__FILE__, __LINE__);
      if (mysqli_num_rows($c_sql) == 0) {
         sql_query('INSERT INTO user_blocks(userid) VALUES('.$row['id'].')');
         header('Location: index.php');
         die();
      }
      $CURBLOCK = mysqli_fetch_assoc($c_sql);
      $CURBLOCK['index_page'] = (int)$CURBLOCK['index_page'];
      $CURBLOCK['global_stdhead'] = (int)$CURBLOCK['global_stdhead'];
      $CURBLOCK['userdetails_page'] = (int)$CURBLOCK['userdetails_page'];
      $mc1->cache_value($blocks_key, $CURBLOCK, 0);
   }
   //== online time pdq, original code by superman
   $userupdate0 = 'onlinetime = onlinetime + 0';
   $new_time = TIME_NOW - $row['last_access_numb'];
   $update_time = 0;
   if ($new_time < 300) {
      $userupdate0 = "onlinetime = onlinetime + ".$new_time;
      $update_time = $new_time;
   }
   $userupdate1 = "last_access_numb = ".TIME_NOW;
   //end online-time
   $update_time = ($row['onlinetime'] + $update_time);
   if (($row['last_access'] != '0') AND (($row['last_access']) < (TIME_NOW - 180))/** 3 mins **/) {
      sql_query("UPDATE users SET last_access=".TIME_NOW.", $userupdate0, $userupdate1 WHERE id=".$row['id']);
      $mc1->begin_transaction('MyUser_'.$row['id']);
      $mc1->update_row(false, array('last_access' => TIME_NOW, 'onlinetime' => $update_time, 'last_access_numb' => TIME_NOW));
      $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
      $mc1->begin_transaction('user'.$row['id']);
      $mc1->update_row(false, array('last_access' => TIME_NOW, 'onlinetime' => $update_time, 'last_access_numb' => TIME_NOW));
      $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
   }
   //==
   if ($row['override_class'] < $row['class']) $row['class'] = $row['override_class']; // Override class and save in GLOBAL array below.
      $GLOBALS["CURUSER"] = $row;
      get_template();
      $mood = create_moods();
   }

  //== 2010 Tbdev Cleanup Manager by ColdFusion
  function autoclean() {
    global $INSTALLER09;
    $now = TIME_NOW;
    $sql = sql_query( "SELECT * FROM cleanup WHERE clean_on = 1 AND clean_time <= {$now} ORDER BY clean_time ASC LIMIT 0,1" );
    $row = mysqli_fetch_assoc( $sql );
    if ( $row['clean_id'] )
		{
			$next_clean = intval( $now + ($row['clean_increment'] ? $row['clean_increment'] : 15*60) );
			sql_query( "UPDATE cleanup SET clean_time = $next_clean WHERE clean_id = {$row['clean_id']}" );
			if ( file_exists( CLEAN_DIR.''.$row['clean_file'] ) )
			{
			require_once( CLEAN_DIR.''.$row['clean_file'] );
         if (function_exists('docleanup')) {    
         register_shutdown_function( 'docleanup', $row );
         }
		  }
	   }
   }

  function get_template(){
	global $CURUSER, $INSTALLER09;
	if(isset($CURUSER)){
		if(file_exists(TEMPLATE_DIR."{$CURUSER['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$CURUSER['stylesheet']}/template.php");
		}else{
			if(isset($INSTALLER09)){
				if(file_exists(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php");
				}else{
					echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
				}
			}else{
				if(file_exists(TEMPLATE_DIR."1/template.php")){
					require_once(TEMPLATE_DIR. "1/template.php");
				}else{
					echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
				}
			}
		}
	}else{
	if(file_exists(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php")){
			require_once(TEMPLATE_DIR."{$INSTALLER09['stylesheet']}/template.php");
		}else{
			echo "Sorry, Templates do not seem to be working properly and missing some code. Please report this to the programmers/owners.";
		}
	}
	if(!function_exists("stdhead")){
		echo "stdhead function missing";
		function stdhead($title="", $message=true){
			return "<html><head><title>$title</title></head><body>";
		}
	}
	if(!function_exists("stdfoot")){
		echo "stdfoot function missing";
		function stdfoot(){
			return "</body></html>";
		}
	}
	if(!function_exists("stdmsg")){
		echo "stdmgs function missing";
		function stdmsg($title, $message){
			return "<b>".$title."</b><br />$message";
		}
	}
	if(!function_exists("StatusBar")){
		echo "StatusBar function missing";
		function StatusBar(){
			global $CURUSER, $lang;
			return "{$lang['gl_msg_welcome']}, {$CURUSER['username']}";
		}
	}
}
//slots - pdq
function make_freeslots($userid, $key) {
   global $mc1, $INSTALLER09;
   if(($slot = $mc1->get_value($key.$userid)) === false) {
       $res_slots = sql_query('SELECT * FROM freeslots WHERE userid = '.$userid) or sqlerr(__file__, __line__);
        $slot = array();
         if (mysqli_num_rows($res_slots)) {
              while ($rowslot = mysqli_fetch_assoc($res_slots))
              $slot[] = $rowslot;
        }
       $mc1->cache_value($key.$userid, $slot, 86400*7);
   }
   return $slot;	
}
//bookmarks - pdq
function make_bookmarks($userid, $key) {
   global $mc1, $INSTALLER09;
   if(($book = $mc1->get_value($key.$userid)) === false) {
       $res_books = sql_query('SELECT * FROM bookmarks WHERE userid = '.$userid) or sqlerr(__file__, __line__);
        $book = array();
         if (mysqli_num_rows($res_books)) {
              while ($rowbook = mysqli_fetch_assoc($res_books))
              $book[] = $rowbook;
        }
       $mc1->cache_value($key.$userid, $book, 86400*7); // 7 days
   }
   return $book;    
}
//genrelist - pdq
function genrelist() {
   global $mc1, $INSTALLER09;
    if (($ret = $mc1->get_value('genrelist')) == false) {
        $ret = array();
        $res = sql_query("SELECT id, image, name FROM categories ORDER BY name");
        while ($row = mysqli_fetch_assoc($res))
        $ret[] = $row;
        $mc1->cache_value('genrelist', $ret, $INSTALLER09['expires']['genrelist']);
    }
    return $ret;  
}

// moods - pdq
function create_moods($force = false) {
   global $mc1, $INSTALLER09;
   $key = 'moods';
   if(($mood = $mc1->get_value($key)) === false || $force) {
    $res_moods = sql_query('SELECT * FROM moods ORDER BY id ASC') or sqlerr(__file__, __line__);
      $mood = array();
      if (mysqli_num_rows($res_moods)) {
         while ($rmood = mysqli_fetch_assoc($res_moods)) {
            $mood['image'][$rmood['id']] = $rmood['image'];
            $mood['name'][$rmood['id']]  = $rmood['name'];
         }
      }
      $mc1->cache_value($key, $mood, 86400*7);
   }
   return $mood;
}

//== delete
function delete_id_keys($keys, $keyname = false) {
   global $mc1;
  
   if (!(is_array($keys) || $keyname)) // if no key given or not an array
       return false;
   else
      foreach ($keys as $id) // proceed
         $mc1->delete_value($keyname.$id);
   return true;
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;
        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

//putyn  08/08/2011
function sqlesc($x) {
    if(is_integer($x))
      return (int)$x;
      
    return sprintf('\'%s\'',((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x) : ((trigger_error("Err", E_USER_ERROR)) ? "" : "")));
}

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $x) : ((trigger_error("", E_USER_ERROR)) ? "" : "")));
}

function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    echo "<h1>Not Found</h1>\n";
    echo "<p>Sorry pal :(</p>\n";
    exit();
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
    set_mycookie( "uid", $id, $expires );
    set_mycookie( "pass", $passhash, $expires );
    set_mycookie( "hashv", hashit($id,$passhash), $expires );
    if ($updatedb)
    sql_query("UPDATE users SET last_login = ".TIME_NOW." WHERE id = $id") or sqlerr(__file__, __line__);
}

function set_mycookie( $name, $value="", $expires_in=0, $sticky=1 )
    {
		global $INSTALLER09;
		
		if ( $sticky == 1 )
    {
      $expires = TIME_NOW + 60*60*24*365;
    }
		else if ( $expires_in )
		{
			$expires = TIME_NOW + ( $expires_in * 86400 );
		}
		else
		{
			$expires = FALSE;
		}
		
		$INSTALLER09['cookie_domain'] = $INSTALLER09['cookie_domain'] == "" ? ""  : $INSTALLER09['cookie_domain'];
      $INSTALLER09['cookie_path']   = $INSTALLER09['cookie_path']   == "" ? "/" : $INSTALLER09['cookie_path'];
      	
		if ( PHP_VERSION < 5.2 )
		{
      if ( $INSTALLER09['cookie_domain'] )
      {
        @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'], $INSTALLER09['cookie_domain'] . '; HttpOnly' );
      }
      else
      {
        @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'] );
      }
    }
    else
    {
      @setcookie( $INSTALLER09['cookie_prefix'].$name, $value, $expires, $INSTALLER09['cookie_path'], $INSTALLER09['cookie_domain'], NULL, TRUE );
    }
			
}

function get_mycookie($name) 
    {
      global $INSTALLER09;
      
    	if ( isset($_COOKIE[$INSTALLER09['cookie_prefix'].$name]) AND !empty($_COOKIE[$INSTALLER09['cookie_prefix'].$name]) )
    	{
    		return urldecode($_COOKIE[$INSTALLER09['cookie_prefix'].$name]);
    	}
    	else
    	{
    		return FALSE;
    	}
}

function logoutcookie() {
    set_mycookie('uid', '-1');
    set_mycookie('pass', '-1');
    set_mycookie('hashv', '-1');
}

function loggedinorreturn() {
    global $CURUSER, $INSTALLER09;
    if (!$CURUSER) {
        header("Location: {$INSTALLER09['baseurl']}/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}

function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function get_row_count($table, $suffix = "")
{
  if ($suffix)
  $suffix = " $suffix";
  ($r = sql_query("SELECT COUNT(*) FROM $table$suffix")) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  ($a = mysqli_fetch_row($r)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  return $a[0];
}


function stderr($heading, $text)
{
    $htmlout = stdhead();
    $htmlout .= stdmsg($heading, $text);
    $htmlout .= stdfoot();
    
    echo $htmlout;
    exit();
}
	
// Basic MySQL error handler
function sqlerr($file = '', $line = '') {
    global $INSTALLER09, $CURUSER;
    
		$the_error    = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
		$the_error_no = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));

    	if ( SQL_DEBUG == 0 )
    	{
			exit();
    	}
     	else if ( $INSTALLER09['sql_error_log'] AND SQL_DEBUG == 1 )
		{
			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n Error Number: " . $the_error_no;
			$_error_string .= "\n Error: " . $the_error;
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n in file ".$file." on line ".$line;
			$_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
			$_error_string .= "\n Username: {$CURUSER['username']}[{$CURUSER['id']}]";
			
			if ( $FH = @fopen( $INSTALLER09['sql_error_log'], 'a' ) )
			{
				@fwrite( $FH, $_error_string );
				@fclose( $FH );
			}
			
			echo "<html><head><title>MySQLI Error</title>
					<style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
		    		   <blockquote><h1>MySQLI Error</h1><b>There appears to be an error with the database.</b><br />
		    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>
				  </body></html>";
		}
		else
		{
    		$the_error = "\nSQL error: ".$the_error."\n";
	    	$the_error .= "SQL error code: ".$the_error_no."\n";
	    	$the_error .= "Date: ".date("l dS \of F Y h:i:s A");
    	
	    	$out = "<html>\n<head>\n<title>MySQLI Error</title>\n
	    		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style>\n</head>\n<body>\n
	    		   <blockquote>\n<h1>MySQLI Error</h1><b>There appears to be an error with the database.</b><br />
	    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>.
	    		   <br /><br /><b>Error Returned</b><br />
	    		   <form name='mysql'><textarea rows=\"15\" cols=\"60\">".htmlentities($the_error, ENT_QUOTES)."</textarea></form><br>We apologise for any inconvenience</blockquote></body></html>";
    		   
    
	       	echo $out;
		}
		
        exit();
}
    
function get_dt_num()
{
  return gmdate("YmdHis");
}

function write_log($text)
{
  $text = sqlesc($text);
  $added = TIME_NOW;
  sql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

function unixstamp_to_human( $unix=0 )
    {
    	$offset = get_time_offset();
    	$tmp    = gmdate( 'j,n,Y,G,i', $unix + $offset );
    	
    	list( $day, $month, $year, $hour, $min ) = explode( ',', $tmp );
  
    	return array( 'day'    => $day,
                    'month'  => $month,
                    'year'   => $year,
                    'hour'   => $hour,
                    'minute' => $min );
    }
    
function get_time_offset() {
    
    	global $CURUSER, $INSTALLER09;
    	$r = 0;
    	
    	$r = ( ($CURUSER['time_offset'] != "") ? $CURUSER['time_offset'] : $INSTALLER09['time_offset'] ) * 3600;
			
      if ( $INSTALLER09['time_adjust'] )
      {
        $r += ($INSTALLER09['time_adjust'] * 60);
      }
      
      if ( $CURUSER['dst_in_use'] )
      {
        $r += 3600;
      }
        
        return $r;
}
    
function get_date($date, $method, $norelative=0, $full_relative=0)
    {
        global $INSTALLER09;
        
        static $offset_set = 0;
        static $today_time = 0;
        static $yesterday_time = 0;

        $time_options = array( 
        'JOINED' => $INSTALLER09['time_joined'],
        'SHORT'  => $INSTALLER09['time_short'],
				'LONG'   => $INSTALLER09['time_long'],
				'TINY'   => $INSTALLER09['time_tiny'] ? $INSTALLER09['time_tiny'] : 'j M Y - G:i',
				'DATE'   => $INSTALLER09['time_date'] ? $INSTALLER09['time_date'] : 'j M Y'
				);
        
        if ( ! $date )
        {
            return '--';
        }
        
        if ( empty($method) )
        {
        	$method = 'LONG';
        }
        
        if ($offset_set == 0)
        {
        	$GLOBALS['offset'] = get_time_offset();
			
          if ( $INSTALLER09['time_use_relative'] )
          {
            $today_time     = gmdate('d,m,Y', ( TIME_NOW + $GLOBALS['offset']) );
            $yesterday_time = gmdate('d,m,Y', ( (TIME_NOW - 86400) + $GLOBALS['offset']) );
          }	
        
          $offset_set = 1;
        }
        
        if ( $INSTALLER09['time_use_relative'] == 3 )
        {
        	$full_relative = 1;
        }
        
        if ( $full_relative and ( $norelative != 1 ) )
        {
          $diff = TIME_NOW - $date;
          
          if ( $diff < 3600 )
          {
            if ( $diff < 120 )
            {
              return '< 1 minute ago';
            }
            else
            {
              return sprintf( '%s minutes ago', intval($diff / 60) );
            }
          }
          else if ( $diff < 7200 )
          {
            return '< 1 hour ago';
          }
          else if ( $diff < 86400 )
          {
            return sprintf( '%s hours ago', intval($diff / 3600) );
          }
          else if ( $diff < 172800 )
          {
            return '< 1 day ago';
          }
          else if ( $diff < 604800 )
          {
            return sprintf( '%s days ago', intval($diff / 86400) );
          }
          else if ( $diff < 1209600 )
          {
            return '< 1 week ago';
          }
          else if ( $diff < 3024000 )
          {
            return sprintf( '%s weeks ago', intval($diff / 604900) );
          }
          else
          {
            return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
          }
        }
        else if ( $INSTALLER09['time_use_relative'] and ( $norelative != 1 ) )
        {
          $this_time = gmdate('d,m,Y', ($date + $GLOBALS['offset']) );
          
          if ( $INSTALLER09['time_use_relative'] == 2 )
          {
            $diff = TIME_NOW - $date;
          
            if ( $diff < 3600 )
            {
              if ( $diff < 120 )
              {
                return '< 1 minute ago';
              }
              else
              {
                return sprintf( '%s minutes ago', intval($diff / 60) );
              }
            }
          }
          
            if ( $this_time == $today_time )
            {
              return str_replace( '{--}', 'Today', gmdate($INSTALLER09['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else if  ( $this_time == $yesterday_time )
            {
              return str_replace( '{--}', 'Yesterday', gmdate($INSTALLER09['time_use_relative_format'], ($date + $GLOBALS['offset']) ) );
            }
            else
            {
              return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
            }
        }
        else
        {
          return gmdate($time_options[$method], ($date + $GLOBALS['offset']) );
        }
}

function ratingpic($num) {
    global $INSTALLER09;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"{$INSTALLER09['pic_base_url']}ratings/{$r}.gif\" border=\"0\" alt=\"Rating: $num / 5\" title=\"Rating: $num / 5\" />";
}

function hash_pad($hash) {
    return str_pad($hash, 20);
}

//== cutname = Laffin
function CutName ($txt, $len=45){
return (strlen($txt)>$len ? substr($txt,0,$len-1) .'...':$txt);
}

    function load_language($file='') {
    global $INSTALLER09;
    if( !isset($GLOBALS['CURUSER']) OR empty($GLOBALS['CURUSER']['language']) )
    {
    if( !file_exists(LANG_DIR."lang_{$file}.php") )
    {
    stderr('SYSTEM ERROR', 'Can\'t find language files');
    }   
    require_once(LANG_DIR."lang_{$file}.php");
    return $lang;
    }
    if( !file_exists(LANG_DIR."lang_{$file}.php") )
    {
    stderr('SYSTEM ERROR', 'Can\'t find language files');
    }
    else
    {
    require_once LANG_DIR."lang_{$file}.php"; 
    }   
    return $lang;
}


function flood_limit($table) {
global $CURUSER,$INSTALLER09,$lang;
	if(!file_exists($INSTALLER09['flood_file']) || !is_array($max = unserialize(file_get_contents($INSTALLER09['flood_file']))))
		return;
	if(!isset($max[$CURUSER['class']]))
	return;
	$tb = array('posts'=>'posts.userid','comments'=>'comments.user','messages'=>'messages.sender');
	$q = sql_query('SELECT min('.$table.'.added) as first_post, count('.$table.'.id) as how_many FROM '.$table.' WHERE '.$tb[$table].' = '.$CURUSER['id'].' AND '.TIME_NOW.' - '.$table.'.added < '.$INSTALLER09['flood_time']);
	$a = mysqli_fetch_assoc($q);
	if($a['how_many'] > $max[$CURUSER['class']])
  stderr($lang['gl_sorry'] ,$lang['gl_flood_msg'].''.mkprettytime($INSTALLER09['flood_time'] - (TIME_NOW - $a['first_post'])));
}

//== Sql query count by pdq
function sql_query($query) {
    global $query_stat;
    $query_start_time = microtime(true); // Start time
    $result            = mysqli_query($GLOBALS["___mysqli_ston"], $query);
    $query_end_time = microtime(true); // End time
    $query_stat[] = array('seconds' => number_format($query_end_time-$query_start_time, 6), 'query' => $query);
    return $result;
}
   
 //=== progress bar
function get_percent_completed_image($p) {
$img = 'progress-';

switch (true) {
case ($p >= 100):
 $img .= 5;
     break;
case (($p >= 0) && ($p <= 10)):
 $img .= 0;
     break;
case (($p >= 11) && ($p <= 40)):
 $img .= 1;
     break;
case (($p >= 41) && ($p <= 60)):
 $img .= 2;
     break;
case (($p >= 61) && ($p <= 80)):
 $img .= 3;
     break;
 case (($p >= 81) && ($p <= 99)):
 $img .= 4;
     break;
  }
return '<img src="/pic/'.$img.'.gif" alt="percent" />';
} 
 
function strip_tags_array($ar) {
	if(is_array($ar)) {
		foreach($ar as $k=>$v)
			$ar[strip_tags($k)] = strip_tags($v);
	} 
	else 
		$ar = strip_tags($ar);
	return $ar;
}

    if (file_exists("install/index.php")){
    $HTMLOUT='';
    $HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
    <title>Warning</title>
    </head>
    <body><div style='font-size:33px;color:white;background-color:red;text-align:center;'>Delete the install directory</div></body></html>";
    print $HTMLOUT;
    exit();
    }
?>
