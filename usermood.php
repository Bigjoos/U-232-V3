<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
// usermood.php for PTF by pdq 2011 
require_once(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
dbconn(false);	
$HTMLOUT = '';
$lang = array_merge( load_language('global') );

if (!isset($CURUSER['id']))
   die('log in to use this feature!');

$more = (($CURUSER['perms'] & bt_options::UNLOCK_MORE_MOODS) ? 2 : 1);

if (isset($_GET['id'])) {
   $moodid = (isset($_GET['id']) ? (int)$_GET['id'] : 1);

   $res_moods = sql_query('SELECT * FROM moods WHERE bonus < '.$more.' AND id = '.$moodid) or sqlerr(__file__, __line__);
   if (mysqli_num_rows($res_moods)) {
      $rmood = mysqli_fetch_assoc($res_moods);
      sql_query('UPDATE users SET mood = '.$moodid.' WHERE id = '.$CURUSER['id']) or sqlerr(__FILE__, __LINE__);

      $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
      $mc1->update_row(false, array('mood' => $moodid));
      $mc1->commit_transaction($INSTALLER09['expires']['curuser']);

      $mc1->begin_transaction('user'.$CURUSER['id']);
      $mc1->update_row(false, array('mood' => $moodid));
      $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
      
      $mc1->delete_value('topmoods');
      
      write_log('<b>Mood Change</b> '.$CURUSER['username'].' '.$rmood['name'].
                 '<img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$rmood['image'].'" alt="" />');

      $HTMLOUT .='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">		
      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
         <meta http-equiv="Content-Language" content="en-us" />
         <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
         <title>moods</title>
      <script type="text/javascript">	 
      <!--
      opener.location.reload(true);
      self.close();
      // -->
      </script>';
   }
   else
      die('Hmmm. Invalid Mood choice.');
}

$HTMLOUT .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">		
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Language" content="en-us" />
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
   <title>moods</title>
   <link rel="stylesheet" href="./templates/'.$CURUSER['stylesheet'].'/'.$CURUSER['stylesheet'].'.css" type="text/css" />
</head>
<body>
<h3 align="center">'.$CURUSER['username'].'\'s Mood</h3>
<table width="500px">';

$res = sql_query('SELECT * FROM moods WHERE bonus < '.$more.' ORDER BY id ASC') or sqlerr(__FILE__, __LINE__);

$count = 0;
while ($arr = mysqli_fetch_assoc($res)) {
   if ($count % 3 == 0)
      $HTMLOUT .= '<tr>';

   $HTMLOUT .= '<td>
         <a href="?id='.$arr['id'].'">
         <img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$arr['image'].'" alt="" />'.$arr['name'].'
         </a>
         </td>';

   $count++;

   if ($count % 3 == 0)
      $HTMLOUT .= '</tr>';
}

$HTMLOUT .= '</table>
      <p><br /></p>
      <a href="javascript:self.close();"><font color="#FF0000">Close window</font></a>
      <noscript><a href="/index.php">Back to site</a></noscript>
      </body>
      </html>';
echo $HTMLOUT;
?>
