<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');

dbconn();

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('users') );
    
    $search = isset($_GET['search']) ? strip_tags(trim($_GET['search'])) : '';
    $class = isset($_GET['class']) ? $_GET['class'] : '-';
    $letter = '';
    $q1 = '';
    if ($class == '-' || !ctype_digit($class))
      $class = '';

    if ($search != '' || $class)
    {
      $query1 = "username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
      if ($search)
          $q1 = "search=" . htmlspecialchars($search);
    }
    else
    {
      $letter = isset($_GET['letter']) ? trim((string)$_GET["letter"]) : '';
      if (strlen($letter) > 1)
        die;

      if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz0123456789", $letter) === false)
        $letter = "";
      $query1 = "username LIKE '$letter%' AND status='confirmed'";
      $q1 = "letter=$letter";
    }

    if (ctype_digit($class))
    {
      $query1 .= " AND class=$class";
      $q1 .= ($q1 ? "&amp;" : "") . "class=$class";
    }

    
    $HTMLOUT = '';
    
    $HTMLOUT  .= "<h1>Users</h1>\n";

    $HTMLOUT  .= "<form method='get' action='users.php?'>\n";
    $HTMLOUT  .= "{$lang['form_search']} <input type='text' size='30' name='search' />\n";
    $HTMLOUT  .= "<select name='class'>\n";
    $HTMLOUT  .= "<option value='-'>(any class)</option>\n";
    for ($i = 0;;++$i)
    {
      if ($c = get_user_class_name($i))
        $HTMLOUT  .= "<option value='$i'" . (ctype_digit($class) && $class == $i ? " selected='selected'" : "") . ">$c</option>\n";
      else
        break;
    }
    $HTMLOUT  .= "</select>\n";
    $HTMLOUT  .= "<input type='submit' value='{$lang['form_btn']}' class='btn' />\n";
    $HTMLOUT  .= "</form>\n";

    $HTMLOUT  .= "<br />\n";


      $aa = range('0','9');
      $bb = range('a','z');
      $cc = array_merge($aa,$bb);
      unset($aa,$bb);
      
      $HTMLOUT  .= "<div align='center'>";
      $count = 0;
      foreach($cc as $L) 
      {
        $HTMLOUT .= ($count == 10) ? "<br /><br />" : '';
        if(!strcmp($L,$letter))
          $HTMLOUT  .= "<span class='btn' style='background:orange;'>".strtoupper($L)."</span>\n";
        else
          $HTMLOUT  .= "<a href='users.php?letter=$L'><span class='btn'>".strtoupper($L)."</span></a>\n";
          $count++;
      }
      
    $HTMLOUT  .= "</div>";

    $HTMLOUT  .= "<br />\n";
      
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perpage = 25;
    $browsemenu = '';
    $pagemenu = '';

    $res = sql_query("SELECT COUNT(*) FROM users WHERE $query1") or sqlerr(__FILE__,__LINE__);
    $arr = mysqli_fetch_row($res);

    if($arr[0] > $perpage) {
    $pages = floor($arr[0] / $perpage);
    if ($pages * $perpage < $arr[0])
      ++$pages;

    if ($page < 1)
      $page = 1;
    else
      if ($page > $pages)
        $page = $pages;

    for ($i = 1; $i <= $pages; ++$i) 
    {
      $PageNo = $i+1;
      if($PageNo < ($page - 2))
        continue;
      
      if ($i == $page)
        $pagemenu .= "&nbsp;<span class='btn' style='background:orange;'>$i</span>\n";
      else
        $pagemenu .= "&nbsp;<a href='users.php?$q1&amp;page=$i'><span class='btn'>$i</span></a>\n";
      if($PageNo > ($page + 3)) break;
    }

    if ($page == 1)
      $browsemenu .= "<span class='btn' style='background:orange;'>&lsaquo;</span>$pagemenu";
    else
      $browsemenu .= "<a href='users.php?$q1&amp;page=1' title='{$lang['pager_first']}(1)'><span class='btn'>&laquo;</span></a>&nbsp;<a href='users.php?$q1&amp;page=" . ($page - 1) . "'><span class='btn'>&lsaquo;</span></a>$pagemenu";

    if ($page == $pages)
      $browsemenu .= "<span class='btn' style='background:orange;'>&rsaquo;</span>";
    else
      $browsemenu .= "<a href='users.php?$q1&amp;page=" . ($page + 1) . "'><span class='btn'>&rsaquo;</span></a>&nbsp;<a href='users.php?$q1&amp;page=" . $pages . "' title='{$lang['pager_last']}($pages)'><span class='btn'>&raquo;</span></a>";
    }

    $HTMLOUT .= ($arr[0] > $perpage) ? "<p>$browsemenu<br /><br /></p>" : '<br /><br />';

    $offset = ($page * $perpage) - $perpage;

    if($arr[0] > 0) {
        $res = sql_query("SELECT users.*, countries.name, countries.flagpic FROM users FORCE INDEX ( username ) LEFT JOIN countries ON country = countries.id WHERE $query1 ORDER BY username LIMIT $offset,$perpage") or sqlerr(__FILE__,__LINE__);

    $HTMLOUT  .= "<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT  .= "<tr><td class='colhead' align='left'>User name</td><td class='colhead'>{$lang['users_regd']}</td><td class='colhead'>{$lang['users_la']}</td><td class='colhead' align='left'>{$lang['users_class']}</td><td class='colhead'>{$lang['users_country']}</td></tr>\n";
    
    while($row = mysqli_fetch_assoc($res))
    {
      
      $country = ($row['name'] != NULL) ? "<td style='padding: 0px' align='center'><img src='{$INSTALLER09['pic_base_url']}flag/{$row['flagpic']}' alt='". htmlspecialchars($row['name']) ."' /></td>" : "<td align='center'>---</td>";
   
      $HTMLOUT .= "<tr><td align='left'><a href='userdetails.php?id={$row['id']}'><b>{$row['username']}</b></a>" .
      ($row["donor"] > 0 ? "<img src='{$INSTALLER09['pic_base_url']}star.gif' border='0' alt='Donor' />" : "")."</td>" .
      "<td>".get_date( $row['added'],'' )."</td><td>".get_date( $row['last_access'], '')."</td>".
        "<td align='left'>" . get_user_class_name($row["class"]) . "</td>$country</tr>\n";
    }
    $HTMLOUT  .= "</table>\n";
    }

    $HTMLOUT  .= ($arr[0] > $perpage) ? "<br /><p>$browsemenu</p>" : '<br /><br />';

    echo stdhead($lang['head_users']) . $HTMLOUT . stdfoot();
    die;

?>
