<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/*******************************
totally automatic hit and run script for TBDev 2011(ish)
~ snuggs
*********************************/

if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
header('HTTP/1.0 404 Not Found');
    
echo '
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL '.htmlspecialchars($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1).' was not found on this server.</p>
<hr>
<address>'.$_SERVER['SERVER_SOFTWARE'].' Server at '.$INSTALLER09['baseurl'].' Port 80</address>
</body></html>';
exit();
}

require_once INCL_DIR.'user_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once INCL_DIR.'pager_new.php';
require_once INCL_DIR.'html_functions.php';

$query = (isset($_GET['really_bad']) ? 'SELECT COUNT(*) FROM snatched LEFT JOIN users ON users.id = snatched.userid WHERE snatched.finished = \'yes\' AND snatched.hit_and_run > 0 AND users.hit_and_run_total > 2' : 'SELECT COUNT(*) FROM `snatched` WHERE `finished` = \'yes\' AND `hit_and_run` > 0');

$HTMLOUT = '';

//$HTMLOUT .= begin_main_frame();	

//=== get stuff for the pager
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
	$res_count = @sql_query($query) or sqlerr(__FILE__, __LINE__);
	$arr_count = mysqli_fetch_row($res_count);
	$count = ($arr_count[0] > 0 ? $arr_count[0] : 0);
    
	list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'staffpanel.php?tool=hit_and_run&amp;action=hit_and_run'); 
	
	$query_2 = (isset($_GET['really_bad']) ? 'SELECT s.torrentid, s.userid, s.hit_and_run  FROM snatched AS s LEFT JOIN users AS u ON u.id = s.userid WHERE finished = \'yes\' AND hit_and_run > 0 AND u.hit_and_run_total > 2 ORDER BY userid '.$LIMIT : 'SELECT torrentid, userid, hit_and_run  FROM `snatched` WHERE `finished` = \'yes\' AND `hit_and_run` > 0 ORDER BY `userid` '.$LIMIT);
	
	$hit_and_run_rez = @sql_query($query_2) or sqlerr(__FILE__,__LINE__);

$HTMLOUT .= '<h2>'.(!isset($_GET['really_bad']) ? 'Current Hit and Runs who still have a chance' : 
		'Hit and Runs with no chance' ).'</h2><br /> 
		<a class="altlink" href="staffpanel.php?tool=hit_and_run&amp;action=hit_and_run">show all current hit and runs</a> || <a class="altlink" href="staffpanel.php?tool=hit_and_run&amp;action=hit_and_run&amp;really_bad=show_them">show disabled hit and runs</a><br /><br />
		'.($arr_count[0] > $perpage ? '<p>'.$menu.'</p>' : '').'
		<table>'.(mysqli_num_rows($hit_and_run_rez) > 0 ? '<tr><td  class="colhead"></td>
		<td  class="colhead"><b>Member</b></td>
		<td class="colhead"><b>Torrent</b></td>
		<td class="colhead"><b>Times</b></td>
		<td class="colhead"><b> Stats</b></td>
		<td class="colhead">Actions</td>' : 
		'<tr><td> no hit and runners at the moment...</td>').'</tr>';

while ($hit_and_run_arr = mysqli_fetch_assoc($hit_and_run_rez)) 
{

//=== peers
$peer_rez = sql_query('SELECT seeder FROM peers WHERE userid='.$hit_and_run_arr['userid'].' AND torrent='.$hit_and_run_arr['torrentid']) or sqlerr(__FILE__, __LINE__);
$peer_arr = mysqli_fetch_assoc($peer_rez);

//=== if really seeding list them
if ($peer_arr['seeder'] !== 'yes')
{

 //=== make sure they are NOT the torrent owner
$res_check_owner = sql_query('SELECT owner, name, added AS torrent_added FROM torrents WHERE id = '.$hit_and_run_arr['torrentid']) or sqlerr(__FILE__, __LINE__);
$arr_check_owner  = mysqli_fetch_assoc($res_check_owner);
 if ($hit_and_run_arr['userid'] !== $arr_check_owner['owner']){

//=== then check to see if there are still seeders / leechers on that torrent
           $res_leechers = sql_query("SELECT COUNT(*)  FROM `peers` WHERE `torrent` = {$hit_and_run_arr['torrentid']} AND seeder = 'no' AND to_go > '0' AND userid <> {$hit_and_run_arr['userid']}") or sqlerr(__FILE__, __LINE__);
 	   $arr_leechers = mysqli_fetch_row($res_leechers);
           $res_seeders = sql_query("SELECT COUNT(*)  FROM `peers` WHERE `torrent` = {$hit_and_run_arr['torrentid']} AND seeder = 'yes' AND userid <> {$hit_and_run_arr['userid']}") or sqlerr(__FILE__, __LINE__);
 	   $arr_seeders = mysqli_fetch_row($res_seeders);
          
//=== get snatched info
		$snatched_rez = sql_query('SELECT * FROM snatched WHERE torrentid='.$hit_and_run_arr['torrentid'].' AND userid='.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);
		$snatched_arr = mysqli_fetch_assoc($snatched_rez);
		
//=== get user info
    $user_rez = sql_query("SELECT id, avatar, username, uploaded, downloaded, class, hit_and_run_total, donor, warned, enabled, leechwarn, chatpost, pirate, king, suspended FROM users  WHERE id=".$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);    
    $user_arr = mysqli_fetch_assoc($user_rez);
    
//=== get count of hit and runs by member
$num_hit_and_runs = sql_query('SELECT COUNT(*) FROM snatched WHERE mark_of_cain = \'yes\' AND userid ='.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);  
$arr_hit_and_runs = mysqli_fetch_row($num_hit_and_runs);

$ratio_site = member_ratio($user_arr['uploaded'], $user_arr['downloaded']);
$ratio_torrent = member_ratio($snatched_arr['uploaded'], $snatched_arr['downloaded']);

$avatar = ($CURUSER['avatars'] == 'yes' ? ($user_arr['avatar'] == '' ? '<img src="pic/default_avatar.gif"  width="40" alt="default avatar" />' : '<img src="'.htmlspecialchars($user_arr['avatar']).'" alt="avatar"  width="40" />') : '');

$torrent_needed_seed_time = $snatched_arr['seedtime'];

//=== get times per class
		switch (true)
			{ 
			case ($user_arr['class'] < UC_POWER_USER):
				$days_3 = 3*86400; //== 3 days
				$days_14 = 2*86400; //== 2 days
				$days_over_14 = 86400; //== 1 day
				break;
			case ($user_arr['class'] < UC_MODERATOR):
				$days_3 = 2*86400; //== 2 days
				$days_14 = 129600; //== 36 hours
				$days_over_14 = 64800; //== 18 hours
				break;
			case ($user_arr['class'] >= UC_MODERATOR):
				$days_3 = 86400; //== 24 hours
				$days_14 = 43200; //== 12 hours
				$days_over_14 = 21600; //== 6 hours
				break;
			}

switch(true) 
{
case (($snatched_arr['start_date'] - $arr_check_owner['torrent_added']) < 7*86400):
$minus_ratio = ($days_3 - $torrent_needed_seed_time);
// or using ratio in the equasion
//$minus_ratio = ($days_3 - $torrent_needed_seed_time) - ($arr_snatch['uploaded'] / $arr_snatch['downloaded'] * 3 * 86400);
break;
case (($snatched_arr['start_date'] - $arr_check_owner['torrent_added']) < 21*86400):
$minus_ratio = ($days_14 - $torrent_needed_seed_time);
// or using ratio in the equasion
//$minus_ratio = ($days_14 - $torrent_needed_seed_time) - ($arr_snatch['uploaded'] / $arr_snatch['downloaded'] * 2 * 86400);
break;
case (($snatched_arr['start_date'] - $arr_check_owner['torrent_added']) >= 21*86400):
$minus_ratio = ($days_over_14 - $torrent_needed_seed_time);
// or using ratio in the equasion
//$minus_ratio = ($days_over_14 - $torrent_needed_seed_time) - ($arr_snatch['uploaded'] / $arr_snatch['downloaded'] * 86400);
break;
}

$minus_ratio = (preg_match("/-/i",$minus_ratio) ? 0 : $minus_ratio); 
$color = ($minus_ratio > 0 ? get_ratio_color($minus_ratio) : 'limegreen');

$HTMLOUT .= '<tr><td align="left">'.$avatar.'</td>
			<td align="left"><a class="altlink" href="userdetails.php?id='.$hit_and_run_arr['userid'].'#finished">' . format_username($user_arr) . '</a>  [ '.get_user_class_name($user_arr['class']).' ] <br />total Hit & Runs: <b>'.$arr_hit_and_runs[0].' </b></td>
			<td align="left"><a class="altlink" href="details.php?id='.$hit_and_run_arr['torrentid'].'">'.$arr_check_owner['name'].'</a><br />
			Leechers: '.$arr_leechers[0].'<br />
			Seeders: '.$arr_seeders[0].'</td>
			<td align="left">Finished DL at: '.get_date($snatched_arr['complete_date'],'').'<br />
			Stoped seeding at: '.get_date($hit_and_run_arr['hit_and_run'],'').'<br />
			Seeded for: '.mkprettytime($snatched_arr['seedtime']).'<br />
			**should still seed for: '.mkprettytime($minus_ratio).'</td>
			<td align="left">uploaded: '.mksize($snatched_arr['uploaded']).'<br />
			downloaded  '.mksize($snatched_arr['downloaded']).'<br />
			torrent ratio:  <font color="'.get_ratio_color($ratio_torrent).'">'.$ratio_torrent.'</font><br />
			site ratio:  <font color="'.get_ratio_color($ratio_site).'" title="includes all bonus and karma stuff">'.$ratio_site.'</font></td>
			<td align="center"><a href="pm_system.php?action=send_message&amp;receiver='.$hit_and_run_arr['userid'].'"><img src="pic/buttons/button_pm.gif" border="0" alt="PM" title="send this mofo a PM and give them a piece of your mind..." /></a><br />
			<a class="altlink" href="staffpanel.php?tool=shit_list&amp;action2=new&amp;shit_list_id='.$hit_and_run_arr['userid'].'&amp;return_to=staffpanel.php?tool=hit_and_run&amp;action=hit_and_run" >Add to shit list</a></td></tr>'; 			       

}//=== end if not owner
}//=== if not seeding list them
}//=== end of while loop

$HTMLOUT .= '</table>'.($arr_count[0] > $perpage ? '<p>'.$menu.'</p>' : '');




//$HTMLOUT .= end_main_frame();
    
    
    echo stdhead('Hit and Runs') . $HTMLOUT . stdfoot();
?>
