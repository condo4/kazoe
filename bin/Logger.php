<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: exceptionManager.php
 * Description: This file contain the mechanisme to intercept all errors
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
$GLOBALS['bots'] = array();
$GLOBALS['bots'][] = "AhrefsBot";
$GLOBALS['bots'][] = "bingbot";
$GLOBALS['bots'][] = "YandexBot";
$GLOBALS['bots'][] = "Cynthia";
$GLOBALS['bots'][] = "FeedValidator";
$GLOBALS['bots'][] = "Googlebot";
$GLOBALS['bots'][] = "Jigsaw";
$GLOBALS['bots'][] = "W3C_Validator";
$GLOBALS['bots'][] = "msnbot";
$GLOBALS['bots'][] = "GSLFbot";
$GLOBALS['bots'][] = "Ezooms";
$GLOBALS['bots'][] = "Crawler";
$GLOBALS['bots'][] = "EC2LinkFinder";
$GLOBALS['bots'][] = "Exabot";
$GLOBALS['bots'][] = "SWEBot";
$GLOBALS['bots'][] = "WBSearchBot";
$GLOBALS['bots'][] = "SeznamBot";
$GLOBALS['bots'][] = "Linguee";
$GLOBALS['bots'][] = "Aboundex";
$GLOBALS['bots'][] = "TurnitinBot";
$GLOBALS['bots'][] = "Wotbox";
$GLOBALS['bots'][] = "MJ12bot";
$GLOBALS['bots'][] = "Baiduspider";
$GLOBALS['bots'][] = "Sogou";
$GLOBALS['bots'][] = "EdisterBot";
$GLOBALS['bots'][] = "findlinks";
$GLOBALS['bots'][] = "CCBot";
$GLOBALS['bots'][] = "OrangeCrawler";
$GLOBALS['bots'][] = "DoCoMo";
$GLOBALS['bots'][] = "360Spider";
$GLOBALS['bots'][] = "discoverybot";
$GLOBALS['bots'][] = "RU_Bot";
$GLOBALS['bots'][] = "EasouSpider";
$GLOBALS['bots'][] = "DigExt";
$GLOBALS['bots'][] = "panscient.com";

class Logger
{
	private $kz;
	
	public function __construct($kz){
		$this->kz = $kz;
	}

	public function log_hit_into_database(){
		$filename = $_SERVER['DOCUMENT_ROOT'].'/var/log/hits.log';

		$REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		$SESSIONID = session_id();
		$USER_AGENT = (array_key_exists("HTTP_USER_AGENT",$_SERVER))?($_SERVER["HTTP_USER_AGENT"]):("");
		
		foreach ( $GLOBALS['bots'] as $bot )
		{
			if (strpos($USER_AGENT,$bot) !== false) {
				$filename = $_SERVER['DOCUMENT_ROOT'].'/var/log/bots.log';
				break;
			}
		}
		
		if($this->kz->getEnv('USER_ID') != -1){
			$user = $this->kz->getenv('USER_LOGIN');
		}
		else{
			$user = "anonymous";
		}
		$line = sprintf("%s | %-16s | %-15s | %-24s | %s | %s",date('Y-m-d H:i:s'),$user,$_SERVER['REMOTE_ADDR'], session_id(), $_SERVER['REQUEST_URI'], $USER_AGENT);
		file_put_contents($filename, $line . PHP_EOL, FILE_APPEND);
		
		return;
	}
}
?>
