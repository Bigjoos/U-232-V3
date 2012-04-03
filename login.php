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
require_once(CLASS_DIR.'page_verify.php');
dbconn();
get_template();


ini_set('session.use_trans_sid', '0');
$stdhead = array(/** include js **/'js' => array('jquery','jquery.simpleCaptcha-0.2'));
$lang = array_merge( load_language('global'), load_language('login') );
$newpage = new page_verify(); 
$newpage->create('takelogin');
  $left='';
  //== 09 failed logins
	function left ()
	{
	global $INSTALLER09;
	$total = 0;
	$ip = sqlesc(getip());
	$fail = sql_query("SELECT SUM(attempts) FROM failedlogins WHERE ip=".sqlesc($ip)) or sqlerr(__FILE__, __LINE__);
	list($total) = mysqli_fetch_row($fail);
	$left = $INSTALLER09['failedlogins'] - $total;
	if ($left <= 2)
	$left = "<span style='color:red'>{$left}</span>";
	else
	$left = "<span style='color:green'>{$left}</span>";
	return $left;
	}
	//== End Failed logins

    $HTMLOUT = '';
    
    unset($returnto);
    if (!empty($_GET["returnto"])) {
      $returnto = htmlsafechars($_GET["returnto"]);
      if (!isset($_GET["nowarn"])) 
      {
        $HTMLOUT .= "<h1>{$lang['login_not_logged_in']}</h1>\n";
        $HTMLOUT .= "{$lang['login_error']}";
        $HTMLOUT .="<h4>{$lang['login_cookies']}</h4>                                              
        <h4>{$lang['login_cookies1']}</h4>  
        <h4>
        <b>[{$INSTALLER09['failedlogins']}]</b> {$lang['login_failed']}<br />{$lang['login_failed_1']} <b> " .left()." </b> {$lang['login_failed_2']}</h4>";
      }
    }

    


    $got_ssl = isset($_SERVER['HTTPS']) && (bool)$_SERVER['HTTPS'] == true ? true : false;
    //== click X by Retro
    $value = array('...','...','...','...','...','...');
    $value[rand(1,count($value)-1)] = 'X';
    $HTMLOUT .= "<script type='text/javascript'>
	  /*<![CDATA[*/
	  $(document).ready(function () {
	  $('#captchalogin').simpleCaptcha();
    });
    /*]]>*/
    </script>
    <form method='post' action='takelogin.php'>
    <table border='0' cellpadding='5'>
    <tr>
    <td class='rowhead'>{$lang['login_username']}</td><td align='left'><input type='text' size='40' name='username' /></td></tr>
    <tr><td class='rowhead'>{$lang['login_password']}</td><td align='left'><input type='password' size='40' name='password' /></td></tr>	
    <tr><td class='rowhead'>{$lang['login_use_ssl']}</td>
    <td>
    <label for='ssl'>{$lang['login_ssl1']}<input type='checkbox' name='use_ssl' ".($got_ssl ? "checked='checked'" : "disabled='disabled' title='SSL connection not available'")." value='1' id='ssl'/></label><br/>
    <label for='ssl2'>{$lang['login_ssl2']}<input type='checkbox' name='perm_ssl' ".($got_ssl ? "" : "disabled='disabled' title='SSL connection not available'")." value='1' id='ssl2'/></label>
    </td>
    </tr>
    <!--<tr><td>{$lang['login_duration']}</td><td align='left'><input type='checkbox' name='logout' value='yes' />{$lang['login_15mins']}</td></tr>-->
    <tr><td align='left' class='rowhead' colspan='2' id='captchalogin'></td></tr>
    <tr><td align='center' colspan='2'>{$lang['login_click']}<strong>{$lang['login_x']}</strong></td></tr>
    <tr><td colspan='2' align='center'>";
    for ($i=0; $i < count($value); $i++) {
    $HTMLOUT .= "<input name=\"submitme\" type=\"submit\" value=\"{$value[$i]}\" class=\"btn\" />";
    }
    if (isset($returnto))
    $HTMLOUT .= "<input type='hidden' name='returnto' value='" . htmlsafechars($returnto) . "' />\n";
    $HTMLOUT .= "</td></tr></table>";
    $HTMLOUT .= "</form>
    {$lang['login_signup']}{$lang['login_forgot']}";
     
echo stdhead("{$lang['login_login_btn']}", true, $stdhead) . $HTMLOUT . stdfoot();

