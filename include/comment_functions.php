<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
 
 function commenttable($rows, $variant = 'torrent') {
	  require_once(INCL_DIR.'html_functions.php'); 
	  global $CURUSER, $INSTALLER09, $mood, $rep_is_on;
	  $lang = load_language( 'torrenttable_functions' );
	  $htmlout = '';
	  $count = 0;
	  $variant_options = array('torrent' => 'details', 'request' => 'viewrequests');                  
    if (isset($variant_options[$variant])) 
    $locale_link = $variant_options[$variant];
    else
    return;
    $extra_link = ($variant == 'request' ? '&type=request' : ($variant == 'offer' ? '&type=offer' : ''));
	  $htmlout .= begin_main_frame();
	  $htmlout .= begin_frame();
	  
	  foreach ($rows as $row) {
    $moodname = (isset($mood['name'][$row['mood']]) ? htmlspecialchars($mood['name'][$row['mood']]) : 'is feeling neutral');
    $moodpic  = (isset($mood['image'][$row['mood']]) ? htmlspecialchars($mood['image'][$row['mood']]) : 'noexpression.gif');
		$htmlout .= "<p class='sub'>#{$row["id"]} {$lang["commenttable_by"]} ";
    if (isset($row["username"])) {
    if ($row['anonymous'] == 'yes') {
    $htmlout .= ($CURUSER['class'] >= UC_MODERATOR ? 'Anonymous - Posted by: <b>'.htmlspecialchars($row['username']).'</b> ID: '.$row['user'].'' : 'Anonymous').' ';
    } else {
    $title = $row["title"];
    if ($title == "")
    $title = get_user_class_name($row["class"]);
    else
    $title = htmlspecialchars($title);
    $username = htmlspecialchars($row['username']);    
     
    $avatar1 = empty($row["avatar"]) ? "<img src=\'{$INSTALLER09['pic_base_url']}default_avatar.gif\' width=\'150\' height=\'150\' border=\'0\' alt=\'Avatar\' title=\'Avatar\' />" : "<img src=\'".htmlspecialchars($row['avatar'])."\' width=\'150\' height=\'150\' border=\'0\' alt=\'Avatar\' title=\'Avatar\' />";       
    $htmlout .= "<a name='comm{$row["id"]}' onmouseover=\"Tip('<b>$username</b><br />$avatar1');\" onmouseout=\"UnTip();\" href='userdetails.php?id={$row["user"]}'><b>".htmlspecialchars($row["username"])."</b></a>".($row["donor"] == "yes" ? "<img src='{$INSTALLER09['pic_base_url']}star.gif' alt='".$lang["commenttable_donor_alt"]."' />" : "") . ($row["warned"] == "yes" ? "<img src='{$INSTALLER09['pic_base_url']}warned.gif' alt='".$lang["commenttable_warned_alt"]."' />" : "")." ($title)\n";
    $htmlout .= '<a href="javascript:;" onclick="PopUp(\'usermood.php\',\'Mood\',530,500,1,1);">
    <span class="tool"><img src="'.$INSTALLER09['pic_base_url'].'smilies/'.$moodpic.'" alt="'.$moodname.'" border="0" />
    <span class="tip">'.htmlspecialchars($row['username']).' '.$moodname.' !</span></span></a>';
    }
    }
		else
    $htmlout .= "<a name='comm{$row["id"]}'><i>(".$lang["commenttable_orphaned"].")</i></a>\n";
		$htmlout .= get_date( $row['added'],'');
		$htmlout .= ($row["user"] == $CURUSER["id"] || $CURUSER["class"] >= UC_STAFF ? "- [<a href='comment.php?action=edit&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_edit"]."</a>]" : "") .
		($CURUSER["class"] >= UC_VIP ? " - [<a href='report.php?type=Comment&amp;id=".$row['id']."'>Report this Comment</a>]" : "") .
		($CURUSER["class"] >= UC_STAFF ? " - [<a href='comment.php?action=delete&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_delete"]."</a>]" : "") .
		($row["editedby"] && $CURUSER["class"] >= UC_STAFF ? "- [<a href='comment.php?action=vieworiginal&amp;cid=".$row['id'].$extra_link."&amp;tid=".$row[$variant]."'>".$lang["commenttable_view_original"]."</a>]" : "") . "</p>\n";
		$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
		if (!$avatar)
	  $avatar = "{$INSTALLER09['pic_base_url']}default_avatar.gif";
		$text = format_comment($row["text"]);
    if ($row["editedby"])
    $text .= "<p><font size='1' class='small'>".$lang["commenttable_last_edited_by"]." <a href='userdetails.php?id={$row['editedby']}'><b>{$row['username']}</b></a> ".$lang["commenttable_last_edited_at"]." ".get_date($row['editedat'],'DATE')."</font></p>\n";
		$htmlout .= begin_table(true);
		$htmlout .= "<tr valign='top'>\n";
		$htmlout .= "<td align='center' width='150' style='padding: 0px'><img width='150' height='150' src='{$avatar}' alt='' /><br />".get_reputation($row, 'comments')."</td>\n";
		$htmlout .= "<td class='text'>$text</td>\n";
		$htmlout .= "</tr>\n";
    $htmlout .= end_table();
    }
	  $htmlout .= end_frame();
	  $htmlout .= end_main_frame();
	  return $htmlout;
    }

        
?>
