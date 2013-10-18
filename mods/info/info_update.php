<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: info_update.php
 * Description: Update item
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

if ($Kz->canDo("section[@id=':{base}']/action[@id='mod']"))
{
	$id 			= $Kz->getPostText("id");
	$type 			= $Kz->getPostText("type");
	$title 			= $Kz->getPostText("title");
	$date_begin 	= $Kz->getPostDate("date_begin");
	$date_expire 	= $Kz->getPostDate("date_expire");
	$info 			= $Kz->getPostText("info", !$Kz->canDo('//richtextedit'));
	if($Kz->canDo('//richtextedit')) $info = "#@RTE@#".$info;
	
	$sql = $Kz->db_query(
		"UPDATE :{apptable} SET type=:TYPE, title=:TITLE, date_begin=:DATE_BEGIN,date_expire=:DATE_EXPIRE,info=:INFO WHERE id=:ID",
		array(
			'ID'            => $id,
			'TYPE'          => $type,
			'TITLE'         => $title,
			'DATE_BEGIN'    => $date_begin,
			'DATE_EXPIRE'   => $date_expire,
			'INFO'          => $info
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
	echo "<a class='action_result'>".$Kz->getText('UpdateSuccess')."</a><br />";
	$Kz->uncache();
}
else {
	echo "<em>".$Kz->getText('Forbidden')."</em><br />\n";
}
?>
