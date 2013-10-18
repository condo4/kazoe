<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: Menu.php
 * Description: This Script generate the Menu of the page
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


class Menu
{
	private $kz;
	private $doc;
	private $meta;

	public function __construct($kz){
		$this->kz = $kz;
		$this->meta = $this->kz->getEnv('META');
		$this->doc = new DOMDocument("1.0",'UTF-8');
		// create root element
		$root = $this->doc->createElement("menu");
		$this->doc->appendChild($root);
		$home = $this->kz->getPath('docroot')."/root/".$this->meta;
		$this->makeNode($this->doc,$root,$home);
	}

	private function addContext($idpp){
		$xpath = new DOMXpath($this->doc);
		// Add flag for navigation
		if($idpp != ""){
			$tab = explode('.',$idpp);
			$xpathquery = '/';
			for($i=0; $i<count($tab); $i++){
				$xpathquery .= '/menu/item[@name="'.$tab[$i].'"]';
				$elem = $xpath->query($xpathquery)->item(0);
				if($elem) {
					if(($i == count($tab) - 1) && !array_key_exists('query',$_REQUEST)){
						$elem->setAttribute("map","selected");
					}
					else{
						$elem->setAttribute("map","path");
					}
				}
			}
		}
	}

	private function getDom(){
		return $this->doc;
	}


    //Recursive function for parse all subdirectory and make all node
	private function makeNode($doc,$parentnode,$path){
		$dom = new DOMDocument("1.0");
		$dom->load($path."/Xmenu.xml");
		$menu = $dom->firstChild;
		foreach($menu->childNodes as $item){
			if($item->nodeType == XML_ELEMENT_NODE){
				//Static menu
				if($item->nodeName == "item"){
					if ($item->hasAttribute("name")) {
						$name = $item->getAttribute("name");
					}
					else {
						throw new Exception('Element don\'t have a "name" attribut');
					}
					$condition = $item->getAttribute("test");
					if($condition){
						$Kz = $this->kz;
						eval('$test = ('.$condition.');');
						if(!$test) continue;
					}

					$child = $doc->createElement("item");
					$child->setAttribute("name",$name);
					if($item->hasAttribute("meta"))     $child->setAttribute("meta",$item->getAttribute("meta"));
					else                                $child->setAttribute("meta",$this->meta);
					if($item->hasAttribute("idpp"))     $child->setAttribute("idpp",$item->getAttribute("idpp"));
					if($item->hasAttribute("class"))    $child->setAttribute("class",$item->getAttribute("class"));
					if($item->hasAttribute("security")) $child->setAttribute("security",$item->getAttribute("security"));
					if($item->hasAttribute("user")) $child->setAttribute("user",$item->getAttribute("user"));
					$parentnode->appendChild($child);
					// Titles copy from Xmenu static
					foreach($item->getElementsByTagName("title") as $title){
						$lng = $title->getAttributeNS('http://www.w3.org/XML/1998/namespace',"lang");
						$val = $doc->createTextNode($title->nodeValue);
						$newtitle = $doc->createElement("title");
						$newtitle->setAttributeNS('http://www.w3.org/XML/1998/namespace',"lang",$lng);
						$newtitle->appendChild($val);
						$child->appendChild($newtitle);
					}
					if($item->hasAttribute("external"))
					{
						$menu = $doc->createElement("menu");
						$child->appendChild($menu);
						$mod = $item->getAttribute("external");
						$xmenu = $this->kz->getPath('docroot').'/kazoe/mods/'.$mod.'/';
						$this->makeNode($doc,$menu,$xmenu);
					}
					else if(is_file($path."/".$name."/Xmenu.xml"))
					{
						$menu = $doc->createElement("menu");
						$child->appendChild($menu);
						$this->makeNode($doc,$menu,$path."/".$name);
					}
				}
				//Dynamic menu
				elseif($item->nodeName == "item_dyn_base"){
					$query = "";
					foreach($item->childNodes as $child){
						if(get_class($child)!='DOMElement') continue;
						switch($child->tagName){
							case 'query':
								$query = $child->nodeValue;
								break;
						}
					}
					if($query == ""){
						throw new Exception($path.'/Xmenu.xml : Element don\'t have a "query" element');
					}
					$idppl = str_replace("/",".",substr($path,strpos($path,$this->kz->getEnv('META'))+strlen($this->kz->getEnv('META'))+1));
					$query = str_replace(":{apptable}",$this->kz->getAppTable($idppl),$query);
					$sql = $this->kz->db_query($query);
					if (!$sql->execute()) throw new Exception($this->kz->db_error($sql));

					while ($row = $sql->fetch()) {
						$meta = $row["meta"];
						$name = $row["name"];
						$child = $doc->createElement("item");
						$child->setAttribute("meta",$meta);
						$child->setAttribute("name",$name);
						$parentnode->appendChild($child);
						// Titles reads in database
						$sql2 = $this->kz->db_query(
							"SELECT kazoe_sections_titles.lang, kazoe_sections_titles.title FROM kazoe_sections_titles JOIN kazoe_sections ON (kazoe_sections_titles.sectionid = kazoe_sections.id) WHERE kazoe_sections.name = :NAME",
							array(
								'NAME'      => $name
							)
						);
						if (!$sql2->execute()) throw new Exception($this->kz->db_error($sql2));
						while($title = $sql2->fetch()){
							$lng = strtolower($title["lang"]);
							$val = $doc->createTextNode($title["title"]);
							$newtitle = $doc->createElement("title");
							$newtitle->setAttributeNS('http://www.w3.org/XML/1998/namespace',"lang",$lng);
							$newtitle->appendChild($val);
							$child->appendChild($newtitle);
						}
					}
				}
			}
		}
	}

	public function template(){
		//Apply context
		$this->addContext($this->kz->getEnv('IDPP'));
		$XmlMenu = $this->getDom();

		//Apply template
		$XslMenu = new DOMDocument();
		$XslMenu->load($this->kz->getSkinPath().'/Menu/menu.xsl');
		$XsltMenu = new XSLTProcessor();
		$XsltMenu->registerPHPFunctions();
		$XsltMenu->importStyleSheet($XslMenu);
		return $XsltMenu->transformToDoc($XmlMenu);
	}
}
?>
