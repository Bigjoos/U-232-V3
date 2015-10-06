<?php
/**
 |----------------------------------------------------------------------------------------------|
 |   https://github.com/Bigjoos/                							   					|
 |----------------------------------------------------------------------------------------------|
 |   Licence Info: GPL																			|
 |----------------------------------------------------------------------------------------------|
 |   Copyright (C) 2010 U-232 V3																|
 |----------------------------------------------------------------------------------------------|
 |   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.						|
 |----------------------------------------------------------------------------------------------|
 |   Project Leaders: Mindless,putyn.															|
 |----------------------------------------------------------------------------------------------|
 **/
//==Template system by Terranova
function stdhead($title = "", $msgalert = true, $stdhead = false)
{
    global $CURUSER, $INSTALLER09, $lang, $free, $_NO_COMPRESS, $query_stat, $querytime, $mc1, $BLOCKS, $CURBLOCK, $mood;
    if (!$INSTALLER09['site_online']) die("Site is down for maintenance, please check back again later... thanks<br />");
    if ($title == "") $title = $INSTALLER09['site_name'] . (isset($_GET['tbv']) ? " (" . TBVERSION . ")" : '');
    else $title = $INSTALLER09['site_name'] . (isset($_GET['tbv']) ? " (" . TBVERSION . ")" : '') . " :: " . htmlsafechars($title);
    if ($CURUSER) {
        $INSTALLER09['stylesheet'] = isset($CURUSER['stylesheet']) ? "{$CURUSER['stylesheet']}.css" : $INSTALLER09['stylesheet'];
        $INSTALLER09['categorie_icon'] = isset($CURUSER['categorie_icon']) ? "{$CURUSER['categorie_icon']}" : $INSTALLER09['categorie_icon'];
        $INSTALLER09['language'] = isset($CURUSER['language']) ? "{$CURUSER['language']}" : $INSTALLER09['language'];
    }
    /** ZZZZZZZZZZZZZZZZZZZZZZZZZZip it! */
     if (!isset($_NO_COMPRESS))
     if (!ob_start('ob_gzhandler'))
     ob_start();
    //== Include js files needed only for the page being used by pdq
    $js_incl = '';
    $js_incl.= '<!-- javascript goes here or in footer -->';
    if (!empty($stdhead['js'])) {
        foreach ($stdhead['js'] as $JS) $js_incl.= "<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/" . $JS . ".js'></script>";
    }
    //== Include css files needed only for the page being used by pdq
    $css_incl = '';
    $css_incl.= '<!-- css goes here -->';
    $salty = md5("Th15T3xtis5add3dto66uddy6he@water..." . $CURUSER['username'] . "");
    if (!empty($stdhead['css'])) {
        foreach ($stdhead['css'] as $CSS) $css_incl.= "<link type='text/css' rel='stylesheet' href='{$INSTALLER09['baseurl']}/templates/{$CURUSER['stylesheet']}/css/" . $CSS . ".css' />";
    }
    if (isset($INSTALLER09['xhtml_strict'])) { //== Use strict mime type/doctype
        //== Only if browser/user agent supports xhtml
        if (isset($_SERVER['HTTP_ACCEPT']) && stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') && ($INSTALLER09['xhtml_strict'] === 1 || $INSTALLER09['xhtml_strict'] == $CURUSER['username'])) {
            header('Content-type:application/xhtml+xml; charset=' . charset());
            $doctype = '<?xml version="1.0" encoding="' . charset() . '"?>' . '<!DOCTYPE html PUBLIC  "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
        }
    }
    if (!isset($doctype)) {
        header('Content-type:text/html; charset=' . charset());
        $doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . '<html xmlns="http://www.w3.org/1999/xhtml">';
    }
    
    $body_class = isset($_COOKIE['theme']) ? htmlsafechars($_COOKIE['theme']) : 'background-15 h-style-1 text-1 skin-1';
    $htmlout = $doctype . "<head>
        <meta http-equiv='Content-Language' content='en-us' />
        <!-- ####################################################### -->
        <!-- #   This website is powered by U-232 V3               # -->
        <!-- #   Download and support at: https://u-232.com        # -->
        <!-- #   This Template was Modded by RogueSurfer	          # -->
        <!-- ####################################################### -->
        <title>{$title}</title>
        <link rel='alternate' type='application/rss+xml' title='Latest Torrents' href='./rss.php?passkey={$CURUSER['passkey']}' />
        <link rel='stylesheet' href='./templates/1/1.css' type='text/css' />
        <link rel='stylesheet' href='./templates/1/themeChanger/css/colorpicker.css' type='text/css' />
        <link rel='stylesheet' href='./templates/1/themeChanger/css/themeChanger.css' type='text/css' />
        <link rel='shortcut icon' href='favicon.ico' />
        <script type='text/javascript' src='./scripts/jquery.js'></script>
        <script type='text/javascript' src='./scripts/jquery.status.js'></script>
        <script type='text/javascript' src='./scripts/jquery.cookie.js'></script>
        <script type='text/javascript' src='./templates/1/themeChanger/js/colorpicker.js'></script>
        <script type='text/javascript' src='./templates/1/themeChanger/js/themeChanger.js'></script>
        <script type='text/javascript' src='./templates/1/js/jquery.smoothmenu.js'></script>
        <script type='text/javascript' src='./templates/1/js/core.js'></script>
        <script type='text/javascript'>
        /*<![CDATA[*/
        function themes() {
          window.open('take_theme.php','My themes','height=150,width=200,resizable=no,scrollbars=no,toolbar=no,menubar=no');
        }
        function radio() {
          window.open('radio_popup.php','My Radio','height=700,width=800,resizable=no,scrollbars=no,toolbar=no,menubar=no');
        }
        /*]]>*/
        </script>
        {$js_incl}{$css_incl}
        <!--[if lt IE 9]>
        <script type='text/javascript' src='./templates/1/js/modernizr.custom.js'></script>
	     <script type='text/javascript' src='http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js'></script>
	     <script type='text/javascript' src='./templates/1/js/ie.js'></script>
        <![endif]-->
        </head>
        <body class='{$body_class}'>
        <!-- ***************** - Wrapper - ******************* -->
        <div id='wrapper'>
	     <div class='clearfix'>
        <!--<header class='clearfix'>-->
		  <!-- ***************** - Main Navigation - ***************** -->";
    if ($CURUSER) {
        $active_users_cache = $last24_cache = 0;
        $keys['last24'] = 'last24';
        $last24_cache = $mc1->get_value($keys['last24']);
        $keys['activeusers']    = 'activeusers';
        $active_users_cache = $mc1->get_value($keys['activeusers']);
        $htmlout.= "<div id='navigation' class='navigation'>
     			<ul>
				<li><a href='#'>Torrent</a>
					<ul class='sub-menu'>
						<li><a href='browse.php'>Torrents</a></li>
						<li><a href='requests.php'>Requests</a></li>
						<li><a href='offers.php'>Offers</a></li>
						<li><a href='./needseed.php?needed=seeders'>Need Seeds</a></li>
						" . (isset($CURUSER) && $CURUSER['class'] <= UC_VIP ? "<li><a href='./uploadapp.php'>Upload Appt</a> </li>" : "<li><a href='upload.php'>Upload</a></li>") . "
                        <li><a href='bookmarks.php'>Bookmarks</a></li>
					</ul><!--/ .sub-menu-->
				</li>
				<li><a href='#'>General</a>
					<ul class='sub-menu'>
                        <li><a href='announcement.php'>Site Announcements</a></li>
                        <li><a href='topten.php'>Statistics</a></li>
                        <li><a href='faq.php'>FAQ</a></li>
        				<li><a href='chat.php'>IRC</a></li>
                        <li><a href='staff.php'>Staff</a></li>
                        <li><a href='./wiki.php'>Wiki</a></li>
						<li><a href='#' onclick='radio();'>Radio</a></li>
						<li><a href='./rsstfreak.php'>Torrent Freak</a></li>
					</ul><!--/ .sub-menu-->
				</li>
				<li><a href='#'>Games</a>
					<ul class='sub-menu'>
                    " . (isset($CURUSER) && $CURUSER['class'] >= UC_POWER_USER ? "
                    <li><a href='casino.php'>Casino</a></li>" : "") . "
                    " . (isset($CURUSER) && $CURUSER['class'] >= UC_POWER_USER ? "
                    <li><a href='blackjack.php'>Blackjack</a></li>" : "") . "
                    <li><a href='arcade.php'>Arcade</a></li>
                    </ul><!--/ .sub-menu-->
				</li>
				<li><a href='./donate.php'>Donate</a></li>
				<li><a href='#'>Forums</a>
					<ul class='sub-menu'>
                        <li><a href='forums.php'>Tracker Forums</a></li>
                        <li><a href='http://forum.u-232.com/index.php'>SMF Support</a></li>
					</ul>
				</li>
                <li> " . (isset($CURUSER) && $CURUSER['class'] < UC_STAFF ? "<a class='brand' href='./bugs.php?action=add'>&nbsp;Bug Report</a>" : "<a class='brand' href='./bugs.php?action=bugs'>&nbsp;Bug Respond</a>") . "</li>
                <li>" . (isset($CURUSER) && $CURUSER['class'] < UC_STAFF ? "<a class='brand' href='./contactstaff.php'> Contact Staff</a>" : "<a class='brand' href='./staffbox.php'>Staff Messages</a>") . "</li>
			</ul>
         <small>
         <strong>
         &nbsp;&nbsp;" . $last24_cache['totalonline24'] . " Member" . $last24_cache['ss24'] . " in last 24 hours<br />";
         if (!empty($active_users_cache))
         $htmlout.= "&nbsp;&nbsp;Active User's&nbsp;[" . $active_users_cache['actcount'] . "]";
         $htmlout.= "</strong>
         </small>
         </div>";
    }
    $htmlout.= "<!--/ #navigation-->
		  <!-- ***************** - END Main Navigation - ******************* -->
		  <!-- ***************** - Logo - ******************* -->
		  <!-- U-232 Source - Print Logo (CSS Controled) -->
		  <div class='cl'>&nbsp;</div>
		  <!-- Logo -->
        <div id='logo'>
		  <h1>U-232<span>&nbsp;&nbsp;Code</span></h1>
		  <p class='description'>&nbsp;&nbsp;&nbsp;your source</p>
		  </div>
		  <!-- / Logo -->
        <!-- ***************** - END Logo - ******************* -->
        </div>
        <!--</header>-->
	     <!-- ***************** - END Header - ***************** -->
	     <!-- *************** - Container - *************** -->
	     <div class='container'>
        <!-- ************** - Search - ************** -->
		  <!-- *************** - END Search - *************** -->
		  <!-- ************** - Platform Navigation - ************** -->";
    if ($CURUSER) {
        $htmlout.= "<div id='platform-menu' class='platform-menu'>
		  	  <a href='index.php' class='home'>Home</a>
          <ul>
            <li><a href='pm_system.php'>Messages</a></li>
            <li><a href='usercp.php?action=default'>Usercp</a></li>
            " . (isset($CURUSER) && $CURUSER['class'] >= UC_STAFF ? "
            <li><a href='staffpanel.php'>Admincp</a>
            </li>" : "") . "
            <li><a href='#' onclick='themes();'>Theme</a></li>
            <li><a href='friends.php'>Friends</a></li>
            <li><a href='logout.php?hash_please={$salty}'>Logout</a></li>
          </ul>
		  <!--/ .platform-menu-->
		  <div class='statusbar-container'>
        <!--/ statusbar start-->";
        if ($CURUSER) {
            $htmlout.= StatusBar() . "<!--/ statusbar end-->
        <!--/ #searchForm-->
	<!-- U-232 Source - Print Global Messages Start -->
        </div><div id='base_globelmessage'>
        <div id='gm_taps'>
        <ul class='gm_taps'>
        <li><b>Current Site Alerts:</b></li>";
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_REPORTS && $BLOCKS['global_staff_report_on']) {
                require_once (BLOCK_DIR . 'global/report.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_UPLOADAPP && $BLOCKS['global_staff_uploadapp_on']) {
                require_once (BLOCK_DIR . 'global/uploadapp.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_HAPPYHOUR && $BLOCKS['global_happyhour_on']) {
                require_once (BLOCK_DIR . 'global/happyhour.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_STAFF_MESSAGE && $BLOCKS['global_staff_warn_on']) {
                require_once (BLOCK_DIR . 'global/staffmessages.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_NEWPM && $BLOCKS['global_message_on']) {
                require_once (BLOCK_DIR . 'global/message.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_DEMOTION && $BLOCKS['global_demotion_on']) {
                require_once (BLOCK_DIR . 'global/demotion.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_FREELEECH && $BLOCKS['global_freeleech_on']) {
                require_once (BLOCK_DIR . 'global/freeleech.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_CRAZYHOUR && $BLOCKS['global_crazyhour_on']) {
                require_once (BLOCK_DIR . 'global/crazyhour.php');
            }
            if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_BUG_MESSAGE && $BLOCKS['global_bug_message_on']) {
                require_once (BLOCK_DIR . 'global/bugmessages.php');
            }
            $htmlout.= "</ul></div></div><!-- U-232 Source - Print Global Messages End -->";
        }
        $htmlout.= "	<!--/ statusbarsbar-container--></div><div class='clearfix'><br /></div>";
    }
    $htmlout.= "
      <table class='mainouter' cellspacing='0' cellpadding='10'>
      <tr><td align='center' class='outer' style='padding-bottom: 10px'>
		<div class='entry clearfix'>
      <!--roguesample end-->";
    return $htmlout;
} // stdhead
function stdfoot($stdfoot = false)
{
    global $CURUSER, $INSTALLER09, $start, $query_stat, $mc1, $querytime;
    $debug = (SQL_DEBUG && in_array($CURUSER['id'], $INSTALLER09['allowed_staff']['id']) ? 1 : 0);
    $cachetime = ($mc1->Time / 1000);
    $seconds = microtime(true) - $start;
    $r_seconds = round($seconds, 5);
    $phptime = $seconds - $querytime - $cachetime;
    $queries = count($query_stat); // sql query count by pdq
    $percentphp = number_format(($phptime / $seconds) * 100, 2);
    $percentsql = number_format(($querytime / $seconds) * 100, 2);
    $percentmc = number_format(($cachetime / $seconds) * 100, 2);
    if (($MemStats = $mc1->get_value('mc_hits')) === false) {
        $MemStats = $mc1->getStats();
        $MemStats['Hits'] = (($MemStats['get_hits'] / $MemStats['cmd_get'] < 0.7) ? '' : number_format(($MemStats['get_hits'] / $MemStats['cmd_get']) * 100, 3));
        $mc1->cache_value('mc_hits', $MemStats, 10);
    }
    // load averages - pdq
    if ($debug) {
        $uptime = $mc1->get_value('uptime');
        if ($uptime === false) {
            $uptime = `uptime`;
            $mc1->cache_value('uptime', $uptime, 25);
        }
        preg_match('/load average: (.*)$/i', $uptime, $load);
    }
    $header = '';
    $header.= number_format($cachetime, 5) . 's';
    $header = round($percentmc, 2) . '&#37; Memcached: ' . number_format($cachetime, 5) . 's Hits: ' . $MemStats['Hits'] . '% Misses: ' . (100 - $MemStats['Hits']) . '% Items: ' . number_format($MemStats['curr_items']);
    $htmlfoot = '';
    //== query stats
    //== include js files needed only for the page being used by pdq
    $htmlfoot.= '<!-- javascript goes here -->';
    if (!empty($stdfoot['js'])) {
        foreach ($stdfoot['js'] as $JS) $htmlfoot.= '<script type="text/javascript" src="' . $INSTALLER09['baseurl'] . '/scripts/' . $JS . '.js"></script>';
    }
    $querytime = 0;
    if ($CURUSER && $query_stat && $debug) {
        $htmlfoot.= "<br />
	  <div align='center' class='headline'>Querys</div>
	  <div class='headbody'>
	  <table width='100%' align='center' cellspacing='5' cellpadding='5' border='0'>
		<tr>
		<td class='colhead' width='5%'  align='center'>ID</td>
		<td class='colhead' width='10%' align='center'>Query Time</td>
		<td class='colhead' width='85%' align='left'>Query String</td>
		</tr>";
        foreach ($query_stat as $key => $value) {
            $querytime+= $value['seconds']; // query execution time
            $htmlfoot.= "<tr>
		<td align='center'>" . ($key + 1) . "</td>
		<td align='center'><b>" . ($value['seconds'] > 0.01 ? "<font color='red' title='You should optimize this query.'>" . $value['seconds'] . "</font>" : "<font color='green' title='Query good.'>" . $value['seconds'] . "</font>") . "</b></td>
		<td align='left'>" . htmlsafechars($value['query']) . "<br /></td>
		</tr>";
        }
        $htmlfoot.= '</table></div>';
    }
    $htmlfoot.= "</div></td></tr></table>";
    /** memcache dump - Snuggles **/
    if ($debug && $CURUSER['id'] == 1) {
        if (isset($_GET['flush'])) {
            /** take a dump :< **/
            $htmlfoot.= '<a name="flush"></a><br /><br />
            <h2><strong>Memcached values flushed :</strong></h2>
            <hr /><div id="footer"><pre>' . $mc1->flush() . '</pre></div>';
        }
    }
    if ($CURUSER) {
        /** just in case **/
        $is_id = (isset($_GET['id']) ? '?id=' . (int)$_GET['id'] . '&amp;' : '?');
        $htmlfoot.= "
    <!-- Ends Page Content -->
    <!-- Ends Content holder -->
    <div id='footer'><div id='footer_left'>
       " . $INSTALLER09['site_name'] . " page was generated in " . $r_seconds . " seconds.<br />" . "
       Server was hit  " . $queries . " time" . ($queries != 1 ? "'s" : "") . " " . ($queries > 4 ? "&nbsp;&amp;&nbsp;&nbsp;" . round($queries / 2) . " hits were handled. " : ".") . "
       " . ($debug ? "<br /><b>" . $header . "</b><br /><b>Uptime:</b> " . $uptime . "</div>" : "</div>") . "
    <div id='footer_right'>
    Powered by " . TBVERSION . "<br />
    Using Valid <b>CSS3, HTML &amp; PHP</b><br />
    Support Forum <b>Click <a href='https://forum.u-232.com/index.php'>here</a></b><br />
    " . ($debug ? "| <a title='System View' rel='external' href='/staffpanel.php?tool=system_view'>System View</a> | " . "<a rel='external' title='APC' href='/staffpanel.php?tool=apc'>APC Stats</a> | " . "<a rel='external' title='Memcache' href='/staffpanel.php?tool=memcache'>Memcache</a>|&nbsp;<a title='Flush My Cache' href='" . $is_id . "flush#flush'>Flush Cache</a>" : "") . "";
        $htmlfoot.= "</div></div>";
    }
    $htmlfoot.= "<!--roguesample start-->
    </div></div><!--/ #wrapper-->
	 <div id='control_panel'>
	 <a href='#' id='control_label'></a>
	 </div><!-- #control_panel -->
    <!-- ***************** - END Wrapper - ***************** -->
    <script type='text/javascript' src='templates/1/js/general.js'></script>
    <!--roguesample ends-->
    <!-- Ends Footer -->
    <script src='https://www.anonymiz.com/js/anonymize.js' type='text/javascript'></script>

    <script type='text/javascript'><!--
    protected_links = '';

    auto_anonymize();
    //--></script>
    </body></html>\n";
    return $htmlfoot;
}
function stdmsg($heading, $text)
{
    $htmlout = "<table class='main' width='750' border='0' cellpadding='0' cellspacing='0'>
    <tr><td class='embedded'>\n";
    if ($heading) $htmlout.= "<h2>$heading</h2>\n";
    $htmlout.= "<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n";
    $htmlout.= "{$text}</td></tr></table></td></tr></table>\n";
    return $htmlout;
}
function hey()
{
    global $CURUSER, $lang;
    $now = date("H", TIME_NOW);
    switch ($now) {
    case ($now >= 7 && $now < 11):
        return "{$lang['gl_stdhey']}";
    case ($now >= 11 && $now < 13):
        return "{$lang['gl_stdhey1']}";
    case ($now >= 13 && $now < 17):
        return "{$lang['gl_stdhey2']}";
    case ($now >= 17 && $now < 19):
        return "{$lang['gl_stdhey3']}";
    case ($now >= 19 && $now < 21):
        return "{$lang['gl_stdhey4']}";
    case ($now >= 23 && $now < 0):
        return "{$lang['gl_stdhey5']}";
    case ($now >= 0 && $now < 7):
        return "{$lang['gl_stdhey6']}";
    default:
        return "{$lang['gl_stdhey7']}";
    }
}
function StatusBar()
{
    global $CURUSER, $INSTALLER09, $lang, $rep_is_on, $mc1, $msgalert;
    if (!$CURUSER) return "";
    $upped = mksize($CURUSER['uploaded']);
    $downed = mksize($CURUSER['downloaded']);
    //==Memcache unread pms
    $PMCount = 0;
    $unread1 = $mc1->get_value('inbox_new_sb_' . $CURUSER['id']);
    if ($unread1 === false) {
        $res1 = sql_query("SELECT COUNT(id) FROM messages WHERE receiver=" . sqlesc($CURUSER['id']) . " AND unread = 'yes' AND location = '1'") or sqlerr(__LINE__, __FILE__);
        list($PMCount) = mysqli_fetch_row($res1);
        $PMCount = (int)$PMCount;
        $unread1 = $mc1->cache_value('inbox_new_sb_' . $CURUSER['id'], $PMCount, $INSTALLER09['expires']['unread']);
    }
    $inbox = ($unread1 == 1 ? "$unread1&nbsp;{$lang['gl_msg_singular']}" : "$unread1&nbsp;{$lang['gl_msg_plural']}");
    //==Memcache peers
    $MyPeersCache = $mc1->get_value('MyPeers_' . $CURUSER['id']);
    if ($MyPeersCache == false) {
        $seed['yes'] = $seed['no'] = 0;
        $seed['conn'] = 3;
        $r = sql_query("SELECT COUNT(id) AS count, seeder, connectable FROM peers WHERE userid=" . sqlesc($CURUSER['id']) . " GROUP BY seeder");
        while ($a = mysqli_fetch_assoc($r)) {
            $key = $a['seeder'] == 'yes' ? 'yes' : 'no';
            $seed[$key] = number_format(0 + $a['count']);
            $seed['conn'] = $a['connectable'] == 'no' ? 1 : 2;
        }
        $mc1->cache_value('MyPeers_' . $CURUSER['id'], $seed, $INSTALLER09['expires']['MyPeers_']);
        unset($r, $a);
    } else {
        $seed = $MyPeersCache;
    }
    // for display connectable  1 / 2 / 3
    if (!empty($seed['conn'])) {
        switch ($seed['conn']) {
        case 1:
            $connectable = "<img src='{$INSTALLER09['pic_base_url']}notcon.png' alt='Not Connectable' title='Not Connectable' />";
            break;

        case 2:
            $connectable = "<img src='{$INSTALLER09['pic_base_url']}yescon.png' alt='Connectable' title='Connectable' />";
            break;

        default:
            $connectable = "N/A";
        }
    } else $connectable = 'N/A';
    //$INSTALLER09['expires']['achievements'] = 900;
    //$Achievement_Points = 0;
    if (($Achievement_Points = $mc1->get_value('user_achievement_points_' . $CURUSER['id'])) === false) {
        $Sql = sql_query("SELECT users.id, users.username, usersachiev.achpoints, usersachiev.spentpoints FROM users LEFT JOIN usersachiev ON users.id = usersachiev.id WHERE users.id = " . sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
        $Achievement_Points = mysqli_fetch_assoc($Sql);
        $Achievement_Points['id'] = (int)$Achievement_Points['id'];
        $Achievement_Points['achpoints'] = (int)$Achievement_Points['achpoints'];
        $Achievement_Points['spentpoints'] = (int)$Achievement_Points['spentpoints'];
        $mc1->cache_value('user_achievement_points_' . $CURUSER['id'], $Achievement_Points, 0); // 5 mins
        
    }
    //////////// REP SYSTEM /////////////
    $member_reputation = get_reputation($CURUSER);
    ////////////// REP SYSTEM END //////////
    $usrclass = "";
    if ($CURUSER['override_class'] != 255) $usrclass = "&nbsp;<b>(" . get_user_class_name($CURUSER['class']) . ")</b>&nbsp;";
    else if ($CURUSER['class'] >= UC_STAFF) $usrclass = "&nbsp;<a href='./setclass.php'><b>(" . get_user_class_name($CURUSER['class']) . ")</b></a>&nbsp;";
    $StatusBar = $clock = '';
    $StatusBar.= "
       <!-- U-232 Source - Print Statusbar/User Menu -->
       <script type='text/javascript'>
       //<![CDATA[
       function showSlidingDiv(){
       $('#slidingDiv').animate({'height': 'toggle'}, { duration: 1000 });
       }
       //]]>
       </script>
       <div id='base_usermenu'>" . format_username($CURUSER) . " &nbsp;&nbsp;&nbsp;<span id='clock'>{$clock}</span>&nbsp;<span class='base_usermenu_arrow'><a href='#' onclick='showSlidingDiv(); return false;'><img src='templates/1/images/usermenu_arrow.png' alt='' /></a></span></div>
       <div id='slidingDiv'>
       <div class='slide_head'>:: Personal Stats</div>
       <div class='slide_a'>User Class</div><div class='slide_b'>{$usrclass}</div>
       <div class='slide_c'>Reputation</div><div class='slide_d'>$member_reputation</div>
       <div class='slide_a'>Invites</div><div class='slide_b'><a href='./invite.php'>{$CURUSER['invites']}</a></div>
       <div class='slide_c'>Bonus Points</div><div class='slide_d'><a href='./mybonus.php'>{$CURUSER['seedbonus']}</a></div>
       <div class='slide_a'>Achievements</div><div class='slide_b'><a href='./achievementhistory.php?id={$CURUSER['id']}'>" . (int)$Achievement_Points['achpoints'] . "</a></div>
       <div class='slide_head'>:: Torrent Stats</div>
       <div class='slide_a'>Share Ratio</div><div class='slide_b'>" . member_ratio($CURUSER['uploaded'], $INSTALLER09['ratio_free'] ? "0" : $CURUSER['downloaded']) . "</div>";
    if ($INSTALLER09['ratio_free']) {
        $StatusBar.= "<div class='slide_c'>Uploaded</div><div class='slide_d'>$upped</div>";
    } else {
        $StatusBar.= "<div class='slide_c'>Uploaded</div><div class='slide_d'>$upped</div>
       <div class='slide_a'>Downloaded</div><div class='slide_b'>$downed</div>";
    }
    $StatusBar.= "<div class='slide_c'>Uploading Files</div><div class='slide_d'>{$seed['yes']}</div>
       <div class='slide_a'>Downloading Files</div><div class='slide_b'>{$seed['no']}</div>
       <div class='slide_c'>Connectable</div><div class='slide_d'>{$connectable}</div>
        " . (isset($CURUSER) && $CURUSER['got_blocks'] == 'yes' ? "<div class='slide_head'>:: Site Config</div><div class='slide_a'>My Blocks</div><div class='slide_b'><a href='./user_blocks.php'>Click here</a></div>" : "") . "
         " . (isset($CURUSER) && $CURUSER['got_moods'] == 'yes' ? "<div class='slide_c'>My Unlocks</div><div class='slide_d'><a href='./user_unlocks.php'>Click here</a></div>" : "") . "
       </div>";
    $StatusBar.= '<script type="text/javascript">
      function refrClock(){
      var d=new Date();
      var s=d.getSeconds();
      var m=d.getMinutes();
      var h=d.getHours();
      var day=d.getDay();
      var date=d.getDate();
      var month=d.getMonth();
      var year=d.getFullYear();
      var am_pm;
      if (s<10) {s="0" + s}
      if (m<10) {m="0" + m}
      if (h>12) {h-=12;am_pm = "Pm"}
      else {am_pm="Am"}
      if (h<10) {h="0" + h}
      document.getElementById("clock").innerHTML=h + ":" + m + ":" + s + " " + am_pm;
      setTimeout("refrClock()",1000);
      }
      refrClock();
      </script>';
    return $StatusBar;
}
?>
