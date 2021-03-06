<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: section_translation_dispname.php
 * Description: Display name of section as subtitle
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
if(array_key_exists('id',$_REQUEST)){
	$sql = $Kz->db_query("SELECT name FROM kazoe_sections WHERE id=:ID");
	$sql->execute();
	$res = $sql->fetch();
	echo "<h2>".$res[0]."</h2>";
}
?>