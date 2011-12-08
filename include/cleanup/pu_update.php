<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

function cleanup_log( $data )
{
  $text = sqlesc($data['clean_title']);
  $added = TIME_NOW;
  $ip = sqlesc($_SERVER['REMOTE_ADDR']);
  $desc = sqlesc($data['clean_desc']);
  sql_query( "INSERT INTO cleanup_log (clog_event, clog_time, clog_ip, clog_desc) VALUES ($text, $added, $ip, {$desc})" ) or sqlerr(__FILE__, __LINE__);
}


function docleanup( $data ) {
        global $INSTALLER09, $queries, $mc1;
        set_time_limit(1200);
        ignore_user_abort(1);
        //== Updated promote power users
  $limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = (TIME_NOW - 86400*28);
    $res = sql_query("SELECT id, uploaded, downloaded, invites, modcomment FROM users WHERE class = ".UC_USER." AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND enabled='yes' AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
    $msgs_buffer = $users_buffer = array();
    if (mysqli_num_rows($res) > 0) {
        $subject ="Auto Promotion";
        $msg = "Congratulations, you have been Auto-Promoted to [b]Power User[/b]. :)\n You get one extra invite.\n";
        while ($arr = mysqli_fetch_assoc($res)) {
            $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - Promoted to Power User by System (UL=" . mksize($arr['uploaded']) . ", DL=" . mksize($arr['downloaded']) . ", R=" . $ratio . ").\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', 1, 1, ' . $modcom . ')';
            $update['invites'] = ($arr['invites'] + 1);
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'invites' => $update['invites']));
            $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
            $mc1->begin_transaction('user_stats_'.$arr['id']);
            $mc1->update_row(false, array('modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            $mc1->begin_transaction('MyUser_'.$arr['id']);
            $mc1->update_row(false, array('class' => 1, 'invites' => $update['invites']));
            $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
            $mc1->delete_value('inbox_new_'.$arr['id']);
            $mc1->delete_value('inbox_new_sb_'.$arr['id']);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, class, invites, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class), invites = invites+values(invites), modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Promoted ".$count." member(s) from User to Power User");
        }
        unset ($users_buffer, $msgs_buffer, $update, $count);
        status_change($arr['id']); //== For Retros announcement mod
    }

write_log("Power User Updates -------------------- Power User Updates Clean Complete using $queries queries--------------------");
       

if( false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"]) )
  {
    $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items deleted/updated";
  }
          
        if( $data['clean_log'] )
        {
        cleanup_log( $data );
        }
        
}
?>
