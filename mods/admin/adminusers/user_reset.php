<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_reset.php
 * Description: Reset passord of user
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

if ($Kz->canDo('section[@id=\':{base}\']/action[@id=\'rpw\']'))
{
	$id 		= $Kz->getPostText("id");
	$password 	= sha1($Kz->getConfig('default_password'));

	$sql = $Kz->db_query(
		"UPDATE kazoe_passwd SET passwords=:PASSWORD WHERE id=:ID",
		array(
			'ID'           => $id,
			'PASSWORD'     => $password
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

	echo "<a class='action_result'>".$Kz->getText("PassInit")."\"".$Kz->getConfig('default_password')."\"</a><br />";
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>