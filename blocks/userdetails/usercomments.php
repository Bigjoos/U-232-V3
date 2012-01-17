<?php
function usercommenttable($rows)
{
    $htmlout='';
    global $CURUSER, $INSTALLER09, $userid;
    $htmlout .= "<table class='main' width='790' border='0' cellspacing='0' cellpadding='0'>" .
    "<tr><td class='embedded'>";
    $htmlout .= begin_frame();
    $count = 0;
    foreach ($rows as $row) {
       $htmlout .="<p class='sub'>#".(int)$row["id"]." by ";
        if (isset($row["username"])) {
            $title = $row["title"];
            if ($title == "")
            $title = get_user_class_name($row["class"]);
            else
            $title = htmlspecialchars($title);
            $htmlout .="<a name='comm".(int)$row['id']."' href='userdetails.php?id=".(int)$row['user']."'><b>" .htmlspecialchars($row['username'])."</b></a>" . ($row["donor"] == "yes" ? "<img src=\"{$INSTALLER09['pic_base_url']}star.gif\" alt='Donor' />" : "") . ($row["warned"] == "yes" ? "<img src=" . "\"{$INSTALLER09['pic_base_url']}warned.gif\" alt=\"Warned\" />" : "") . " ($title)\n";
        } else
            $htmlout .="<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n";

        $htmlout .=" ".get_date($row["added"], 'DATE',0,1)."" .
            ($userid == $CURUSER["id"] || $row["user"] == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF ? " - [<a href='usercomment.php?action=edit&amp;cid={$row['id']}'>Edit</a>]" : "") .
            ($userid == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF ? " - [<a href='usercomment.php?action=delete&amp;cid=".(int)$row['id']."'>Delete</a>]" : "") .
            ($row["editedby"] && $CURUSER['class'] >= UC_STAFF ? " - [<a href='usercomment.php?action=vieworiginal&amp;cid=".(int)$row['id']."'>View original</a>]" : "") . "</p>\n";
        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
        if (!$avatar)
        $avatar = "{$INSTALLER09['pic_base_url']}default_avatar.gif";
        $text = format_comment($row["text"]);
        if ($row["editedby"])
        $text .= "<font size='1' class='small'><br /><br />Last edited by <a href='userdetails.php?id=".(int)$row['editedby']."'><b>".htmlspecialchars($row['username'])."</b></a> ".get_date($row['editedat'], 'DATE',0,1)."</font>\n";
        $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>";
        $htmlout .="<tr valign='top'>\n";
        $htmlout .="<td align='center' width='150' style='padding:0px'><img width='150' src=\"{$avatar}\" alt=\"Avatar\" /></td>\n";
        $htmlout .="<td class='text'>$text</td>\n";
        $htmlout .="</tr>\n";
        $htmlout .="</table>";
    }
    $htmlout .= end_frame();
    $htmlout .= "</td></tr></table>";
    return $htmlout;
}
//==Usercomments - pdq
    $HTMLOUT .="<h1>Comments left for <a href='userdetails.php?id=$id'>".htmlspecialchars($user['username'])."</a></h1>
    <a name='startcomments'></a>";
    $commentbar = "<a href='usercomment.php?action=add&amp;userid={$id}'>Add a comment</a>\n";
    $subres = sql_query("SELECT COUNT(id) FROM usercomments WHERE userid = $id");
    $subrow = mysqli_fetch_array($subres,  MYSQLI_NUM);
    $count = $subrow[0];
    if (!$count) {
    $HTMLOUT .="<h2>No comments yet</h2>\n";
    } else {
    require_once(INCL_DIR.'pager_functions.php');
    $pager = pager(5, $count, "userdetails.php?id=$id&amp;", array('lastpagedefault' => 1));
    $subres = sql_query("SELECT usercomments.id, text, user, usercomments.added, editedby, editedat, avatar, warned, username, title, class, leechwarn, chatpost, pirate, king, donor FROM usercomments LEFT JOIN users ON usercomments.user = users.id WHERE userid = {$id} ORDER BY usercomments.id {$pager['limit']}") or sqlerr(__FILE__, __LINE__);
    $allrows = array();
    while ($subrow = mysqli_fetch_assoc($subres))
    $allrows[] = $subrow;
    $HTMLOUT .= ($commentbar);
    $HTMLOUT .= ($pager['pagertop']);
    $HTMLOUT .= usercommenttable($allrows);
    $HTMLOUT .= ($pager['pagerbottom']);
    }
    $HTMLOUT .= ($commentbar);
//==end
// End Class

// End File
