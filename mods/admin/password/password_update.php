<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: errors_delete_all.php
 * Description: Make sql query to delete error
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

if ($Kz->canDo('section'))
{
	$pass1 = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["password1"])):($_REQUEST["password1"]);
	$pass2 = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["password2"])):($_REQUEST["password2"]);
	if($pass1 == $pass2) $valid = True;
	else $valid = False;
	$password = sha1($pass1);

	if($valid){
		$sql = $Kz->db_query(
			"UPDATE kazoe_passwd SET passwords=:PASSWORD WHERE login=:USER_LOGIN",
			array(
				'PASSWORD'      => $password
			)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

		echo "<a class='action_result'>".$Kz->getText('ChangeSuccess')."</a><br />";
	}
	else {
		echo "<em>".$Kz->getText('ChangeError')."</em><br />\n";
	}
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>