<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: connect.php
 * Description: Manage connection
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

if(!isset($_SESSION["auth_counter"])) $_SESSION["auth_counter"] = 0;

if($_SESSION["auth_counter"] > 2){
	$cryptinstall="kazoe/lib/crypt/cryptographp.fct.php";
	include $cryptinstall; 
	$valid = chk_crypt($_POST['cryptogramme']);
}
else {
	$valid = True;
}

if ($valid)  {
	$login =      $_REQUEST["login"];
	$password =   $_REQUEST["password"];
	$sql = $Kz->db_query(
		"SELECT count(*) AS nb FROM kazoe_passwd WHERE login = :LOGIN AND passwords = :PASSWORD",
		array(
			'LOGIN'         => $login,
			'PASSWORD'      => sha1($password)
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
	$line = $sql->fetch();
	if($line['nb'] == 1){
		$_SESSION["user"] = $login;
		$_SESSION["auth_counter"] = 0;
		
		$sql = $Kz->db_query(
			"SELECT id FROM kazoe_passwd WHERE login = :LOGIN AND passwords = :PASSWORD",
			array(
				'LOGIN'         => $login,
				'PASSWORD'      => sha1($password)
			)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

		$linec = $sql->fetch();

		$sql = $Kz->db_query(
			"UPDATE kazoe_users SET lastconnection=NOW() WHERE id=(SELECT id FROM kazoe_passwd WHERE login=:LOGIN AND passwords = :PASSWORD)",
			array(
				'LOGIN'         => $login,
				'PASSWORD'      => sha1($password)
			)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

		$_SESSION["userid"] = $linec['id'];
		$Kz->reload();
	}
	else {
		echo "<strong>".$Kz->getText("Retry")."</strong>\n";
		$_SESSION["user"] = "";
		$_SESSION["userid"] = -1;
		$_SESSION["auth_counter"] ++;
	}
}
else {
	echo "<strong>".$Kz->getText("BadCode")."</strong>\n";
	$Kz->setEnv('QUERY','');
}
?>
