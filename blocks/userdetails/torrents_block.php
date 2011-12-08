<?php
function snatchtable($res) {
global $INSTALLER09, $lang, $CURUSER;
$htmlout = '';
 $htmlout = "<table class='main' border='1' cellspacing='0' cellpadding='5'>
 <tr>
 <td class='colhead'>Category</td>
 <td class='colhead'>Torrent</td>
 <td class='colhead'>Up.</td>
 <td class='colhead'>Rate</td>
 <td class='colhead'>Downl.</td>
 <td class='colhead'>Rate</td>
 <td class='colhead'>Ratio</td>
 <td class='colhead'>Activity</td>
 <td class='colhead'>Finished</td>
 </tr>";

 while ($arr = mysqli_fetch_assoc($res)) {

 $upspeed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
 $downspeed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
 $ratio = ($arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 3) : ($arr["uploaded"] > 0 ? "Inf." : "---"));

 $htmlout .= "<tr>
 <td style='padding: 0px'><img src='{$INSTALLER09['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/".htmlspecialchars($arr["catimg"])."' alt='".htmlspecialchars($arr["catname"])."' width='42' height='42' /></td>
 <td><a href='details.php?id=$arr[torrentid]'><b>".(strlen($arr["name"]) > 50 ? substr($arr["name"], 0, 50 - 3)."..." : $arr["name"])."</b></a></td>
 <td>".mksize($arr["uploaded"])."</td>
 <td>$upspeed/s</td>
 <td>".mksize($arr["downloaded"])."</td>
 <td>$downspeed/s</td>
 <td>$ratio</td>
 <td>".mkprettytime($arr["seedtime"] + $arr["leechtime"])."</td>
 <td>".($arr["complete_date"] <> "0" ? "<font color='green'><b>Yes</b></font>" : "<font color='red'><b>No</b></font>")."</td>
 </tr>\n";
 }
 $htmlout .= "</table>\n";

 return $htmlout;
}

function maketable($res)
    {
      global $INSTALLER09, $lang, $CURUSER;
      
      $htmlout = '';
      
      $htmlout .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>" .
        "<tr><td class='colhead' align='center'>{$lang['userdetails_type']}</td><td class='colhead'>{$lang['userdetails_name']}</td><td class='colhead' align='center'>{$lang['userdetails_size']}</td><td class='colhead' align='right'>{$lang['userdetails_se']}</td><td class='colhead' align='right'>{$lang['userdetails_le']}</td><td class='colhead' align='center'>{$lang['userdetails_upl']}</td>\n" .
        "<td class='colhead' align='center'>{$lang['userdetails_downl']}</td><td class='colhead' align='center'>{$lang['userdetails_ratio']}</td></tr>\n";
      foreach ($res as $arr)
      {
        if ($arr["downloaded"] > 0)
        {
          $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
          $ratio = "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
        }
        else
          if ($arr["uploaded"] > 0)
            $ratio = "{$lang['userdetails_inf']}";
          else
            $ratio = "---";
      $catimage = "{$INSTALLER09['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/{$arr['image']}";
      $catname = htmlspecialchars($arr["catname"]);
      $catimage = "<img src=\"".htmlspecialchars($catimage) ."\" title=\"$catname\" alt=\"$catname\" width='42' height='42' />";
      $size = str_replace(" ", "<br />", mksize($arr["size"]));
      $uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
      $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
      $seeders = number_format($arr["seeders"]);
      $leechers = number_format($arr["leechers"]);
        $htmlout .= "<tr><td style='padding: 0px'>$catimage</td>\n" .
        "<td><a href='details.php?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr["torrentname"]) .
        "</b></a></td><td align='center'>$size</td><td align='right'>$seeders</td><td align='right'>$leechers</td><td align='center'>$uploaded</td>\n" .
        "<td align='center'>$downloaded</td><td align='center'>$ratio</td></tr>\n";
      }
      $htmlout .= "</table>\n";
      return $htmlout;
    }
    
    if ($user['paranoia'] < 2 ||  $user['hidecur'] == "yes" || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    if (isset($torrents))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_uploaded_t']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica\" alt=\"Show/Hide\" /></a><div id=\"ka\" style=\"display: none;\">$torrents</div></td></tr>\n";
    /*
    if (isset($torrents)) {    
       $HTMLOUT .= "   <tr valign=\"top\">    
                        <td class=\"rowhead\" width=\"10%\">
                         {$lang['userdetails_uploaded_t']}   
                      </td>    
                      <td align=\"left\" width=\"90%\">    
                         <a href=\"#\" id=\"slick-toggle\">Show/Hide</a>       
                         <div id=\"slickbox\" style=\"display: none;\">{$torrents}</div>    
                      </td>    
                   </tr>";    
    } 
    */
    /*
    if (isset($seeding)) {    
       $HTMLOUT .= "   <tr valign=\"top\">    
                        <td class=\"rowhead\" width=\"10%\">
                         {$lang['userdetails_cur_seed']} 
                      </td>    
                      <td align=\"left\" width=\"90%\">    
                         <a href=\"#\" id=\"slick-toggle\">Show/Hide</a>       
                         <div id=\"slickbox\" style=\"display: none;\">".maketable($seeding)."</div>    
                      </td>    
                   </tr>";    
    } 
    */
    if (isset($seeding))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_seed']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a1')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica1\" alt=\"Show/Hide\" /></a><div id=\"ka1\" style=\"display: none;\">".maketable($seeding)."</div></td></tr>\n";
    /*
    if (isset($leeching)) {    
       $HTMLOUT .= "   <tr valign=\"top\">    
                        <td class=\"rowhead\" width=\"10%\">
                         {$lang['userdetails_cur_leech']}
                      </td>    
                      <td align=\"left\" width=\"90%\">    
                         <a href=\"#\" id=\"slick-toggle\">Show/Hide</a>       
                         <div id=\"slickbox\" style=\"display: none;\">".maketable($leeching)."</div>    
                      </td>    
                   </tr>";    
    }
    */
    if (isset($leeching))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_leech']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a2')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica2\" alt=\"Show/Hide\" /></a><div id=\"ka2\" style=\"display: none;\">".maketable($leeching)."</div></td></tr>\n";
    //==Snatched
    $user_snatches_data = $mc1->get_value('user_snatches_data_'.$id);
    if ($user_snatches_data === false) {
    $ressnatch = sql_query("SELECT s.*, t.name AS name, c.name AS catname, c.image AS catimg FROM snatched AS s INNER JOIN torrents AS t ON s.torrentid = t.id LEFT JOIN categories AS c ON t.category = c.id WHERE s.userid = $user[id]") or sqlerr(__FILE__, __LINE__);
    $user_snatches_data = snatchtable($ressnatch);
    $mc1->cache_value('user_snatches_data_'.$id, $user_snatches_data, $INSTALLER09['expires']['user_snatches_data']);
    }
    /*
    if (isset($user_snatches_data)) 
       $HTMLOUT .= "   <tr valign=\"top\">    
                        <td class=\"rowhead\" width=\"10%\">
                         {$lang['userdetails_cur_snatched']}
                      </td>    
                      <td align=\"left\" width=\"90%\">    
                         <a href=\"#\" id=\"slick-toggle\">Show/Hide</a>       
                         <div id=\"slickbox\" style=\"display: none;\">$user_snatches_data</div>    
                      </td>    
                   </tr>";    
    //}
    */
    if (isset($user_snatches_data))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_snatched']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"pic/plus.png\" id=\"pica3\" alt=\"Show/Hide\" /></a><div id=\"ka3\" style=\"display: none;\">$user_snatches_data</div></td></tr>\n";
    }
//==End
// End Class

// End File