<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
// userimages.php
// pic management by pdq
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global'), load_language('userimages') );

$HTMLOUT ="";

@session_start();

if ($CURUSER['class'] < UC_MODERATOR) // for staff only
    die('No access');

$Name = (isset($_GET['user'])?htmlspecialchars($_GET['user']):htmlspecialchars($_SESSION['picname']));
if (!isset($Name))
    stderr ($lang['userimages_hmm'], "{$lang['userimages_nouser']}");

$SaLt = '9#Qhj5%^2SA'; // change this!
$skey = '5j$h#%2yq^Q# qty\ty'; // change this!

if ($CURUSER['username'] != $Name) {
    $staffnames = array('Bob', 'Tam', 'System'); // :P paranoid users here
    if (in_array($Name, $staffnames))
        stderr ($lang['userimages_forbidden'], "{$lang['userimages_shoo']}");
}

$_SESSION['picname'] = $Name;
$PICSALT = $CURUSER['username'] . $SaLt;
$address = $INSTALLER09['baseurl']. '/';

if (isset($_GET["delete"]) && ($CURUSER['class'] >= UC_STAFF)) {
    $getfile = htmlspecialchars($_GET['delete']);
    $delfile = urldecode(decrypt($getfile));
    $delhash = md5($delfile . $_SESSION['picname'] . $skey);

    if ($delhash != $_GET['delhash'])
        stderr($lang['userimages_umm'], "{$lang['userimages_waydh']}");
    
    $myfile = ROOT_DIR . '/' . $delfile;  //== for pdq define directories
    //$myfile = '/home/yourdir/public_html/'.$delfile; // Full relative path to web root
    
    if (is_file($myfile))
        unlink($myfile);
    else
        stderr($lang['userimages_hey'], "{$lang['userimages_imagenf']}");

    if (isset($_GET["type"]) && $_GET["type"] == 2)
        header("Refresh: 2; url={$INSTALLER09['baseurl']}/userimages.php?images=2&user=$_SESSION[picname]");
    else
        header("Refresh: 2; url={$INSTALLER09['baseurl']}/userimages.php?images=1&user=$_SESSION[picname]");
    die('Deleting Image (' . $delfile . '), Redirecting...');
}

if (isset($_GET["avatar"]) && $_GET["avatar"] != '' && (($_GET["avatar"]) != $CURUSER["avatar"])) {
    $type = ((isset($_GET["type"]) && $_GET["type"] == '1')?1:2);
    if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $_GET["avatar"]))
    stderr($lang['userimages_error'], "{$lang['userimages_mustbe']}");
    $avatar = sqlesc($_GET['avatar']);
    sql_query("UPDATE users SET avatar = $avatar WHERE id = {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);
    header("Refresh: 0; url={$INSTALLER09['baseurl']}/userimages.php?images=$type&updated=avatar&user=$_SESSION[picname]");
}
if (isset($_GET["updated"]) && $_GET["updated"] == 'avatar') {
$HTMLOUT .="<h3>{$lang['userimages_updated']}<p><img src=\"".htmlspecialchars($CURUSER['avatar'])."\" border=\"0\" alt=\"\" /></p></h3>";
}
$HTMLOUT .="<script type=\"text/javascript\">
/*<![CDATA[*/
function SelectAll(id)
{
document.getElementById(id).focus();
document.getElementById(id).select();
}
/*]]>*/
</script>";
if (isset($_GET['images']) && $_GET['images'] == 1) {
$HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?images=2&amp;user={$Name}\">{$lang['userimages_view']} {$Name} {$lang['userimages_avvy']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?user={$Name}\">{$lang['userimages_hide']} {$Name} {$lang['userimages_images']}</a></p>";
} 
elseif (isset($_GET['images']) && $_GET['images'] == 2) {
$HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?images=1&amp;user={$Name}\">{$lang['userimages_view']} {$Name} {$lang['userimages_images']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?user={$Name}\">{$lang['userimages_hide']} {$Name} {$lang['userimages_avvy']}</a></p>";
} else {
$HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?images=1&amp;user={$Name}\">{$lang['userimages_view']} {$Name} {$lang['userimages_images']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?images=2&amp;user={$Name}\">{$lang['userimages_view']} {$Name} {$lang['userimages_avvy']}</a></p>";
}

if (isset($_GET['images'])) {
    foreach ((array) glob((($_GET['images'] == 2)?'avatars/':'bitbucket/') . $Name . '_*') as $filename) {
        if (!empty($filename)) {
            $encryptedfilename = urlencode(encrypt($filename));
            $eid = md5($filename);
            $HTMLOUT .="<a href=\"{$INSTALLER09['baseurl']}/{$filename}\"><img src=\"{$INSTALLER09['baseurl']}/{$filename}\" width=\"200\" alt=\"\" /><br />{$INSTALLER09['baseurl']}/{$filename}</a><br />";

    

$HTMLOUT .="<p>{$lang['userimages_directlink']}<br />
<input style=\"font-size: 9pt;text-align: center;\" id=\"d".$eid."d\" onclick=\"SelectAll('d".$eid."d');\" type=\"text\" size=\"70\" value=\"{$INSTALLER09['baseurl']}/{$filename}\" readonly=\"readonly\" /></p>
<p align=\"center\">{$lang['userimages_tag']}<br />
<input style=\"font-size: 9pt;text-align: center;\" id=\"t".$eid."t\" onclick=\"SelectAll('t".$eid."t');\" type=\"text\" size=\"70\" value=\"[img]{$INSTALLER09['baseurl']}/{$filename}[/img]\" readonly=\"readonly\" /></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?type=".((isset($_GET['images']) && $_GET['images'] == 2)?'2':'1')."&amp;avatar={$INSTALLER09['baseurl']}/{$filename}\">{$lang['userimages_maketma']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/userimages.php?type=".((isset($_GET['images']) && $_GET['images'] == 2)?'2':'1')."&amp;delete={$encryptedfilename}&amp;delhash=".md5($filename . $_SESSION['picname'] . $skey)."\">{$lang['userimages_delete']}</a></p>
<br />";
} 
else
$HTMLOUT .="{$lang['userimages_noimage']}";
}
}

echo stdhead($Name . '\'s images') . $HTMLOUT . stdfoot();
exit();

function encrypt($text)
{
    global $PICSALT;
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $PICSALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

function decrypt($text)
{
    global $PICSALT;
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $PICSALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

?>
