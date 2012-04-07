<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'function_happyhour.php');
require_once(CLASS_DIR.'class.bencdec.php');
dbconn();

  $pkey = isset($_GET['passkey']) && strlen($_GET['passkey']) == 32 ? $_GET['passkey'] : '';
  if(!empty($pkey)) {
	  $q0 = sql_query('SELECT * FROM users where passkey = '.sqlesc($pkey)) or sqlerr(__FILE__, __LINE__);
	  if(mysqli_num_rows($q0) == 0)
		  die($lang['download_passkey']);
	  else
		  $CURUSER = mysqli_fetch_assoc($q0);
  }else
	  loggedinorreturn();

  $lang =  array_merge( load_language('global'),load_language('download'));
  
  if (function_exists('parked'))
    parked();
  
  $id = isset($_GET['torrent']) ? (int)$_GET['torrent'] : 0;
  $ssluse = isset($_GET['ssl']) && $_GET['ssl'] == 1 || $CURUSER['ssluse'] == 3 ? 1 : 0;
  $zipuse = isset($_GET['zip']) && $_GET['zip'] == 1 ? true : false;

  if (!is_valid_id($id))
    stderr($lang['download_user_error'], $lang['download_no_id']);

  $res = sql_query('SELECT name, owner, vip, category, filename FROM torrents WHERE id = '.sqlesc($id)) or sqlerr(__FILE__, __LINE__);
  $row = mysqli_fetch_assoc($res);

  $fn = $INSTALLER09['torrent_dir'].'/'.$id.'.torrent';

  if (!$row || !is_file($fn) || !is_readable($fn))
    stderr('Err','There was an error with the file or with the query, please contact staff');

  if ( happyHour('check') && happyCheck('checkid', $row['category'])) {
    $multiplier = happyHour('multiplier');
    happyLog($CURUSER['id'],$id,$multiplier);
    sql_query('INSERT INTO happyhour (userid, torrentid, multiplier ) VALUES ('.sqlesc($CURUSER['id']).','.sqlesc($id).','.sqlesc($multiplier).')' ) or sqlerr(__FILE__,__LINE__);
    $mc1-> delete_value($CURUSER['id'].'_happy');
  }
   
  if (($CURUSER['downloadpos'] == 0 || $CURUSER['downloadpos'] > 1 || $CURUSER['suspended'] == 'yes') && !($CURUSER['id'] == $row['owner']))
    stderr("Error","Your download rights have been disabled.");

  if ($row['vip'] == 1 && $CURUSER['class'] < UC_VIP)
    stderr('VIP Access Required', 'You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!');
 
  sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = ".sqlesc($id));
  /** free mod by pdq **/
  /** freeslots/doubleseed by pdq **/
  if (isset($_GET['slot'])) {
    $added = (TIME_NOW + 14*86400);
    $slots_sql = sql_query('SELECT * FROM freeslots WHERE torrentid = '.sqlesc($id).' AND userid = '.sqlesc($CURUSER['id']));
    $slot = mysqli_fetch_assoc($slots_sql);
    $used_slot = $slot['torrentid'] == $id && $slot['userid'] == $CURUSER['id']; 
    /** freeslot **/
    if ($_GET['slot'] == 'free') {    
    if ($used_slot && $slot['free'] == 'yes')
        stderr('Doh!', 'Freeleech slot already in use.');

      if ($CURUSER['freeslots'] < 1)
        stderr('Doh!', 'No Slots.');
                
      $CURUSER['freeslots'] = ($CURUSER['freeslots'] - 1);  
      sql_query('UPDATE users SET freeslots = freeslots - 1 WHERE id = '.sqlesc($CURUSER['id']).' LIMIT 1') or sqlerr(__FILE__, __LINE__);
            
            if ($used_slot && $slot['doubleup'] == 'yes')
                sql_query('UPDATE freeslots SET free = "yes", addedfree = '.$added.' WHERE torrentid = '.$id.' AND userid = '.$CURUSER['id'].' AND doubleup = "yes"') or sqlerr(__FILE__, __LINE__);
            elseif ($used_slot && $slot['doubleup'] == 'no')
                sql_query('INSERT INTO freeslots (torrentid, userid, free, addedfree) VALUES ('.sqlesc($id).', '.sqlesc($CURUSER['id']).', "yes", '.$added.')') or sqlerr(__FILE__, __LINE__);
            else
                sql_query('INSERT INTO freeslots (torrentid, userid, free, addedfree) VALUES ('.sqlesc($id).', '.sqlesc($CURUSER['id']).', "yes", '.$added.')') or sqlerr(__FILE__, __LINE__);
        }
        /** doubleslot **/
        elseif ($_GET['slot'] == 'double') {
            
            if ($used_slot && $slot['doubleup'] == 'yes')
                stderr('Doh!', 'Doubleseed slot already in use.');
                
            if ($CURUSER['freeslots'] < 1)
                stderr('Doh!', 'No Slots.');
            
            $CURUSER['freeslots'] = ($CURUSER['freeslots'] - 1);
            sql_query('UPDATE users SET freeslots = freeslots - 1 WHERE id = '.sqlesc($CURUSER['id']).' LIMIT 1') or sqlerr(__FILE__, __LINE__);
            
            if ($used_slot && $slot['free'] == 'yes')
                sql_query('UPDATE freeslots SET doubleup = "yes", addedup = '.$added.' WHERE torrentid = '.sqlesc($id).' AND userid = '.sqlesc($CURUSER['id']).' AND free = "yes"') or sqlerr(__FILE__, __LINE__);
            elseif ($used_slot && $slot['free'] == 'no')
                sql_query('INSERT INTO freeslots (torrentid, userid, doubleup, addedup) VALUES ('.sqlesc($id).', '.sqlesc($CURUSER['id']).', "yes", '.$added.')') or sqlerr(__FILE__, __LINE__);
            else
                sql_query('INSERT INTO freeslots (torrentid, userid, doubleup, addedup) VALUES ('.sqlesc($id).', '.sqlesc($CURUSER['id']).', "yes", '.$added.')') or sqlerr(__FILE__, __LINE__);
        }
        else
            stderr('ERROR', 'What\'s up doc?');
            
    $mc1->delete_value('fllslot_'.$CURUSER['id']);
    make_freeslots($CURUSER['id'], 'fllslot_');
    $user['freeslots'] = ($CURUSER['freeslots'] - 1);
    $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
    $mc1->update_row(false, array('freeslots' => $CURUSER['freeslots']));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$CURUSER['id']);
    $mc1->update_row(false, array('freeslots' => $user['freeslots']));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    }
  /** end **/
  $mc1->delete_value('MyPeers_'.$CURUSER['id']);
  $mc1->delete_value('top5_tor_');
  $mc1->delete_value('last5_tor_');
  $mc1->delete_value('scroll_tor_');
  if (!isset($CURUSER['passkey']) || strlen($CURUSER['passkey']) != 32) 
  {
    $CURUSER['passkey'] = md5($CURUSER['username'].TIME_NOW.$CURUSER['passhash']);
    sql_query('UPDATE users SET passkey='.sqlesc($CURUSER['passkey']).' WHERE id='.$CURUSER['id']);
    $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
    $mc1->update_row(false, array('passkey' => $CURUSER['passkey']));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$CURUSER['id']);
    $mc1->update_row(false, array('passkey' => $CURUSER['passkey']));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
  }

  $dict = bencdec::decode_file($fn, $INSTALLER09['max_torrent_size']);
  $dict['announce'] = $INSTALLER09['announce_urls'][$ssluse].'?passkey='.$CURUSER['passkey'];
  $dict['uid'] = 0 + $CURUSER['id'];
  $tor = bencdec::encode($dict);

  if($zipuse) {
    require_once(INCL_DIR.'phpzip.php');
    $row['name'] = str_replace(array(' ','.','-'),'_',$row['name']);
    $file_name = $INSTALLER09['torrent_dir'].'/'.$row['name'].'.torrent';
    if(file_put_contents($file_name,$tor)) {
      $zip = new PHPZip();
      $files = array($file_name);
      $file_name = $INSTALLER09['torrent_dir'].'/'.$row['name'].'.zip';
      $zip->Zip($files,$file_name);
      $zip->forceDownload($file_name);
      unlink($INSTALLER09['torrent_dir'].'/'.$row['name'].'.torrent');
      unlink($INSTALLER09['torrent_dir'].'/'.$row['name'].'.zip');
    } else 
      stderr('Error','Can\'t create the new file, please contatct staff');
  } else {
    header('Content-Disposition: attachment; filename="['.$INSTALLER09['site_name'].']'.$row['filename'].'"');
    header("Content-Type: application/x-bittorrent");
    echo($tor);
  }
?>
