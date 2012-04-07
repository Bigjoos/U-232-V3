<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
/*
+------------------------------------------------
|   $Date$ 10022011
|   $Revision$ 1.0
|   $Author$ pdq,Bigjoos
|   $User unlocks
|   
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'html_functions.php');
require_once(INCL_DIR.'user_functions.php');
dbconn(false);
loggedinorreturn();

$lang = load_language('global');

$id = (isset($_GET['id']) ? $_GET['id'] : $CURUSER['id']);
if (!is_valid_id($id) || $CURUSER['class'] < UC_STAFF)
    $id = $CURUSER['id'];

if ($CURUSER['got_moods'] == 'no'){
stderr("Error", "Time shall unfold what plighted cunning hides\n\nWho cover faults, at last shame them derides.... Yer simply no tall enough.");
die;
}  

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $updateset = array();
    $setbits = $clrbits = 0;
   
   
   if (isset($_POST['unlock_user_moods']))
   $setbits |= bt_options::UNLOCK_MORE_MOODS; // Unlock bonus moods
   else
   $clrbits |= bt_options::UNLOCK_MORE_MOODS; // lock bonus moods

//if ($setbits)
      //$updateset[] = 'perms = (perms | '.$setbits.')';
    
    //if ($clrbits)
      //$updateset[] = 'perms = (perms & ~'.$clrbits.')';
    
    //if (count($updateset))
      //sql_query('UPDATE users SET '.implode(',', $updateset).' WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);
      
   // update perms
   if ($setbits || $clrbits)
      sql_query('UPDATE users SET perms = ((perms | '.$setbits.') & ~'.$clrbits.') 
                 WHERE id = '.$id) or sqlerr(__file__, __line__); 
      
      //if ($id == $CURUSER['id']) {
      // grab current data     
      $res = sql_query('SELECT perms FROM users 
                     WHERE id = '.sqlesc($id).' LIMIT 1') or sqlerr(__file__, __line__); 
      $row = mysqli_fetch_assoc($res);
      $row['perms'] = (int)$row['perms'];
      $mc1->begin_transaction('MyUser_'.$id);
      $mc1->update_row(false, array('perms' => $row['perms']));
      $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
      //}
      header('Location: '.$INSTALLER09['baseurl'].'/user_unlocks.php');
      exit();
    }
    
    $checkbox_unlock_moods = (($CURUSER['perms'] & bt_options::UNLOCK_MORE_MOODS) ? ' checked="checked"' : '');
  
$HTMLOUT='';
$HTMLOUT .= begin_frame();
  
    $HTMLOUT.='<form action="" method="post">
        <div><h1>Unlock User Moods</h1></div>
        <table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
        <td width="50%">
        <b>Enable Bonus Moods?</b>
        <div style="color: gray;">Check this option to unlock bonus mood smilies.</div></td>
        <td width="30%"><div style="width: auto;" align="right">
        <input type="checkbox" name="unlock_user_moods" value="yes"'.$checkbox_unlock_moods.' /></div></td>
        </tr></table>';
        
        
    $HTMLOUT.='<input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" /></form>';
    
$HTMLOUT .= end_frame();
    
echo stdhead("User unlocks") . $HTMLOUT . stdfoot();
?>
