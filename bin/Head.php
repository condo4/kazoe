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

class Head{
	private $kz;

	public function __construct($kz){
		$this->kz = $kz;
	}
	
	public function template(){
		$XmlHeader = new DOMDocument();
		$XmlHeader->load($this->kz->getPath('docroot').'/root/etc/header.xml');
		$Headers = new DOMXpath($XmlHeader);
		$XmlSkin = new DOMDocument();
		$XmlSkin->load($this->kz->getSkinPath().'/skin.xml');
		$XPath_XmlSkin = new DOMXpath($XmlSkin);
		$XmlHead = new DOMDocument();
		$XmlHead_root = $XmlHead->createElement("templatenode");
		$XmlHead->appendChild($XmlHead_root);
		$XmlHead_head = $XmlHead->createElement("head");
		$XmlHead_root->appendChild($XmlHead_head);

		//build meta
		$XmlHead_meta = $XmlHead->createElement("meta");
		$XmlHead_meta->setAttribute('http-equiv','Content-Language');
		$XmlHead_meta->setAttribute('content',$this->kz->getEnv('LANG'));
		$XmlHead_head->appendChild($XmlHead_meta);
		$XmlHead_meta = $XmlHead->createElement("meta");
		$XmlHead_meta->setAttribute('http-equiv','Content-Type');
		$XmlHead_meta->setAttribute('content','text/html; charset=utf-8');
		$XmlHead_head->appendChild($XmlHead_meta);

		foreach($Headers->query("//meta/node()") as $srcmeta){
			if($srcmeta->nodeType != 1) continue;
			$XmlHead_meta = $XmlHead->createElement("meta");
			$XmlHead_meta->setAttribute('name',$srcmeta->nodeName);
			$XmlHead_meta->setAttribute('content',$srcmeta->nodeValue);
			$XmlHead_head->appendChild($XmlHead_meta);
		}

		//Build title
		$path = $this->kz->getEnv('PAGEPATH');
		$fileXpage = $path.'/Xpage.xml';
		$XmlXpage = new DOMDocument('1.0');
		$XmlXpage->load($fileXpage);
		$XPath_XmlXpage = new DOMXpath($XmlXpage);
		if(!$XPath_XmlXpage->registerNamespace('xml','http://www.w3.org/XML/1998/namespace'))    throw new Exception("Impossible to register xml namespace for ".$fileXpage);
		if(!$XPath_XmlXpage->registerNamespace('xp','http://kazoe.org.free.fr/xsd/Xpages.xsd'))  throw new Exception("Impossible to register xp namespace for ".$fileXpage);
		
		$node = $XPath_XmlXpage->query('//xp:title[@xml:lang=\''.$this->kz->getEnv('LANG').'\']');
		if($node->length > 0) $titleval = $node->item(0)->nodeValue;
		else {
			$node = $XPath_XmlXpage->query('//xp:title[@xml:lang=\''.$this->kz->getEnv('LANG_DEF').'\']');
			if($node->length > 0) $titleval = $node->item(0)->nodeValue;
			else $titleval = $this->kz->getText("PageTitle");
		}
		$XmlHead_title = $XmlHead->createElement("title");
		$XmlHead_head->appendChild($XmlHead_title);
		$XmlHead_title->appendChild($XmlHead->createTextNode($Headers->query("/header/title")->item(0)->nodeValue.' : '.$titleval));

		//Build favicon
		if($Headers->query("/header/favicon")->length == 1){
			$XmlHead_link = $XmlHead->createElement("link");
			$XmlHead_link->setAttribute('rel',"shortcut icon");
			$XmlHead_link->setAttribute('href',($Headers->query("/header/favicon")->item(0)->nodeValue));
			$XmlHead_head->appendChild($XmlHead_link);
		}

		//Build globals stylesheets
		$XmlSkin_skin = $XPath_XmlSkin->query('/skin/stylesheets/css');
		foreach($XmlSkin_skin as $css){
			$headmetacondition = $css->getAttribute('meta');
			if(($headmetacondition == 'all') | (stripos($headmetacondition,'*'.$this->kz->getEnv('SKIN').'*'))){
				$XmlHead_link = $XmlHead->createElement("link");
				$XmlHead_link->setAttribute('rel','stylesheet');
				$XmlHead_link->setAttribute('href','skin/'.$this->kz->getEnv('SKIN').'/'.$css->getAttribute('name').'/'.$css->getAttribute('media').'.css');
				$XmlHead_link->setAttribute('type','text/css');
				$XmlHead_link->setAttribute('media',$css->getAttribute('media'));
				$XmlHead_head->appendChild($XmlHead_link);
			}
		}

		//Build locals stylesheets
		$XmlXpage_css = $XPath_XmlXpage->query('//xp:css');
		foreach($XmlXpage_css as $css){
				$XmlHead_link = $XmlHead->createElement("link");
				$XmlHead_link->setAttribute('rel','stylesheet');
				$XmlHead_link->setAttribute('href',str_replace('//','/',$path.'/'.$css->nodeValue.'.css'));
				$XmlHead_link->setAttribute('type','text/css');
				$XmlHead_link->setAttribute('media',$css->getAttribute('media'));
				$XmlHead_head->appendChild($XmlHead_link);
		}

		//Build globals scripts
		$XmlSkin_js = $XPath_XmlSkin->query('/skin/scripts/js');
		foreach($XmlSkin_js as $js){
			$headmetacondition = $js->getAttribute('meta');
			if(($headmetacondition == 'all') | (stripos($headmetacondition,'*'.$this->kz->getEnv('SKIN').'*'))){
				$XmlHead_script = $XmlHead->createElement("script");
				$XmlHead_script->setAttribute('src','skin/'.$this->kz->getEnv('SKIN').'/'.$js->getAttribute('file'));
				$XmlHead_script->setAttribute('type','text/javascript');
				$XmlHead_head->appendChild($XmlHead_script);
			}
		}
		//Build locals scripts
		$XmlXpage_js = $XPath_XmlXpage->query('//xp:js');
		foreach($XmlXpage_js as $js){
				$XmlHead_script = $XmlHead->createElement("script");
				$XmlHead_script->setAttribute('src',str_replace('//','/',$path.'/'.$js->nodeValue.'.js'));
				$XmlHead_script->setAttribute('type','text/javascript');
				$XmlHead_head->appendChild($XmlHead_script);
		}


		//Apply template
		$XslHead = new DOMDocument();
		$XslHead->load($this->kz->getSkinPath().'/Head/Head.xsl');
		$XsltHead = new XSLTProcessor();
		$XsltHead->importStyleSheet($XslHead);
		return $XsltHead->transformToDoc($XmlHead);
	}
}
?>