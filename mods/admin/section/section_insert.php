<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: section_insert.php
 * Description: Insert item
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

if ($Kz->canDo('section[@id=\':{base}\']/action[@id=\'add\']'))
{
	$name = $Kz->getPostText("name");;
	$sectname = $Kz->getEnv('SECTION');
	$Kz->db_beginTransaction();
	$sql = $Kz->db_query(
		"INSERT INTO kazoe_sections (name,secname,meta,_owner) VALUES (:NAME,:SECNAME,:META,:USER_ID)",
		array(
			'NAME'              => $name,
			'SECNAME'        	=> $sectname
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

	$sql = $Kz->db_query(
		"SELECT id FROM kazoe_sections WHERE name=:NAME",
		array(
			'NAME'              => $name
		)
	);
	$sql->execute();
	$res = $sql->fetch();
	if (!$res) throw new Exception($Kz->db_error($sql));
	$sectionid = $res[0];
	$langdef = $Kz->getEnv('LANG_DEF');

	$sql = $Kz->db_query(
		"INSERT INTO kazoe_sections_titles (sectionid,lang,title,_owner) VALUES (:SECTIONID,:LANG,:TITLE,:USER_ID)",
		array(
			'SECTIONID'	=> $sectionid,
			'LANG'      => $langdef,
			'TITLE'     => $sectname
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
	$Kz->db_commit();

	echo "<a class='action_result'>".$Kz->getText('InsertSuccess')."</a><br />";
	$Kz->uncache(str_replace('html','*',$Kz->getBasePage()));
}
else {
	echo "<em>".$Kz->getText('Forbidden')."</em><br />\n";
}

?>