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

/*
 * Early Error Management
 */
ini_set('display_errors','On');
ini_set('log_errors','On');
ini_set('warn_plus_overloading','On');
ini_set('magic_quotes_gpc', 0);

function __autoload($class_name) {
    include $_SERVER['DOCUMENT_ROOT'].'/kazoe/bin/'.$class_name . '.php';
}

libxml_disable_entity_loader(false);

$conf = new DOMDocument();
$conf->load($_SERVER['DOCUMENT_ROOT'].'/root/etc/configurations.xml');
$xconf = new DOMXpath($conf);
date_default_timezone_set($xconf->query("/configuration/default_timezone")->item(0)->nodeValue);

/*
 * Prepare var directory 
 */
if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/var')){
    mkdir($_SERVER['DOCUMENT_ROOT'].'/var',0755);
	$f = fopen($_SERVER['DOCUMENT_ROOT']."/var/.htaccess","w");
	fwrite($f,"deny from all\n<Files \"*.jpg\">\nAllow from all\n</Files>");
	fclose($f);
}
if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/var/log')){
    mkdir($_SERVER['DOCUMENT_ROOT'].'/var/log',0755);
}
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/var/log/messages')){
    touch($_SERVER['DOCUMENT_ROOT'].'/var/log/messages');
}
if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/var/cache')){
    mkdir($_SERVER['DOCUMENT_ROOT'].'/var/cache',0755);
}

if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/var/images')){
    mkdir($_SERVER['DOCUMENT_ROOT'].'/var/images',0755);
}

ini_set('error_log',$_SERVER['DOCUMENT_ROOT'].'/var/log/messages');
error_reporting(E_ALL);
?>
