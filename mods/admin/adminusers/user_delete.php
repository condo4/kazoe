<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_delete.php
 * Description: Make sql query to delete user
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

if ($Kz->canDo('section[@id=\':{base}\']/action[@id=\'del\']'))
{
	$key = $Kz->getPostText("id");
	$Kz->db_beginTransaction();
	$sql = $Kz->db_query(
		"SELECT kazoe_users.id as userid, kazoe_passwd.id as adminid FROM kazoe_users JOIN kazoe_passwd ON kazoe_users._passwd = kazoe_passwd.login WHERE kazoe_passwd.id = :KEY",
		array(
			'KEY'      => $key
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}
	$keys = $sql->fetch();
	$userid = $keys["userid"];
	$adminid = $keys["adminid"];
	$sql = $Kz->db_query(
		"DELETE FROM kazoe_users WHERE kazoe_users.id = :KEY",
		array(
			'KEY'      => $userid
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}
	$sql = $Kz->db_query(
		"DELETE FROM kazoe_passwd WHERE kazoe_passwd.id = :KEY",
		array(
			'KEY'      => $adminid
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}
	$Kz->db_commit();
	echo "<a class='action_result'>".$Kz->getText("DeleteSuccess")."</a><br />";
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>
