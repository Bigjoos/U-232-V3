<?php
//=== start snatched
$count_snatched = $count2 = $dlc = '';
if ($CURUSER['class'] >= UC_STAFF) {
    if (isset($_GET["snatched_table"])) {
        $HTMLOUT.= "<tr><td class='one' align='right' valign='top'><b>Snatched:</b><br />[ <a href=\"userdetails.php?id=$id\" class=\"sublink\">Hide list</a> ]</td><td class='one'>";
        $res = sql_query("SELECT sn.start_date AS s, sn.complete_date AS c, sn.last_action AS l_a, sn.seedtime AS s_t, sn.seedtime, sn.leechtime AS l_t, sn.leechtime, sn.downspeed, sn.upspeed, sn.uploaded, sn.downloaded, sn.torrentid, sn.start_date, sn.complete_date, sn.seeder, sn.last_action, sn.connectable, sn.agent, sn.seedtime, sn.port, cat.name, cat.image, t.size, t.seeders, t.leechers, t.owner, t.name AS torrent_name "."FROM snatched AS sn "."LEFT JOIN torrents AS t ON t.id = sn.torrentid "."LEFT JOIN categories AS cat ON cat.id = t.category "."WHERE sn.userid=".sqlesc($id)." ORDER BY sn.start_date DESC") or sqlerr(__FILE__, __LINE__);
        $HTMLOUT.= "<table border='1' cellspacing='0' cellpadding='5' align='center'><tr><td class='colhead' align='center'>Category</td><td class='colhead' align='left'>Torrent</td>"."<td class='colhead' align='center'>S / L</td><td class='colhead' align='center'>Up".($INSTALLER09['ratio_free'] ? "" : "/ Down")."</td><td class='colhead' align='center'>Torrent Size</td>"."<td class='colhead' align='center'>Ratio</td><td class='colhead' align='center'>Client</td></tr>";
        while ($arr = mysqli_fetch_assoc($res)) {
            //=======change colors
            $count2 = (++$count2) % 2;
            $class = ($count2 == 0 ? 'one' : 'two');
            //=== speed color red fast green slow ;)
            if ($arr["upspeed"] > 0) $ul_speed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
            else $ul_speed = mksize(($arr["uploaded"] / ($arr['l_a'] - $arr['s'] + 1)));
            if ($arr["downspeed"] > 0) $dl_speed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
            else $dl_speed = mksize(($arr["downloaded"] / ($arr['c'] - $arr['s'] + 1)));
            switch (true) {
            case ($dl_speed > 600):
                $dlc = 'red';
                break;

            case ($dl_speed > 300):
                $dlc = 'orange';
                break;

            case ($dl_speed > 200):
                $dlc = 'yellow';
                break;

            case ($dl_speed < 100):
                $dlc = 'Chartreuse';
                break;
            }
            if ($arr["downloaded"] > 0) {
                $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
                $ratio = "<font color='".get_ratio_color($ratio)."'><b>Ratio:</b><br />$ratio</font>";
            } else if ($arr["uploaded"] > 0) $ratio = "Inf.";
            else $ratio = "N/A";
            $HTMLOUT.= "<tr><td class='$class' align='center'>".($arr['owner'] == $id ? "<b><font color='orange'>Torrent owner</font></b><br />" : "".($arr['complete_date'] != '0' ? "<b><font color='lightgreen'>Finished</font></b><br />" : "<b><font color='red'>Not Finished</font></b><br />")."")."<img src='{$INSTALLER09['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/".htmlsafechars($arr['image'])."' alt='".htmlsafechars($arr['name'])."' title='".htmlsafechars($arr['name'])."' /></td>"."
    <td class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/details.php?id=".(int)$arr['torrentid']."'><b>".htmlsafechars($arr['torrent_name'])."</b></a>".($arr['complete_date'] != '0' ? "<br /><font color='yellow'>started: ".get_date($arr['start_date'], 0, 1)."</font><br />" : "<font color='yellow'>started:".get_date($arr['start_date'], 0, 1)."</font><br /><font color='orange'>Last Action:".get_date($arr['last_action'], 0, 1)."</font>".get_date($arr['complete_date'], 0, 1)." ".($arr['complete_date'] == '0' ? "".($arr['owner'] == $id ? "" : "[ ".mksize($arr["size"] - $arr["downloaded"])." still to go ]")."" : "")."")." Finished: ".get_date($arr['complete_date'], 0, 1)."".($arr['complete_date'] != '0' ? "<br /><font color='silver'>Time to download: ".($arr['leechtime'] != '0' ? mkprettytime($arr['leechtime']) : mkprettytime($arr['c'] - $arr['s'])."")."</font> <font color='$dlc'>[ DLed at: $dl_speed ]</font><br />" : "<br />")."<font color='lightblue'>".($arr['seedtime'] != '0' ? "Total seeding time: ".mkprettytime($arr['seedtime'])." </font><font color='$dlc'> " : "Total seeding time: N/A")."</font><font color='lightgreen'> [ up speed: ".$ul_speed." ] </font>".($arr['complete_date'] == '0' ? "<br /><font color='$dlc'>Download speed: $dl_speed</font>" : "")."</td>"."
    <td align='center' class='$class'>Seeds: ".(int)$arr['seeders']."<br />Leechers: ".(int)$arr['leechers']."</td><td align='center' class='$class'><font color='lightgreen'>Uploaded:<br /><b>".mksize($arr["uploaded"])."</b></font>".($INSTALLER09['ratio_free'] ? "" : "<br /><font color='orange'>Downloaded:<br /><b>".mksize($arr["downloaded"])."</b></font>")."</td><td align='center' class='$class'>".mksize($arr["size"])."".($INSTALLER09['ratio_free'] ? "" : "<br />Difference of:<br /><font color='orange'><b>".mksize($arr['size'] - $arr["downloaded"])."</b></font>")."</td><td align='center' class='$class'>".$ratio."<br />".($arr['seeder'] == 'yes' ? "<font color='lightgreen'><b>seeding</b></font>" : "<font color='red'><b>Not seeding</b></font>")."</td><td align='center' class='$class'>".htmlsafechars($arr["agent"])."<br />port: ".(int)$arr["port"]."<br />".($arr["connectable"] == 'yes' ? "<b>Connectable:</b> <font color='lightgreen'>Yes</font>" : "<b>Connectable:</b> <font color='red'><b>no</b></font>")."</td></tr>\n";
        }
        $HTMLOUT.= "</table></td></tr>\n";
    } else $HTMLOUT.= tr("<b>Snatched:</b><br />", "[ <a href=\"userdetails.php?id=$id&amp;snatched_table=1#snatched_table\" class=\"sublink\">Show</a> ]  - $count_snatched <font color='red'><b>staff only!!!</b></font>", 1);
}
//=== end snatched
// End Class
// End File
