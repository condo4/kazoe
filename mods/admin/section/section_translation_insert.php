<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: section_translation_insert.php
 * Description: Insert translation
 *
 * @author Fabien Proriol Copyright (C) 2009.
 *
 * @see The GNU Public License (GPL)
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

if ($Kz->canDo('section[@id=\':{base}\']/action[@id=\'modtranslation\']'))
{
	$sectionid 	= $Kz->getPostText("id");
	$lang 		= $Kz->getPostText("lang");
	$title 		= $Kz->getPostText("title");

	$sql = $Kz->db_query(
		"SELECT count(*) FROM kazoe_sections_titles WHERE lang=:LANGUAGE AND sectionid=:SECTIONID",
		array(
			'LANGUAGE'  => $lang,
			'SECTIONID'	=> $sectionid
		)
	);

	$res = $sql->execute();
	if($res[0] == 0){
		$sql = $Kz->db_query(
			"INSERT INTO kazoe_sections_titles (sectionid,lang,title,_owner) VALUES (:SECTIONID,:LANGUAGE,:TEXT,:USER_ID)",
		array(
			'LANGUAGE'  => $lang,
			'SECTIONID'	=> $sectionid,
			'TEXT'		=> $title
		)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

		echo "<a class='action_result'>".$Kz->getText('InsertSuccess')."</a><br />";
		$Kz->uncache();
	}
	else echo "<a class='action_result'>".$Kz->getText('TranslationExist')."</a><br />";
}
else {
	echo "<em>".$Kz->getText('Forbidden')."</em><br />\n";
}
?>