<?php
//== O9 Top 5 and last5 torrents with tooltip
   $HTMLOUT .="<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/wz_tooltip.js'></script>";
   $HTMLOUT .="
   <div class='headline'>{$lang['index_latest']}</div><div class='headbody'>
   <!--<a href=\"javascript: klappe_news('a4')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica4\" alt=\"[Hide/Show]\" /></a><div id=\"ka4\" style=\"display: none;\">-->
   <table width='100%' border='2' cellpadding='10' cellspacing='0' align='center'>
   <tr><td align='center'>";
   if(($top5torrents = $mc1->get_value('top5_tor_')) === false) {
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
   if (strlen($torrname) > 50)
   $torrname = substr($torrname, 0,50) . "...";
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
   if(($last5torrents = $mc1->get_value('last5_tor_')) === false) {
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
   if (strlen($torrname) > 50)
   $torrname = substr($torrname, 0,50) . "...";
   $poster = empty($last5torrentarr["poster"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}noposter.jpg\' width=\'150\' height=\'220\' />" : "<img src=\'".$last5torrentarr['poster']."\' width=\'150\' height=\'220\' />";
   $HTMLOUT .="<tr><td><a href=\"{$INSTALLER09['baseurl']}/details.php?id=".(int)$last5torrentarr['id']."&amp;hit=1\"></a><a href=\"{$INSTALLER09['baseurl']}/details.php?id=".(int)$last5torrentarr['id']."&amp;hit=1\" onmouseover=\"Tip('<b>Name:".htmlspecialchars($last5torrentarr['name'])."</b><br /><b>Seeders:".(int)$last5torrentarr['seeders']."</b><br /><b>Leechers:".(int)$last5torrentarr['leechers']."</b><br />$poster');\" onmouseout=\"UnTip();\">{$torrname}</a></td>";
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
//==
// End Class

// End File
