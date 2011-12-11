<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
//=== mass bonus stuff for members coded for TB sites 2011 ~ snuggs
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
header('HTTP/1.0 404 Not Found');  
$HTMLOUT ='';
$HTMLOUT .= '
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL '.htmlspecialchars($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1).' was not found on this server.</p>
<hr />
<address>'.$_SERVER['SERVER_SOFTWARE'].' Server at '.$INSTALLER09['baseurl'].' Port 80</address>
</body></html>';
echo $HTMLOUT;
exit();
}

require_once INCL_DIR.'user_functions.php';
require_once INCL_DIR.'bbcode_functions.php';
require_once(CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);
//=== all the defaults
$lang = array_merge( $lang );
$h1_thingie = $HTMLOUT = '';
//=== check if action_2 is sent ($_POST) if so make sure it's what you want it to be
$action_2 = (isset($_POST['action_2']) ? $_POST['action_2'] : 'no_action');
$good_stuff = array('upload_credit','karma','freeslots', 'invite');
$action = (($action_2  && in_array($action_2, $good_stuff, true)) ? $action_2 : '');

//=== see if the credit is for all classes or selected classes all_or_selected_classes
if (isset($_POST['all_or_selected_classes']))
	{
    $free_for_classes = 1;	
	}
	else
	{
	$free_for_classes = 0;
  $free_for = (isset($_POST['free_for_classes']) ? $_POST['free_for_classes'] : '');
	}

//=== switch for the actions \\o\o/o//
switch ($action)
    {
    case 'upload_credit':
    
$GB = isset($_POST['GB']) ? 0 + $_POST['GB'] : 0;

if ($GB < 1073741824 || $GB > 53687091200) //=== forgot to enter GB or wrong numbers
    stderr('Upload Credit Error', 'You forgot to select an amount!');
    
$bonus_added = ($GB / 1073741824);

//=== if for all classes
if ($free_for_classes === 1)
    {
    $res_GB = sql_query('SELECT id, uploaded, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\'') or sqlerr(__FILE__, __LINE__);
    $pm_buffer = $users_buffer = array();
    if (mysqli_num_rows($res_GB) > 0) {
    	 $subject = sqlesc("Upload added");
		   $msg = sqlesc("Hey,\n we have decided to add ".$bonus_added." GB upload credit to all classes.\n Cheers ".$INSTALLER09['site_name'] ." staff");
    	while ($arr_GB = mysqli_fetch_assoc($res_GB))
            {
            $GB_new = ($arr_GB['uploaded'] + $GB);
            $modcomment = $arr_GB['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$bonus_added." GB Mass Bonus added - AutoSystem.\n". $modcomment;
			      $modcom =  sqlesc($modcomment);
			      $pm_buffer[] = '(0, '.$arr_GB['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			      $users_buffer[] = '(' . $arr_GB['id'] . ', '.$GB_new.', ' . $modcom . ')';
            $mc1->begin_transaction('user_stats_'.$arr_GB['id']);
            $mc1->update_row(false, array('uploaded' => $GB_new, 'modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            $mc1->begin_transaction('userstats_'.$arr_GB['id']);
            $mc1->update_row(false, array('uploaded' => $GB_new));
            $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
            delete_id_keys('inbox_new_'.$arr_GB['id']);
            delete_id_keys('inbox_new_sb_'.$arr_GB['id']);
            }
            
            $count = count($users_buffer);
            if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, uploaded, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE uploaded=uploaded+values(uploaded),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Staff mass bonus - added upload credit to ".$count." members in all classes by ".$CURUSER['username']."");
            }
            
            unset ($users_buffer, $pm_buffer, $count);  
            }
            header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&GB=1');
            die(); 
            
            }
            
elseif ($free_for_classes === 0)
    {
    foreach ($free_for as $class)
        {
            if (ctype_digit($class))
                {
                $res_GB = sql_query('SELECT id, uploaded, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\' AND class = '.$class);
                $pm_buffer = $users_buffer = array();
                if (mysqli_num_rows($res_GB) > 0) {
                    $subject = sqlesc("Upload added");
				            $msg = sqlesc("Hey,\n we have decided to add ".$bonus_added." GB upload credit to your group class.\n Cheers ".$INSTALLER09['site_name'] ." staff");
                    while ($arr_GB = mysqli_fetch_assoc($res_GB))
                        {               
                        $GB_new = ($arr_GB['uploaded'] + $GB);
                        $modcomment = $arr_GB['modcomment'];
                        $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$bonus_added." GB Mass Bonus added - AutoSystem.\n". $modcomment;
                        $modcom =  sqlesc($modcomment);
                        $pm_buffer[] = '(0, '.$arr_GB['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			                  $users_buffer[] = '(' . $arr_GB['id'] . ', '.$GB_new.', ' . $modcom . ')';
                        $mc1->begin_transaction('user_stats_'.$arr_GB['id']);
                        $mc1->update_row(false, array('uploaded' => $GB_new, 'modcomment' => $modcomment));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                        $mc1->begin_transaction('userstats_'.$arr_GB['id']);
                        $mc1->update_row(false, array('uploaded' => $GB_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                        delete_id_keys('inbox_new_'.$arr_GB['id']);
                        delete_id_keys('inbox_new_sb_'.$arr_GB['id']);
                        }
                        
                        $count = count($users_buffer);
                        if ($count > 0){
                        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
                        sql_query("INSERT INTO users (id, uploaded, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE uploaded=uploaded+values(uploaded),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
                        write_log("Staff mass bonus - added upload credit to ".$count." members by ".$CURUSER['username']."");
                        }
                        unset ($users_buffer, $pm_buffer, $count);
                        }
                        }
                        }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&GB=2');
    die(); 
    }

        break;
    case 'karma':
 
$karma = isset($_POST['karma']) ? 0 + $_POST['karma'] : 0; 

if ($karma < 100 || $karma > 5000) //=== forgot to enter karma or wrong numbers
    stderr('Karma Bonus Error', 'You forgot to select an amount!');
    
//=== if for all classes
if ($free_for_classes === 1)
    {
    $res_karma = sql_query('SELECT id, seedbonus, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\'')  or sqlerr(__FILE__, __LINE__);
    $pm_buffer = $users_buffer = array();
    if (mysqli_num_rows($res_karma) > 0) {
    	$subject = sqlesc("Karma added");
		  $msg = sqlesc("Hey,\n we have decided to add ".$karma." Karma bonus points to all classes.\n Cheers ".$INSTALLER09['site_name'] ." staff");
    	while ($arr_karma = mysqli_fetch_assoc($res_karma))
            {
            $karma_new = ($arr_karma['seedbonus'] + $karma);
            $modcomment = $arr_karma['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$karma." Mass Bonus Karma Points added - AutoSystem.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
			      $pm_buffer[] = '(0, '.$arr_karma['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			      $users_buffer[] = '(' . $arr_karma['id'] . ', '.$karma_new.', ' . $modcom . ')';
            $mc1->begin_transaction('user_stats_'.$arr_karma['id']);
            $mc1->update_row(false, array('seedbonus' => $karma_new, 'modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            $mc1->begin_transaction('userstats_'.$arr_karma['id']);
            $mc1->update_row(false, array('seedbonus' => $karma_new));
            $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
            delete_id_keys('inbox_new_'.$arr_karma['id']);
            delete_id_keys('inbox_new_sb_'.$arr_karma['id']);
            }
            
            $count = count($users_buffer);
            if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, seedbonus, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE seedbonus=seedbonus+values(seedbonus),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Staff mass bonus - added karma points to ".$count." members in all classes by ".$CURUSER['username']."");
            }
  
            unset ($users_buffer, $pm_buffer, $count);
            }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&karma=1');
    die(); 
    }
elseif ($free_for_classes === 0)
    {
    foreach ($free_for as $class)
        {
            if (ctype_digit($class))
                {
                $res_karma = sql_query('SELECT id, seedbonus, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\' AND class = '.$class);
                    $pm_buffer = $users_buffer = array();
                    if (mysqli_num_rows($res_karma) > 0) {
                    $subject = sqlesc("Karma added");
				            $msg = sqlesc("Hey,\n we have decided to add ".$karma." bonus points to your group class.\n Cheers ".$INSTALLER09['site_name'] ." staff");
                    while ($arr_karma = mysqli_fetch_assoc($res_karma))
                        {               
                        $karma_new = ($arr_karma['seedbonus'] + $karma);
                        $modcomment = $arr_karma['modcomment'];
                        $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$karma." Mass Bonus Karma Points added - AutoSystem.\n". $modcomment;
                        $modcom =  sqlesc($modcomment);
			                  $pm_buffer[] = '(0, '.$arr_karma['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			                  $users_buffer[] = '(' . $arr_karma['id'] . ', '.$karma_new.', ' . $modcom . ')';                      
                        $mc1->begin_transaction('user_stats_'.$arr_karma['id']);
                        $mc1->update_row(false, array('seedbonus' => $karma_new, 'modcomment' => $modcomment));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                        $mc1->begin_transaction('userstats_'.$arr_karma['id']);
                        $mc1->update_row(false, array('seedbonus' => $karma_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                        delete_id_keys('inbox_new_'.$arr_karma['id']);
                        delete_id_keys('inbox_new_sb_'.$arr_karma['id']);
                        }
                        
                        $count = count($users_buffer);
                        if ($count > 0){
                        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
                        sql_query("INSERT INTO users (id, seedbonus, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE seedbonus=seedbonus+values(seedbonus),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
                        write_log("Staff mass bonus - added karma points to ".$count." members by ".$CURUSER['username']."");
                        }
                        
                        unset ($users_buffer, $pm_buffer, $count);
                        }
                        }
                        }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&karma=2');
    die(); 
    }    
        break;    
    case 'freeslots':
    
$freeslots = isset($_POST['freeslots']) ? 0 + $_POST['freeslots'] : 0; 
if ($freeslots < 1 || $freeslots > 10) //=== forgot to enter freeslots or wrong numbers
    stderr('Free Leech Slot Error', 'You forgot to select an amount!');
    
//=== if for all classes
if ($free_for_classes === 1)
    {
    $res_freeslots = sql_query('SELECT id, freeslots, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\'') or sqlerr(__FILE__, __LINE__);
    	$pm_buffer = $users_buffer = array();
      if (mysqli_num_rows($res_freeslots) > 0) {
    	$subject = sqlesc("Free Slots added");
	    $msg = sqlesc("Hey,\n we have decided to add ".$freeslots." free slots to all classes.\n Cheers ".$INSTALLER09['site_name'] ." staff");
    	while ($arr_freeslots = mysqli_fetch_assoc($res_freeslots))
            {
            $freeslots_new = ($arr_freeslots['freeslots'] + $freeslots);
            $modcomment = $arr_freeslots['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$freeslots." Free Leech Slots Mass Bonus added - AutoSystem.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
			      $pm_buffer[] = '(0, '.$arr_freeslots['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			      $users_buffer[] = '(' . $arr_freeslots['id'] . ', '.$freeslots_new.', ' . $modcom . ')'; 
            $mc1->begin_transaction('MyUser_'.$arr_freeslots['id']);
            $mc1->update_row(false, array('freeslots' => $freeslots_new));
            $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
            $mc1->begin_transaction('user'.$arr_freeslots['id']);
            $mc1->update_row(false, array('freeslots' => $freeslots_new));
            $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
            $mc1->begin_transaction('user_stats_'.$arr_freeslots['id']);
            $mc1->update_row(false, array('modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            delete_id_keys('inbox_new_'.$arr_freeslots['id']);
            delete_id_keys('inbox_new_sb_'.$arr_freeslots['id']);
            }
            
            $count = count($users_buffer);
            if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, freeslots, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE freeslots=freeslots+values(freeslots),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Staff mass bonus - added freeslots to ".$count." members in all classes by ".$CURUSER['username']."");
            }
                        
            unset ($users_buffer, $pm_buffer, $count);
            }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&freeslots=1');
    die(); 
    }
elseif ($free_for_classes === 0)
    {
    foreach ($free_for as $class)
        {
            if (ctype_digit($class))
                {
                $res_freeslots = sql_query('SELECT id, freeslots, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\' AND class = '.$class);
                    $pm_buffer = $users_buffer = array();
                    if (mysqli_num_rows($res_freeslots) > 0) {
                    $subject = sqlesc("Free Slots added");
				            $msg = sqlesc("Hey,\n we have decided to add ".$freeslots." free slots to your group class.\n Cheers ".$INSTALLER09['site_name'] ." staff");
                    while ($arr_freeslots = mysqli_fetch_assoc($res_freeslots))
                        {               
                        $freeslots_new = ($arr_freeslots['freeslots'] + $freeslots);
                        $modcomment = $arr_freeslots['modcomment'];
                        $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$freeslots." Free Leech Slots Mass Bonus added - AutoSystem.\n". $modcomment;
                        $modcom =  sqlesc($modcomment);
			                  $pm_buffer[] = '(0, '.$arr_freeslots['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			                  $users_buffer[] = '(' . $arr_freeslots['id'] . ', '.$freeslots_new.', ' . $modcom . ')'; 
                        $mc1->begin_transaction('MyUser_'.$arr_freeslots['id']);
                        $mc1->update_row(false, array('freeslots' => $freeslots_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                        $mc1->begin_transaction('user'.$arr_freeslots['id']);
                        $mc1->update_row(false, array('freeslots' => $freeslots_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                        $mc1->begin_transaction('user_stats_'.$arr_freeslots['id']);
                        $mc1->update_row(false, array('modcomment' => $modcomment));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                        delete_id_keys('inbox_new_'.$arr_freeslots['id']);
                        delete_id_keys('inbox_new_sb_'.$arr_freeslots['id']);
                        }
                        $count = count($users_buffer);
                        if ($count > 0){
                        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
                        sql_query("INSERT INTO users (id, freeslots, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE freeslots=freeslots+values(freeslots),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
                        write_log("Staff mass bonus - added freeslots to ".$count." members by ".$CURUSER['username']."");
                        }
                        
                        unset ($users_buffer, $pm_buffer, $count);
                        }
                        }
                        }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&freeslots=2');
    die(); 
    }   
        break;
     case 'invite':

$invites = isset($_POST['invites']) ? 0 + $_POST['invites'] : 0;   

if ($invites < 1 || $invites > 50) //=== forgot to enter invites or wrong numbers
    stderr('Invite Error', 'You forgot to select an amount!');
    
//=== if for all classes
if ($free_for_classes === 1)
    {
    $res_invites = sql_query('SELECT id, invites, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\' AND invite_on = \'yes\'');
     $pm_buffer = $users_buffer = array();
     if (mysqli_num_rows($res_invites) > 0) {
    	$subject = sqlesc("Invites added");
		  $msg = sqlesc("Hey,\n we have decided to add ".$invites." invites to all classes.\n Cheers ".$INSTALLER09['site_name'] ." staff");
    	while ($arr_invites = mysqli_fetch_assoc($res_invites))
            {
            $invites_new = ($arr_invites['invites'] + $invites);
            $modcomment = $arr_invites['modcomment'];
            $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$invites." Invites Mass Bonus added - AutoSystem.\n". $modcomment;
            $modcom =  sqlesc($modcomment);
			      $pm_buffer[] = '(0, '.$arr_invites['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			      $users_buffer[] = '(' . $arr_invites['id'] . ', '.$invites_new.', ' . $modcom . ')'; 
            $mc1->begin_transaction('MyUser_'.$arr_invites['id']);
            $mc1->update_row(false, array('invites' => $invites_new));
            $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
            $mc1->begin_transaction('user'.$arr_invites['id']);
            $mc1->update_row(false, array('invites' => $invites_new));
            $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
            $mc1->begin_transaction('user_stats_'.$arr_invites['id']);
            $mc1->update_row(false, array('modcomment' => $modcomment));
            $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
            delete_id_keys('inbox_new_'.$arr_invites['id']);
            delete_id_keys('inbox_new_sb_'.$arr_invites['id']);
            }
            
            $count = count($users_buffer);
            if ($count > 0){
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO users (id, invites, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE invites=invites+values(invites),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
            write_log("Staff mass bonus - added invites to ".$count." members in all classes by ".$CURUSER['username']."");
            }
                        
            unset ($users_buffer, $pm_buffer, $count);
            }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&invites=1');
    die(); 
    }
elseif ($free_for_classes === 0)
    {
    foreach ($free_for as $class)
        {
            if (ctype_digit($class))
                {
                $res_invites = sql_query('SELECT id, invites, modcomment FROM users WHERE enabled = \'yes\' AND suspended = \'no\' AND invite_on = \'yes\' AND class = '.$class);
                $pm_buffer = $users_buffer = array();
                if (mysqli_num_rows($res_invites) > 0) {
                    $subject = sqlesc("Invites added");
				            $msg = sqlesc("Hey,\n we have decided to add ".$invites." invites to your group class.\n Cheers ".$INSTALLER09['site_name'] ." staff");
                    while ($arr_invites = mysqli_fetch_assoc($res_invites))
                        {               
                        $invites_new = ($arr_invites['invites'] + $invites);
                        $modcomment = $arr_invites['modcomment'];
                        $modcomment =  get_date( TIME_NOW, 'DATE', 1 ) . " - ".$invites." Invites Mass Bonus added - AutoSystem.\n". $modcomment;
                        $modcom =  sqlesc($modcomment);
			                  $pm_buffer[] = '(0, '.$arr_invites['id'].', '.TIME_NOW.', '.$msg.', '.$subject.')';
			                  $users_buffer[] = '(' . $arr_invites['id'] . ', '.$invites_new.', ' . $modcom . ')';
                        $mc1->begin_transaction('MyUser_'.$arr_invites['id']);
                        $mc1->update_row(false, array('invites' => $invites_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                        $mc1->begin_transaction('user'.$arr_invites['id']);
                        $mc1->update_row(false, array('invites' => $invites_new));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                        $mc1->begin_transaction('user_stats_'.$arr_invites['id']);
                        $mc1->update_row(false, array('modcomment' => $modcomment));
                        $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                        delete_id_keys('inbox_new_'.$arr_invites['id']);
                        delete_id_keys('inbox_new_sb_'.$arr_invites['id']);
                        }
                        
                        $count = count($users_buffer);
                        if ($count > 0){
                        sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $pm_buffer)) or sqlerr(__FILE__, __LINE__);
                        sql_query("INSERT INTO users (id, invites, modcomment) VALUES " . implode(', ', $users_buffer) . " ON DUPLICATE key UPDATE invites=invites+values(invites),modcomment=concat(values(modcomment),modcomment)") or sqlerr(__FILE__, __LINE__);
                        write_log("Staff mass bonus - added invites to ".$count." members by ".$CURUSER['username']."");
                        }
                        
                       unset ($users_buffer, $pm_buffer, $count);
                       }
                       }
                       }
    header('Location: staffpanel.php?tool=mass_bonus_for_members&action=mass_bonus_for_members&invites=2');
    die(); 
    } 
    
        break;       

} //=== end switch        
        
//=== make the class based selection thingie bit here :D
	$count = 1;
	$all_classes_check_boxes = '<table border="0" cellspacing="5" cellpadding="5" align="left"><tr>';
	for ($i = 1; $i <= UC_MAX; ++$i)
	{
	$all_classes_check_boxes .= '<td class="one">
		<input type="checkbox" name="free_for_classes[]" value="'.$i.'" checked="checked" /> <span style="font-weight: bold;color:#'.get_user_class_color($i).';">'.get_user_class_name($i).'</span></td>';
	if ($count == 6)
	{
	$all_classes_check_boxes .= '</tr>'.($i < UC_MAX ? '<tr>' : '');
	$count = 0;
	}
	$count++;
	}
	$all_classes_check_boxes .= ($count ==  0 ? '</table>' : '<tr><td colspan="'.(7 - $count).'" class="one"></td></tr></table>').'';

$bonus_GB = '<select name="GB">
        <option class="head" value="">Add Upload Credit</option>
        <option class="body" value="1073741824">1 GB</option>
        <option class="body" value="2147483648">2 GB</option>
        <option class="body" value="3221225472">3 GB</option>
        <option class="body" value="4294967296">4 GB</option>
        <option class="body" value="5368709120">5 GB</option>
        <option class="body" value="6442450944">6 GB</option>
        <option class="body" value="7516192768">7 GB</option>
        <option class="body" value="8589934592">8 GB</option>
        <option class="body" value="9663676416">9 GB</option>
        <option class="body" value="10737418240">10 GB</option>
        <option class="body" value="16106127360">15 GB</option>
        <option class="body" value="21474836480">20 GB</option>
        <option class="body" value="26843545600">25 GB</option>
        <option class="body" value="32212254720">30 GB</option>
        <option class="body" value="53687091200">50 GB</option>
        </select> select amount of bonus GB to add to members upload credit.';
        
$karma_drop_down = '
        <select name="karma">
        <option class="head" value="">Add Karma Bonus Points</option>';
$i = 100;
    while ($i <= 5000)
        {
        $karma_drop_down .= '<option class="body" value="'.$i.'.0">'.$i.' Karma Points</option>';
            $i = ($i < 1000 ? $i = $i + 100 : $i = $i + 500);
        }
    $karma_drop_down .= '</select> select amount of Karma Bonus Points to add.';

$free_leech_slot_drop_down = '
        <select name="freeslots">
        <option class="head" value="">Add freeslots</option>';
$i = 1;
    while ($i <= 50)
        {
        $free_leech_slot_drop_down .= '<option class="body" value="'.$i.'.0">'.$i.' freeslot'.($i !== 1 ? 's' : '').'</option>';
            $i = ($i < 10 ? $i = $i + 1 : $i = $i + 5);
        }
    $free_leech_slot_drop_down .= '</select> select amount of freeslots to add.';

$invites_drop_down = '
        <select name="invites">
        <option class="head" value="">Add Invites</option>';
$i = 1;
    while ($i <= 50)
        {
        $invites_drop_down .= '<option class="body" value="'.$i.'.0">'.$i.' invite'.($i !== 1 ? 's' : '').'</option>';
            $i = ($i < 10 ? $i = $i + 1 : $i = $i + 5);
        }
    $invites_drop_down .= '</select> select amount of invites to add.';
    
$drop_down = '
        <select name="bonus_options_1" id="bonus_options_1">
        <option value="">Select Bonus Type</option>
        <option value="upload_credit">Upload Credit</option>
        <option value="karma">Karma Points</option>
        <option value="freeslots">Free Leech Slots</option>
        <option value="invite">Invites</option>
        <option value="">Reset bonus type</option>
        </select>';

//=== h1 stuffzzzz
$h1_thingie .= (isset($_GET['GB']) ? ($_GET['GB'] === 1 ? '<h2>Bonus GB added to all enabled members</h2>' : '<h2>Bonus GB added to selected member classes</h2>') : '');
$h1_thingie .= (isset($_GET['karma']) ? ($_GET['karma'] === 1 ? '<h2>Bonus Karma added to all enabled members</h2>' : '<h2>Bonus Karma added to selected member classes</h2>') : '');
$h1_thingie .= (isset($_GET['freeslots']) ? ($_GET['freeslots'] === 1 ? '<h2>Bonus Free Leech Slots added to all enabled members<h2>' : '<h2>Bonus Free Leech Slots added to selected member classes</h2>') : '');
$h1_thingie .= (isset($_GET['invites']) ? ($_GET['invites'] === 1 ? '<h2>Bonus invites added to all enabled members</h2>' : '<h2>Bonus invites added to selected member classes</h2>') : '');


        
        $HTMLOUT .='<h1>'.$INSTALLER09['site_name'].' Mass Bonus</h1>'.$h1_thingie;
        
$HTMLOUT .= '<form name="inputform" method="post" action="staffpanel.php?tool=mass_bonus_for_members&amp;action=mass_bonus_for_members" enctype="multipart/form-data">
        <input type="hidden" id="action_2" name="action_2" value="" />
    <table align="center" width="80%" border="0" cellspacing="5" cellpadding="5">
    <tr>
        <td align="center" class="colhead" colspan="2">Mass bonus for all or selected members:</td>
    </tr>
    <tr>
        <td align="right" class="one" width="160px" valign="top"><span style="font-weight: bold;">Bonus Type:</span></td>
        <td align="left" class="one">'.$drop_down.'
        <div id="div_upload_credit" class="select_me"><br />'.$bonus_GB.'<hr /></div>
        <div id="div_karma" class="select_me"><br />'.$karma_drop_down.'<hr /></div>
        <div id="div_freeslots" class="select_me"><br />'.$free_leech_slot_drop_down.'<hr /></div>
        <div id="div_invite" class="select_me"><br />'.$invites_drop_down.'<hr /></div>
        </td>
    </tr>                
	<tr>
		<td class="one" valign="top" align="right"><span style="font-weight: bold;">Apply bonus to:</span></td>
        <td valign="top" align="left" class="one">
        <input type="checkbox" id="all_or_selected_classes" name="all_or_selected_classes" value="1"  checked="checked" /> 
        <span style="font-weight: bold;">all classes</span> [un-check to select what classes will get the bonus]
        <div id="classes_open" style="display:none;"><br />'.$all_classes_check_boxes.'</div></td>
	</tr>            
	<tr>
		<td class="one" valign="top" align="right"></td>
        <td valign="top" align="left" class="one">*** Please note, pm\'s are automatically sent to all users awarded by the script.<br /></td>
	</tr>        
    <tr>
        <td align="center" class="one" colspan="2">
        <input type="submit" class="btn" name="button" value="Do it!"  /></td>
    </tr>
    </table></form>
<script type="text/javascript">
<!--
$(document).ready(function(){
 $(".select_me").hide();
  $("#bonus_options_1").change(function() {
    $(".select_me").hide();
    $("#div_" + $(this).val()).show();

    //=== change the hidden input actin 2 thingie
      var text = $(this).val();
      $("#action_2").val(text);
 });
//=== show hide selected classes
$("#all_or_selected_classes").click(function() {
  $("#classes_open").slideToggle("slow", function() {
  });
});

});
-->
</script>';  
echo stdhead('Mass Bonus For Members') . $HTMLOUT . stdfoot();
?>
