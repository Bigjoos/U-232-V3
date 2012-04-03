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

//get the config from db
$pconf = sql_query('SELECT name, value FROM paypal_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysqli_fetch_assoc($pconf))
  $paypal_config[$ac['name']] = $ac['value'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    //can't be 0
    foreach(array('gb'>0,'weeks'>0,'invites'=>0,'enable'=>0) as $key=>$type) {
      if(isset($_POST[$key]) && ($type == 0 && $_POST[$key] == 0 || $type == 0 && count($_POST[$key]) == 0))
      stderr('Err','You forgot to fill some data');
    }
    foreach($paypal_config as $c_name=>$c_value)
    if(isset($_POST[$c_name]) && $_POST[$c_name] != $c_value)
      $update[] = '('.sqlesc($c_name).','.sqlesc(is_array($_POST[$c_name]) ? join('|',$_POST[$c_name]) : $_POST[$c_name]).')';

    if(sql_query('INSERT INTO paypal_config(name,value) VALUES '.join(',',$update).' ON DUPLICATE KEY update value=values(value)'))
      stderr('Success','Paypal configuration was saved! Click <a href=\'staffpanel.php?tool=paypal_settings\'>here to get back</a>');
      else
      stderr('Error','There was an error while executing the update query. Mysql error: '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
  exit;
}


 //var_dump($_POST);

 $HTMLOUT .="<h3>Paypal Settings</h3>
<form action='staffpanel.php?tool=paypal_settings' method='post'>
<table width='100%' border='1' cellpadding='5' cellspacing='0' >
<tr><td width='50%' class='table' align='left'>Amount of GB to add Per &#163;:</td><td class='table' align='left'><input type='text' name='gb' size='2' value='".htmlsafechars($paypal_config['gb'])."' /></td></tr>
<tr><td width='50%' class='table' align='left'>How many weeks per &#163;5:</td><td class='table' align='left'><input type='text' name='weeks' size='2' value='".htmlsafechars($paypal_config['weeks'])."' /></td></tr>
<tr><td width='50%' class='table' align='left'>How many invites per &#163;5:</td><td class='table' align='left'><input type='text' name='invites' size='2' value='".htmlsafechars($paypal_config['invites'])."' /></td></tr>
<tr><td width='50%' class='table' align='left'>Your Paypal Email:</td><td class='table' align='left'><input type='text' name='email' size='25' value='".htmlsafechars($paypal_config['email'])."' /></td></tr>
<tr><td width='50%' class='table' align='left'>Paypal Donations Enabled:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='enable' value='1' ".($paypal_config['enable'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='enable' value='0' ".(!$paypal_config['enable'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td colspan='2' class='table' align='center'><input type='submit' value='Apply changes' /></td></tr>
</table></form>";

echo stdhead('PayPal Settings') . $HTMLOUT . stdfoot();
?>
