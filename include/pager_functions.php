<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/
function pager($rpp, $count, $href, $opts = array()) // thx yuna or whoever wrote it
{
    $pages = ceil($count / $rpp);

    if (!isset($opts["lastpagedefault"]))
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = 0 + $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    } else
        $page = $pagedefault;

    $pager = "<td align=\"center\" class=\"pager\">Page:</td><td class=\"pagebr\">&nbsp;</td>";

    $mp = $pages - 1;
    $as = "<b>&#171;</b>";
    if ($page >= 1) {
        $pager .= "<td  align=\"center\" class=\"pager\">";
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\" style=\"text-decoration: none;\">$as</a>";
        $pager .= "</td><td  align=\"center\" class=\"pagebr\">&nbsp;</td>";
    }

    $as = "<b>&#187;</b>";
    $pager2 = $bregs = '';
    if ($page < $mp && $mp >= 0) {
        $pager2 .= "<td  align=\"center\" class=\"pager\">";
        $pager2 .= "<a href=\"{$href}page=" . ($page + 1) . "\" style=\"text-decoration: none;\">$as</a>";
        $pager2 .= "</td>$bregs";
    } else $pager2 .= $bregs;

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "<td  align=\"center\" class=\"pager\">...</td><td  align=\"center\" class=\"pagebr\">&nbsp;</td>";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;

            $text = $i + 1;
            if ($i != $page)
                $pagerarr[] = "<td  align=\"center\" class=\"pager\"><a title=\"$start&nbsp;-&nbsp;$end\" href=\"{$href}page=$i\" style=\"text-decoration: none;\"><b>$text</b></a></td><td  align=\"center\" class=\"pagebr\">&nbsp;</td>";
            else
                $pagerarr[] = "<td  align=\"center\" class=\"highlight\"><b>$text</b></td><td align=\"center\" class=\"pagebr\">&nbsp;</td>";
        }
        $pagerstr = join("", $pagerarr);
        $pagertop = "<table align=\"center\" class=\"main\"><tr>$pager $pagerstr $pager2</tr></table>\n";
        $pagerbottom = "<div align=\"center\">Overall $count items in ".($i)." page".($i>1 ? '\'s': '').", showing $rpp per page.</div><br /><table align=\"center\" class=\"main\"><tr>$pager $pagerstr $pager2</tr></table>\n";
    } else {
        $pagertop = $pager;
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

       return array('pagertop' => $pagertop, 'pagerbottom' => $pagerbottom, 'limit' => "LIMIT $start,$rpp");
}

?>
