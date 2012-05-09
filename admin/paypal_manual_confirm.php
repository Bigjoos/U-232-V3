<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);


$lang = array_merge( $lang );

$HTMLOUT = $class = $letter = $amount = $count = $curuser_cache = $user_cache = $stats_cache = $user_stats_cache = '';

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
//=== do it all :D
$enable = $paypal_config['enable'];

if ($paypal_config['enable'] != 1)
stderr("Sorry", "Donations not accepted at the moment");

if (isset($_GET['doit'])) {

$id = (int)$_POST['select_this_user'];
if(!is_valid_id($id))
stderr("Error", "No user with that ID.");
if ($_POST['amount'] < 4)
stderr("Error", "No Amount Selected.");

$res = sql_query("SELECT * FROM users WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$user = mysqli_fetch_array($res) or stderr("Error", "No user with that ID!");
$modcomment = htmlsafechars($user['modcomment']);

//=== add donated amount  to user and to funds table /  Set donor status
if (isset($_POST['amount'])) {
$donated = 0 + $_POST['amount']; 	
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
$uploaded = $donated * $givegb; //=== 6442450944 = 6 GB for every 1£ donated || set to 1073741824 for 1 GB for every 1£ donated
$upadded = mksize($uploaded);
$total = $uploaded + $curuploaded;
$updateset[] = "uploaded = " . sqlesc($total);
$stats_cache['uploaded'] = $total;
$user_stats_cache['uploaded'] = $total;
//=== add invites
$curinvites = (int)$user['invites'];
$invites_added = $donated / 5 * $giveinvites;
$updateset[] = "invites = $curinvites + $invites_added";
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
$msg = sqlesc("Dear ".htmlsafechars($user['username'])."
:wave:
Thanks for your support to {$INSTALLER09['site_name']}!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards server upgrades.
As a donor, you are given $upadded bonus added to your uploaded amount, $invites_added new invites added, the status of VIP, as well as the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
{$INSTALLER09['site_name']} staff.
PS. Your donator status will last for {$dur}  and can be found on your user details page. It can only be seen by you.");

$modcomment = get_date( TIME_NOW, 'DATE', 1 ) . " - Donator status set for $dur -- $upadded bonus invites set to $invites_added, added by system.\n".$modcomment;
$updateset[] = "donoruntil = ".sqlesc($donoruntil_val);
$updateset[] = "donor = 'yes'";
$updateset[] = "vipclass_before = ".sqlesc($vipbefore);
$curuser_cache['donoruntil'] = $donoruntil_val;
$user_cache['donoruntil'] = $donoruntil_val;
$curuser_cache['donor'] = 'yes';
$user_cache['donor'] = 'yes';
$curuser_cache['vipclass_before'] = $vipbefore;
$user_cache['vipclass_before'] = $vipbefore;
} //=== end if donor no

elseif ($user['donor'] == 'yes') {
$donorlengthadd = $donated / 5;
$dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : ""); 
if ($user['donoruntil'] <= TIME_NOW) {
$donoruntil_val = TIME_NOW + $donorlengthadd * $givetime; //===> 1209600 = 2 weeks for 5$ --- 604800 = 1 week for 5£ donation
}
else {
$donoruntil_val = $donorlengthadd * $givetime; //===> 1209600 = 2 weeks for 5$ --- 604800 = 1 week for 5£ donation
}
$donorlengthadd = $donoruntil_val;
$subject = sqlesc("Thank You for Your Donation... Again!");
$msg = sqlesc("Dear " . htmlsafechars($user['username']) ."
:wave:
Thanks for your continued support to {$INSTALLER09['site_name']}!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
As a donor, you are given $upadded bonus added to your uploaded amount, $invites_added new invites added, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
{$INSTALLER09['site_name']} Staff

PS. Your donator status will last for an extra ".$dur." on top of your current donation status, and can be found on your user details page. It can only be seen by you.");

$modcomment = get_date( TIME_NOW, 'DATE', 1 ) . " - Donator status set for another {$dur} -- {$upadded} bonus added by system.\n".$modcomment;
sql_query("UPDATE users SET donoruntil = donoruntil + $donorlengthadd, vipclass_before = ".sqlesc($vipbefore)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('inbox_new_'.$id);
$mc1->delete_value('inbox_new_sb_'.$id);
$curuser_cache['donoruntil'] = ($user['donoruntil'] + $donorlengthadd);
$user_cache['donoruntil'] = ($user['donoruntil'] + $donorlengthadd);
$curuser_cache['vipclass_before'] = $vipbefore;
$user_cache['vipclass_before'] = $vipbefore;
} //=== end if adding to donor time...

$added = TIME_NOW;
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $id, $msg, $added)") or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('inbox_new_'.$id);
$mc1->delete_value('inbox_new_sb_'.$id);
if ($user['class'] < UC_UPLOADER) { //=== set this to the lowest class you don't want changed to VIP						  
$updateset[] = "class = '".UC_VIP."'";
$curuser_cache['class'] = UC_VIP;
$user_cache['class'] = UC_VIP;
$user_class = "VIP [ used to be ".get_user_class_name($user['class'])." ]";
}
else
$user_class =  get_user_class_name($user['class'])." [ no change ]";
//=== change title if blank	
if ($user['title'] == '')				  
$updateset[] = "title = 'ChangeMe'"; //=== change this lol
$curuser_cache['title'] = 'ChangeMe';
$user_cache['title'] = 'ChangeMe';
//=== Add ModComment to the update set...
$updateset[] = "modcomment = " . sqlesc($modcomment);
$user_stats_cache['modcomment'] = $modcomment;
sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$total_donated = $user["total_donated"] + $donated;
$curuploaded = mksize($curuploaded);
$totalinvites = $invites_added + $curinvites;

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

$HTMLOUT .= begin_main_frame();
$HTMLOUT .="
<br /><table width='80%' border='0' align='center'>
<tr><td align='center' valign='middle' class='colhead'><h1>Success!</h1></td></tr>
<tr><td align='center' valign='middle' class='one'><br /><b>Successfully entered donation on <b>".htmlsafechars($user['username'])."</b>!</b><br /><br />
<b><u>Changes to ".htmlsafechars($user['username'])."'s account:</u></b><br /><br /><table width='60%'><tr><td><b>Donor status set for:</b> {$dur}<br /><b>Class set to:</b> ".$user_class."<br />
<b>Upload Bonus:</b> $upadded [ added to $curuploaded ]<br />
<b>Invites set to:</b> $totalinvites [ used to be  ".(int)$user['invites']." ]<br />
<b>Current Donation:</b> $donated.0<br /><b>Total Donations:</b> $total_donated.0</td></tr></table><br /><br /><a class='altlink' href='staffpanel.php?tool=paypal_manual_confirm'><b>Add another ?</b></a> || go to <b><a class='altlink' href='{$INSTALLER09['baseurl']}/userdetails.php?id={$id}'>".htmlsafechars($user['username'])."'s</a></b> profile?<br /><br />";
$HTMLOUT .= end_frame();
$HTMLOUT .= end_main_frame();
echo stdhead('Success') . $HTMLOUT . stdfoot();
die();
}
//=== end do it all
$HTMLOUT .= begin_main_frame();
//=== search users
$search = (isset($_POST["search"]) ? htmlsafechars($_POST["search"]) : "");
$class = (isset($_GET['class']) ? (int)$_GET['class'] : '');
if ($class == '-' || !is_valid_id($class))
if ($search != '' || $class)
{
  $query = "username LIKE ".sqlesc("%$search%")." AND status='confirmed'";
	if ($search)
		  $q1 = "search=" . htmlsafechars($search);
}
else
{
  $letter = (isset($_GET["letter"]) ? htmlsafechars($_GET["letter"]) : "");
  if (strlen($letter) > 1)
    die;

  if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
    $letter = "";
  $query = "username LIKE ".sqlesc("$letter%")." AND status='confirmed'";
  $q1 = "letter=$letter";
}

$HTMLOUT .="<div align='center'>
<br />
<table width='80%' border='0' align='center'>
<tr><td align='center' valign='middle' class='colhead'><h1>{$INSTALLER09['site_name']} manual donation confirm page</h1></td></tr>
<tr><td align='center' valign='middle' class='one'><br /><br /><h1>Find User</h1>
<form method='post' action='staffpanel.php?tool=paypal_manual_confirm&search=1'> 
Search: 
<input type='text' size='30' name='search' />
<input class='button' type='submit' value='Okay' /></form><br /><br />\n";

for ($i = 97; $i < 123; ++$i)
{
	$l = chr($i);
	$L = chr($i - 32);
	if ($l == $letter)
    $HTMLOUT .="<b>$L</b>\n";
	else
   $HTMLOUT .= "<a href='staffpanel.php?tool=paypal_manual_confirm&amp;searchl=1&amp;letter=$l'><b>$L</b></a>\n";
}


if (isset($_GET['search']) || isset($_GET['searchl'])){
$page = (isset($_GET['page']) ? $_GET['page'] : '');
$perpage = 50;
$browsemenu='';
$pagemenu='';
$res = sql_query("SELECT COUNT(id) FROM users WHERE $query") or sqlerr(__FILE__, __LINE__);
$arr = mysqli_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "<b>$i</b>\n";
  else
    $pagemenu .= "<a href='?tool=paypal_manual_confirm&searchl=1&amp;$q1&amp;page=$i'><b>$i</b></a>\n";

if ($page == 1)
  $browsemenu .= "<b>&lt;&lt; Prev</b>";
else
  $browsemenu .= "<a href='?tool=paypal_manual_confirm&searchl=1&amp;$q1&amp;page=" . ($page - 1) . "'><b>&lt;&lt; Prev</b></a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "<b>Next &gt;&gt;</b>";
else
  $browsemenu .= "<a href='?tool=paypal_manual_confirm&searchl=1&amp;$q1&amp;page=" . ($page + 1) . "'><b>Next &gt;&gt;</b></a>";

$offset = ($page * $perpage) - $perpage;

$res = sql_query("SELECT username, class, donated, donor, warned, enabled, leechwarn, chatpost, pirate, king, id FROM users WHERE $query ORDER BY username LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
$num = mysqli_num_rows($res);
if (isset($_GET['search'])  && $num|| isset($_GET['searchl']) && $num)
$HTMLOUT .="<form action='staffpanel.php?tool=paypal_manual_confirm&amp;doit=1' method='post'>
<table border='1' cellspacing='0' cellpadding='5'>
<tr><td class='colhead' align='left'>Select this user</td>
<td class='colhead' align='left'>User name</td>
<td class='colhead' align='left'>Class</td></tr>\n";
for ($i = 0; $i < $num; ++$i)
{
$arr = mysqli_fetch_assoc($res);
//=======change colors
$count= (++$count)%2;
$class = ($count==0?'one':'two');
$HTMLOUT .="<tr>
<td align='center' class='$class'>
<input type='radio' name='select_this_user' value='".(int)$arr['id']."' /></td>
<td align='left' class='$class'><a class='altlink' href='userdetails.php?id=".(int)$arr['id']."'><b>".format_username($arr)."</b></a></td>
<td align='left' class='$class'>" . get_user_class_name($arr["class"]) . "</td></tr>\n";
}
if (isset($_GET['search'])  && $num || isset($_GET['searchl']) && $num)
$HTMLOUT .="</table>\n";
else
$HTMLOUT .="<h1>nothing found!</h1>";
if (isset($_GET['search'])  && $num || isset($_GET['searchl']) && $num)
$HTMLOUT .="<p>$pagemenu<br />$browsemenu</p>";
}
//=== end search users
//======= regular page start
$amount .= "<select name='amount'><option>-- select --</option>";
$i = "5";
while($i <= 200){
$amount .= "<option value='".$i."'>Donation of &#163;".$i.".00 GBP</option>";
//$i = $i + 5;
$i = ($i < 100 ? $i = $i + 5 : $i = $i + 10);
}
$amount .= "</select>";
$HTMLOUT .="<br /><br /><b>Select donation amount:</b>{$amount}<br /><br /><br />
<b>Click User Name from the above search, and select the donated amount,<br /> then click the <b>Do it!</b> button to enter the donation and award the goodies!</b><br />
<br /><br /><input type='submit' class='button' value='Do it!' />
<br /><br /></form></td></tr></table></div>";

$HTMLOUT .= end_main_frame();
echo stdhead('Paypal manual confirm') . $HTMLOUT . stdfoot();
die();
?>
