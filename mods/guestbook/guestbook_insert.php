<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: guestbook_insert.php
 * Description: Insert item
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


$cryptinstall="kazoe/lib/crypt/cryptographp.fct.php";
include $cryptinstall;

if (chk_crypt($_POST['cryptogramme']))  {
	$name =      $Kz->getPostText("name");
	$email =    $Kz->getPostText("email");
	$resume =   $Kz->getPostText("resume");
	if($email=="") $email = null;

	$sql = $Kz->db_query(
		"INSERT INTO :{apptable} (name,email,date_input,lang,comment) VALUES (:NAME,:MAIL,NOW(),:LNG,:RESUME)",
		array(
			'NAME'          => $name,
			'MAIL'          => $email,
			'RESUME'        => $resume,
			'LNG'           => $Kz->getEnv('LANG')
		)
	);
	if (!$sql->execute()) throw new Exception($Kz->db_error($sql));
	echo "<a class='action_result'>".$Kz->getText('InsertSuccess')."</a><br />";
	$Kz->uncache();

	$mailheader  ='MIME-Version: 1.0'."\r\n";
	$mailheader .='From: "'.$name.'"<'.$Kz->getConfig("//webmaster_email").'>'."\r\n";
	$mailheader .='Content-Type: text/plain; charset="utf-8"'."\r\n";
	$mailheader .='Content-Transfer-Encoding: 8bit'."\r\n"; 
	$mailheader .='Bcc: spc_sendmail@kazoe.org';

	$compose = str_replace('\\\'','\'',$Kz->getText('MailIntroNot')." :\n".$resume."\n\n".$name."\n".$email);
	mail($Kz->getConfig("//webmaster_email"), ($Kz->getText('MailTag').$Kz->getText('NewNotify')), $compose, $mailheader);
}
else {
    echo "<em>".$Kz->getText('Forbidden')."</em><br />\n";
}
?>