<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

function cleanup_log($data)
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
        //== Updated demote power users
        $minratio = 0.85;
        $res = sql_query("SELECT id, uploaded, downloaded, modcomment FROM users WHERE class = ".UC_POWER_USER." AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
        $subject ="Auto Demotion";
        $msgs_buffer = $users_buffer = array();
        if (mysqli_num_rows($res) > 0) {
        $msg = "You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below  $minratio.\n";
        while ($arr = mysqli_fetch_assoc($res)) {
            $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
            $modcomment = $arr['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - Demoted To User by System (UL=" . mksize($arr['uploaded']) . ", DL=" . mksize($arr['downloaded']) . ", R=" . $ratio . ").\n". $modcomment;
            $modcom =  sqlesc($modcomment);
            $msgs_buffer[] = '(0,' . $arr['id'] . ', '. TIME_NOW .', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $users_buffer[] = '(' . $arr['id'] . ', 0, ' . $modcom . ')';
            $mc1->begin_transaction('user'.$arr['id']);
            $mc1->update_row(false, array('class' => 0));
            $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
            $mc1->begin_transaction('user_stats_'.$arr['id']);
            $mc1->update_row(false, array('modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            $mc1->begin_transaction('MYuser_'.$arr['id']);
            $mc1->update_row(false, array('class' => 0));
            $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
            $mc1->delete_value('inbox_new_'.$arr['id']);
            $mc1->delete_value('inbox_new_sb_'.$arr['id']);
        }
        $count = count($users_buffer);
        if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, class, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE class=values(class),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Cleanup: Demoted ".$count." member(s) from Power User to User");
            status_change($arr['id']);
        }
        unset ($users_buffer, $msgs_buffer, $count);
        status_change($arr['id']); //== For Retros announcement mod
    }
    //==End
    if($queries > 0)
    write_log("Power User Demote Updates -------------------- Power User Demote Updates Clean Complete using $queries queries--------------------");
       
    if(false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"]))
    {
    $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items deleted/updated";
    }
          
    if($data['clean_log'])
    {
    cleanup_log($data);
    }     
}
?>
