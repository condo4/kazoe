<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: builder_dynamic.php
 * Description: Builder for dynamic page generate from sql bases
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

class ContentDynamic{
	private $contents;
	private $kdata;

	function __construct($kdata, $contents) {
		$this->contents = $contents;
		$this->kdata = $kdata;
	}
	
	function append_xml($XmlData,$XmlData_Root,$filename)
	{
		$XmlDataSource = new DOMDocument('1.0');
		$XmlDataSource_data = $XmlDataSource->createElement("data");
		$XmlDataSource->appendChild($XmlDataSource_data);
	
		if($this->contents->getElementsByTagName("limit")->length > 0){
			$limit = (int)$this->contents->getElementsByTagName("limit")->item(0)->nodeValue;
		}
		else{
			$limit = 0;
		}
		$offset = $this->kdata->getEnv('PAGE')*$limit;
		if($limit != 0) $extend_query = " LIMIT ".$limit." OFFSET ".$offset;
		else $extend_query = "";

		if($this->contents->getElementsByTagName("template")->length > 0){
			$template_base = $this->contents->getElementsByTagName("template")->item(0)->nodeValue;
			if($this->contents->getElementsByTagName("template")->item(0)->getAttribute("mltlng") == 'yes'){
				if(is_file(str_replace('Xpage.xml',$template_base.'_'.strtoupper($this->kdata->getEnv('LANG')).'.xsl',$filename))){
					$template = str_replace('Xpage.xml',$template_base.'_'.strtoupper($this->kdata->getEnv('LANG')).'.xsl',$filename);
				}
				else{
					$template = str_replace('Xpage.xml',$template_base.'_'.strtoupper($this->kdata->getEnv('LANG_DEF')).'.xsl',$filename);
				}
			}
			else{
				$template = str_replace('Xpage.xml',$template_base.'.xsl',$filename);
			}
		}
		else{
			$template = "";
		}

		if($this->contents->getElementsByTagName("query_counter")->length > 0){
			$query_counter = $this->contents->getElementsByTagName("query_counter")->item(0)->nodeValue;
			$query = $this->kdata->db_query($query_counter);
			if($query->execute()){
				$nbmax = $query->fetch();
				$nbmax = $nbmax[0];
			}
			else{
				throw new Exception($this->kdata->db_error($query));
			}
		}
		else{
			$nbmax = 1;
		}

		if($this->contents->getElementsByTagName("section")->length > 0){
			$section = $this->contents->getElementsByTagName("section")->item(0)->nodeValue;
		}
		else{
			$section = $this->kdata->getEnv('BASE');
		}
		$query = $this->contents->getElementsByTagName("query")->item(0)->nodeValue;
		$query  = $query.$extend_query;
		$sql = $this->kdata->db_query($query);

		if($sql->execute()){
			$nbreal = $sql->rowCount();
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$XmlDataSource_item = $XmlDataSource->createElement("item");
				$XmlDataSource_data->appendChild($XmlDataSource_item);
				foreach(array_keys($row) as $key){
					$value = $row[$key];
					$XmlDataSource_key = $XmlDataSource->createElement($key);
					$XmlDataSource_item->appendChild($XmlDataSource_key);
					$rte = !strncmp($value, "#@RTE@#", strlen("#@RTE@#"));
					if($rte)
					{
						$value = str_replace("#@RTE@#","",$value);
					}
					else
					{
						$value = str_replace("\n","<br />\n",$value);
					}
					$XmlDataSource_key_value = $XmlDataSource->createCDATASection($value);
					$XmlDataSource_key->appendChild($XmlDataSource_key_value);
				}
				if($section != ""){
					if ($this->kdata->canDo('section[@id=\''.$section.'\']/action')){
						foreach($this->kdata->getRight('section[@id=\''.$section.'\']/action') as  $action){
							$type = "__".strtoupper($action->getAttribute("id"))."__";
							$XmlDataSource_key = $XmlDataSource->createElement($type);
							$XmlDataSource_item->appendChild($XmlDataSource_key);
						}
					}
				}
			}
		}
		else{
			throw new Exception($this->kdata->db_error($sql));
		}
		if($limit > 0) $nbpagemax = ceil($nbmax / $limit);
		else $nbpagemax = 1;
		$this->kdata->setEnv('PAGEMAX',$nbpagemax);

		//Apply local template
		if($template != ""){
			$XslDataSource = new DOMDocument();
			$XslDataSource->load($template);
			$XsltDataSource = new XSLTProcessor();
			$XsltDataSource->registerPHPFunctions();
			$XsltDataSource->importStyleSheet($XslDataSource);
			$XmlDataContent = $XsltDataSource->transformToDoc($XmlDataSource);

			//Transfert node to final result
			$XmlData_body = $XmlData->createElement("body");
			$XmlData_Root->appendChild($XmlData_body);
			foreach($XmlDataContent->getElementsByTagName("data") as $data){
				foreach($data->childNodes as $child){
					$XmlData_item = $XmlData->importNode($child,true);
					$XmlData_body->appendChild($XmlData_item);
				}
			}
		}
	}
}

?>
