<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: builder_php.php
 * Description: Builder for native php page
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
class ContentPhp {
	private $contents;
	private $kdata;

	function __construct($kdata, $contents) {
		$this->contents = $contents;
		$this->kdata = $kdata;
	}
	
	function append_xml($XmlData,$XmlData_Root,$filename)
	{
		$XmlData_body = $XmlData->createElement("body");
		$XmlData_Root->appendChild($XmlData_body);

		foreach($this->contents->getElementsByTagName("namefile") as $namefile){
			$condition = $namefile->getAttribute('test');
			if($condition){
				eval('$test = ('.$condition.');');
				if(!$test) continue;
			}
			if($namefile->getAttribute("mltlng") == 'yes'){
				if(is_file(str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG')).'.php',$filename))){
					$filephp = str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG')).'.php',$filename);
				}
				else{
					$filephp = str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG_DEF')).'.php',$filename);
				}
			}
			else{
				$filephp = str_replace('Xpage.xml',$namefile->nodeValue.'.php',$filename);
			}
			break;
		}


		$XmlSrc = new DOMDocument('1.0');
		$root = $XmlSrc->createElement("root");
		$XmlSrc->appendChild($root);
		$src = new DOMDocument('1.0');
		$Kz = $this->kdata;
		$out = '<body>';
		ob_start();
		include $filephp;
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= '</body>';
		$src->loadXML($out);
		$node = $XmlData->importNode($src->firstChild,true);
		$XmlData_body->appendChild($node);
	}
}
?>
