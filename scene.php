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
require_once(INCL_DIR.'bbcode_functions.php');
dbconn(true);
loggedinorreturn();

$HTMLOUT = $curuser_cache = $user_cache = $stats_cache = $user_stats_cache = '';

//get the config from db
$pconf = sql_query('SELECT name, value FROM paypal_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysqli_fetch_assoc($pconf))
  $paypal_config[$ac['name']] = $ac['value'];
  
//GB TO GIVE PER £//
$givegb = $paypal_config['gb']*1024*1024*1024;
//$givegb = 1*1024*1024; //1GB per £1 donated
//TIME TO GIVE PER £5//
$givetime = $paypal_config['weeks']*604800;
//$givetime = 0.5*108000; //108000 = 30 days  //set for 15 days per £5 donated
//INVITES TO GIVE PER £5//
$giveinvites = $paypal_config['invites'];
//$giveinvites = 1;
$email = $paypal_config['email'];

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate

$header = "GET /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_type = $_POST['payment_type'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

//$payment_status = "Completed";//Only uncomment if using sandbox mode

$id = (int)$_POST['custom'];
if(!is_valid_id($id))
stderr("Error", "No user with that ID.");

if (!$fp)
stderr("Error", "Please contact Sysop.");

//MAKE A REPORT IF PENDING//
if ($payment_status == "pending"){
$dt = TIME_NOW;
sql_query("INSERT into reports (reported_by,reporting_what,reporting_type,reason,added) VALUES ('2','$id','User', 'Pending donation', $dt)") or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('new_report_');
}
//IF PENDING SEND THEM TO THE ECHECK PAGE TO TELL THEM THEY WONT BE UPDATED TILL STAFF SORT THERE SHIT OUT
//=== echecks. paypal posts payments again when echecks clear... NOT! it's a paypal thing... if they pay with an echeque, you will have to use the manual confirm page when their $$ is confirmed...
if ($payment_type == "echeck" || $payment_status == "Pending"){
header("Location: {$INSTALLER09['baseurl']}/paypal_success.php?echeck=1"); //=== location of your success page
die;
}

//=== check for proper values from PayPal
if ($payment_type == 'instant' && $payment_status == 'Completed' && $payment_amount > '0'){

//=== process payment set $receiver_email to your email on the donate page
if ($receiver_email != "$email") //=== change to your paypal email
stderr("Error", "Please contact Sysop.");
settype($payment_amount, "float");
if ($payment_amount > 1)
settype($payment_amount, "string");
$res = sql_query("SELECT * FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$user = mysqli_fetch_array($res) or stderr("Error", "No user with that ID!");
$modcomment = htmlspecialchars($user['modcomment']);

//=== add donated amount  to user and to funds table /  Set donor status
if (isset($_POST['mc_gross'])){
$donated = 0 + $_POST['mc_gross'];
$added = TIME_NOW;
sql_query("INSERT INTO funds (cash, user, added) VALUES (".sqlesc($donated).", ".sqlesc($user['id']).", $added)") or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('totalfunds_');
$updateset[] = "donated = ".sqlesc($donated);
$updateset[] = "total_donated = ".sqlesc($user['total_donated'] + $donated);
$curuser_cache['donated'] = $donated;
$user_cache['donated'] = $donated;
$curuser_cache['total_donated'] = ($user['total_donated'] + $donated);
$user_cache['total_donated'] = ($user['total_donated'] + $donated);
}

//=== add to uploaded amount
$curuploaded = $user['uploaded'];
$uploaded = $donated * $givegb; 
$upadded = mksize($uploaded);
$total = $uploaded + $curuploaded;
$updateset[] = "uploaded = ".sqlesc($total);
$stats_cache['uploaded'] = $total;
$user_stats_cache['uploaded'] = $total;
//=== add invites
$curinvites = (int)$user['invites'];
$invites_added = $donated / 5 * $giveinvites;
$updateset[] = "invites = ".sqlesc($curinvites + $invites_added);
$curuser_cache['invites'] = $curinvites + $invites_added;
$user_cache['invites'] = $curinvites + $invites_added;
//==Check class for vipclass_before
if ($user['class'] <> UC_VIP) {
$vipbefore = (int)$user['class'];
}
else 
$vipbefore = (int)$user['vipclass_before'];
//=== check to see if they are a donor yet
$donorlength = $donated / 5;
if ($user['donor'] == 'no') {
$donoruntil = TIME_NOW + $donorlength * $givetime; //===> 2419200 = 2 weeks for 5£ --- 1209600 = 1 week for 5£ donation
$donoruntil_val = TIME_NOW + $donorlength * $givetime; //===> 1209600 = 2 weeks for 5$ --- 604800 = 1 week for 5£ donation
$dur = $donorlength . " week" . ($donorlength > 1 ? "s" : ""); //=== I left the 1 ? "s" in case you want to have only one week...
$subject = sqlesc("Thank You for Your Donation!");
$msg = sqlesc("Dear " . htmlspecialchars($user['username']) ."
:wave:
Thanks for your support to ".$INSTALLER09['site_name']."!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards server upgrades.
As a donor, you are given $upadded bonus added to your uploaded amount, $invites_added new invites added, the status of VIP, as well as the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
".$INSTALLER09['site_name']." staff.


PS. Your donator status will last for ".$dur."  and can be found on your user details page. It can only be seen by you.");
$modcomment = get_date( TIME_NOW, 'DATE', 1 ) . " - Donator status set for $dur -- $upadded GB bonus -- $invites_added new invites. added by system.\n".$modcomment;
$updateset[] = "donoruntil = ".sqlesc($donoruntil_val);
$updateset[] = "donor = 'yes'";
$curuser_cache['donoruntil'] = $donoruntil_val;
$user_cache['donoruntil'] = $donoruntil_val;
$curuser_cache['donor'] = 'yes';
$user_cache['donor'] = 'yes';
$mc1->delete_value('inbox_new_'.$id);
$mc1->delete_value('inbox_new_sb_'.$id);
} //=== end if donor no

elseif ($user['donor'] == 'yes') {
$donorlengthadd = $donated / 5;
//CHECK TO MAKE SURE THEY HAVE DONOR TIME IF NOT THEN SET IT FROM NOW
if ($user['donoruntil'] <= TIME_NOW) {
$donoruntil_val = TIME_NOW + $donorlengthadd * $givetime; 
}
else {
$donoruntil_val = $donorlengthadd * $givetime; 
}
$dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : "");
$subject = sqlesc("Thank You for Your Donation... Again!");
$msg = sqlesc("Dear ".htmlspecialchars($user['username']) ."
:wave:
Thanks for your continued support to ".$INSTALLER09['site_name']."!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
As a donor, you are given $upadded bonus added to your uploaded amount, $invites_added new invites added, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
".$INSTALLER09['site_name']." Staff

PS. Your donator status will last for an extra ".$dur." on top of your current donation status, and can be found on your user details page. It can only be seen by you.");
$modcomment = get_date( TIME_NOW, 'DATE', 1 ) . " - Donator status set for another $dur -- $upadded GB bonus added -- $invites_added new invites. added by system.\n".$modcomment;
$donorlengthadd = $donoruntil_val;
sql_query("UPDATE users SET donoruntil = donoruntil + $donorlengthadd, vipclass_before = ".sqlesc($vipbefore)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$curuser_cache['donoruntil'] = $donorlengthadd;
$user_cache['donoruntil'] = $donorlengthadd;
$curuser_cache['vipclass_before'] = $vipbefore;
$user_cache['vipclass_before'] = $vipbefore;
$mc1->delete_value('inbox_new_'.$id);
$mc1->delete_value('inbox_new_sb_'.$id);
}
//=== end if adding to donor time...
$added = TIME_NOW;
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $id, $msg, $added)") or sqlerr(__FILE__, __LINE__);
if ($CURUSER['class'] < UC_UPLOADER) //=== set this to the lowest class you don't want changed to VIP
$updateset[] = "class = '".UC_VIP."'";
$curuser_cache['class'] = UC_VIP;
$user_cache['class'] = UC_VIP;
//=== Add ModComment to the update set...
$updateset[] = "modcomment = " . sqlesc($modcomment);
$user_stats_cache['modcomment'] = $modcomment;
sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('inbox_new_'.$id);
$mc1->delete_value('inbox_new_sb_'.$id);
if ($curuser_cache) {
                   $mc1->begin_transaction('MyUser_'.$id);
                   $mc1->update_row(false, $curuser_cache);
                   $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
                }
    if ($user_cache) {
                   $mc1->begin_transaction('user'.$id);
                   $mc1->update_row(false, $user_cache);
                   $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
                }
    if ($stats_cache) {
                   $mc1->begin_transaction('userstats_'.$id);
                   $mc1->update_row(false, $stats_cache);
                   $mc1->commit_transaction($INSTALLER09['expires']['u_stats']);
                }
    if ($user_stats_cache) {
                   $mc1->begin_transaction('user_stats_'.$id);
                   $mc1->update_row(false, $user_stats_cache);
                   $mc1->commit_transaction($INSTALLER09['expires']['user_stats']);
                }
fclose ($fp);
header("Location: {$INSTALLER09['baseurl']}/paypal_success.php"); //=== location of your success page
}
else
$HTMLOUT .= stdmsg("Thanks for your support", "Please pm the sysops with the transaction details to recieve your status and gigs.");
echo $HTMLOUT;
die();
?>
