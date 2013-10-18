<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_update.php
 * Description: Make query to update users personnals informations
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

if ($Kz->canDo('section[@id=\':{base}\']/action[@id=\'mod\']'))
{
	$Kz->db_beginTransaction();
	$id = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["id"])):($_REQUEST["id"]);

	$sql = $Kz->db_query(
		"SELECT kazoe_users.id as userid, kazoe_passwd.id as adminid FROM kazoe_users JOIN kazoe_passwd ON kazoe_users._passwd = kazoe_passwd.login WHERE kazoe_passwd.id = :ID",
		array(
			'ID'          => $id
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}

	$keys = $sql->fetch();
	if (!$keys) {
		$Kz->db_rollBack();
		throw new Exception("User desn't exist: ".$key);
	}
	$userid = $keys["userid"];
	$adminid = $keys["adminid"];

	$name = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["name"])):($_REQUEST["name"]);
	$firstname = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["firstname"])):($_REQUEST["firstname"]);
	$email = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["email"])):($_REQUEST["email"]);
	$address = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["address"])):($_REQUEST["address"]);
	$phone = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["phone"])):($_REQUEST["phone"]);
	if($phone == "") $phone = null;
	$mobile = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["mobile"])):($_REQUEST["mobile"]);
	if($mobile == "") $mobile = null;
	$functions = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["functions"])):($_REQUEST["functions"]);

	$sql = $Kz->db_query(
		"UPDATE kazoe_users SET name=:NAME, firstname=:FIRSTNAME, email=:EMAIL, address=:ADDRESS, phone=:PHONE, mobile=:MOBILE, functions=:FUNCTIONS WHERE id=:ID",
		array(
			'NAME'          => $name,
			'FIRSTNAME'     => $firstname,
			'EMAIL'         => $email,
			'ADDRESS'       => $address,
			'PHONE'         => $phone,
			'MOBILE'        => $mobile,
			'FUNCTIONS'     => $functions,
			'ID'            => $userid,
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}

	$Kz->db_commit();
	echo "<a class='action_result'>".$Kz->getText("UpdateSuccess")."</a><br />";
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>