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

if ($Kz->canDo('section'))
{
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
        "UPDATE kazoe_users SET name=:NAME, firstname=:FIRSTNAME, email=:EMAIL, address=:ADDRESS, phone=:PHONE, mobile=:MOBILE, functions=:FUNCTIONS WHERE _passwd=:USER_LOGIN",
        array(
            'NAME'          => $name,
            'FIRSTNAME'     => $firstname,
            'EMAIL'         => $email,
            'ADDRESS'       => $address,
            'PHONE'         => $phone,
            'MOBILE'        => $mobile,
            'FUNCTIONS'     => $functions
        )
    );
    if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

	echo "<a class='action_result'>".$Kz->getText("UpdateSuccess")."</a><br />";
}
else {
	echo "<em>".$Kz->getText("Forbidden")."</em><br />\n";
}
?>