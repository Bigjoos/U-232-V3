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
dbconn();

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('email-gateway') );
    
    $id = 0 + $_GET["id"];
    
    if ( !is_valid_id($id) )
      stderr("{$lang['email_error']}", "{$lang['email_bad_id']}");

    $res = sql_query("SELECT username, class, email FROM users WHERE id=$id");
    $arr = mysqli_fetch_assoc($res) or stderr("{$lang['email_error']}", "{$lang['email_no_user']}");
    $username = $arr["username"];
    
    if ($arr["class"] < UC_STAFF)
      stderr("{$lang['email_error']}", "{$lang['email_email_staff']}");

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $to = $arr["email"];

      $from = substr(trim($_POST["from"]), 0, 80);
      if ($from == "") $from = "{$lang['email_anon']}";

      $from_email = substr(trim($_POST["from_email"]), 0, 80);
      
      if ($from_email == "") $from_email = "{$INSTALLER09['site_email']}";
      if (!strpos($from_email, "@")) stderr("{$lang['email_error']}", "{$lang['email_invalid']}");

      $from = "$from <$from_email>";

      $subject = substr(trim($_POST["subject"]), 0, 80);
      if ($subject == "") $subject = "(No subject)";
      $subject = "Fw: $subject";

      $message = trim($_POST["message"]);
      if ($message == "") stderr("{$lang['email_error']}", "{$lang['email_no_text']}");

      $message = "Message submitted from {$_SERVER['REMOTE_ADDR']} at " . get_date(TIME_NOW, 'DATE',0,1) . ".\n" .
        "{$lang['email_note']}\n" .
        "---------------------------------------------------------------------\n\n" .
        $message . "\n\n" .
        "---------------------------------------------------------------------\n".
        "{$INSTALLER09['site_name']}{$lang['email_gateway']}\n";

      $success = mail($to, $subject, $message, "{$lang['email_from']}{$INSTALLER09['site_email']}");

      if ($success)
        stderr("{$lang['email_success']}", "{$lang['email_queued']}");
      else
        stderr("{$lang['email_error']}", "{$lang['email_failed']}");
    }

    $HTMLOUT = '';

    $HTMLOUT .= "<table border='0' class='main' cellspacing='0' cellpadding='0'>
    <tr>
      <td class='embedded'><img src='pic/email.gif' alt='' /></td>
      <td class='embedded' style='padding-left: 10px'><font size='3'><b>{$lang['email_send']}{$username}</b></font></td>
    </tr>
    </table>
    <form method='post' action='email-gateway.php?id=$id'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='rowhead'>{$lang['email_your_name']}</td><td><input type='text' name='from' size='80' /></td></tr>
    <tr><td class='rowhead'>{$lang['email_your_email']}</td><td><input type='text' name='from_email' size='80' /></td></tr>
    <tr><td class='rowhead'>{$lang['email_subject']}</td><td><input type='text' name='subject' size='80' /></td></tr>
    <tr><td class='rowhead'>{$lang['email_message']}</td><td><textarea name='message' cols='80' rows='20'></textarea></td></tr>
    <tr><td colspan='2' align='center'><input type='submit' value='{$lang['email_send']}' class='btn' /></td></tr>
    </table>
    </form>
    <p>
    <font class='small'><b>{$lang['email_note_ip']}</b>{$lang['email_ip']}<br />
    {$lang['email_valid']}</font>
    </p>";

///////////////////////// HTML OUTPUT ////////////////////
    echo stdhead("{$lang['email_gateway']}") . $HTMLOUT . stdfoot(); 
?>
