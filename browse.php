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
require_once INCL_DIR.'torrenttable_functions.php';
require_once INCL_DIR.'pager_functions.php';
require_once(INCL_DIR.'searchcloud_functions.php');
require_once(INCL_DIR.'function_subcat.php');
dbconn(false);
loggedinorreturn();

if (isset($_GET['clear_new']) && $_GET['clear_new'] == '1'){
    sql_query("UPDATE users SET last_browse=".TIME_NOW." WHERE id=".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
    $mc1->update_row(false, array('last_browse' => TIME_NOW));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$CURUSER['id']);
    $mc1->update_row(false, array('last_browse' => TIME_NOW));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    header("Location: {$INSTALLER09['baseurl']}/browse.php");
    }

    $stdfoot = array(/** include js **/'js' => array('java_klappe','wz_tooltip','browse'));
    $stdhead = array(/** include css **/'css' => array('browse'));
    $lang = array_merge( load_language('global'), load_language('browse'), load_language('torrenttable_functions') );
    if (function_exists('parked'))
    parked();
    $HTMLOUT = $searchin = $select_searchin = $where = $addparam = $new_button = '';
    
    $cats = genrelist2();
	
    if(isset($_GET["search"])) 
    {
      $searchstr = sqlesc($_GET["search"]);
      $cleansearchstr = searchfield($searchstr);
      if (empty($cleansearchstr))
        unset($cleansearchstr);
    }
	
	$valid_searchin = array('title'=>array('name'),'descr'=>array('descr'),'genre'=>array('newgenre'),'all'=>array('name','newgenre','descr'));
    if(isset($_GET['searchin']) && isset($valid_searchin[$_GET['searchin']])) {
		$searchin = $valid_searchin[$_GET['searchin']];
		$select_searchin = $_GET['searchin'];
    $addparam .= sprintf('search=%s&amp;searchin=%s&amp;',$searchstr,$select_searchin);
	}
	
    if (isset($_GET['sort']) && isset($_GET['type'])) {
    $column = $ascdesc = '';
    $_valid_sort = array('id','name','numfiles','comments','added','size','times_completed','seeders','leechers','owner');
    $column = isset($_GET['sort']) && isset($_valid_sort[(int)$_GET['sort']]) ? $_valid_sort[(int)$_GET['sort']] : $_valid_sort[0];

    switch (htmlsafechars($_GET['type'])) {
        case 'asc': $ascdesc = "ASC";
            $linkascdesc = "asc";
            break;
        case 'desc': $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
        default: $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
    }

    $orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
    $pagerlink = "sort=" . intval($_GET['sort']) . "&amp;type=" . $linkascdesc . "&amp;";
    } else {
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $pagerlink = "";
    }

    
    $wherea = array();
    $wherecatina = array();

    if (isset($_GET["incldead"]) &&  $_GET["incldead"] == 1)
    {
      $addparam .= "incldead=1&amp;";
      if (!isset($CURUSER) || $CURUSER["class"] < UC_ADMINISTRATOR)
        $wherea[] = "banned != 'yes'";
    }
    else
    {
      if (isset($_GET["incldead"]) && $_GET["incldead"] == 2)
      {
      $addparam .= "incldead=2&amp;";
        $wherea[] = "visible = 'no'";
      }
      else
        $wherea[] = "visible = 'yes'";
    }
   
    //=== added an only free torrents option \\o\o/o//     
    if (isset($_GET['only_free']) &&  $_GET['only_free'] == 1)
    {     
    $wherea[] = "free >= '1'";
    $addparam .= "only_free=1&amp;";   
     }
  
    $category = (isset($_GET["cat"])) ? (int)$_GET["cat"] : false;
    
    $all = isset($_GET["all"]) ? $_GET["all"] : false;

    if (!$all)
    {
    if (!$_GET && $CURUSER["notifs"])
    {
    $i = 0;
    
    foreach ($cats as $cat)
    {
    $subcats = $cat['subcategory'];
    if (count($subcats) > 0)
    {
    foreach ($subcats as $subcat)
    {
    if (strpos($CURUSER["notifs"], "[cat{$subcat['id']}]") !== false)
    {
    $wherecatina[] = $subcat['id'];
    $addparam .= "cats$cat[tabletype][]=$subcat[id]&amp;";
    }
    }
    }

    if (count($subcats) > 0)
    {
    foreach ($subcats as $subcat)
    {
    if ( in_array($subcat['id'],$wherecatina) )
    {
    $cats[$i]['checked'] = true;
    }
    else
    {
    $cats[$i]['checked'] = false;
    break;
    }
    }
    }
    $i++;
    }
    }
    elseif ($_GET)
    {
    $i = 0;
    if(count($cats) > 0);
    foreach ($cats as $cat)
    {
    $categoriesarray = isset($_GET["cats".$cat['tabletype']]) && is_array($_GET["cats".$cat['tabletype']]) ? $_GET["cats".$cat['tabletype']] : array();
    if (count($categoriesarray) > 0)
    {
    foreach ($categoriesarray as $category)
    {
    if (!is_valid_id($category))
    stderr("Browse Error", "Not valid browse category");
    $wherecatina[] = $category;
    $addparam .= "cats$cat[tabletype][]=$category&amp;";
    }
    }

    $subcats = $cat['subcategory'];
    
    if (count($subcats) > 0)
    {
    foreach ($subcats as $subcat)
    {
    if ( in_array($subcat['id'],$wherecatina) )
    {
    $cats[$i]['checked'] = true;
    }
    else
    {
    $cats[$i]['checked'] = false;
    break;
    }
    }
    }
    $i++;
    }

    if(isset($_GET['cat']))
    {
    $getcategory = 0 + $_GET['cat'];

    if (!is_valid_id($getcategory))
    stderr("Browse Error", "Not valid browse category");

    if(count($cats) > 0);
    foreach ($cats as $cat)
    {
    $subcats = $cat['subcategory'];

    if (count($subcats) > 0)
    {
    foreach ($subcats as $subcat)
    {
    if ($subcat['id'] == $getcategory)
    {
    $wherecatina[] = $getcategory;
    $addparam .= "cats$cat[tabletype][]=$getcategory&amp;";
    break;
    }
    }
    }
    if ($subcat['id'] == $getcategory)
    break;
    }
    }
    }
    }else{
    $i = 0;
    if(count($cats) > 0);
    foreach ($cats as $cat)
    {
    $subcats = $cat['subcategory'];
 
    if (count($subcats) > 0)
    {
    $cats[$i]['checked'] = true;
    }
    $i++;
    }
    }

    if (count($wherecatina) > 1)
      $wherea[] = 'category IN ('.join(', ',$wherecatina).') ';
    elseif (count($wherecatina) == 1)
      $wherea[] = 'category ='.$wherecatina[0];

    if (isset($cleansearchstr ) ) {
		  $orderby = 'ORDER BY id DESC';
		  $searcha = explode(' ', $cleansearchstr);
		//==Memcache search cloud by putyn
      searchcloud_insert($cleansearchstr);
      //==
		  foreach($searcha as $foo) {
			  foreach($searchin as $boo)
				  $searchincrt[] = sprintf('%s LIKE \'%s\'',$boo,'%'.$foo.'%');	
		  }
        $wherea[] = join(' OR ',$searchincrt);
    }

    $where = count($wherea) ? 'WHERE '.join(' AND ',$wherea) : '';
		  
    $res = sql_query("SELECT COUNT(id) FROM torrents $where") or sqlerr(__FILE__, __LINE__);
    $row = mysqli_fetch_row($res);
    $count = $row[0];
    
    
    $torrentsperpage = $CURUSER["torrentsperpage"];
    if (!$torrentsperpage)
      $torrentsperpage = 15;

    if ($count)
    {
      if ($addparam != "") {
            if ($pagerlink != "") {
                if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
                    $addparam = $addparam . "&" . $pagerlink;
                } else {
                    $addparam = $addparam . $pagerlink;
                }
            }
        } else {
            $addparam = $pagerlink;
        }
      $pager = pager($torrentsperpage, $count, "browse.php?" . $addparam);

    $query = "SELECT id, search_text, category, leechers, seeders, bump, release_group, subs, name, times_completed, size, added, poster, descr, type, free, silver, comments, numfiles, filename, anonymous, sticky, nuked, vip, nukereason, newgenre, description, owner, username, youtube, checked_by, IF(nfo <> '', 1, 0) as nfoav," .
    "IF(num_ratings < {$INSTALLER09['minvotes']}, NULL, ROUND(rating_sum / num_ratings, 1)) AS rating ".
    "FROM torrents $where $orderby {$pager['limit']}";
    $res = sql_query($query) or sqlerr(__FILE__, __LINE__);
    }
    else
    {
      unset($res);
    }
    
    if (isset($cleansearchstr))
      $title = "{$lang['browse_search']}\"$searchstr\"";
    else
      $title = '';

    if ($CURUSER['viewscloud'] === "yes") {
    $HTMLOUT .= "<div class='article' align='center'><div id='wrapper1' style='width:80%;border:1px solid black;background-color:pink;' align='center'>";
    //== print out the tag cloud
    $HTMLOUT .= cloud() . "
    </div>";
    }
    
     //== clear new tag manually
    if ($CURUSER['clear_new_tag_manually'] == 'yes') {     
    $new_button ="<a href='?clear_new=1'><input type='submit' value='{$lang['clear_new_btn']}' class='btn' /></a>";
    } else {     
    //== clear new tag automatically 
    sql_query("UPDATE users SET last_browse=".TIME_NOW." where id=".sqlesc($CURUSER['id']));
    $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
    $mc1->update_row(false, array('last_browse' => TIME_NOW));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$CURUSER['id']);
    $mc1->update_row(false, array('last_browse' => TIME_NOW));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
    }
    
    $HTMLOUT .= "<br /><br />
    <form method='get' action='browse.php'>";
    $cattable = categories_table($cats, $wherecatina, "browse.php");
    $HTMLOUT .= ($cattable);
    $HTMLOUT .= "<br /><table width='75%' class='main' border='0' cellspacing='0' cellpadding='0'>
<tr><td class='embedded'>";
    $HTMLOUT .= "<p align='center'>
    {$lang['search_search']}
    <input type='text' name='search' size='40' value='".(isset($cleansearchstr) ? $cleansearchstr : '')."' />";
    $selected = (isset($_GET["incldead"])) ? (int)$_GET["incldead"] : "";
    //=== only free option :o)
    $only_free = ((isset($_GET['only_free'])) ? intval($_GET['only_free']) : '');
    //=== checkbox for only free torrents
    $only_free_box = '<input type="checkbox" name="only_free" value="1"'.(isset($_GET['only_free']) ? ' checked="checked"' : '').' /> Only Free Torrents ';
    $deadcheck = "";
    $deadcheck .=" in: <select name='incldead'>
    <option value='0'>{$lang['browse_active']}</option>
    <option value='1'".($selected == 1 ? " selected='selected'" : "").">{$lang['browse_inc_dead']}</option>
    <option value='2'".($selected == 2 ? " selected='selected'" : "").">{$lang['browse_dead']}</option>
    </select>";
	  $searchin = ' by: <select name="searchin">';
		foreach(array('title'=>'Name','descr'=>'Description','genre'=>'Genre','all'=>'All') as $k=>$v)
			$searchin .= '<option value="'.$k.'" '.($select_searchin == $k ? 'selected=\'selected\'' : '').'>'.$v.'</option>';
		$searchin .= '</select>';
    $HTMLOUT .= $searchin.'&nbsp;'.$deadcheck.'&nbsp;'.$only_free_box;
    $HTMLOUT .= "<input type='submit' value='{$lang['search_search_btn']}' class='btn' />";
    $HTMLOUT .="</p>
     </td></tr></table></form><br />";
    
    $HTMLOUT .="{$new_button}";
   
    if (isset($cleansearchstr))
    {
      $HTMLOUT .= "<h2>{$lang['browse_search']}\"" . htmlsafechars($searchstr, ENT_QUOTES) . "\"</h2>\n";
    }
    
    if ($count) 
    {
      $HTMLOUT .= $pager['pagertop'];
      $HTMLOUT .="<br />";
      $HTMLOUT .= torrenttable($res);
      $HTMLOUT .= $pager['pagerbottom'];
    }
    else 
    {
      if (isset($cleansearchstr)) 
      {
        $HTMLOUT .= "<h2>{$lang['browse_not_found']}</h2>\n";
        $HTMLOUT .= "<p>{$lang['browse_tryagain']}</p>\n";
      }
      else 
      {
        $HTMLOUT .= "<h2>{$lang['browse_nothing']}</h2>\n";
        $HTMLOUT .= "<p>{$lang['browse_sorry']}(</p>\n";
      }
    }
  
$ip = sqlesc(getip());
//== Start ip logger - Melvinmeow, Mindless, pdq
$no_log_ip = ($CURUSER['perms'] & bt_options::PERMS_NO_IP);
if ($no_log_ip) {
   $ip = '127.0.0.1';
}
         if (!$no_log_ip) {
           $userid = (int)$CURUSER['id'];
           $added = TIME_NOW;
           $res = sql_query("SELECT * FROM ips WHERE ip = ".sqlesc($ip)." AND userid = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
           if (mysqli_num_rows($res) == 0 ) {
           sql_query("INSERT INTO ips (userid, ip, lastbrowse, type) VALUES (".sqlesc($userid).", ".sqlesc($ip).", $added, 'Browse')") or sqlerr(__FILE__, __LINE__);
           $mc1->delete_value('ip_history_'.$userid);
        }
        else {
            sql_query("UPDATE ips SET lastbrowse = $added WHERE ip=".sqlesc($ip)." AND userid = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
            $mc1->delete_value('ip_history_'.$userid);
           }
          }
        //== End Ip logger

echo stdhead($title, true, $stdhead) . $HTMLOUT . stdfoot($stdfoot);
?>
