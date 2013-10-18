<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_insert.php
 * Description: Make sql query to create user
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
	$login 		= $Kz->getPostText("login");
	$name 		= $Kz->getPostText("name");
	$firstname 	= $Kz->getPostText("firstname");
	$email 		= $Kz->getPostText("email");
	$address 	= $Kz->getPostText("address");
	$phone 		= ($Kz->getPostText("phone") != '')?($Kz->getPostText("phone")):(null);
	$mobile 	= ($Kz->getPostText("mobile") != '')?($Kz->getPostText("mobile")):(null);
	$functions 	= $Kz->getPostText("functions");
	$password 	= sha1($Kz->getConfig('default_password'));

	$Kz->db_beginTransaction();
	$sql = $Kz->db_query(
		"INSERT INTO kazoe_passwd (login, passwords, _owner) VALUES (:LOGIN, :PASSWORD, :USER_ID)",
		array(
			'LOGIN'         => $login,
			'PASSWORD'      => $password
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}
	$sql = $Kz->db_query(
		"INSERT INTO kazoe_users (name, firstname, email, address, phone, mobile, functions, _passwd, _owner) VALUES (:NAME, :FIRSTNAME, :EMAIL, :ADDRESS, :PHONE, :MOBILE, :FUNCTIONS, :LOGIN, :USER_ID)",
		array(
			'NAME'          => $name,
			'FIRSTNAME'     => $firstname,
			'EMAIL'         => $email,
			'ADDRESS'       => $address,
			'PHONE'         => $phone,
			'MOBILE'        => $mobile,
			'FUNCTIONS'     => $functions,
			'LOGIN'         => $login
		)
	);
	if (!$sql->execute()) {
		$Kz->db_rollBack();
		throw new Exception($Kz->db_error($sql));
	}

	$Kz->db_commit();
	echo "<a class='action_result'>".$Kz->getText("InsertSuccess")."</a><br />";
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>