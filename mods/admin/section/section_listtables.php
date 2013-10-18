<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: section_listtables.php
 * Description: List all apps with sections
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
echo "<br />";

$config = new DOMDocument('1.0');
$config->Load($Kz->getPath('docroot')."/root/etc/category.xml");
$xpath = new DOMXpath($config);
$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');

$nodes = $xpath->query("/category/item");
foreach($nodes as $node){
	$id = $node->getAttribute('id');
	$descs = $xpath->query("/category/item[@id='".$id."']/desc[@xml:lang='".$Kz->getEnv('LANG')."']");
	if($descs->length == 1){
		$desc = $Kz->getText('ManageThis').$descs->item(0)->nodeValue;
	}
	else {
		$descs = $xpath->query("/category/item[@id='".$id."']/desc[@xml:lang='".$Kz->getEnv('LANG_DEF')."']");
		if($descs->length != 1) throw new Exception("Unknows languge translation for ".$id." in [".$Kz->getEnv('LANG').",".$Kz->getEnv('LANG_DEF')."]");
		$desc = $Kz->getText('ManageThis').$descs->item(0)->nodeValue;
	}

	$action= str_replace('html',$id.'.html',$Kz->getEnv("PAGEURL"));
	echo "<form  method='post' action='".$action."'><p><input type='submit' value='".$desc."' /></p></form>";
}
?>