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


$lang = array_merge( $lang );

//get the config from db
$pconf = sql_query('SELECT * FROM site_config') or sqlerr(__FILE__,__LINE__);
while($ac = mysqli_fetch_assoc($pconf))
 $site_settings[$ac['name']] = $ac['value'];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  
   //can't be 0
   foreach(array('site_online'>0,'autoshout_on'>0,'seedbonus_on'>0,'forums_online'>0,'maxusers'>0,'invites'>0) as $key=>$type) {
     if(isset($_POST[$key]) && ($type == 0 && $_POST[$key] == 0 || $type == 0 && count($_POST[$key]) == 0))
     stderr('Err','You forgot to fill some data');
   }
   foreach($site_settings as $c_name=>$c_value)
   if(isset($_POST[$c_name]) && $_POST[$c_name] != $c_value)
     $update[] = '('.sqlesc($c_name).','.sqlesc(is_array($_POST[$c_name]) ? join('|',$_POST[$c_name]) : $_POST[$c_name]).')';

   if(sql_query('INSERT INTO site_config(name,value) VALUES '.join(',',$update).' ON DUPLICATE KEY update value=values(value)')){
$t = '$INSTALLER09';

$configfile = "<"."?php\n\n/**\nThis file created on ".date('M d Y H:i:s').".\nSite Config mod by stoner with a little help from pdq for 09SOURCE.\n**/\n\n";

$res = sql_query("SELECT * from site_config ");
while($arr = mysqli_fetch_assoc($res)) {
  




$configfile .= "".$t."['$arr[name]'] = '$arr[value]';\n";
}
$configfile .= "\n\n\n?".">";
    $filenum = fopen('./cache/site_settings.php', 'w');
    ftruncate($filenum, 0);
    fwrite($filenum, $configfile);
    fclose($filenum);
     stderr('Success','Site configuration was saved! Click <a href=\'staffpanel.php?tool=site_settings\'>here to get back</a>');
     
}



else
     stderr('Error','There was an error while executing the update query. Mysql error: '.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
 exit;
}

$HTMLOUT .="<h3>Site Settings</h3>
<form action='staffpanel.php?tool=site_settings' method='post'>
<table width='100%' border='1' cellpadding='5' cellspacing='0' >
<tr><td width='50%' class='table' align='left'>Site Online:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='site_online' value='1' ".($site_settings['site_online'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='site_online' value='0' ".(!$site_settings['site_online'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td width='50%' class='table' align='left'>Forums Autoshout:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='autoshout_on' value='1' ".($site_settings['autoshout_on'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='autoshout_on' value='0' ".(!$site_settings['autoshout_on'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td width='50%' class='table' align='left'>Forums SeedBonus:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='seedbonus_on' value='1' ".($site_settings['seedbonus_on'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='seedbonus_on' value='0' ".(!$site_settings['seedbonus_on'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td width='50%' class='table' align='left'>Forums Online:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='forums_online' value='1' ".($site_settings['forums_online'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='forums_online' value='0' ".(!$site_settings['forums_online'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td width='50%' class='table' align='left'>Open Reg:</td><td class='table' align='left'>Yes <input class='table' type='radio' name='openreg' value='true' ".($site_settings['openreg'] ? 'checked=\'checked\'' : '')." /> No <input class='table' type='radio' name='openreg' value='false' ".(!$site_settings['openreg'] ? 'checked=\'checked\'' : '')." /></td></tr>
<tr><td width='50%' class='table' align='left'>Max Users</td><td class='table' align='left'><input type='text' name='maxusers' size='2' value='".$site_settings['maxusers']."' /></td></tr>
<tr><td width='50%' class='table' align='left'>Max Invited</td><td class='table' align='left'><input type='text' name='invites' size='2' value='".$site_settings['invites']."' /></td></tr>
<tr><td colspan='2' class='table' align='center'><input type='submit' value='Apply changes' /></td></tr>
</table></form>";

echo stdhead('Site Settings') . $HTMLOUT . stdfoot();
?>
