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
require_once(INCL_DIR.'html_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
dbconn();
loggedinorreturn();

$lang = array_merge( load_language('global') );

//$stdhead = array(/** include js **/'js' => array(''));
$stdhead = array(/** include css **/'css' => array('wiki'));

$HTMLOUT="";

global $CURUSER;

function newmsg($heading = '', $text = '', $div = 'success', $htmlstrip = false)
{
    if ($htmlstrip) {
        $heading = htmlspecialchars(trim($heading));
        $text = htmlspecialchars(trim($text));
    }
    $htmlout='';
    $htmlout.="<table class=\"main\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td class=\"embedded\">\n";
    $htmlout.="<div class=\"$div\">" . ($heading ? "<b>$heading</b><br />" : "") . "$text</div></td></tr></table>\n";
    return $htmlout;
}

function newerr($heading = '', $text = '', $die = true, $div = 'error', $htmlstrip = false)
{
    $htmlout='';
    $htmlout.= newmsg($heading, $text, $div, $htmlstrip);
    echo stdhead() .$htmlout . stdfoot();
    if ($die)
    die;
}

function datetimetransform($input)
{
    $todayh = getdate($input);

    if ($todayh["seconds"] < 10) {
        $todayh["seconds"] = "0" . $todayh["seconds"] . "";
    }
    if ($todayh["minutes"] < 10) {
        $todayh["minutes"] = "0" . $todayh["minutes"] . "";
    }
    if ($todayh["hours"] < 10) {
        $todayh["hours"] = "0" . $todayh["hours"] . "";
    }
    if ($todayh["mday"] < 10) {
        $todayh["mday"] = "0" . $todayh["mday"] . "";
    }
    if ($todayh["mon"] < 10) {
        $todayh["mon"] = "0" . $todayh["mon"] . "";
    }
    $sec = $todayh['seconds'];
    $min = $todayh['minutes'];
    $hours = $todayh['hours'];
    $d = $todayh['mday'];
    $m = $todayh['mon'];
    $y = $todayh['year'];

    $input = "$d-$m-$y $hours:$min:$sec";
    return $input;
}

function navmenu()
{
    $ret = '<div id="wiki-navigation" align="center"><div><a href="wiki.php">Index</a> - <a href="wiki.php?action=add">Add</a></div><div align="right"><form action="wiki.php" method="post">';
    $ret .= "\n" . '<a href="wiki.php?action=sort&amp;letter=a">A</a>';
    for($i = 0;$i < 25;$i++) {
        $ret .= "\n- " . '<a href="wiki.php?action=sort&amp;letter=' . chr($i + 98) . '">' . chr($i + 66) . '</a>';
    }
    $ret .= "\n" . '<input type="text" name="article" /> <input type="submit" value="Search" name="wiki" /></form></div></div>';
    return $ret;
}

function articlereplace($input)
{
    $input = str_replace(" ", "+", $input);
    return $input;
}

function wikisearch($input)
{
    return str_replace(array("%", "_"), array("\\%", "\\_"), ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $input) : ((trigger_error("Error", E_USER_ERROR)) ? "" : "")));
}

function wikireplace($input)
{
    return preg_replace(array('/\[\[(.+?)\]\]/i', '/\=\=\ (.+?)\ \=\=/i'), array('<a href="wiki.php?action=article&name=$1">$1</a>', '<div id="$1" style="border-bottom: 1px solid grey; font-weight: bold; width: 100%; font-size: 14px;">$1</div>'), $input);
}

function wikimenu()
{
    $res2 = sql_query("SELECT name FROM wiki ORDER BY id DESC LIMIT 1");
    $latest = mysqli_fetch_assoc($res2);
    $latestarticle = articlereplace($latest["name"]);
    $ret = "<div id=\"wiki-content-right\">
					<div id=\"details\">
						<ul>
							<li><b>Permissions:</b></li></ul>
							Read: User<br />
							Write: User<br />
							Edit: Staff
							<ul><li><b>Latest Article:</b></li></ul>
							<a href=\"wiki.php?action=article&amp;name=$latestarticle\">".htmlspecialchars($latest['name'])."</a>
					</div>
				</div>
		";
    return $ret;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["article-add"])) {
        $name = sqlesc($_POST["article-name"]);
        $body = sqlesc($_POST["article-body"]);
        sql_query("INSERT INTO `wiki` ( `name` , `body` , `userid`, `time` )
VALUES ($name, $body, '" . $CURUSER["id"] . "', '" . TIME_NOW . "')") or sqlerr(__FILE__, __LINE__);
        $HTMLOUT .="<meta http-equiv=\"refresh\" content=\"0; url=wiki.php?action=article&name=" . $_POST["article-name"] . "\">";
    }
    if (isset($_POST["article-edit"])) {
        $id = $_POST["article-id"];
        $name = sqlesc($_POST["article-name"]);
        $body = sqlesc($_POST["article-body"]);
        sql_query("UPDATE wiki SET name = $name, body = $body, lastedit = '" . TIME_NOW . "', lastedituser = '" . $CURUSER["id"] . "' WHERE id = $id");
        $HTMLOUT .="<meta http-equiv=\"refresh\" content=\"0; url=wiki.php?action=article&name=" . $_POST["article-name"] . "\">";
    }
    if (isset($_POST["wiki"])) {
        $wikisearch = articlereplace($_POST["article"]);
        $HTMLOUT .="<meta http-equiv=\"refresh\" content=\"0; url=wiki.php?action=article&name=$wikisearch\">";
    }
}

$HTMLOUT .= begin_main_frame();

                $HTMLOUT .="
        <div class='global_icon'><img src='images/global.design/wiki.png' alt='' title='Wiki' class='global_image' width='25'/></div>
        <div class='global_head_wiki'>Wiki</div><br />
        <div class='global_text'><br />";
if (isset($_GET["action"])) {
    $action = htmlspecialchars($_GET["action"]);
    if (isset($_GET["name"])) {
        $mode = "name";
        $name = htmlspecialchars($_GET["name"]);
    }
    if (isset($_GET["id"])) {
        $mode = "id";
        $id = (int)$_GET["id"];
        if (!is_valid_id($id))
        die();
        }
    
if (isset($_GET["letter"]))
        $letter = htmlspecialchars($_GET["letter"]);
} else {
    $action = "article";
    $mode = "name";
    $name = "index";
}

if ($action == "article") {
    $res = sql_query("SELECT * FROM wiki WHERE $mode = '" . ($mode == "name" ? "$name" : "$id") . "'");
    if (mysqli_num_rows($res) == 1) {
        $HTMLOUT .=navmenu();
        $edit='';
        $HTMLOUT .="
        <div id=\"wiki-container\">
  <div id=\"wiki-row\">";
        while ($wiki = mysqli_fetch_array($res)) {
            if ($wiki['lastedit']) {
                $check = sql_query("SELECT username FROM users WHERE id = $wiki[lastedituser]");
                $checkit = mysqli_fetch_assoc($check);
                $edit = "<i>Last Updated by: <a href=\"userdetails.php?id=$wiki[userid]\">$checkit[username]</a> - " . datetimetransform($wiki['lastedit']) . "</i>";
            }
            $check = sql_query("SELECT username FROM users WHERE id = $wiki[userid]");
            $author = mysqli_fetch_assoc($check);
            $HTMLOUT .="
				<div id=\"wiki-content-left\" align=\"right\">
					<div id=\"name\"><b><a href=\"wiki.php?action=article&amp;name=$wiki[name]\">$wiki[name]</a></b></div>
					<div id=\"content\">" . ($wiki['userid'] > 0 ? "<font style=\"color: grey; font-size: 9px;\"><i>Article added by <a href=\"userdetails.php?id=$wiki[userid]\"><b>$author[username]</b></a></i></font><br /><br />" : "") . wikireplace(format_comment($wiki["body"])) . "";
            $HTMLOUT .="<div align=\"right\">" . ($edit ? "<font style=\"color: grey; font-size: 9px;\">$edit</font>" : "") . ($CURUSER['class'] >= UC_STAFF || $CURUSER["id"] == $wiki["userid"] ? " - <a href=\"wiki.php?action=edit&amp;id=$wiki[id]\">Edit</a>" : "") . "</div>";
            $HTMLOUT .="</div></div>";
        }

        $HTMLOUT .=wikimenu();

        $HTMLOUT .="</div>";
        $HTMLOUT .="</div>";
    } else {
        $search = sql_query("SELECT * FROM wiki WHERE name LIKE '%" . wikisearch($name) . "%'");
        if (mysqli_num_rows($search) > 0) {
            $HTMLOUT .="Search results for: <b>".htmlspecialchars($name)."</b>";
            while ($wiki = mysqli_fetch_array($search)) {
                if ($wiki["userid"] !== 0)
                    $wikiname = mysqli_fetch_assoc(sql_query("SELECT username FROM users WHERE id = $wiki[userid]"));
                $HTMLOUT .="
				<div class=\"wiki-search\">
					<b><a href=\"wiki.php?action=article&amp;name=" . articlereplace($wiki["name"]) . "\">$wiki[name]</a></b> Added by: <a href=\"userdetails.php?id=$wiki[userid]\">$wikiname[username]</a></div>";
            }
        } else {
            $HTMLOUT .=newerr("Error", "No article found.");
        }
    }
}
$wiki=0;
if ($action == "add") {
    $HTMLOUT .=navmenu();
     
    $HTMLOUT .="<div id=\"wiki-container\">
  <div id=\"wiki-row\">";
     $HTMLOUT .="
				<div id=\"wiki-content-left\" align=\"right\">
					<form method=\"post\" action=\"wiki.php\">
					<div><input type=\"text\" name=\"article-name\" id=\"name\" /></div>
					<div id=\"content-add\"><textarea name=\"article-body\" rows=\"70\" cols=\"10\" id=\"body\">$wiki[body]</textarea>
					<div align=\"center\"><input type=\"submit\" name=\"article-add\" value=\"OK\" /></div>
				</div></form></div>";

    $HTMLOUT .=wikimenu();

    $HTMLOUT .="</div>";
    $HTMLOUT .="</div>";
}

if ($action == "edit") {
    $res = sql_query("SELECT * FROM wiki WHERE id = $id");
    $rescheck = sql_query("SELECT userid FROM wiki WHERE id = $id");

    $wikicheck = mysqli_fetch_assoc($rescheck);
    if (($CURUSER['class'] >= UC_STAFF) OR ($CURUSER["id"] == $wikicheck["userid"])) {
        $HTMLOUT .=navmenu();

        $HTMLOUT .="<div id=\"wiki-container\">
  <div id=\"wiki-row\">";
        while ($wiki = mysqli_fetch_array($res)) {
           $HTMLOUT .="
				<div id=\"wiki-content-left\" align=\"right\">
					<form method=\"post\" action=\"wiki.php\">
					<div><input type=\"hidden\" name=\"article-id\" value=\"$wiki[id]\" />
					<input type=\"text\" name=\"article-name\" id=\"name\" value=\"$wiki[name]\" /></div>
					<div id=\"content-add\"><table width=\"100%\" style=\"height: 100%;\" id=\"wikiedit\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><textarea name=\"article-body\" rows=\"70\" cols=\"10\" id=\"body\">$wiki[body]</textarea>
					<div align=\"center\"><input type=\"submit\" name=\"article-edit\" value=\"Edit\" /></div></td></tr></table>";
           $HTMLOUT .="</div></form></div>";
        }

        $HTMLOUT .=wikimenu();

        $HTMLOUT .="</div>";
        $HTMLOUT .="</div>";
    } else
        $HTMLOUT .=newerr("Error", "Access Denied");
}

if ($action == "sort") {
    $sortres = sql_query("SELECT * FROM wiki WHERE name LIKE '$letter%' ORDER BY name");
    if (mysqli_num_rows($sortres) > 0) {
        $HTMLOUT .=navmenu();
        $HTMLOUT .="Articles starting with the letter <b>".htmlspecialchars($letter)."</b>";
        while ($wiki = mysqli_fetch_array($sortres)) {
            if ($wiki["userid"] !== 0)
                $wikiname = mysqli_fetch_assoc(sql_query("SELECT username FROM users WHERE id = $wiki[userid]"));
           $HTMLOUT .="
				<div class=\"wiki-search\">
					<b><a href=\"wiki.php?action=article&amp;name=" . articlereplace($wiki["name"]) . "\">$wiki[name]</a></b> Added by: <a href=\"userdetails.php?id=$wiki[userid]\">$wikiname[username]</a></div>";
        }
    } else {
        $HTMLOUT .= navmenu();
        $HTMLOUT .= newerr("Error", "No articles starting with letter <b>$letter</b> found.");
    }
}
$HTMLOUT .="</div>";
$HTMLOUT .= end_main_frame();
echo stdhead("Wiki" , true, $stdhead) . $HTMLOUT . stdfoot();
?>
