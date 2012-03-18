<?php
    //== Best film of the week
    $categorie = genrelist();
    foreach($categorie as $key => $value)
    $change[$value['id']] = array('id' => $value['id'], 'name'  => $value['name'], 'image' => $value['image']);
    if(($motw_cache = $mc1->get_value('top_movie_')) === false) {
    $motw_cache = mysqli_fetch_assoc(sql_query("SELECT torrents.id, torrents.leechers, torrents.seeders, torrents.category, torrents.name, torrents.times_completed FROM torrents INNER JOIN avps ON torrents.id=avps.value_u WHERE avps.arg='bestfilmofweek' LIMIT 1"));
    $motw_cache['id'] = (int)$motw_cache['id'];
    $motw_cache['leechers'] = (int)$motw_cache['leechers'];
    $motw_cache['seeders'] = (int)$motw_cache['seeders'];
    $motw_cache['times_completed'] = (int)$motw_cache['times_completed'];
    $motw_cache['category'] = (int)$motw_cache['category'];
    $mc1->cache_value('top_movie_', $motw_cache, 0);
    }
     
    $motw_cache['cat_name'] = htmlspecialchars($change[$motw_cache['category']]['name']);
    $motw_cache['cat_pic'] = htmlspecialchars($change[$motw_cache['category']]['image']);
     
    if ($motw_cache)
    $HTMLOUT .="
    <div class='headline'>
    Movie of the Week</div>
    <div class='headbody'>
    <table align='center' border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='colhead' align='left'>Type</td>
    <td class='colhead' align='left'>Name</td>
    <td class='colhead' align='left'>Snatched</td>
    <td class='colhead' align='left'>Seeders</td>
    <td class='colhead' align='left'>Leechers</td></tr>
    <tr><td align='center'><img border='0' src='pic/caticons/{$CURUSER['categorie_icon']}/".htmlentities($motw_cache["cat_pic"])."' alt='".htmlentities($motw_cache["cat_name"])."' title='".htmlentities($motw_cache["cat_name"])."' /></td>
    <td style='padding-right: 5px'><a href='{$INSTALLER09['baseurl']}/details.php?id=".(int)$motw_cache["id"]."'><b>".htmlentities($motw_cache["name"])."</b></a></td>
    <td align='left'>".(int)$motw_cache["times_completed"]."</td>
    <td align='left'>".(int)$motw_cache["seeders"]."</td>
    <td align='left'>".(int)$motw_cache["leechers"]."</td></tr></table></div><br />";
    //==End
    // End Class
     
    // End File
