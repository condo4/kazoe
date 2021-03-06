<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_perms_updateraw.php
 * Description: Make page to manage users rules in XML
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

if ($Kz->canDo("section[@id=':{base}']/action[@id='raw']"))
{
	$xml = $Kz->getPostText("_properties",false);
	$id = $Kz->getPostText("id");

	$test = new DomDocument();
	@$test->loadXML($xml);
	if($test->childNodes->length) {
		$valid = True;
		$value = $test->saveXML();
	}
	else $valid = False;

	if($valid){
		$sql = $Kz->db_query(
			"UPDATE kazoe_passwd SET _properties=:VALUE WHERE id=:ID",
			array(
				'ID'        => $id,
				'VALUE'     => $value
			)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

		echo "<a class='action_result'>".$Kz->getText("UpdateSuccess")."</a><br />";
	}
	else {
		echo "<em>".$Kz->getText("SyntaxError")."</em><br />\n";
	}
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>