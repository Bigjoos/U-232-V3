<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once INCL_DIR.'user_functions.php';
require_once(INCL_DIR.'bbcode_functions.php');
require_once(INCL_DIR.'html_functions.php');
dbconn();
loggedinorreturn();

$lang = load_language('global');

$valid_actions = array('add'=>1,'remove'=>1,'block'=>1,'confirm'=>1,'list'=>1,);
$action = isset($_GET['do'])  && isset($valid_actions[$_GET['do']]) ? $_GET['do'] : 'list';
$sure = isset($_GET['sure']) && $_GET['sure'] == 1 ? true : false;
$uid = isset($_GET['uid']) && is_valid_id($_GET['uid']) ? (int)$_GET['uid'] : 0;
$cid = (int)$CURUSER['id'];
$cna = $CURUSER['username'];
//$rid = isset($_GET['rid']) && is_valid_id($_GET['rid']) ? $_GET['rid'] : 0;

//some custom shit function 
function fooboo($q) {
  return sprintf('(%s)',join(', ',array_map('sqlesc',$q)));
}


//this needs to be moved to lang
$errs = array(
'friends_friends' => 'You and <b>%s</b> are already friends !',
'friends_neutral' => 'You already sent a friendship request to <b>%s</b> but he didn\'t reviewed yet!',
'neutral_friends' => 'User <b>%s</b> already sent you a friendship request, you need to accep it from <a href="relations.php#requests">here</a>',
'blocked_x'       => 'You blocked user <b>%s</b>, if you want to start a friendship with him you need to remove the block on him from here <a href="relations.php#blocks"></a>',
'x_blocked'       => 'User <b>%s</b> blocked you, there is nothing you can do!',
);

switch($action) {
  case 'add':
     if($uid === 0 || $uid === $cid)
        stderr('Err',sprintf('<b>%s</b>, wtf are you trying to do ?%s',ucfirst($CURUSER['username']),($uid === $cid ? 'You can\'t add yourself as friend, fool !' : '')));
     if($sure === false) {
       $q1 = sql_query(sprintf('SELECT u.username , r1.relation as your_relation, r2.relation as his_relation FROM users as u LEFT JOIN relations as r1 ON r1.user = %d AND r1.relation_with = %d LEFT JOIN relations as r2 ON r2.user = %d AND r2.relation_with = %d WHERE u.id = %d',$cid,$uid,$uid,$cid,$uid)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

       if(mysqli_num_rows($q1) === 1) {
        $ar1 = mysqli_fetch_assoc($q1);
          
        $err_key = sprintf('%s_%s',($ar1['his_relation'] == 'blocked' ? 'x' : $ar1['your_relation']),($ar1['your_relation'] == 'blocked' ? 'x' : $ar1['his_relation']));
        
        if(isset($errs[$err_key])) 
          stderr('Err',sprintf($errs[$err_key],$ar1['username']));
        else
          stderr('Confirm action',sprintf('%s you are about to request friendship from a user named <b>%s</b>, are you sure you want to do that ? The user may reject your request, if you are sure click <a href="relations.php?do=add&amp;uid=%d&amp;sure=1">here</a>',$cna,$ar1['username'],$uid));
       }
     } else {
        $message =array('subject'=>sprintf('Friendship request from %s',$cna),'msg'=>format_comment(sprintf('Hey, [url=userdetails.php?id=%d]%s[/url] would like to be your firend, you can manage your firendship requests [url=relations.php#requests]here[/url]',$cid,$cna)),'sender'=>(int)$INSTALLER09['bot_id'],'receiver'=>$uid,'added'=>(int)TIME_NOW);
        $relation = array(array('user'=>$CURUSER['id'],'relation_with'=>$uid,'relation'=> 'friends'), array('user'=>$uid,'relation_with'=>$CURUSER['id'], 'relation'=>'neutral'));
       
        sql_query(sprintf('INSERT INTO messages(%s) VALUES(%s)',join(', ',array_keys($message)),join(', ',array_map('sqlesc',$message)))) or sqlerr(__FILE__,__LINE__);
        sql_query(sprintf('INSERT INTO relations (%s) VALUES %s',join(', ',array_keys($relation[0])),join(', ',array_map('fooboo',$relation)))) or sqlerr(__FILE__,__LINE__);
           
        stderr('Success','Your friendship request was sent, wait till the user reviewes it!');
     }
  break;
  case 'block':
  break;
  case 'remove' : //remove as friend
  break;
  case 'list' :
    die('just a tiny bit');
}
?>
