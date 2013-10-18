<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: builder_static.php
 * Description: Builder for statics pages
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

class ContentStatic {
	private $contents;
	private $kdata;

	function __construct($kdata, $contents) {
		$this->contents = $contents;
		$this->kdata = $kdata;
	}
	
	function append_xml($XmlData,$XmlData_Root,$filename)
	{
		foreach($this->contents->getElementsByTagName("namefile") as $namefile){
			$condition = $namefile->getAttribute('test');
			if($condition){
				eval('$test = ('.$condition.');');
				if(!$test) continue;
			}
			if($namefile->getAttribute("mltlng") == 'yes'){
				if(is_file(str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG')).'.xml',$filename))){
					$filename = str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG')).'.xml',$filename);
				}
				else{
					$filename = str_replace('Xpage.xml',$namefile->nodeValue.'_'.strtoupper($this->kdata->getEnv('LANG_DEF')).'.xml',$filename);
				}
			}
			else{
				$filename =$namefile->nodeValue.'.xml';
			}
			break;
		}

		if($this->kdata->canEdit($filename) && (((isset($_POST["action"]))?($_POST["action"]):("not")) == "save"))
		{
			$XmlSrc = new DOMDocument('1.0');
			$doc = htmlspecialchars_decode($_POST["page"]);
			$doc = html_entity_decode($doc,ENT_COMPAT,"UTF-8");
			$doc = str_replace('<!--<pictures_frame>','<pictures_frame>',$doc);
			$doc = str_replace('</pictures_frame>-->','</pictures_frame>',$doc);
			$doc = str_replace('<!--?xml version="1.0" encoding="utf-8"?-->','',$doc);
			$doc = '<body>'.$doc."\n</body>\n";
			$XmlSrc->loadXML($doc);
			$content = html_entity_decode($XmlSrc->saveXML(),ENT_COMPAT,"UTF-8");
			$content = str_replace('<!--<pictures_frame>','<pictures_frame>',$content);
			$content = str_replace('</pictures_frame>-->','</pictures_frame>',$content);
			$content = str_replace('<!--?xml version="1.0" encoding="utf-8"?-->','',$content);
			$content = str_replace('<?xml version="1.0"?>','',$content);
			$content = str_replace('<!--?xml version="1.0"?-->','',$content);
			$content = '<?xml version="1.0" encoding="utf-8"?>'."\n".$content;
			file_put_contents($filename,$content);
			$command = 'git commit -m "'.str_replace("\"","\\\"",$_POST["comment"]).'" --author "'.$this->kdata->getEnv('USER_NAME').' <'.$this->kdata->getEnv('USER_EMAIL').'>" '.str_replace("root/","",$filename)." 2>&1 >> ".$_SERVER['DOCUMENT_ROOT']."/var/log/git.log";
			$olddir = getcwd();
			chdir($_SERVER['DOCUMENT_ROOT']."/root");
			shell_exec($command);
			chdir($olddir);
			$this->kdata->uncache();
			$XmlSrc = new DOMDocument('1.0');
			$XmlSrc->load($filename);
			$XmlData_body = $XmlData->importNode($XmlSrc->firstChild,true);
			if($this->kdata->canEdit($filename))
			{
				$src = new DOMDocument('1.0');
				$xml = "<form name='input' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='filename' value='".$filename."' /><input type='submit' value='".$this->kdata->getText("EditPage")."' /></form>";
				$src->loadXML($xml);
				$node = $XmlData->importNode($src->firstChild,true);
				$XmlData_body->appendChild($node);
			}
			$XmlData_Root->appendChild($XmlData_body);
		}
		elseif($this->kdata->canEdit($filename) && (((isset($_POST["action"]))?($_POST["action"]):("not")) == "preview"))
		{
			$XmlSrc = new DOMDocument('1.0');
			$doc = htmlspecialchars_decode($_POST["page"]);
			$doc = html_entity_decode($doc,ENT_COMPAT,"UTF-8");
			$doc = str_replace('<!--<pictures_frame>','<pictures_frame>',$doc);
			$doc = str_replace('</pictures_frame>-->','</pictures_frame>',$doc);
			$doc = str_replace('<!--?xml version="1.0" encoding="utf-8"?-->','',$doc);
			$doc = '<body>'.$doc."\n</body>\n";
			$XmlSrc->loadXML($doc);
			$XmlData_body = $XmlData->importNode($XmlSrc->firstChild,true);
			
			$src = new DOMDocument('1.0');
			$xml = "<form class='save' method='post'><input type='hidden' name='action' value='save' /><textarea name='page' id='page' rows='80' cols='100' style='display: none'>";
			
			$data = $_POST["page"];
			$data = str_replace('<pictures_frame>','<!--<pictures_frame>',$data);
			$data = str_replace('</pictures_frame>','</pictures_frame>-->',$data);
			$data = htmlspecialchars($data);

			$xml .=  $data;
			$xml .= "</textarea><h1>".$this->kdata->getText("NeedComment")."</h1><br/><textarea name='comment' id='comment' rows='15' cols='100' style='width: 100%'></textarea><br/><input type='hidden' name='filename' value='".$filename."' /><input type='submit' value='".$this->kdata->getText("SavePage")."' /></form>";
			file_put_contents('/tmp/FULL.xml',$xml);
			$src->loadXML($xml);
			$node = $XmlData->importNode($src->firstChild,true);
			$XmlData_body->appendChild($node);
			
			$XmlData_Root->appendChild($XmlData_body);
		}
		elseif($this->kdata->canEdit($filename) && (((isset($_POST["action"]))?($_POST["action"]):("not")) == "edit"))
		{
			$XmlData_body = $XmlData->createElement("body");
			$XmlData_Root->appendChild($XmlData_body);
			$XmlSrc = new DOMDocument('1.0');
			$root = $XmlSrc->createElement("root");
			$XmlSrc->appendChild($root);
			$src = new DOMDocument('1.0');
			$out = '<body>';
			$out .= "<form name='input' method='post'>";
			$out .= "<input type='hidden' name='action' value='preview' />";
			$out .= "<input type='hidden' name='filename' value='".$filename."' />";
			$out .= "<textarea class='ckeditor' name='page' id='page' rows='80' cols='100' style='width: 100%'>";
			
			$data = file_get_contents($filename);
			$data = str_replace('<pictures_frame>','<!--<pictures_frame>',$data);
			$data = str_replace('</pictures_frame>','</pictures_frame>-->',$data);
			$data = htmlspecialchars($data);
			$out .= $data;
			
			$out .= "</textarea><br />";
			$out .= "<input type='submit' value='".$this->kdata->getText("PreviewPage")."' />";
			$out .= "</form>";
			$out .= '</body>';
			$src->loadXML($out);
			$node = $XmlData->importNode($src->firstChild,true);
			$XmlData_body->appendChild($node);
			
			$this->kdata->addJsScript('kazoe/lib/tinymce/tinymce.min.js');
			$this->kdata->addJsScript('kazoe/lib/tinymce_config.php');

		}
		else
		{
			$XmlSrc = new DOMDocument('1.0');
			$XmlSrc->load($filename);
			$XmlData_body = $XmlData->importNode($XmlSrc->firstChild,true);
			if($this->kdata->canEdit($filename))
			{
				$src = new DOMDocument('1.0');
				$xml = "<form name='input' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='filename' value='".$filename."' /><input type='submit' value='".$this->kdata->getText("EditPage")."' /></form>";
				$src->loadXML($xml);
				$node = $XmlData->importNode($src->firstChild,true);
				$XmlData_body->appendChild($node);
			}
			$XmlData_Root->appendChild($XmlData_body);
		}
	}
}
?>
