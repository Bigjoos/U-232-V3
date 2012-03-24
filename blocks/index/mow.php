<?php
    //== Best film of the week
        $categorie = genrelist();
foreach($categorie as $key => $value)
$change[$value['id']] = array('id' => $value['id'], 'name'  => $value['name'], 'image' => $value['image']);
if(($motw_cached = $mc1->get_value('top_movie_2')) === false) {
$motw = sql_query("SELECT torrents.id, torrents.leechers, torrents.seeders, torrents.category, torrents.name, torrents.times_completed FROM torrents INNER JOIN avps ON torrents.id=avps.value_u WHERE avps.arg='bestfilmofweek' LIMIT 1") or sqlerr(__FILE__, __LINE__);
while($motw_cache = mysqli_fetch_assoc($motw))
$motw_cached[] = $motw_cache;
$mc1->cache_value('top_movie_2', $motw_cached, 0);
}
if (count($motw_cached) > 0)
{
$HTMLOUT .= "<div class='headline'>
    Movie of the Week</div>
    <div class='headbody'>
    <table align='center' border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='colhead' align='left'>Type</td>
    <td class='colhead' align='left'>Name</td>
    <td class='colhead' align='left'>Snatched</td>
    <td class='colhead' align='left'>Seeders</td>
    <td class='colhead' align='left'>Leechers</td></tr>";
if ($motw_cached)
{
foreach($motw_cached as $m_w) {
$mw['cat_name'] = htmlspecialchars($change[$m_w['category']]['name']);
$mw['cat_pic'] = htmlspecialchars($change[$m_w['category']]['image']);
$HTMLOUT .= "<tr><td align='center'><img border='0' src='pic/caticons/{$CURUSER['categorie_icon']}/".htmlentities($mw["cat_pic"])."' alt='".htmlentities($mw["cat_name"])."' title='".htmlentities($mw["cat_name"])."' /></td>
    <td style='padding-right: 5px'><a href='{$INSTALLER09['baseurl']}/details.php?id=".(int)$m_w["id"]."'><b>".htmlentities($m_w["name"])."</b></a></td>
    <td align='left'>".(int)$m_w["times_completed"]."</td>
    <td align='left'>".(int)$m_w["seeders"]."</td>
    <td align='left'>".(int)$m_w["leechers"]."</td></tr>";
}
$HTMLOUT .= "</table></div><br />";
} else {
//== If there are no movie of the week
if (empty($motw_cached))
$HTMLOUT .= "<tr><td colspan='5'>No Movie of the week set!</td></tr></table></div><br />";
}
}
    //==End
    // End Class
     
    // End File
