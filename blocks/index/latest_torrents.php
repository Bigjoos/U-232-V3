<?php

//== O9 Top 5 and last5 torrents with tooltip
   $HTMLOUT .="<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/wz_tooltip.js'></script>";
   $HTMLOUT .="
   <div class='headline'>{$lang['index_latest']}</div><div class='headbody'>
   <!--<a href=\"javascript: klappe_news('a4')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica4\" alt=\"[Hide/Show]\" /></a><div id=\"ka4\" style=\"display: none;\">-->
   <table width='100%' border='2' cellpadding='10' cellspacing='0' align='center'>
   <tr><td align='center'>";
   $top5torrents = $mc1->get_value('top5_tor_');
   if($top5torrents === false ) {
   $res = sql_query("SELECT id, seeders, poster, leechers, name from torrents ORDER BY seeders + leechers DESC LIMIT {$INSTALLER09['latest_torrents_limit']}") or sqlerr(__FILE__, __LINE__);
   while ($top5torrent = mysqli_fetch_assoc($res)) 
   $top5torrents[] = $top5torrent;
   $mc1->cache_value('top5_tor_', $top5torrents, $INSTALLER09['expires']['top5_torrents']);
   }
   if (count($top5torrents) > 0)
   {
   $HTMLOUT .="<div class='top5'><table width='100%' align='center' class='table' border='2' cellspacing='0' cellpadding='5'>\n";
   $HTMLOUT .=" <tr>
                <td align='left' class='colhead'><b>{$lang['top5torrents_title']}</b></td>
                <td align='center' class='colhead'>{$lang['top5torrents_seeders']}</td>
                <td align='center' class='colhead'>{$lang['top5torrents_leechers']}</td></tr>\n";
   if ($top5torrents)
   {
   foreach($top5torrents as $top5torrentarr) {
   $torrname = htmlspecialchars($top5torrentarr['name']);
   if (strlen($torrname) > 56)
   $torrname = substr($torrname, 0,56) . "...";
   $poster = empty($top5torrentarr["poster"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}noposter.jpg\' width=\'150\' height=\'220\' />" : "<img src=\'".htmlspecialchars($top5torrentarr['poster'])."\' width=\'150\' height=\'220\' />";
   $HTMLOUT .=" <tr>
                <td class='table'><a href=\"{$INSTALLER09['baseurl']}/details.php?id=".(int)$top5torrentarr['id']."&amp;hit=1\" onmouseover=\"Tip('<b>Name:".htmlspecialchars($top5torrentarr['name'])."</b><br /><b>Seeders:".(int)$top5torrentarr['seeders']."</b><br /><b>Leechers:".(int)$top5torrentarr['leechers']."</b><br />$poster');\" onmouseout=\"UnTip();\">{$torrname}</a></td>
<td align='center' width='100'>".(int)$top5torrentarr['seeders']."</td>
<td align='center' width='100'>".(int)$top5torrentarr['leechers']."</td>
</tr>\n";
   }
   $HTMLOUT .="</table></div>\n";
   } else {
   //== If there are no torrents
   if (empty($top5torrents))
   $HTMLOUT .= "<tr><td colspan='4'>{$lang['top5torrents_no_torrents']}</td></tr></table>";
   }
   }
   //==Last 5 begin
   $last5torrents = $mc1->get_value('last5_tor_');
   if($last5torrents === false ) {
   $sql = "SELECT id, seeders, poster, leechers, name FROM torrents WHERE visible='yes' ORDER BY added DESC LIMIT {$INSTALLER09['latest_torrents_limit']}";
   $result = sql_query($sql) or sqlerr(__FILE__, __LINE__);
   while( $last5torrent = mysqli_fetch_assoc($result) )
   $last5torrents[] = $last5torrent;
   $mc1->cache_value('last5_tor_', $last5torrents, $INSTALLER09['expires']['last5_torrents']);
   }
   if (count($last5torrents) > 0)
   {
   $HTMLOUT .="<div class='last5'><table width='100%' border='2' cellspacing='0' cellpadding='5'>";
   $HTMLOUT .="<tr>";
   
   $HTMLOUT .="<td align='left' class='colhead'><b>{$lang['last5torrents_title']}</b></td>";
   $HTMLOUT .="<td align='center' class='colhead'>{$lang['last5torrents_seeders']}</td>";
   $HTMLOUT .="<td align='center' class='colhead'>{$lang['last5torrents_leechers']}</td>";
   $HTMLOUT .="</tr>";
   if ($last5torrents)
   {
   foreach($last5torrents as $last5torrentarr) {
   $torrname = htmlspecialchars($last5torrentarr['name']);
   if (strlen($torrname) > 56)
   $torrname = substr($torrname, 0,56) . "...";
   $poster = empty($last5torrentarr["poster"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}noposter.jpg\' width=\'150\' height=\'220\' />" : "<img src=\'".$last5torrentarr['poster']."\' width=\'150\' height=\'220\' />";
   $HTMLOUT .="<tr>";
   $HTMLOUT .="<td><a href=\"{$INSTALLER09['baseurl']}/details.php?id=".(int)$last5torrentarr['id']."&amp;hit=1\"></a><a href=\"{$INSTALLER09['baseurl']}/details.php?id=".(int)$last5torrentarr['id']."&amp;hit=1\" onmouseover=\"Tip('<b>Name:".htmlspecialchars($last5torrentarr['name'])."</b><br /><b>Seeders:".(int)$last5torrentarr['seeders']."</b><br /><b>Leechers:".(int)$last5torrentarr['leechers']."</b><br />$poster');\" onmouseout=\"UnTip();\">{$torrname}</a></td>";
   $HTMLOUT .="<td align='center' width='100'>{$last5torrentarr['seeders']}</td>";
   $HTMLOUT .="<td align='center' width='100'>{$last5torrentarr['leechers']}</td>";
   $HTMLOUT .="</tr>";
   }
   $HTMLOUT .="</table></div>";
   } else {
   //== If there are no torrents
   if (empty($last5torrents))
   $HTMLOUT .= "<tr><td colspan='4'>{$lang['last5torrents_no_torrents']}</td></tr></table>";
   }
   }
   $HTMLOUT .="</td></tr></table></div><!--</div>--><br />";
   //== End 09 last5 and top5 torrents
   
   /*
   //== Latest torrents [see limit on config]
   $HTMLOUT .= "<div class='headline'>{$lang['latesttorrents_title']}</div><div class='headbody'>";
   $torrents = $mc1->get_value('lastest_tor_');
   if($torrents === false ) {
   $res = sql_query("SELECT t.id, t.name, t.category, t.seeders, t.leechers, c.name AS cat_name, c.image AS cat_img ".
   "FROM torrents AS t ".
   "LEFT JOIN categories AS c ON c.id = t.category ".
   "WHERE t.visible='yes' ".
   "ORDER BY t.added DESC LIMIT {$INSTALLER09['latest_torrents_limit']}") or sqlerr(__FILE__, __LINE__);
   while($torrent = mysql_fetch_assoc($res))
   $torrents[] = $torrent;
   $mc1->cache_value('lastest_tor_', $torrents, $INSTALLER09['expires']['latesttorrents']);
   }
   
   if (count($torrents) > 0)
   {
   $HTMLOUT .= "<table width='100%' cellspacing='0' cellpadding='5'><tr>
   <td class='colhead' align='center' width='1%'>{$lang['latesttorrents_type']}</td>
   <td class='colhead' align='left'>{$lang['latesttorrents_name']}</td>
   <td class='colhead' align='center' width='1%'>{$lang['latesttorrents_seeders']}</td>
   <td class='colhead' align='center' width='1%'>{$lang['latesttorrents_leechers']}</td></tr>";
   
   if ($torrents)
   {
   foreach($torrents as $torrentarr) {
   $dispname = htmlspecialchars($torrentarr['name']);
   $catname = htmlspecialchars($torrentarr['cat_name']);
   $catpic = htmlspecialchars($torrentarr['cat_img']);

   $HTMLOUT .= "<tr><td align='center' style='padding:0px;'><a href='/browse.php?cat=".(int)$torrentarr['category']."'><img border='0' src='{$INSTALLER09['pic_base_url']}caticons/{$catpic}' alt='{$catname}' /></a></td>
   <td align='left'><a href='/details.php?id={$torrentarr['id']}&amp;hit=1' title='{$dispname}'><b>" . CutName($dispname , 45) . "</b></a></td>
   <td align='center'>".(int)$torrentarr['seeders']."</td>
   <td align='center'>".(int)$torrentarr['leechers']."</td></tr>";
   }
   $HTMLOUT .= "</table></div><br />\n";
   } else {
   //== If there are no torrents
   if (empty($torrents))
   $HTMLOUT .= "<tr><td colspan='4'>{$lang['latesttorrents_no_torrents']}</td></tr></table></div><br />";
   }
   }
   //== End latest torrents
   */
//==
// End Class

// End File
