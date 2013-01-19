<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once (INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'));
$HTMLOUT = '';
if ($CURUSER["game_access"] == 0 || $CURUSER["game_access"] > 1 || $CURUSER['suspended'] == 'yes') {
    stderr("Error", "Your gaming rights have been disabled.");
    exit();
}
if ($CURUSER['class'] < UC_POWER_USER) stderr("Sorry", "You must be a Power User+ or above to play Blackjack.");
$mb = 1024 * 1024 * 1024;
$now = TIME_NOW;
$game = isset($_POST["game"]) ? htmlsafechars($_POST["game"]) : '';
$start_ = isset($_POST["start_"]) ? htmlsafechars($_POST["start_"]) : '';
if ($game) {
    function cheater_check($arg)
    {
        if ($arg) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
    $cardcount = 52;
    $points = $showcards = $aces = '';
    $sql = sql_query('SELECT uploaded, downloaded, bjwins, bjlosses '.'FROM users '.'WHERE id = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $User = mysqli_fetch_assoc($sql);
    $User['uploaded'] = $User['uploaded'];
    $User['downloaded'] = $User['downloaded'];
    $User['bjwins'] = (int)$User['bjwins'];
    $User['bjlosses'] = (int)$User['bjlosses'];
    if ($start_ != 'yes') {
        $playeres = sql_query("SELECT * FROM blackjack WHERE userid = " . sqlesc($CURUSER['id']));
        $playerarr = mysqli_fetch_assoc($playeres);
        if ($game == 'hit') $points = $aces = 0;
        $gameover = ($playerarr['gameover'] == 'yes' ? true : false);
        cheater_check($gameover && ($game == 'hit' ^ $game == 'stop'));
        $cards = $playerarr["cards"];
        $usedcards = explode(" ", $cards);
        $arr = array();
        foreach ($usedcards as $array_list) $arr[] = $array_list;
        foreach ($arr as $card_id) {
            $used_card = sql_query("SELECT * FROM cards WHERE id=" . sqlesc($card_id));
            $used_cards = mysqli_fetch_assoc($used_card);
            $showcards.= "<img src='{$INSTALLER09['pic_base_url']}cards/" . htmlsafechars($used_cards["pic"]) . "' width='71' height='96' border='0' alt='Cards' title='Cards' />";
            if ($used_cards["points"] > 1) $points+= $used_cards['points'];
            else $aces++;
        }
    }
    if ($_POST["game"] == 'hit') {
        if ($start_ == 'yes') {
            if ($CURUSER["uploaded"] < $mb) stderr("Sorry " . $CURUSER["username"], "You haven't uploaded " . mksize($mb) . " yet.");
            $required_ratio = 0.3;
            if ($CURUSER["downloaded"] > 0) $ratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 3);
            elseif ($CURUSER["uploaded"] > 0) $ratio = 999;
            else $ratio = 0;
            if ($ratio < $required_ratio) stderr("Sorry " . $CURUSER["username"], "Your ratio is lower than the requirement of " . $required_ratio . "%.");
            $res = sql_query("SELECT status, gameover FROM blackjack WHERE userid = " . sqlesc($CURUSER['id']));
            $arr = mysqli_fetch_assoc($res);
            if ($arr['status'] == 'waiting') stderr("Sorry", "You'll have to wait until your last game completes before you play a new one.");
            elseif ($arr['status'] == 'playing') stderr("Sorry", "You must finish your old game first.<form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='hidden' name='continue' value='yes' readonly='readonly' /><input type='submit' value='Continue old game' /></form>");
            cheater_check($arr['gameover'] == 'yes');
            $cardids = array();
            for ($i = 0; $i <= 1; $i++) $cardids[] = rand(1, $cardcount);
            foreach ($cardids as $cardid) {
                while (in_array($cardid, $cardids)) $cardid = rand(1, $cardcount);
                $cardres = sql_query("SELECT points, pic FROM cards WHERE id='$cardid'");
                $cardarr = mysqli_fetch_assoc($cardres);
                if ($cardarr["points"] > 1) $points+= $cardarr["points"];
                else $aces++;
                $showcards.= "<img src='{$INSTALLER09['pic_base_url']}cards/" . $cardarr['pic'] . "' width='71' height='96' border='0' alt='Cards' title='Cards' />";
                $cardids2[] = $cardid;
            }
            for ($i = 0; $i < $aces; $i++) $points+= ($points < 11 && $aces - $i == 1 ? 11 : 1);
            sql_query("INSERT INTO blackjack (userid, points, cards, date) VALUES(" . sqlesc($CURUSER['id']) . ", '$points', '" . join(" ", $cardids2) . "', $now)");
            if ($points < 21) {
                $HTMLOUT.= "<h1>Welcome, {$CURUSER['username']}!</h1>
				<table cellspacing='0' cellpadding='3' width='600'>
				<tr><td colspan='2'>
				<table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
				<tr><td align='center'>" . trim($showcards) . "</td></tr>
				<tr><td align='center'><b>Points = {$points}</b></td></tr>
				<tr><td align='center'>
				<form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='submit' value='Hitme' /></form>
				</td></tr>";
                if ($points >= 10) {
                    $HTMLOUT.= "<tr><td align='center'>
				<form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='stop' readonly='readonly' /><input type='submit' value='Stay' /></form>
				</td></tr>";
                }
                $HTMLOUT.= "</table></td></tr></table>";
                echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
                die();
            }
        } elseif (($start_ != 'yes' && isset($_POST['continue']) != 'yes') && !$gameover) {
            cheater_check(empty($playerarr));
            $cardid = rand(1, $cardcount);
            while (in_array($cardid, $arr)) $cardid = rand(1, $cardcount);
            $cardres = sql_query("SELECT points, pic FROM cards WHERE id='$cardid'");
            $cardarr = mysqli_fetch_assoc($cardres);
            $showcards.= "<img src='{$INSTALLER09['pic_base_url']}cards/" . $cardarr['pic'] . "' width='71' height='96' border='0' alt='Cards' title='Cards' />";
            if ($cardarr["points"] > 1) $points+= $cardarr["points"];
            else $aces++;
            for ($i = 0; $i < $aces; $i++) $points+= ($points < 11 && $aces - $i == 1 ? 11 : 1);
            sql_query("UPDATE blackjack SET points='$points', cards='" . $cards . " " . $cardid . "' WHERE userid=" . sqlesc($CURUSER['id']));
        }
        if ($points == 21 || $points > 21) {
            $waitres = sql_query("SELECT COUNT(userid) AS c FROM blackjack WHERE status = 'waiting' AND userid != " . sqlesc($CURUSER['id']));
            $waitarr = mysqli_fetch_assoc($waitres);
            $HTMLOUT.= "<h1>Game over</h1>
			<table cellspacing='0' cellpadding='3' width='600'>
			<tr><td colspan='2'>
			<table width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
			<tr><td align='center'>" . trim($showcards) . "</td></tr>
			<tr><td align='center'><b>Points = {$points}</b></td></tr>";
        }
        if ($points == 21) {
            if ($waitarr['c'] > 0) {
                $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != " . sqlesc($CURUSER['id']) . " ORDER BY bj.date ASC LIMIT 1");
                $a = mysqli_fetch_assoc($r);
                if ($a["points"] != 21) {
                    $winorlose = "you won " . mksize($mb);
                    sql_query("UPDATE users SET uploaded = uploaded + $mb, bjwins = bjwins + 1 WHERE id=" . sqlesc($CURUSER['id']));
                    sql_query("UPDATE users SET uploaded = uploaded - $mb, bjlosses = bjlosses + 1 WHERE id=" . sqlesc($a['userid']));
                    $update['uploaded'] = ($User['uploaded'] + $mb);
                    $update['uploaded_loser'] = ($a['uploaded'] - $mb);
                    $update['bjwins'] = ($User['bjwins'] + 1);
                    $update['bjlosses'] = ($a['bjlosses'] + 1);
                    //==stats
                    $mc1->begin_transaction('userstats_'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                    $mc1->begin_transaction('user_stats_'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                    $mc1->begin_transaction('userstats_'.$a['userid']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded_loser']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                    $mc1->begin_transaction('user_stats_'.$a['userid']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded_loser']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                    //== curuser values
                    $mc1->begin_transaction('MyUser'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'bjwins' => $update['bjwins']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                    $mc1->begin_transaction('user'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'bjwins' => $update['bjwins']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                    $mc1->begin_transaction('MyUser'.$a['userid']);
                    $mc1->update_row(false, array(
                        'bjlosses' => $update['bjlosses']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                    $mc1->begin_transaction('user'.$a['userid']);
                    $mc1->update_row(false, array(
                        'bjlosses' => $update['bjlosses']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                    $msg = sqlesc("You lost to " . $CURUSER['username'] . " (You had " . $a['points'] . " points, " . $CURUSER['username'] . " had 21 points).\n\n");
                } else {
                    $subject = sqlesc("Blackjack Results");
                    $winorlose = "nobody won";
                    $msg = sqlesc("You tied with " . $CURUSER['username'] . " (You both had " . $a['points'] . " points).\n\n");
                }
                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, " .sqlesc($a['userid']) . ", $now, $msg, $subject)");
                $mc1->delete_value('inbox_new_'.$a['userid']);
                $mc1->delete_value('inbox_new_sb_'.$a['userid']);
                sql_query("DELETE FROM blackjack WHERE userid IN (" . sqlesc($CURUSER['id']) . ", " . sqlesc($a['userid']) . ")");
                $HTMLOUT.= "<tr><td align='center'>Your opponent was " . $a["username"] . ", he/she had " . $a['points'] . " points, $winorlose.<br /><br /><b><a href='/blackjack.php'>Play again</a></b></td></tr>";
            } else {
                sql_query("UPDATE blackjack SET status = 'waiting', date=" . $now . ", gameover = 'yes' WHERE userid = " . sqlesc($CURUSER['id']));
                $HTMLOUT.= "<tr><td align='center'>There are no other players, so you'll have to wait until someone plays against you.<br />You will receive a PM with the game results.<br /><br /><b><a href='/blackjack.php'>Back</a></b><br /></td></tr>";
            }
            $HTMLOUT.= "</table></td></tr></table><br />";
            echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
        } elseif ($points > 21) {
            if ($waitarr['c'] > 0) {
                $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != " . sqlesc($CURUSER['id']) . " ORDER BY bj.date ASC LIMIT 1");
                $a = mysqli_fetch_assoc($r);
                if ($a["points"] > 21) {
                    $subject = sqlesc("Blackjack Results");
                    $winorlose = "nobody won";
                    $msg = sqlesc("Your opponent was " . $CURUSER['username'] . ", nobody won.\n\n");
                } else {
                    $subject = sqlesc("Blackjack Results");
                    $winorlose = "you lost " . mksize($mb);
                    sql_query("UPDATE users SET uploaded = uploaded + $mb, bjwins = bjwins + 1 WHERE id=" . sqlesc($a['userid']));
                    sql_query("UPDATE users SET uploaded = uploaded - $mb, bjlosses = bjlosses + 1 WHERE id=" . sqlesc($CURUSER['id']));
                    $update['uploaded'] = ($a['uploaded'] + $mb);
                    $update['uploaded_loser'] = ($User['uploaded'] - $mb);
                    $update['bjwins'] = ($a['bjwins'] + 1);
                    $update['bjlosses'] = ($User['bjlosses'] + 1);
                    //==stats
                    $mc1->begin_transaction('userstats_'.$a['userid']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                    $mc1->begin_transaction('user_stats_'.$a['userid']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                    $mc1->begin_transaction('userstats_'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded_loser']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                    $mc1->begin_transaction('user_stats_'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'uploaded' => $update['uploaded_loser']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                    //== curuser values
                    $mc1->begin_transaction('MyUser'.$a['userid']);
                    $mc1->update_row(false, array(
                        'bjwins' => $update['bjwins']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                    $mc1->begin_transaction('user'.$a['userid']);
                    $mc1->update_row(false, array(
                        'bjwins' => $update['bjwins']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                    $mc1->begin_transaction('MyUser'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'bjlosses' => $update['bjlosses']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                    $mc1->begin_transaction('user'.$CURUSER['id']);
                    $mc1->update_row(false, array(
                        'bjlosses' => $update['bjlosses']
                    ));
                    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                    $msg = sqlesc("You beat " . $CURUSER['username'] . " (You had " . $a['points'] . " points, " . $CURUSER['username'] . " had $points points).\n\n");
                }
                sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, " . $a['userid'] . ", $now, $msg, $subject)");
                $mc1->delete_value('inbox_new_'.$a['userid']);
                $mc1->delete_value('inbox_new_sb_'.$a['userid']);
                sql_query("DELETE FROM blackjack WHERE userid IN (" . sqlesc($CURUSER['id']) . ", " . sqlesc($a['userid']) . ")");
                $HTMLOUT.= "<tr><td align='center'>Your opponent was " . $a["username"] . ", he/she had " . $a['points'] . " points, $winorlose.<br /><br /><b><a href='blackjack.php'>Play again</a></b></td></tr>";
            } else {
                sql_query("UPDATE blackjack SET status = 'waiting', date=" . $now . ", gameover='yes' WHERE userid = " . sqlesc($CURUSER['id']));
                $HTMLOUT.= "<tr><td align='center'>There are no other players, so you'll have to wait until someone plays against you.<br />You will receive a PM with the game results.<br /><br /><b><a href='/blackjack.php'>Back</a></b><br /></td></tr>";
            }
            $HTMLOUT.= "</table></td></tr></table><br />";
            echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
        } else {
            cheater_check(empty($playerarr));
            $HTMLOUT.= "<h1>Welcome, {$CURUSER['username']}!</h1>
			<table cellspacing='0' cellpadding='3' width='600'>
			<tr><td colspan='2'>
			<table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
			<tr><td align='center'>{$showcards}</td></tr>
			<tr><td align='center'><b>Points = {$points}</b></td></tr>";
            $HTMLOUT.= "<tr>
      <td align='center'><form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='submit' value='HitMe' /></form></td>
      </tr>";
            $HTMLOUT.= "<tr>
      <td align='center'><form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='stop' readonly='readonly' /><input type='submit' value='Stay' /></form></td>
      </tr>";
            $HTMLOUT.= "</table></td></tr></table><br />";
            echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
        }
    } elseif ($_POST["game"] == 'stop') {
        cheater_check(empty($playerarr));
        $waitres = sql_query("SELECT COUNT(userid) AS c FROM blackjack WHERE status='waiting' AND userid != " . sqlesc($CURUSER['id']));
        $waitarr = mysqli_fetch_assoc($waitres);
        $HTMLOUT.= "<h1>Game over</h1>
		<table cellspacing='0' cellpadding='3' width='600'>
		<tr><td colspan='2'>
		<table class='message' width='100%' cellspacing='0' cellpadding='5' bgcolor='white'>
		<tr><td align='center'>{$showcards}</td></tr>
		<tr><td align='center'><b>Points = {$playerarr['points']}</b></td></tr>";
        if ($waitarr['c'] > 0) {
            $r = sql_query("SELECT bj.*, u.username FROM blackjack AS bj LEFT JOIN users AS u ON u.id=bj.userid WHERE bj.status='waiting' AND bj.userid != " . sqlesc($CURUSER['id']) . " ORDER BY bj.date ASC LIMIT 1");
            $a = mysqli_fetch_assoc($r);
            if ($a["points"] == $playerarr['points']) {
                $subject = sqlesc("Blackjack Results");
                $winorlose = "nobody won";
                $msg = sqlesc("Your opponent was " . $CURUSER['username'] . ", you both had " . $a['points'] . " points - it was a tie.\n\n");
            } else {
                if (($a["points"] < $playerarr['points'] && $a['points'] < 21) || ($a["points"] > $playerarr['points'] && $a['points'] > 21)) {
                    $subject = sqlesc("Blackjack Results");
                    $msg = sqlesc("You lost to " . $CURUSER['username'] . " (You had " . $a['points'] . " points, " . $CURUSER['username'] . " had " . $playerarr['points'] . " points).\n\n");
                    $winorlose = "you won " . mksize($mb);
                    $st_query = "+ " . $mb . ", bjwins = bjwins +";
                    $nd_query = "- " . $mb . ", bjlosses = bjlosses +";
                } elseif (($a["points"] > $playerarr['points'] && $a['points'] < 21) || $a["points"] == 21 || ($a["points"] < $playerarr['points'] && $a['points'] > 21)) {
                    $subject = sqlesc("Blackjack Results");
                    $msg = sqlesc("You beat " . $CURUSER['username'] . " (You had " . $a['points'] . " points, " . $CURUSER['username'] . " had " . $playerarr['points'] . " points).\n\n");
                    $winorlose = "you lost " . mksize($mb);
                    $st_query = "- " . $mb . ", bjlosses = bjlosses +";
                    $nd_query = "+ " . $mb . ", bjwins = bjwins +";
                }
                sql_query("UPDATE users SET uploaded = uploaded " . $st_query . " 1 WHERE id=" . sqlesc($CURUSER['id']));
                sql_query("UPDATE users SET uploaded = uploaded " . $nd_query . " 1 WHERE id=" . sqlesc($a['userid']));
                $update['uploaded'] = ($a['uploaded'] + $mb);
                $update['uploaded_loser'] = ($User['uploaded'] - $mb);
                $update['bjwins'] = ($a['bjwins'] + 1);
                $update['bjlosses'] = ($User['bjlosses'] + 1);
                //==stats
                $mc1->begin_transaction('userstats_'.$a['userid']);
                $mc1->update_row(false, array(
                    'uploaded' => $update['uploaded']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                $mc1->begin_transaction('user_stats_'.$a['userid']);
                $mc1->update_row(false, array(
                    'uploaded' => $update['uploaded']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                $mc1->begin_transaction('userstats_'.$CURUSER['id']);
                $mc1->update_row(false, array(
                    'uploaded' => $update['uploaded_loser']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                $mc1->begin_transaction('user_stats_'.$CURUSER['id']);
                $mc1->update_row(false, array(
                    'uploaded' => $update['uploaded_loser']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                //== curuser values
                $mc1->begin_transaction('MyUser'.$a['userid']);
                $mc1->update_row(false, array(
                    'bjwins' => $update['bjwins']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                $mc1->begin_transaction('user'.$a['userid']);
                $mc1->update_row(false, array(
                    'bjwins' => $update['bjwins']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                $mc1->begin_transaction('MyUser'.$CURUSER['id']);
                $mc1->update_row(false, array(
                    'bjlosses' => $update['bjlosses']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                $mc1->begin_transaction('user'.$CURUSER['id']);
                $mc1->update_row(false, array(
                    'bjlosses' => $update['bjlosses']
                ));
                $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
            }
            sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, " . $a['userid'] . ", $now, $msg, $subject)");
            $mc1->delete_value('inbox_new_'.$a['userid']);
            $mc1->delete_value('inbox_new_sb_'.$a['userid']);
            sql_query("DELETE FROM blackjack WHERE userid IN (" . sqlesc($CURUSER['id']) . ", " . sqlesc($a['userid']) . ")");
            $HTMLOUT.= "<tr><td align='center'>Your opponent was " . $a["username"] . ", he/she had " . $a['points'] . " points, $winorlose.<br /><br /><b><a href='/blackjack.php'>Play again</a></b></td></tr>";
        } else {
            sql_query("UPDATE blackjack SET status = 'waiting', date=" . $now . ", gameover='yes' WHERE userid = " . sqlesc($CURUSER['id']));
            $HTMLOUT.= "<tr><td align='center'>There are no other players, so you'll have to wait until someone plays against you.<br />You will receive a PM with the game results.<br /><br /><b><a href='/blackjack.php'>Back</a></b><br /></td></tr>";
        }
        $HTMLOUT.= "</table></td></tr></table><br />";
        echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
    }
} else {
    $sql = sql_query('SELECT bjwins, bjlosses '.'FROM users '.'WHERE id = '.sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $User = mysqli_fetch_assoc($sql);
    $User['bjwins'] = (int)$User['bjwins'];
    $User['bjlosses'] = (int)$User['bjlosses'];
	 $tot_wins = (int)$User['bjwins'];
    $tot_losses = (int)$User['bjlosses'];
	 $tot_games = $tot_wins + $tot_losses;
    $win_perc = ($tot_losses == 0 ? ($tot_wins == 0 ? "---" : "100%") : ($tot_wins == 0 ? "0" : number_format(($tot_wins / $tot_games) * 100, 1)) . '%');
    $plus_minus = ($tot_wins - $tot_losses < 0 ? '-' : '') . mksize((($tot_wins - $tot_losses >= 0 ? ($tot_wins - $tot_losses) : ($tot_losses - $tot_wins))) * $mb);
    $HTMLOUT.= "<h1>{$INSTALLER09['site_name']} Blackjack</h1>
	<table cellspacing='0' cellpadding='3' width='400'>
	<tr><td colspan='2' align='center'>
	<table class='message' width='100%' cellspacing='0' cellpadding='10' bgcolor='white'>
	<tr><td align='center'><img src='{$INSTALLER09['pic_base_url']}cards/tp.bmp' width='71' height='96' border='0' alt='' />&nbsp;<img src='{$INSTALLER09['pic_base_url']}cards/vp.bmp' width='71' height='96' border='0' alt='' /></td></tr>
	<tr><td align='left'>You must collect 21 points without going over.<br /><br />
	<b>NOTE:</b> By playing blackjack, you are betting 100 MB of upload credit!</td></tr>
	<tr><td align='center'>
	<form method='post' action='" . $_SERVER['PHP_SELF'] . "'><input type='hidden' name='game' value='hit' readonly='readonly' /><input type='hidden' name='start_' value='yes' readonly='readonly' /><input type='submit' value='Start!' /></form>
	</td></tr></table>
	</td></tr></table>
	<br /><br /><br />
  <table cellspacing='0' cellpadding='3' width='400'>
    <tr><td colspan='2' align='center'>
    <h1>Personal Statistics</h1></td></tr>
    <tr><td align='left'><b>Wins</b></td><td align='center'><b>{$tot_wins}</b></td></tr>
    <tr><td align='left'><b>Losses</b></td><td align='center'><b>{$tot_losses}</b></td></tr>
    <tr><td align='left'><b>Games Played</b></td><td align='center'><b>{$tot_games}</b></td></tr>
    <tr><td align='left'><b>Win Percentage</b></td><td align='center'><b>{$win_perc}</b></td></tr>
    <tr><td align='left'><b>+/-</b></td><td align='center'><b>{$plus_minus}</b></td></tr>
    </table>";
    echo stdhead('Blackjack') . $HTMLOUT . stdfoot();
}
?>
