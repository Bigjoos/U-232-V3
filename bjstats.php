<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
$_NO_COMPRESS = true;
ob_start("ob_gzhandler");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'html_functions.php');
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global') );

if ($CURUSER['class'] < UC_POWER_USER)
{
	stderr("Sorry...", "You must be a Power User or above to play Blackjack.");
	exit;
}

     $HTMLOUT='';
     $mingames = 1;
     $cachefile = "./cache/bjstats.txt";
     $cachetime = 60 * 30; // 30 minutes
     if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)))
     {
     require_once($cachefile);
     $HTMLOUT .="<p align='center'><font class='small'>This page last updated ".date('Y-m-d H:i:s', filemtime($cachefile)).". </font></p>";
     echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
     exit;
     }
     $HTMLOUT .= ob_start();
     


function bjtable($res, $frame_caption)
{
	$htmlout='';
	$htmlout .= begin_frame($frame_caption, true);
	$htmlout .= begin_table();
	$htmlout .="<tr>
	<td class='colhead'>Rank</td>
	<td class='colhead' align='left'>User</td>
	<td class='colhead' align='right'>Wins</td>
	<td class='colhead' align='right'>Losses</td>
	<td class='colhead' align='right'>Games</td>
	<td class='colhead' align='right'>Percentage</td>
	<td class='colhead' align='right'>Win/Loss</td>
	</tr>";

	$num = 0;
	while ($a = mysqli_fetch_assoc($res))
	{
		++$num;
		//==Calculate Win %
		$win_perc = number_format(($a['wins'] / $a['games']) * 100, 1);
		//==Add a user's +/- statistic
		$plus_minus = $a['wins'] - $a['losses'];
		if ($plus_minus >= 0)
		{
		$plus_minus = mksize(($a['wins'] - $a['losses']) * 100*1024*1024);
		}
		else
		{
			$plus_minus = "-";
			$plus_minus .= mksize(($a['losses'] - $a['wins']) * 100*1024*1024);
		}
		
		$htmlout .="<tr><td>$num</td><td align='left'>".
		"<b><a href='userdetails.php?id=".$a['id']."'>".$a['username']."</a></b></td>".
		"<td align='right'>".number_format($a['wins'], 0)."</td>".
		"<td align='right'>".number_format($a['losses'], 0)."</td>".
		"<td align='right'>".number_format($a['games'], 0)."</td>".
		"<td align='right'>$win_perc</td>".
		"<td align='right'>$plus_minus</td>".
		"</tr>\n";
	}
	$htmlout .= end_table();
	$htmlout .= end_frame();
	return $htmlout;
}


   

$HTMLOUT .="<h1>Blackjack Stats</h1>";
//$HTMLOUT .="<p>Stats are cached and updated every 30 minutes. You need to play at least $mingames games to be included.</p>";
$HTMLOUT .="<br />";
//==Most Games Played
$res = sql_query("SELECT id, username, bjwins AS wins, bjlosses AS losses, bjwins + bjlosses AS games FROM users WHERE bjwins + bjlosses > $mingames ORDER BY games DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Games Played","Users");
$HTMLOUT .="<br /><br />";
//==Most Games Played
//==Highest Win %
$res = sql_query("SELECT id, username, bjwins AS wins, bjlosses AS losses, bjwins + bjlosses AS games, bjwins / (bjwins + bjlosses) AS winperc FROM users WHERE bjwins + bjlosses > $mingames ORDER BY winperc DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Highest Win Percentage","Users");
$HTMLOUT .="<br /><br />";
//==Highest Win %
//==Most Credit Won
$res = sql_query("SELECT id, username, bjwins AS wins, bjlosses AS losses, bjwins + bjlosses AS games, bjwins - bjlosses AS winnings FROM users WHERE bjwins + bjlosses > $mingames ORDER BY winnings DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Credit Won","Users");
$HTMLOUT .="<br /><br />";
//==Most Credit Won
//==Most Credit Lost
$res = sql_query("SELECT id, username, bjwins AS wins, bjlosses AS losses, bjwins + bjlosses AS games, bjlosses - bjwins AS losings FROM users WHERE bjwins + bjlosses > $mingames ORDER BY losings DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= bjtable($res, "Most Credit Lost","Users");
//==Most Credit Lost
$HTMLOUT .="<br /><br />";
// open the cache file for writing      
$fp = fopen($cachefile, 'w');
// save the contents of output buffer to the file    
fwrite($fp, ob_get_contents());
// close the file
fclose($fp);
// Send the output to the browser
$HTMLOUT .= ob_end_flush();
echo stdhead('Blackjack Stats') . $HTMLOUT . stdfoot();
?>
