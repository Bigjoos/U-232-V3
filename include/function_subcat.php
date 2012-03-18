<?php
/**
 *   https://github.com/Bigjoos/
 *   Licence Info: GPL
 *   Copyright (C) 2010 U-232 v.3
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless, putyn.
 **/

 function genrelist2() {
 global $mc1, $INSTALLER09;
 if(!($cats = $mc1->get_value('categories'))) {
 $row = sql_query("SELECT id, name, image, parent_id, tabletype FROM categories ORDER BY name");
 while ($mysqlicats = mysqli_fetch_assoc($row))
 $allcats[] = $mysqlicats;
 $allcats2 = $allcats;
 $i = 0;
 foreach ($allcats as $cat)
 {
 if ($cat['parent_id'] == -1) 
 {
 $cats[] = $cat;
 $j = 0;
 $cats[$i]['categories'] = '';
 foreach ($allcats2 as $subcat)
 {
 if ($cat['id'] == $subcat['parent_id']) {
 //Subcategories
 $cats[$i]['subcategory'][] = $subcat;
 //Subcategories add parenttabletype
 $cats[$i]['subcategory'][$j]['parenttabletype'] = $cat['tabletype'];
 //Subcategories add idtabletype
 $cats[$i]['subcategory'][$j]['idtabletype'] = $subcat['id'].$subcat['tabletype'];
 //Subcategories description
 $cats[$i]['subcategory'][$j]['description'] = $cat['name']."->".$subcat['name'];
 //All link array for cats
 $cats[$i]['categories'] .= "cats$cat[tabletype][]=$subcat[id]&amp;";
 $j++;
 }
 }
 //All link for cats
 $cats[$i]['categories'] = substr($cats[$i]['categories'],0,-5);
 $i++;
 }
 }
 $mc1->add_value('categories', $cats, $INSTALLER09['expires']['genrelist2']);
 }
 return $cats;
 }

 function categories_table($cats, $wherecatina, $linkpage = '', $display = 'block')
 {
 global $lang, $CURUSER, $INSTALLER09;
 $html = "";
 $html .= "<div id=\"cats\" style=\"display: {$display};\"><table><tbody align=\"left\"><tr>";
 $i = 0;
 $ncats = count($cats);
 if(count($ncats) > 0);
 foreach( $cats as $cat )
 {
 $html .= ($i && $i % $INSTALLER09['catsperrow'] == 0) ? "</tr><tr>" : "";
 $html .= "<td class=\"colhead\" style=\"padding-bottom: 2px;padding-left: 7px;white-space: nowrap;\">
 <input id=\"checkAll".(int)$cat['tabletype']."\" type=\"checkbox\" onclick=\"checkAllFields(1,".(int)$cat['tabletype'].");\" " . (isset($cat['checked']) && $cat['checked'] ? "checked='checked'" : "") . " />
 <a href=\"javascript: ShowHideMainSubCats(".(int)$cat['tabletype'].",{$ncats})\">
 <img border=\"0\" src=\"pic/plus.png\" id=\"pic".(int)$cat['tabletype']."\" alt=\"Show/Hide\" />&nbsp;" . htmlspecialchars($cat['name']) . "</a>&nbsp;".(($linkpage != '') ? "<a class=\"catlink\" href=\"{$linkpage}?{$cat['categories']}\">(All)</a>" : "")."</td>\n";
 $i++;
 }
 $nrows = ceil($ncats/$INSTALLER09['catsperrow']);
 $lastrowcols = $ncats % $INSTALLER09['catsperrow'];
 if ($lastrowcols != 0)
 {
 if ($INSTALLER09['catsperrow'] - $lastrowcols != 1)
 $html .= "<td class=\"one\" rowspan=\"" . ($INSTALLER09['catsperrow'] - $lastrowcols) . "\">&nbsp;</td>";
 else
 $html .= "<td class=\"one\">&nbsp;</td>";
 }
 $html .= "</tr></tbody></table></div>";
 if(count($cats) > 0);
 foreach( $cats as $cat )
 {
 $subcats = isset($cat['subcategory']) && is_array($cat['subcategory']) ? $cat['subcategory'] : array();
 if (count($subcats) > 0)
 {
 $html .= subcategories_table($cat, $wherecatina, $linkpage, $ncats);
 }
 }
 return $html;
 }

 function subcategories_table($cats, $wherecatina = array(), $linkpage = '', $ncats) { 
 global $lang, $CURUSER, $INSTALLER09;
 $html = "";
 $html .= "<div id=\"tabletype{$cats['tabletype']}\" style=\"display: none;\">";
 $subcats = $cats['subcategory'];
 $html .= "<table>";
 $html .= "<tbody align=\"left\"><tr>";
 $i = 0;
 if (count($subcats) >0)
 foreach ($subcats as $cat)
 {
 $html .= ($i && $i % $INSTALLER09['subcatperrow'] == 0) ? "</tr><tr>" : "";
 $html .= "<td class=\"one\" style=\"padding-bottom: 2px;padding-left: 7px;white-space: nowrap;\">
 <input onclick=\"checkAllFields(2,".(int)$cats['tabletype'].");\" name=\"cats".(int)$cats['tabletype']."[]\" value=\"".(int)$cat['id']."\" type=\"checkbox\" ". (in_array($cat['id'],$wherecatina) ? "checked='checked'" : "") . " />
 ".(($linkpage != '') ? "<a href=\"{$linkpage}?cats".(int)$cats['tabletype']."[]=".(int)$cat['id']."\"><img src='{$INSTALLER09['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/" . htmlspecialchars($cat['image']) . "' alt='" . htmlspecialchars($cat['name']) . "' title='" . htmlspecialchars($cat['name']) . "' /></a>" : htmlspecialchars($cat['name']))."</td>\n";
 $i++;
 }
 $nsubcats = count($subcats);
 $nrows = ceil($nsubcats/$INSTALLER09['subcatperrow']);
 $lastrowcols = $nsubcats % $INSTALLER09['subcatperrow'];
 if ($lastrowcols != 0)
 {
 if ($INSTALLER09['subcatperrow'] - $lastrowcols != 1)
 $html .= "<td class=\"one\" rowspan=\"".($INSTALLER09['subcatperrow'] - $lastrowcols)."\">&nbsp;</td>";
 else
 $html .= "<td class=\"one\">&nbsp;</td>";
 }
 $html .= "</tr></tbody></table></div>";
 return $html;
 }
 
 function validsubcat($subcatid,$cats) {
	//==Find Category with subcat
	$i = 0;
	if(count($cats) > 0);
	foreach ($cats as $cat)
	{
		$subcats = $cat['subcategory'];
		if (count($subcats) > 0)
		{
			foreach ($subcats as $subcat)
			{
				if ($subcat['id'] == $subcatid)
				return True;
			}
		}	
	}
	return False;
}
?>
