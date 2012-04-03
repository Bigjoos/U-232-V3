<?php
// 09 poster mod
    if(($scroll_torrents = $mc1->get_value('scroll_tor_')) === false) {
    $scroll = sql_query("SELECT id, seeders, leechers, name, poster FROM torrents WHERE seeders >= '1' ORDER BY added DESC LIMIT {$INSTALLER09['latest_torrents_limit_scroll']}") or sqlerr(__FILE__, __LINE__);
    while($scroll_torrent = mysqli_fetch_assoc($scroll))
    $scroll_torrents[] = $scroll_torrent;
    $mc1->cache_value('scroll_tor_', $scroll_torrents, $INSTALLER09['expires']['scroll_torrents']);
    }

    if (count($scroll_torrents) > 0)
    {
    $HTMLOUT .="<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/scroll.js'></script>";
    $HTMLOUT .= "<div class='headline'>{$lang['index_latest']}</div>
    <div class='headbody'>
    <div style=\"overflow:hidden\">
    <div id=\"marqueecontainer\" onmouseover=\"copyspeed=pausespeed\" onmouseout=\"copyspeed=marqueespeed\"> 
    <span id=\"vmarquee\" style=\"position: absolute; width: 98%;\"><span style=\"white-space: nowrap;\">";
    
    if ($scroll_torrents)
    {
    foreach($scroll_torrents as $s_t) {
     $i = $INSTALLER09['latest_torrents_limit_scroll'];
        $id = (int) $s_t['id'];
        $name = htmlsafechars($s_t['name']);
        $poster = ($s_t['poster'] == '' ? ''.$INSTALLER09['pic_base_url'].'noposter.png' : htmlsafechars( $s_t['poster'] ));
        $seeders = number_format((int)$s_t['seeders']);
        $leechers = number_format((int)$s_t['leechers']);
        $name = str_replace( '_', ' ' , $name );
        $name = str_replace( '.', ' ' , $name );
        $name = substr( $name, 0, 50 );
        if ( $i == 0 )
        $HTMLOUT .= "</span></span><span id=\"vmarquee2\" style=\"position: absolute; width: 98%;\"></span></div></div><div style=\"overflow:hidden\"><div id=\"marqueecontainer\" onmouseover=\"copyspeed=pausespeed\" onmouseout=\"copyspeed=marqueespeed\"> <span id=\"vmarquee\" style=\"position: absolute; width: 98%;\"><span style=\"white-space: nowrap;\">";
        $HTMLOUT .= "<a href='{$INSTALLER09['baseurl']}/details.php?id=$id'><img src='" . htmlsafechars($poster) . "' alt='{$name}' title='{$name} - Seeders : {$seeders} - Leechers : {$leechers}' width='100' height='120' border='0' /></a>&nbsp;&nbsp;&nbsp;";
        $i++;
    }
    $HTMLOUT .= "</span></span><span id=\"vmarquee2\" style=\"position: absolute; width: 98%;\"></span></div></div></div><br />\n";
    } else {
    //== If there are no torrents
    if (empty($scroll_torrents))
    $HTMLOUT .= "No torrents here yet</div></div></div><br />";
    }
    }
//== end 09 poster mod
// End Class

// End File
