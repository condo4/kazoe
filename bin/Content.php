<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: Content.php
 * Description: Script to pre-build main contents of the page
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
 
class Content {
	private $kz;
	private $config;
	private $root_config;
	private $filename;
	private $title;
	private $modpath;

	public function __construct($kz){
		$this->kz = $kz;
		$this->modpath = $this->kz->getEnv('PAGEPATH').'/';
		$this->filename = $this->modpath.'Xpage.xml';
		$this->config = new DOMDocument('1.0');
		$this->config->Load($this->filename);
		$this->kz->setEnv('PAGEROOTCONF',$this->config);
		$this->root_config = $this->config;
		$xpath = new DOMXpath($this->config);
		$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
		$xpath->registerNamespace('xp','http://kazoe.org.free.fr/xsd/Xpages.xsd');
		$import = $xpath->query('//xp:import');
		if($import->length > 0)
		{
			$src = $xpath->query('//xp:import/@src');
			$apptable = $xpath->query('//xp:import/@apptable')->item(0)->nodeValue;
			$this->kz->setEnv("APPTABLE",$apptable);
			$modname = $src->item(0)->nodeValue;
			$this->modpath = 'kazoe/mods/'.$modname.'/';
			$this->filename = $this->modpath.'Xpage.xml';
			$this->config = new DOMDocument('1.0');
			$this->config->Load($this->filename);
			$xpath = new DOMXpath($this->config);
			$xpath->registerNamespace('xml','http://www.w3.org/XML/1998/namespace');
			$xpath->registerNamespace('xp','http://kazoe.org.free.fr/xsd/Xpages.xsd');
		}
		$this->kz->setEnv('MODPATH',$this->modpath);
		
		$this->title = $this->kz->getText("PageTitle",false);
		if($this->title == ""){
			$node = $xpath->query('//xp:title[@xml:lang=\''.$this->kz->getEnv('LANG').'\']');
			if($node->length > 0) $this->title = $node->item(0)->nodeValue;
			else {
				$node = $xpath->query('//xp:title[@xml:lang=\''.$this->kz->getEnv('LANG_DEF').'\']');
				if($node->length > 0) $this->title = $node->item(0)->nodeValue;
			}
		}
	}
	
	private function getPage(){
		$XmlData = new DOMDocument('1.0');
		$XmlData_Root = $XmlData->createElement("root");
		$XmlData->appendChild($XmlData_Root);
		$XmlData_Title = $XmlData->createElement("title");
		$XmlData_Title_value = $XmlData->createTextNode($this->title);
		$XmlData_Title->appendChild($XmlData_Title_value);
		$XmlData_Root->appendChild($XmlData_Title);
		if(($this->kz->getEnv('SECTION') != "")){
			$sql = $this->kz->db_query("SELECT COALESCE(sectiontitle.title,sectiontitledef.title) AS title FROM kazoe_sections INNER JOIN kazoe_sections_titles AS sectiontitledef ON (sectiontitledef.sectionid = kazoe_sections.id AND sectiontitledef.lang = :LANG_DEF) LEFT OUTER JOIN kazoe_sections_titles AS sectiontitle ON (sectiontitle.sectionid = kazoe_sections.id AND sectiontitle.lang = :LANG) WHERE kazoe_sections.name = :SECTION AND kazoe_sections.secname = :BASE");
			if (!$sql->execute()) throw new Exception($this->kz->db_error($sql));

			$section = $sql->fetch();
			$section = $section[0];
			$this->title = $this->title.' : '.$section;
			$XmlData_Section = $XmlData->createElement("section");
			$XmlData_Section_value = $XmlData->createTextNode($section);
			$XmlData_Section->appendChild($XmlData_Section_value);
			$XmlData_Root->appendChild($XmlData_Section);
		}
		$nbmax = 0;
		$nbreal = 0;
		foreach($this->config->firstChild->childNodes as $contents) {
			if(strpos($contents->nodeName,"Content") === False){
				continue;
			}
			$condition = $contents->getAttribute('test');
			if($condition){
				$Kz = $this->kz;
				if(eval('$test = ('.$condition.');')===False) throw new Exception("Error in condition [".'$test = ('.$condition.');'."]");
				if(!$test) continue;
			}
			// Need to disable cache ?
			$nocache = $contents->getAttribute('cache');
			if($nocache){
				if($nocache == 'no') $this->kz->disable_cache();
			}
			// Need to don't save in history ?
			$history = $contents->getAttribute('history');
			if($history){
				if($history == 'no') $this->kz->setEnv('HISTORY',False);
			}
			
			$classname = $contents->nodeName;
			$maker = new $classname($this->kz,$contents);
			$maker->append_xml($XmlData,$XmlData_Root,$this->filename);
			
			
			$continue = ($contents->getAttribute('continue'))?($contents->getAttribute('continue')):("no");
			if($continue != "yes") break;

		}
		return $XmlData;
	}
	
	public function template(){
		//Apply parser
		$XmlData = $this->getPage();
		//Apply template
		$XslSkinContent = new DOMDocument();
		$XslSkinContent->load($this->kz->getSkinPath().'/Content/content.xsl');
		$XsltContent = new XSLTProcessor();
		$XsltContent->registerPHPFunctions();
		$XsltContent->importStyleSheet($XslSkinContent);
		return $XsltContent->transformToDoc($XmlData);
	}
}
?>
