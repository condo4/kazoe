<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: kernel.php
 * Description: Main entry point for KaZoe website
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
include('kconfig.php');
/*
 * Enable Session
 */
session_start();

/*
 * Set encoding
 */
header("Content-Type: text/html; charset= UTF-8");



/*
 * Initialize all globals
 */
$Kz = new KData();


/*
 * Set exceptions manager
 */
include($Kz->getPath('docroot').'/kazoe/bin/exceptionManager.php');
set_exception_handler('exception_handler');
set_error_handler('error_handler');
/*
 * Cache System
 */
if(($Kz->getCache()) && (is_file($Kz->getPath('docroot')."/var/cache/".$Kz->getEnv('SKIN')."/".$Kz->getPageName()))){
    $page = file_get_contents($Kz->getPath('docroot')."/var/cache/".$Kz->getEnv('SKIN')."/".$Kz->getPageName(),'r');
}
else {
    /*
     * Load ROOT Template
     */
    $XmlTemplate = new DOMDocument();
    $XmlTemplate->load($Kz->getSkinPath().'/template.xml');
    $Kz->setTemplate($XmlTemplate);
    /*
     * Replace recursively all sub-Template by calculate result
     */
    $XmlTemplate_template = $XmlTemplate->getElementsByTagName('template');
    while($XmlTemplate_template->length > 0){
        $XmlTemplate_templateNode = $XmlTemplate_template->item(0);
        $templatename = $XmlTemplate_templateNode->getAttribute('src');
        $return = "";
        $templateBuilder = new $templatename($Kz);
        $subtemplate = $templateBuilder->template();
        $XmlTemplate_templateNoderoot = $subtemplate->getElementsByTagName("templatenode")->Item(0);
        foreach($XmlTemplate_templateNoderoot->childNodes as $child){
            if($child->nodeType == XML_ELEMENT_NODE){
                $newroot = $child;
            }
        }
        $newnode = $XmlTemplate->importNode($newroot,true);
        $XmlTemplate_templateNode->parentNode->replaceChild($newnode,$XmlTemplate_templateNode);
        if($templatename == "Head")
        {
            $Kz->setHeaders($newnode);
        }
        // If they are sub-template in the current sub-template; we redo until they are no template tag in result
        $XmlTemplate_template = $XmlTemplate->getElementsByTagName('template');
    }

    /*
     * Display the result
     */
    $page = $XmlTemplate->saveXML();
    date_default_timezone_set("Europe/Berlin");
    $page .= "\n<!--".date("Y/m/d H:i:s")."-->";
    if($Kz->getCache()){
		if(!is_dir($Kz->getPath('docroot')."/var/cache/".$Kz->getEnv('SKIN'))){
			mkdir($Kz->getPath('docroot')."/var/cache/".$Kz->getEnv('SKIN'),0755);
		}
        $f = fopen($Kz->getPath('docroot')."/var/cache/".$Kz->getEnv('SKIN')."/".$Kz->getPageName(),"w");
        fwrite($f,$page);
        fclose($f);
    }
}
echo $page;
$Kz->quit();
?>
