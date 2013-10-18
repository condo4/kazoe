<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: builder_forms.php
 * Description: Builder for formular page
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
class ContentForm {
	private $contents;
	private $kdata;

	function __construct($kdata, $contents) {
		$this->contents = $contents;
		$this->kdata = $kdata;
	}
	
	function append_xml($XmlData,$XmlData_Root,$filename)
	{
		global $Kz;
		$Kz = $this->kdata;
		$XmlData_body = $XmlData->createElement("body");
		$XmlData_Root->appendChild($XmlData_body);
		$XmlData_form = $XmlData->createElement("form");
		$XmlData_form->setAttribute("method","post");
		$XmlData_form->setAttribute("action",$this->kdata->getEnv('PAGEURL'));
		$XmlData_form->setAttribute("enctype","multipart/form-data");
		$XmlData_body->appendChild($XmlData_form);
		$dataset        = $this->contents->getElementsByTagName("dataset")->item(0)->nodeValue;
		$return           = $this->contents->getElementsByTagName("return")->item(0)->nodeValue;

		if($this->kdata->canDo('//richtextedit'))
		{
			foreach($this->contents->getElementsByTagName("script") as $script){
				$script = $script->nodeValue;
				$res = preg_replace_callback('#(\$\{[A-Za-z]*\})#',
					function ($matches)
					{
						global $Kz;
						global $script;
						preg_match('@^.*\$\{([A-Za-z]*)\}.*@i',$matches[1], $varname);
						return $this->kdata->getEnv($varname[1]);
					},$script);
				$this->kdata->addJsScript($res);
			}
		}

		$sqlquery = $this->contents->getElementsByTagName("query");
		if($sqlquery->length > 0){
			$sql = $this->kdata->db_query($sqlquery->item(0)->nodeValue);
			if(!$sql->execute()) throw new Exception($this->kdata->db_error($sql->errorInfo()));
			$pre_value = $sql->fetch(PDO::FETCH_ASSOC);
		}
		else {
			$pre_value = False;
		}

		
		$datasrcfile    = $this->kdata->getEnv('MODPATH').$dataset.".dataset.xml";
		if(!is_file($datasrcfile)) throw new Exception("Unknown dataset:".$dataset);
		$XmlSrc = new DOMDocument("1.0");
		$XmlSrc->load($datasrcfile);
		foreach($XmlSrc->getElementsByTagName("field") as $field){
			$name = $field->getAttribute("name");
			/* If we are in MODIFY mod, we add default_value node to each element */
			if($pre_value){
				if(array_key_exists($name,$pre_value)){
					$value = $XmlSrc->createElement("value");
					$field->appendChild($value);
					$rte = !strncmp($pre_value[$name], "#@RTE@#", strlen("#@RTE@#"));
					if($rte)
					{
						$pre_value[$name] = str_replace("#@RTE@#","",$pre_value[$name]);
					}
					
					$cdata = $XmlSrc->createCDATASection($pre_value[$name]);
					$value->appendChild($cdata);
				}
			}
			$hidden = ($field->hasAttribute("hidden"))?($field->getAttribute("hidden")=="true"):false;
			if($hidden) continue;
			$widget = null;
			$legend = null;
			foreach($field->childNodes as $child){
				if($child->nodeType != XML_ELEMENT_NODE) continue;
				if(!(strpos($child->nodeName,"widget") === false)) $widget = $child;
			}
			if($widget == null) throw new Exception("No widget found in ".$datasrcfile);
			$wtype = substr($widget->nodeName,7);
			$widgetfile = "kazoe/bin/widgets/".$wtype.".xsl";
			$widgetpreconfig = "kazoe/bin/widgets/".$wtype.".php";
			if(!is_file($widgetfile)) continue;
			if(is_file($widgetpreconfig)) include $widgetpreconfig;
			$XslWidget = new DOMDocument();
			$XslWidget->load($widgetfile);
			$XsltWidget = new XSLTProcessor();
			$XsltWidget->importStyleSheet($XslWidget);
			$XsltWidget->setParameter(null,"name",$name);
			$XsltWidget->setParameter(null,"lng",$this->kdata->getEnv('LANG'));
			$XsltWidget->setParameter(null,"lngdef",$this->kdata->getEnv('LANG_DEF'));
			$XmlDataContent = $XsltWidget->transformToDoc($field);
			$XmlData_item = $XmlData->importNode($XmlDataContent->firstChild,true);
			$XmlData_form->appendChild($XmlData_item);
		}
		$XslWidget = new DOMDocument();
		$XslWidget->load("kazoe/bin/widgets/validation.xsl");
		$XsltWidget = new XSLTProcessor();
		$XsltWidget->importStyleSheet($XslWidget);
		$XsltWidget->setParameter(null,"name","Submit");
		$XsltWidget->setParameter(null,"return",$return);
		$XmlDataContent = $XsltWidget->transformToDoc($field);
		$XmlData_item = $XmlData->importNode($XmlDataContent->firstChild,true);
		$XmlData_form->appendChild($XmlData_item);
	}
}
?>
