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
require_once(INCL_DIR.'password_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
global $CURUSER;
if(!$CURUSER){
get_template();
}
dbconn();

    if(!$INSTALLER09['openreg'])
    stderr('Sorry', 'Invite only - Signups are closed presently if you have an invite code click <a href="'.$INSTALLER09['baseurl'].'/invite_signup.php"><b> Here</b></a>');
    
    $res = sql_query("SELECT COUNT(id) FROM users") or sqlerr(__FILE__, __LINE__);
    $arr = mysqli_fetch_row($res);
    
    if ($arr[0] >= $INSTALLER09['maxusers'])
    stderr($lang['takesignup_error'], $lang['takesignup_limit']);

    $lang = array_merge( load_language('global'), load_language('takesignup') );
    $newpage = new page_verify(); 
    $newpage->check('tesu');

    foreach( array('wantusername','wantpassword','passagain','email','captchaSelection','submitme','passhint','hintanswer') as $x )
    {
      if( !isset($_POST[ $x ]) )
      {
        stderr($lang['takesignup_user_error'], $lang['takesignup_form_data']);
      }
      
      ${$x} = $_POST[ $x ];
    }
    
    if ($submitme != 'X')
    stderr('Ha Ha', 'You Missed, You plonker !');
  
    if(empty($captchaSelection) || $_SESSION['simpleCaptchaAnswer'] != $captchaSelection){
        header('Location: signup.php');
        exit();
    }

    function validusername($username)
    {
    global $lang;
    
    if ($username == "")
      return false;
    
    $namelength = strlen($username);
    
    if( ($namelength < 3) OR ($namelength > 32) )
    {
      stderr($lang['takesignup_user_error'], $lang['takesignup_username_length']);
    }
    // The following characters are allowed in user names
    $allowedchars = $lang['takesignup_allowed_chars'];
    
    for ($i = 0; $i < $namelength; ++$i)
    {
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;
    }
    
    return true;
    }


    if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($passhint) || empty($hintanswer))
    stderr($lang['takesignup_user_error'], $lang['takesignup_blank']);

    if(!blacklist($wantusername))
    stderr($lang['takesignup_user_error'],sprintf($lang['takesignup_badusername'],htmlspecialchars($wantusername)));

    if ($wantpassword != $passagain)
      stderr($lang['takesignup_user_error'], $lang['takesignup_nomatch']);

    if (strlen($wantpassword) < 6)
      stderr($lang['takesignup_user_error'], $lang['takesignup_pass_short']);

    if (strlen($wantpassword) > 40)
      stderr($lang['takesignup_user_error'], $lang['takesignup_pass_long']);

    if ($wantpassword == $wantusername)
      stderr($lang['takesignup_user_error'], $lang['takesignup_same']);

    if (!validemail($email))
      stderr($lang['takesignup_user_error'], $lang['takesignup_validemail']);

    if (!validusername($wantusername))
      stderr($lang['takesignup_user_error'], $lang['takesignup_invalidname']);

    if (!(isset($_POST['day']) || isset($_POST['month']) || isset($_POST['year'])))
	  stderr('Error','You have to fill in your birthday.');

    if (checkdate($_POST['month'], $_POST['day'], $_POST['year']))
	  $birthday = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
    else
	  stderr('Error','You have to fill in your birthday correctly.');

    if ((date('Y') - $_POST['year']) < 17)
     stderr('Error','You must be at least 18 years old to register.');
    
    // make sure user agrees to everything...
    if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
    stderr($lang['takesignup_failed'], $lang['takesignup_qualify']);

    // check if email addy is already in use
    $a = (@mysqli_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE email='$email'"))) or sqlerr(__FILE__, __LINE__);
    if ($a[0] != 0)
    stderr($lang['takesignup_user_error'], $lang['takesignup_email_used']);
    /*
    //=== check if ip addy is already in use
    $c = (@mysqli_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE ip='" . $_SERVER['REMOTE_ADDR'] . "'"))) or sqlerr(__FILE__, __LINE__);
    if ($a[0] != 0)
    stderr("Error", "The ip " . $_SERVER['REMOTE_ADDR'] . " is already in use. We only allow one account per ip address.");
    */
    // TIMEZONE STUFF
    if(isset($_POST["user_timezone"]) && preg_match('#^\-?\d{1,2}(?:\.\d{1,2})?$#', $_POST['user_timezone']))
    {
    $time_offset = sqlesc($_POST['user_timezone']);
    }
    else
    { 
    $time_offset = isset($INSTALLER09['time_offset']) ? sqlesc($INSTALLER09['time_offset']) : '0'; }
    // have a stab at getting dst parameter?
    $dst_in_use = localtime(TIME_NOW + ($time_offset * 3600), true);
    // TIMEZONE STUFF END
    
    $secret = mksecret();
    $wantpasshash = make_passhash( $secret, md5($wantpassword) );
    $editsecret = ( !$arr[0] ? "" : make_passhash_login_key() );
    $wanthintanswer = md5($hintanswer);
    
    //$ip = getip();
    
    $ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, birthday, passhint, hintanswer, email, status, ". (!$arr[0]?"class, ":"") ."added, last_access, time_offset, dst_in_use) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $birthday, $passhint, $wanthintanswer, $email, (!$arr[0]?'confirmed':'pending')))).
		", ". (!$arr[0]?UC_SYSOP.", ":""). "". TIME_NOW ." ,". TIME_NOW ." , $time_offset, {$dst_in_use['tm_isdst']})");
    $mc1->delete_value('birthdayusers');
    $message = "Welcome New {$INSTALLER09['site_name']} Member : - " . htmlspecialchars($wantusername) . "";
   
    if (!$ret) 
    {
      if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 1062)
        stderr($lang['takesignup_user_error'], $lang['takesignup_user_exists']);
      stderr($lang['takesignup_user_error'], $lang['takesignup_fatal_error']);
    }

    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    
    //==New member pm
    $added = TIME_NOW;
    $subject = sqlesc("Welcome");
    $msg = sqlesc("Hey there {$wantusername} ! Welcome to {$INSTALLER09['site_name']} ! :clap2: \n\n Please ensure your connectable before downloading or uploading any torrents\n - If your unsure then please use the forum and Faq or pm admin onsite.\n\ncheers {$INSTALLER09['site_name']} staff.\n");
    sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $id, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    
    //==End new member pm
    
    $latestuser_cache['id'] =  (int)$id;
    $latestuser_cache['username'] = $wantusername;
    /** OOP **/
    $mc1->cache_value('latestuser', $latestuser_cache, $INSTALLER09['expires']['latestuser']);
    write_log("User account $id ($wantusername) was created");

    $psecret = $editsecret; 
    if($INSTALLER09['autoshout_on'] == 1){
    autoshout($message);
    $mc1->delete_value('shoutbox_');
    }
    $body = str_replace(array('<#SITENAME#>', '<#USEREMAIL#>', '<#IP_ADDRESS#>', '<#REG_LINK#>'),
                        array($INSTALLER09['site_name'], $email, $_SERVER['REMOTE_ADDR'], "{$INSTALLER09['baseurl']}/confirm.php?id=$id&secret=$psecret"),
                        $lang['takesignup_email_body']);
   
    if($arr[0])
    mail($email, "{$INSTALLER09['site_name']} {$lang['takesignup_confirm']}", $body, "{$lang['takesignup_from']} {$INSTALLER09['site_email']}");
    else
    logincookie($id, $wantpasshash);

header("Refresh: 0; url=ok.php?type=". (!$arr[0]?"sysop":("signup&email=" . urlencode($email))));
?>
