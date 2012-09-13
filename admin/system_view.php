<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 *
 */
if (!defined('IN_INSTALLER09_ADMIN')) {
    $htmlout = '';
    $htmlout.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
    echo $htmlout;
    exit();
}
require_once (INCL_DIR.'user_functions.php');
require_once (CLASS_DIR.'class_check.php');
class_check(UC_SYSOP, true, true);
$lang = array_merge($lang);
$htmlout = '';
if (isset($_GET['phpinfo']) AND $_GET['phpinfo']) {
    @ob_start();
    phpinfo();
    $parsed = @ob_get_contents();
    @ob_end_clean();
    preg_match("#<body>(.*)</body>#is", $parsed, $match1);
    $php_body = $match1[1];
    // PREVENT WRAP: Most cookies
    $php_body = str_replace("; ", ";<br />", $php_body);
    // PREVENT WRAP: Very long string cookies
    $php_body = str_replace("%3B", "<br />", $php_body);
    // PREVENT WRAP: Serialized array string cookies
    $php_body = str_replace(";i:", ";<br />i:", $php_body);
    // PREVENT WRAP: LS_COLORS env
    $php_body = str_replace(":*.", "<br />:*.", $php_body);
    // PREVENT WRAP: PATH env
    $php_body = str_replace("bin:/", "bin<br />:/", $php_body);
    // PREVENT WRAP: Cookie %2C split
    $php_body = str_replace("%2C", "%2C<br />", $php_body);
    // PREVENT WRAP: Cookie , split
    $php_body = preg_replace("#,(\d+),#", ",<br />\\1,", $php_body);
    $php_style = "<style type='text/css'>
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left; }
.center th { text-align: center; }
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold;}
.h {background-color: #9999cc; font-weight: bold;}
.v {background-color: #cccccc; white-space: normal;}
</style>\n";
    $html = $php_style.$php_body;
    echo $html;
    stdfoot();
    exit();
}
$html = array();
function sql_get_version()
{
    $query = sql_query("SELECT VERSION() AS version");
    if (!$row = mysqli_fetch_assoc($query)) {
        unset($row);
        $query = sql_query("SHOW VARIABLES LIKE 'version'");
        $row = mysqli_fetch_row($query);
        $row['version'] = $row[1];
    }
    $true_version = $row['version'];
    $tmp = explode('.', preg_replace("#[^\d\.]#", "\\1", $row['version']));
    $mysql_version = sprintf('%d%02d%02d', $tmp[0], $tmp[1], $tmp[2]);
    return $mysql_version." (".$true_version.")";
}
$php_version = phpversion()." (".@php_sapi_name().") ( <a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=system_view&amp;action=system_view&amp;phpinfo=1'>PHP INFO</a> )";
$server_software = php_uname();
// print $php_version ." ".$server_software;
$load_limit = "--";
$server_load_found = 0;
$using_cache = 0;
$avp = @sql_query("SELECT value_s FROM avps WHERE arg = 'loadlimit'");
if (false !== $row = mysqli_fetch_assoc($avp)) {
    $loadinfo = explode("-", $row['value_s']);
    if (intval($loadinfo[1]) > (time() - 20)) {
        $server_load_found = 1;
        $using_cache = 1;
        $load_limit = $loadinfo[0];
    }
}
if (!$server_load_found) {
    if (@file_exists('/proc/loadavg')) {
        if ($fh = @fopen('/proc/loadavg', 'r')) {
            $data = @fread($fh, 6);
            @fclose($fh);
            $load_avg = explode(" ", $data);
            $load_limit = trim($load_avg[0]);
        }
    } else if (strstr(strtolower(PHP_OS) , 'win')) {
        $serverstats = @shell_exec("typeperf \"Processor(_Total)\% Processor Time\" -sc 1");
        if ($serverstats) {
            $server_reply = explode("\n", str_replace("\r", "", $serverstats));
            $serverstats = array_slice($server_reply, 2, 1);
            $statline = explode(",", str_replace('"', '', $serverstats[0]));
            $load_limit = round($statline[1], 4);
        }
    } else {
        if ($serverstats = @exec("uptime")) {
            preg_match("/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load);
            $load_limit = $load[1];
        }
    }
    if ($load_limit) {
        @sql_query("UPDATE avps SET value_s = '".$load_limit."-".time()."' WHERE arg = 'loadlimit'");
    }
}
$total_memory = $avail_memory = "--";
if (strstr(strtolower(PHP_OS) , 'win')) {
    $mem = @shell_exec('systeminfo');
    if ($mem) {
        $server_reply = explode("\n", str_replace("\r", "", $mem));
        if (count($server_reply)) {
            foreach ($server_reply as $info) {
                if (strstr($info, "Total Physical Memory")) {
                    $total_memory = trim(str_replace(":", "", strrchr($info, ":")));
                }
                if (strstr($info, "Available Physical Memory")) {
                    $avail_memory = trim(str_replace(":", "", strrchr($info, ":")));
                }
            }
        }
    }
} else {
    $mem = @shell_exec("free -m");
    $server_reply = explode("\n", str_replace("\r", "", $mem));
    $mem = array_slice($server_reply, 1, 1);
    $mem = preg_split("#\s+#", $mem[0]);
    $total_memory = $mem[1].' MB';
    $avail_memory = $mem[3].' MB';
}
$disabled_functions = @ini_get('disable_functions') ? str_replace(",", ", ", @ini_get('disable_functions')) : "<i>no information</i>";
if (strstr(strtolower(PHP_OS) , 'win')) {
    $tasks = @shell_exec("tasklist");
    $tasks = str_replace(" ", " ", $tasks);
} else {
    $tasks = @shell_exec("top -b -n 1");
    $tasks = str_replace(" ", " ", $tasks);
}
if (!$tasks) {
    $tasks = "<i>Unable to obtain process information</i>";
} else {
    $tasks = "<pre>".$tasks."</pre>";
}
$load_limit = $load_limit." (From Cache: ".($using_cache == 1 ? "<span style='color:green;font-weight:bold;'>True)</span>" : "<span style='color:red;font-weight:bold;'>False)</span>");
$html[] = array(
    'MySQL Version',
    sql_get_version()
);
$html[] = array(
    "PHP Version",
    $php_version
);
$html[] = array(
    "Safe Mode",
    @ini_get('safe_mode') == 1 ? "<span style='color:red;font-weight:bold;'>ON</span>" : "<span style='color:green;font-weight:bold;'>OFF</span>"
);
$html[] = array(
    "Disabled PHP Functions",
    $disabled_functions
);
$html[] = array(
    "Server Software",
    $server_software
);
$html[] = array(
    "Current Server Load",
    $load_limit
);
$html[] = array(
    "Total Server Memory",
    $total_memory
);
$html[] = array(
    "Available Physical Memory",
    $avail_memory
);
$html[] = array(
    "System Processes",
    $tasks
);
$htmlout.= '<table>';
foreach ($html as $key => $value) {
    $htmlout.= '<tr><td>'.$value[0].'</td><td>'.$value[1].'</td></tr>';
}
$htmlout.= '</table>';
echo stdhead('System Overview').$htmlout.stdfoot();
?>
