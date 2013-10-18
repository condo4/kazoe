<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_tree.php
 * Description: Display tree of users
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

if ($Kz->canDo('section[@id=\':{base}\']'))
{
	$tree = array();
	$sql = $Kz->db_query("SELECT kazoe_passwd.id as id, kazoe_passwd._owner as parent, kazoe_users.name || ' ' || kazoe_users.firstname as name FROM kazoe_users JOIN kazoe_passwd ON kazoe_users._passwd = kazoe_passwd.login ORDER BY kazoe_passwd._owner DESC");
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
	$keys = $sql->fetch();
	while($keys){
		$node = array('id' => $keys['id'], 'name' => $keys['name'], 'parent' => $keys['parent']);
		$tree[$keys['id']] = $node;
		$keys = $sql->fetch();
	}

	function display_user($tree,$id,$level){
		if($level > 0) echo str_repeat("    ",$level-1)."---> ".$tree[$id]['name']."\n";
		else echo $tree[$id]['name']."\n";
		foreach($tree as $k => $user){
			if($user['parent'] === $id){
				display_user($tree,$user['id'],$level+1);
			}
		}
	}
	echo "<pre>";
	display_user($tree, 0, 0);
	echo "</pre>";
}
?>