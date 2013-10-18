<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: Head.php
 * Description: Script to pre-build head of the page
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
class Skin{
	private $kz;

	public function __construct($kz){
		$this->kz = $kz;
	}
	
	public function template(){
		$XmlSession = new DOMDocument();
		$XmlSession_root = $XmlSession->createElement("templatenode");
		$XmlSession->appendChild($XmlSession_root);
		
		foreach($this->kz->skinconf->query('//skin') as $contents) {
			$id = $contents->attributes->getNamedItem('id')->value;
			$lang = $this->kz->getEnv('LANG');
			$node = $this->kz->skinconf->query("//skin[@id='$id']/desc[@xml:lang='$lang']");
			if($node->length != 1)
			{
				$lang = $this->kz->getEnv('LANG_DEF');
				$node = $this->kz->skinconf->query("//skin[@id='$id']/desc[@xml:lang='$lang']");
			}
			$desc = $node->item(0)->nodeValue;
			$XmlSession_skin = $XmlSession->createElement("skin");
			$XmlSession_skin->setAttribute('id',$id);
			$XmlSession_skin->nodeValue = $desc;
			$XmlSession_root->appendChild($XmlSession_skin);
		}

		//Apply template
		$XslSession = new DOMDocument();
		$XslSession->load($this->kz->getSkinPath().'/Skin/Skin.xsl');
		$XsltSession = new XSLTProcessor();
		$XsltSession->registerPHPFunctions();
		$XsltSession->importStyleSheet($XslSession);
		return $XsltSession->transformToDoc($XmlSession);
	}
}
?>