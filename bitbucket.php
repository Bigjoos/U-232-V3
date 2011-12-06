<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
// by system
// pic management by pdq
// no rights reserved - public domain FTW!
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
dbconn();
loggedinorreturn();

$lang = array_merge( load_language('global'), load_language('bitbucket') );

$HTMLOUT ="";

$SaLt = 'mE0wI924dsfsfs!@B'; // change this!
$skey = 'eTe5$Ybnsccgbsfdsfsw4h6W'; // change this!
$maxsize = $INSTALLER09['bucket_maxsize'];
// valid file formats
$formats = array('.gif',
    '.jpg',
    '.png',
    );
// path to bucket/avatar directories
$bucketdir = (isset($_POST["avy"])?'avatars/':'bitbucket/');
$address = $INSTALLER09['baseurl']. '/';

$PICSALT = $SaLt . $CURUSER['username'];

if (!isset($_FILES['file'])) {
    if (isset($_GET["delete"])) {
        $getfile = htmlspecialchars($_GET['delete']);
        $delfile = urldecode(decrypt($getfile));
        $delhash = md5($delfile . $CURUSER['username'] . $SaLt);

        if ($delhash != $_GET['delhash'])
            stderr($lang['bitbucket_umm'], "{$lang['bitbucket_wayd']}");
        
        $myfile = ROOT_DIR . '/' . $delfile;  //== for pdq define directories
        //$myfile = '/home/yourdir/public_html/'.$delfile; // Full relative path to web root
        
        if (is_file($myfile))
            unlink($myfile);
        else
            stderr($lang['bitbucket_hey'], "{$lang['bitbucket_imagenf']}");

        if (isset($_GET["type"]) && $_GET["type"] == 2)
            header("Refresh: 2; url={$INSTALLER09['baseurl']}/bitbucket.php?images=2");
        else
            header("Refresh: 2; url={$INSTALLER09['baseurl']}/bitbucket.php?images=1");
        die('Deleting Image (' . $delfile . '), Redirecting...');
    }

    if (isset($_GET["avatar"]) && $_GET["avatar"] != '' && (($_GET["avatar"]) != $CURUSER["avatar"])) {
        $type = ((isset($_GET["type"]) && $_GET["type"] == 1)?1:2);
        if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $_GET["avatar"]))
            stderr($lang['bitbucket_error'], "{$lang['bitbucket_mustbe']}Avatar MUST be in jpg, gif or png format. Make sure you include http:// in the URL.");
        $avatar = sqlesc($_GET['avatar']);
        sql_query("UPDATE users SET avatar = $avatar WHERE id = {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);
        header("Refresh: 0; url={$INSTALLER09['baseurl']}/bitbucket.php?images=$type&updated=avatar");
    }
    
    if (isset($_GET["updated"]) && $_GET["updated"] == 'avatar') {
    $HTMLOUT .="<h3>{$lang['bitbucket_updated']}<p><img src='".htmlspecialchars($CURUSER['avatar'])."' border='0' alt='' /></p></h3>";
    }

    $HTMLOUT .="<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" enctype=\"multipart/form-data\">
    <table width=\"300\" align=\"center\">
    <tr>
    <td class=\"clearalt6\" align=\"center\"><p><b>{$lang['bitbucket_invalid_extension']}".join(', ', $formats)."</b></p>
    <p><b>{$lang['bitbucket_max']}".mksize($maxsize)."</b></p>
    <p>{$lang['bitbucket_disclaimer']}</p></td>
    </tr>
    <tr>
    <td align=\"center\"><input type=\"file\" name=\"file\" /></td>
    </tr>
    <tr><td align=\"center\"> <input type=\"checkbox\" name=\"avy\" value=\"1\" />{$lang['bitbucket_tick']}</td> </tr>
    <tr>
    <td align=\"center\"><input class=\"btn\" type=\"submit\" value=\"{$lang['bitbucket_upload']}\" /></td>
    </tr>
    </table>
    </form>";
    
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
    $HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=2\">{$lang['bitbucket_viewmya']}</a></p>
    <p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php\">{$lang['bitbucket_hidemyi']}</a></p>";
    } 
    elseif (isset($_GET['images']) && $_GET['images'] == 2) {
    $HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=1\">{$lang['bitbucket_viewmyi']}</a></p>
    <p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php\">{$lang['bitbucket_hidemya']}</a></p>";
    } 
    else {
    $HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=1\">{$lang['bitbucket_viewmyi']}</a></p>
    <p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=2\">{$lang['bitbucket_viewmya']}</a></p>";
    }
    if (isset($_GET['images'])) {
       foreach ((array) glob((($_GET['images'] == 2)?'avatars/':'bitbucket/') . $CURUSER['username'] . '_*') as $filename) {
            if (!empty($filename)) {
                $encryptedfilename = urlencode(encrypt($filename));
                $eid = md5($filename);
    $HTMLOUT .="<a href=\"{$INSTALLER09['baseurl']}/{$filename}\"><img src=\"{$INSTALLER09['baseurl']}/{$filename}\" width=\"200\" alt=\"\" /><br />{$INSTALLER09['baseurl']}/{$filename}</a><br />";
    $HTMLOUT .="<p>Direct link to image<br /><input style=\"font-size: 9pt;text-align: center;\" id=\"d".$eid."d\" onclick=\"SelectAll('d".$eid."d');\" type=\"text\" size=\"70\" value=\"{$INSTALLER09['baseurl']}/{$filename}\" readonly=\"readonly\" /></p>";
    $HTMLOUT .="<p align=\"center\">Tag for forums or comments<br /><input style=\"font-size: 9pt;text-align: center;\" id=\"t".$eid."t\" onclick=\"SelectAll('t".$eid."t');\" type=\"text\" size=\"70\" value=\"[img]{$INSTALLER09['baseurl']}/{$filename}[/img]\" readonly=\"readonly\" /></p>";
		$HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?type=".((isset($_GET['images']) && $_GET['images'] == 2)?'2':'1')."&amp;avatar={$INSTALLER09['baseurl']}/{$filename}\">{$lang['bitbucket_maketma']}</a></p>";
    $HTMLOUT .="<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?type=".((isset($_GET['images']) && $_GET['images'] == 2)?'2':'1')."&amp;delete=".$encryptedfilename."&amp;delhash=". md5($filename . $CURUSER['username'] . $SaLt)."\">{$lang['bitbucket_delete']}</a></p><br />";
            } else
                $HTMLOUT .="{$lang['bitbucket_noimages']}";
        }
    }
    echo stdhead($lang['bitbucket_bitbucket']) . $HTMLOUT . stdfoot();
    exit();
}
if ($_FILES['file']['size'] == 0) stderr($lang['bitbucket_error'], $lang['bitbucket_upfail']);
if ($_FILES['file']['size'] > $maxsize) stderr($lang['bitbucket_error'], $lang['bitbucket_to_large']);
$file = preg_replace('`[^a-z0-9\-\_\.]`i', '', $_FILES['file']['name']);
$allow = ',' . join(',', $formats);
if (! function_exists('exif_imagetype')) {
    function exif_imagetype ($filename)
    {
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
            return $type;
        }
        return false;
    }
}
$it1 = exif_imagetype($_FILES['file']['tmp_name']);
if ($it1 != IMAGETYPE_GIF && $it1 != IMAGETYPE_JPEG && $it1 != IMAGETYPE_PNG) {
    $HTMLOUT .="<h1>{$lang['bitbucket_upfail']}<br />{$lang['bitbucket_sorry']}";
    exit;
}
$path = $bucketdir . $CURUSER['username'] . '_' . $file;
$loop = 0;
while (true) {
    if ($loop > 10) stderr($lang['bitbucket_error'], $lang['bitbucket_upfail']);
    if (!file_exists($path)) break;
    $path = $bucketdir . $CURUSER['username'] . '_' . bucketrand() . $file;
    $loop++;
}
if (!move_uploaded_file($_FILES['file']['tmp_name'], $path))
    stderr($lang['bitbucket_error'], $lang['bitbucket_upfail']);
if(isset($_POST["from"]) && $_POST["from"] == "upload"){
echo "<p><b><font color='red'>{$lang['bitbucket_success']}</b></p>
<p><b><strong>".$address.$path."</strong></font></b></p>";
exit;
}
$HTMLOUT .="<table width=\"300\" align=\"center\">
<tr class=\"clear\">
<td align=\"center\"><p><a href=\"".$_SERVER['PHP_SELF']."\"><strong>{$lang['bitbucket_up_another']}</strong></a></p>
<p>{$lang['bitbucket_thefile']}</p>
<p><img src=\"".$address . $path."\" border=\"0\" alt=\"\"/></p>";

$HTMLOUT .="<script type=\"text/javascript\">
/*<![CDATA[*/
function SelectAll(id)
{
document.getElementById(id).focus();
document.getElementById(id).select();
}
/*]]>*/
</script>";

$HTMLOUT .="<p>{$lang['bitbucket_directlink']}<br />
<input style=\"font-size: 9pt;text-align: center;\" id=\"direct\" onclick=\"SelectAll('direct');\" type=\"text\" size=\"70\" value=\"".$address . $path."\" readonly=\"readonly\" /></p>
<p align=\"center\">{$lang['bitbucket_tags']}
<input style=\"font-size: 9pt;text-align: center;\" id=\"tag\" onclick=\"SelectAll('tag');\" type=\"text\" size=\"70\" value=\"[img]".$address . $path."[/img]\" readonly=\"readonly\" /></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?type=2&amp;avatar=".$address . $path."\">{$lang['bitbucket_maketma']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=1\">{$lang['bitbucket_viewmyi']}</a></p>
<p align=\"center\"><a href=\"{$INSTALLER09['baseurl']}/bitbucket.php?images=2\">{$lang['bitbucket_viewmya']}</a></p>
</td>
</tr>
</table>";

echo stdhead($lang['bitbucket_bitbucket']) . $HTMLOUT . stdfoot();

function bucketrand()
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $out = '';
    for($i = 0;$i < 6;$i++) $out .= $chars[mt_rand(0, 61)];
    return $out;
}

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
