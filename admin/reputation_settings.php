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

require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_MAX);

	$lang = array_merge( $lang );
	
$rep_set_cache = "./cache/rep_settings_cache.php";

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
	{
	unset($_POST['submit']);
	//print_r($_POST);
	rep_cache();
	exit;
	}
	
/////////////////////////////
//	cache rep function
/////////////////////////////
function rep_cache()
	{
		
		global $rep_set_cache, $INSTALLER09;
		
		$rep_out = "<"."?php\n\n\$GVARS = array(\n";
		
		foreach( $_POST as $k => $v)
		{
			$rep_out .= ($k == 'rep_undefined') ? "\t'{$k}' => '".htmlsafechars($v, ENT_QUOTES)."',\n" : "\t'{$k}' => ".intval($v).",\n";
		}
		
		$rep_out .= "\t'g_rep_negative' => TRUE,\n";
		$rep_out .=	"\t'g_rep_seeown' => TRUE,\n";
		$rep_out .= "\t'g_rep_use' => \$CURUSER['class'] > UC_USER ? TRUE : FALSE\n";
		$rep_out .= "\n);\n\n?".">";
		
		if( file_exists( $rep_set_cache ) && is_writable( pathinfo($rep_set_cache, PATHINFO_DIRNAME) ) )
		{
			$filenum = fopen ( $rep_set_cache, 'w' );
			ftruncate( $filenum, 0 );
			fwrite( $filenum, $rep_out );
			fclose( $filenum );
			//echo '<pre>'.$rep_out.'</pre>';exit;
		}
		
		redirect('staffpanel.php?tool=reputation_settings', 'Reputation Settings Have Been Updated!', 3);
	}
	
		
function get_cache_array() 
	{
		return array(	'rep_is_online' => 1,
						'rep_adminpower' => 5,
						'rep_minpost' => 50,
						'rep_default' => 10,
						'rep_userrates' => 5,
						'rep_rdpower' => 365,
						'rep_pcpower' => 1000,
						'rep_kppower' => 100,
						'rep_minrep' => 10,
						'rep_minpost' => 50,
						'rep_maxperday' => 10,
						'rep_repeat' => 20,
						'rep_undefined' => 'is off the scale',
						/*'g_rep_negative' => TRUE,
						'g_rep_seeown' => TRUE,
						'g_rep_use' => $CURUSER['class'] > UC_USER ? TRUE : FALSE*/
					);
	}

	
	if ( ! file_exists( $rep_set_cache ) )
	{
		$GVARS = get_cache_array();
	}
	else
	{
		require_once $rep_set_cache;
		
		if( ! is_array($GVARS) || ( count($GVARS) < 15 ) )
		{	
			$GVARS = get_cache_array();
		}
	}
	




$HTMLOUT = '<div>
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				   <tr>
					<td style="font-size: 12px; vertical-align: middle; font-weight: bold; color: rgb(0, 0, 0);" align="center">Reputation System Settings</td></tr>

					<tr><td>This section allows you to configure the User Reputation system.</td>
								 </tr>
								 </table>
</div>
<br />
<div style="border: 1px solid rgb(0, 0, 0); padding: 5px;">

	<form action="staffpanel.php?tool=reputation_settings" name="repoptions" method="post">

				<div>Reputation On/Off</div>
					<div style="padding: 5px; background-color: rgb(30,30, 30);">
							<div style="border: 1px solid rgb(0, 0, 0);">
							
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Enable User Reputation system?</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgray;">Set this option to \'Yes\' if you want to enable the User Reputation system.</div></td>

							 <td width="23%"><div style="width: auto;" align="left"><#rep_is_online#></div></td>
							 </tr>
				  </table>
				  </div></div>

				  <div>Default Reputation Level</div>
				 <div style="padding: 5px; background-color: rgb(30, 30, 30);">
						<div>
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Default Reputation</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">What reputation level shall new users receive upon registration? Make sure that you have a reputation level that is at least equal to or less than this value.</div></td>
							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_default" value="<#rep_default#>" size="30" type="text"></div></td>
							 </tr>
				  </table>

				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Default Reputation Phrase</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">If you have any user gain a reputation that exceeds your lowest negative level, then this phrase will be used for them. If you do not wish to use this phrase, make sure you set a negative reputation that is larger than the largest score (negative) that a user on your forum has.</div></td>
							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_undefined" value="<#rep_undefined#>" size="30" type="text"></div></td>
							 </tr>
				  </table>

					<table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Number of Reputation Ratings to Display</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">Controls how many ratings to display in the user&#39;s profile (userdeatails).</div></td>
							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_userrates" value="<#rep_userrates#>" size="30" type="text"></div></td>
							 </tr>

				  </table>
				  </div></div>

				  <div>Reputation Powers</div>
				 <div style="padding: 5px; background-color: rgb(30, 30, 30);">
							<div style="border: 1px solid rgb(0, 0, 0);">
							
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Administrator&#39;s Reputation Power</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">How many reputation points does an administrator give or take away with each click?<br />Set to 0 to have administrators follow the same rules as everyone else.</div></td>
							 <td class="tablerow2" width="20%"><div style="width: auto;" align="left"><input name="rep_adminpower" value="<#rep_adminpower#>" size="30" type="text"></div></td>

							 </tr>
				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Register Date Factor</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">For every X number of days, users gain 1 point of reputation-altering power.</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_rdpower" value="<#rep_rdpower#>" size="30" type="text">

</div></td>
							 </tr>

				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Post Count Factor</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">For every X number of posts, users gain 1 point of reputation-altering power.</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_pcpower" value="<#rep_pcpower#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Reputation Point Factor</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">For every X points of reputation, users gain 1 point of reputation-altering power.</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_kppower" value="<#rep_kppower#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  </div></div>

				  <div>User Reputation Settings</div>
				  <div style="padding: 5px; background-color: rgb(30, 30, 30);">
						<div>
						
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Minimum Post Count</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">How many posts must a user have before his reputation hits count on others?</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_minpost" value="<#rep_minpost#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Minimum Reputation Count</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">How much reputation must a user have before his reputation hits count on others?</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_minrep" value="<#rep_minrep#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Daily Reputation Clicks Limit</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">How many reputation clicks can a user give over each 24 hour period? Administrators are exempt from this limit.</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_maxperday" value="<#rep_maxperday#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  
				  <table width="100%" border="0" cellpadding="5" cellspacing="0">
							 <tr>
							 <td width="70%"><b>Reputation User Spread</b><div><hr style="color:#A83838;" size="1" /></div><div style="color: lightgrey;">How many different users must you give reputation to before you can hit the same person again? Administrators are exempt from this limit.</div></td>

							 <td width="20%"><div style="width: auto;" align="left"><input name="rep_repeat" value="<#rep_repeat#>" size="30" type="text"></div></td>
							 </tr>
				  </table>
				  </div></div>

<input type="submit" name="submit" value="Submit" class="btn" tabindex="2" accesskey="s" />
</form>
</div>';


$HTMLOUT = preg_replace_callback( "|<#(.*?)#>|", "template_out", $HTMLOUT);



echo stdhead("Reputation Settings") . $HTMLOUT . stdfoot();






function template_out($matches)
	{
	  global $GVARS, $INSTALLER09;
	  
	  if ( $matches[1] == 'rep_is_online' )
	  {
	  return 'Yes &nbsp; <input name="rep_is_online" value="1" '.($GVARS['rep_is_online'] == 1 ? 'checked="checked"' : "").' type="radio">&nbsp;&nbsp;&nbsp;<input name="rep_is_online" value="0" '.($GVARS['rep_is_online'] == 1 ? "" : 'checked="checked"').' type="radio"> &nbsp; No';
	  }
	  else
	  {
	  return $GVARS[ $matches[1] ];
	  }
	}


function redirect($url, $text, $time=2)
	{
		global $INSTALLER09;
		
		$page_title  = "Admin Rep Redirection";
		$page_detail = "<em>Redirecting...</em>";
		
		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='refresh' content=\"{$time}; url={$INSTALLER09['baseurl']}/{$url}\" />
		<title>Block Settings</title>
    <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
    </head>
    <body>
						    <div>
							<div>Redirecting</div>
							<div style='padding:8px'>
							 <div style='font-size:12px'>$text
							 <br />
							 <br />
							 <center><a href='{$INSTALLER09['baseurl']}/{$url}'>Click here if not redirected...</a></center>
							 </div>
							</div>
						   </div></body></html>";
		
		echo $html;
		exit;
	}         
            

?>
