<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: user_perms.php
 * Description: Make page to manage users rules
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
	$parent_node = $Kz->getRight("/authorization")->item(0);
	$parent = new DomDocument();
	$node = $parent->importNode($parent_node,True);
	$parent->appendChild($node);
	$xparent = new DOMXpath($parent);
	$id = (get_magic_quotes_gpc())?(stripslashes($_REQUEST["id"])):($_REQUEST["id"]);

	$sql = $Kz->db_query(
		"SELECT _properties FROM kazoe_passwd WHERE id=:ID",
		array(
			'ID'           => $id
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

	$line = $sql->fetch();
	if(!$line){
		throw new Exception("No user with id ".$id);
	}
	$xmlauth = $line['_properties'];
	$userdom = new DomDocument();
	$userdom->loadXML($xmlauth);
	$xuser = new DOMXpath($userdom);
	$user_node = $xuser->query('/properties/authorization')->item(0);
	if($user_node == null){
		$user_node_prop = $xuser->query('/properties')->item(0);
		$authorization = $userdom->createElement("authorization");
		$user_node_prop->appendChild($authorization);
		$user_node = $xuser->query('/properties/authorization')->item(0);
	}
	$udoc = new DomDocument();
	if($user_node){
		$node = $udoc->importNode($user_node,True);
		$udoc->appendChild($node);
		$right = new DOMXpath($udoc);
	}
	else{
		$udoc->loadXML("<null />");
		$right = new DOMXpath($udoc);
	}
	
	$changed = false;
	
	if(array_key_exists('editstatic',$_REQUEST)){
		foreach($parent_node->childNodes as $node){
			if(($node->nodeType == 1) and ($node->nodeName == "static")){
				$staticid = $node->getAttribute('id');
				$staticidname = str_replace(".","_",$staticid);
				if($right->query("//static[@id='".$staticid."']")->length == 0){
					// User have not right
					if(array_key_exists($staticidname,$_REQUEST)){
						// Need add node
						$srcnode = $userdom->importNode($xparent->query("//static[@id='".$staticid."']")->item(0),True);
						$destnode = $xuser->query('/properties/authorization')->item(0);
						$destnode->appendChild($srcnode);
						$changed = true;
					}
				}
				else
				{
					// User have right
					if(!array_key_exists($staticidname,$_REQUEST)){
						// Need remove node
						$oldnode = $xuser->query("//static[@id='".$staticid."']")->item(0);
						$destnode = $xuser->query('/properties/authorization')->item(0);
						$destnode->removeChild($oldnode);
						$changed = true;
					}
				}
			}
		}
	}
	
	if($xparent->query("//richtextedit")->length != 0){
		if(array_key_exists('RTE',$_REQUEST)){
			if(array_key_exists('RTE_value',$_REQUEST))
			{
				//Add right
				if($right->query("//richtextedit")->length == 0){
					$srcnode = $userdom->importNode($xparent->query("//richtextedit")->item(0),True);
					$destnode = $xuser->query('/properties/authorization')->item(0);
					$destnode->appendChild($srcnode);
					$changed = true;
				}
			}
			else
			{
				//Remove Right
				if($right->query("//richtextedit")->length != 0){
					$oldnode = $xuser->query("//richtextedit")->item(0);
					$destnode = $xuser->query('/properties/authorization')->item(0);
					$destnode->removeChild($oldnode);
					$changed = true;
				}
			}
		}
	}
	
	if($changed)
	{
		$xmlauthfinal = $userdom->saveXML();
		$sql = $Kz->db_query(
			"UPDATE kazoe_passwd SET _properties=:NEWXML WHERE id=:ID",
			array(
				'ID'           => $id,
				'NEWXML'       => $xmlauthfinal
			)
		);
		if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
		$userdom = new DomDocument();
		$userdom->loadXML($xmlauthfinal);
		$xuser = new DOMXpath($userdom);
		$user_node = $xuser->query('/properties/authorization')->item(0);
		if($user_node == null){
			$user_node_prop = $xuser->query('/properties')->item(0);
			$authorization = $userdom->createElement("authorization");
			$user_node_prop->appendChild($authorization);
			$user_node = $xuser->query('/properties/authorization')->item(0);
		}
		$udoc = new DomDocument();
		if($user_node){
			$node = $udoc->importNode($user_node,True);
			$udoc->appendChild($node);
			$right = new DOMXpath($udoc);
		}
		else{
			$udoc->loadXML("<null />");
			$right = new DOMXpath($udoc);
		}
	}
	
	if(array_key_exists('action',$_REQUEST)){
		$action = $_REQUEST['action'];
		$path = $_SESSION["path_table"][$_REQUEST['path']];
		if($action == 'add'){
			$srcnode = $userdom->importNode($xparent->query('/'.$path)->item(0),True);
			$toremove = array();
			foreach($srcnode->childNodes as $node){
				if(($node->nodeType == 1) and ($node->nodeName != "desc")){
					array_push($toremove,$node);
				}
			}
			foreach($toremove as $node){
				$srcnode->removeChild($node);
			}
			$destpath = '/properties/authorization'.substr($path,0,strrpos($path,'/'));
			$destnode = $xuser->query($destpath)->item(0);
			$destnode->appendChild($srcnode);
			
			$xmlauthfinal = $userdom->saveXML();
			$sql = $Kz->db_query(
				"UPDATE kazoe_passwd SET _properties=:NEWXML WHERE id=:ID",
				array(
					'ID'           => $id,
					'NEWXML'       => $xmlauthfinal
				)
			);
			if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
			$sql = $Kz->db_query(
				"SELECT _properties FROM kazoe_passwd WHERE id=:ID",
				array(
					'ID'           => $id
				)
			);
			if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
			$line = $sql->fetch();
			if(!$line){
				throw new Exception("No user with id ".$id);
			}
			$xmlauth = $line['_properties'];
			$userdom = new DomDocument();
			$userdom->loadXML($xmlauth);
			$xuser = new DOMXpath($userdom);
			$user_node = $xuser->query('/properties/authorization')->item(0);
			$udoc = new DomDocument();
			if($user_node){
				$node = $udoc->importNode($user_node,True);
				$udoc->appendChild($node);
				$right = new DOMXpath($udoc);
			}
			else{
				$udoc->loadXML("<null />");
				$right = new DOMXpath($udoc);
			}
		}
		elseif($action == 'del'){
			$removenode = $xuser->query('/properties/authorization'.$path)->item(0);
			$destpath = '/properties/authorization'.substr($path,0,strrpos($path,'/'));
			$destnode = $xuser->query($destpath)->item(0);
			
			$destnode->removeChild($removenode);
			
			$xmlauthfinal = $userdom->saveXML();

			$sql = $Kz->db_query(
				"UPDATE kazoe_passwd SET _properties=:NEWXML WHERE id=:ID",
				array(
					'ID'           => $id,
					'NEWXML'       => $xmlauthfinal
				)
			);
			if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
			$sql = $Kz->db_query(
				"SELECT _properties FROM kazoe_passwd WHERE id=:ID",
				array(
					'ID'           => $id
				)
			);
			if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
			$line = $sql->fetch();
			if(!$line){
				throw new Exception("No user with id ".$id);
			}
			$xmlauth = $line['_properties'];
			$userdom = new DomDocument();
			$userdom->loadXML($xmlauth);
			$xuser = new DOMXpath($userdom);
			$user_node = $xuser->query('/properties/authorization')->item(0);
			$udoc = new DomDocument();
			if($user_node){
				$node = $udoc->importNode($user_node,True);
				$udoc->appendChild($node);
				$right = new DOMXpath($udoc);
			}
			else{
				$udoc->loadXML("<null />");
				$right = new DOMXpath($udoc);
			}
		}
	}
	$_SESSION["path_table"] = array();


	function display_node($node,$xparent,$path,$user,$mode,$id){
		global $Kz;
		$continue = True;
		$result = False;
		$return = False;
		$desc = '';
		if($path != ""){
			$desc = $xparent->query('/'.$path.'/desc')->item(0)->nodeValue;
			if($mode == 'ADD'){
				if($user->query("/".$path)->length == 0){
					print "<form method='post' action='".$Kz->getEnv('PAGEURL')."'>\n";
					print "    <div class='addright'>\n";
					print "        <input type='hidden' name='query'   value='".$_REQUEST['query']."' />\n";
					print "        <input type='hidden' name='id'      value='".$id."' />\n";
					print "        <input type='hidden' name='action'  value='add' />\n";
					print "        <input type='hidden' name='path'    value='".sha1($path)."' />\n";
					$_SESSION["path_table"][sha1($path)] = $path;
					print "        <input type='submit'                value='".$Kz->getText("AddRight")."' />\n";
					print $desc;
					print "    </div>\n";
					print "</form>\n";
					$continue = False;
				}
			}
		}
		foreach($node->childNodes as $node){
			if(($node->nodeType == 1) and ($node->nodeName != "desc") and ($node->nodeName != "static") and ($node->nodeName != "richtextedit")){
				$childpath = $path.'/'.$node->nodeName."[@id='".$node->getAttribute('id')."']";
				if($continue){
					if(display_node($node,$xparent,$childpath,$user,$mode,$id)) $result = True;
				}
			}
		}
		if($path != ""){
			if($mode == 'DEL'){
				if($user->query("/".$path)->length != 0){
					$return = True;
					if(!$result) {
						print "<form method='post' action='".$Kz->getEnv('PAGEURL')."'>\n";
						print "    <div class='delright'>\n";
						print "        <input type='hidden' name='query'   value='".$_REQUEST['query']."' />\n";
						print "        <input type='hidden' name='id'      value='".$id."' />\n";
						print "        <input type='hidden' name='action'  value='del' />\n";
						print "        <input type='hidden' name='path'    value='".sha1($path)."' />\n";
						$_SESSION["path_table"][sha1($path)] = $path;
						print "        <input type='submit'                value='".$Kz->getText("DeleteRight")."' />\n";
						print $desc;
						print "    </div>\n";
						print "</form>\n";
					}
				}
			}
		}
		return $return;
	}
	function display_pages($node,$user,$id){
		global $Kz;
		$continue = True;
		$result = False;
		$return = False;
		$desc = '';
		
		print "<form method='post' action='".$Kz->getEnv('PAGEURL')."'>\n";
		print "    <div class='staticpage'>\n";
		print "        <input type='hidden' name='query'   value='".$_REQUEST['query']."' />\n";
		print "        <input type='hidden' name='id'      value='".$id."' />\n";
		print "        <input type='hidden' name='editstatic'      value='yes' />\n";
		
		foreach($node->childNodes as $node){
			if(($node->nodeType == 1) and ($node->nodeName == "static")){
				$id = $node->getAttribute('id');
				if($user->query("//static[@id='".$id."']")->length == 0){
					print "<input type='checkbox' name='".$id."' value='".$id."' />".$id."<br />\n";
				}
				else
				{
					print "<input type='checkbox' name='".$id."' value='".$id."' checked='checked' />".$id."<br />\n";
				}
				
			}
		}
		print "        <input type='submit'                value='".$Kz->getText("Save")."' />\n";
		print "    </div>\n";
		print "</form>\n";

		return $return;
	}
	
	print "<h1>".$Kz->getText("AddRight")."</h1>\n";
	display_node($parent_node,$xparent ,"",$right,"ADD",$id);
	print "<h1>".$Kz->getText("DeleteRight")."</h1>\n";
	display_node($parent_node,$xparent ,"",$right,"DEL",$id);
	if($xparent->query("//richtextedit")->length != 0){
		print "<h1>".$Kz->getText("RteRight")."</h1>\n";
		print "<form method='post' action='".$Kz->getEnv('PAGEURL')."'>\n";
		print "    <div class='rte'>\n";
		print "        <input type='hidden' name='query'   value='".$_REQUEST['query']."' />\n";
		print "        <input type='hidden' name='id'      value='".$id."' />\n";
		print "        <input type='hidden' name='RTE'     value='yes' />\n";
		if($right->query("//richtextedit")->length == 0){
			print "<input type='checkbox' name='RTE_value' id='RTE_value' />".$Kz->getText("AddRteRight")."<br />\n";
		}
		else
		{
			print "<input type='checkbox' name='RTE_value' id='RTE_value' checked='checked' />".$Kz->getText("AddRteRight")."<br />\n";
		}
		print "        <input type='submit'                value='".$Kz->getText("Save")."' />\n";
		print "    </div>\n";
		print "</form>\n";
	}
	print "<h1>".$Kz->getText("StaticPageRight")."</h1>\n";
	display_pages($parent_node,$right,$id);
}
?>
