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

function log_error_bug($level,$message,$file,$line){
	global $Kz;
	$traces = print_r(debug_backtrace(),true);
	$Kz->setError();
	
	$filename = $_SERVER['DOCUMENT_ROOT'].'/var/log/error_'.date('Y-m-d_H:i:s').'.log';
	
	if($Kz->getEnv('USER_ID') != -1){
		$user = $Kz->getenv('USER_LOGIN');
	}
	else{
		$user = "anonymous";
	}

	file_put_contents($filename, "DATE: ".date('Y-m-d H:i:s'). PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR']. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "LOGIN: ".$user. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "SESSION: ".session_id(). PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "SERVER_NAME: ".$_SERVER['SERVER_NAME']. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "REQUEST_URI: ".$_SERVER['REQUEST_URI']. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "HTTP_USER_AGENT: ".$_SERVER['HTTP_USER_AGENT']. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "FILE: ".$file. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "LINE: ".$line. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "ERROR: ".$message. PHP_EOL, FILE_APPEND);
	file_put_contents($filename, "GET:". PHP_EOL, FILE_APPEND);
	file_put_contents($filename, print_r($_GET,true), FILE_APPEND);
	file_put_contents($filename, "POST:". PHP_EOL, FILE_APPEND);
	file_put_contents($filename, print_r($_POST,true), FILE_APPEND);
	file_put_contents($filename, "BACKTRACE:". PHP_EOL, FILE_APPEND);
	file_put_contents($filename, $traces, FILE_APPEND);
	
	$msg = file_get_contents($filename);
	
	if($Kz->getConfig("//mail_on_error") != ""){
		$mailheader = "MIME-Version: 1.0\nContent-Transfer-Encoding: 8bit\n".'Content-Type: text/plain; charset="utf-8"';
		mail($Kz->getConfig("//mail_on_error"),"[".$_SERVER['SERVER_NAME']."]".$Kz->getConfig("//mail_error_tag").' '.$message, str_replace('\\\'','\'',$message." (".$file."@".$line.")\n$msg"), $mailheader);
	}
    echo $Kz->getText("GlobalError");
}

//Error manager
function exception_handler($exception) {
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();
    $level = $exception->getCode();
    log_error_bug($level,$message,$file,$line);
}

function error_handler($level,$message,$file,$line) {
    log_error_bug($level,$message,$file,$line);
}

?>
