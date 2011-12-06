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
require_once(INCL_DIR.'html_functions.php');
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global'));

$HTMLOUT = $amount ="";

$HTMLOUT .= begin_main_frame();
//get the config from db
$pconf = sql_query('SELECT name, value FROM paypal_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysqli_fetch_assoc($pconf))
  $paypal_config[$ac['name']] = $ac['value'];

$email = $paypal_config['email'];
$enable = $paypal_config['enable'];

if ($paypal_config['enable'] != 1)
stderr("Sorry", "Donations not accepted at the moment");
$nick = ($CURUSER ? $CURUSER["username"] : ("Guest" . rand(1000, 9999)));

///======= note! you may have to turn on IPN at paypal to get your user back to the site... notify_url does not always work

$amount .= "<select name=\"amount\"><option value=\"0\">Please select donation amount</option>";
$i = "5";
while($i <= 200){
$amount .= "<option value=\"".$i."\">Donation of &#163;".$i.".00 GBP</option>";
//$i = $i + 5;
$i = ($i < 100 ? $i = $i + 5 : $i = $i + 10);
}
$amount .= "</select>";

$HTMLOUT .="<script type='text/javascript'>
function popup(URL) {
day = new Date();
id = day.getTime();
eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=380,left = 340,top = 280');\");
}
</script>";

$HTMLOUT .="<table width='80%' border='0' align='center'>
	<tr><td align='center' valign='middle' class='colhead'><h1>{$INSTALLER09['site_name']}</h1></td></tr>
	<tr><td align='center' valign='middle' class='embedded'>
	<br /><br />
	<p align='center'><b>Select Donation amount, and click the PayPal button to donate !</b><br />
	</p>
<!-- form goes here -->
<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
<input type='hidden' name='cmd' value='_xclick' />
<input type='hidden' name='business' value='".$email."' /> <!-- change to your paypal email -->
<input type='hidden' name='item_name' value='( {$nick} donation )' />
<input type='hidden' name='item_number' value='1' />
<input type='hidden' name='no_note' value='1' />
<p align='center'>
<b>Donate:</b>{$amount}<br /><br />
<input type='hidden' name='currency_code' value='GBP' /> <!--Use the right currency//Might fail if the user is using diff to u-->
<input type='hidden' name='tax' value='0' />
<input type='hidden' name='no_shipping' value='1' />
<input type='hidden' name='custom' value='".$CURUSER['id']."' />
<input type='hidden' name='notify_url' value='{$INSTALLER09['baseurl']}/scene.php' /> <!-- link to your paypal.php script, change to another name ;)  -->
<input type='hidden' name='return' value='{$INSTALLER09['baseurl']}/scene.php' /> <!-- link to your paypal.php script, change to another name ;)  -->
<input type='image' align='middle' src='https://www.paypal.com/en_US/i/btn/x-click-but04.gif' name='submit' alt='Make payments with PayPal - its fast, free and secure!' />
</p>
<!-- form ends here -->
</form>
<br /><p><b><u>The donation process is fully automated</u></b>:<br />
However, once you have completed your donation at the PayPal site, you <b>MUST</b> click the <b>return to merchant button,</b></p>
<p align='center'><u><b>Please note</b></u> - all donations go towards running the site. Remember, we run this site out of love for the community on a volenteer basis. The actual costs include:
<br />
<br />
Domain Name registration. [yearly]
<br />
Server . [ram - cpu - HD etc]
<br />
Site Seedbox
<br />	
<b>Thank you for your support!</b></p>
<p align='center'>Processed through {$INSTALLER09['site_name']}'s Secure & Reliable Paypal Payment Portal<br />
<img src='{$INSTALLER09['pic_base_url']}paypal/visa.gif' alt='visa' /> <img src='{$INSTALLER09['pic_base_url']}paypal/mastercard.gif' alt='mastercard' /> <img src='{$INSTALLER09['pic_base_url']}paypal/amex.gif' alt='amex' /> <img src='{$INSTALLER09['pic_base_url']}paypal/discover.gif' alt='discover' /> <img src='{$INSTALLER09['pic_base_url']}paypal/echeck.gif' alt='echeck' /> or  <img src='{$INSTALLER09['pic_base_url']}paypal/paypal.gif' alt='paypal' /><br />
A PayPal account is not required for Credit Card payments.  [ <a href=\"javascript:popup('popup_paypal_cc_help.php')\">more info</a> ]<br /><br /></p>
</td></tr></table>";

$HTMLOUT .= end_main_frame();

echo stdhead('Donate') . $HTMLOUT . stdfoot();
die();
?>
