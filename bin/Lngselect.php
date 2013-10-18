<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: Lngselect.php
 * Description: Script to pre-build language selector of the page
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

class Lngselect{
	private $kz;

	public function __construct($kz){
		$this->kz = $kz;
	}
	
	public function template(){
		$XmlLngselect = new DOMDocument();
		$XmlLngselect_root = $XmlLngselect->createElement("templatenode");
		$XmlLngselect->appendChild($XmlLngselect_root);
		$XmlLngselect_lngselect = $XmlLngselect->createElement("lngselect");
		$XmlLngselect_root->appendChild($XmlLngselect_lngselect);

		for($i=0; $i < $this->kz->getNbLang(); $i++){
			$lang = $this->kz->getLangKey($i);
			$XmlLngselect_lang = $XmlLngselect->createElement("lang");
			$XmlLngselect_lang->setAttribute('lang',$lang);
			$XmlLngselect_lang->setAttribute('name',$this->kz->getLangDesc($lang));
			$XmlLngselect_lngselect->appendChild($XmlLngselect_lang);
		}

		//Apply template
		$XslLngselect = new DOMDocument();
		$XslLngselect->load($this->kz->getSkinPath().'/Lngselect/Lngselect.xsl');
		$XsltLngselect = new XSLTProcessor();
		$XsltLngselect->registerPHPFunctions();
		$XsltLngselect->importStyleSheet($XslLngselect);
		return $XsltLngselect->transformToDoc($XmlLngselect);
	}
}

?>
