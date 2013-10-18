<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: info_sendmail.php
 * Description: Send a mail
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
	$sql = $Kz->db_query("SELECT email FROM kazoe_users JOIN kazoe_passwd ON kazoe_users._passwd = kazoe_passwd.login WHERE kazoe_passwd.id = :KEY");
	$sql->execute();
	$res = $sql->fetch();
	$toemail = $res[0];
	$subject 	= $Kz->getPostText("subject");
	$email 		= $Kz->getPostText("email");
	$name 		= $Kz->getPostText("name");
	$message 	= $Kz->getPostText("message");
	if(!preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#',$email)){
      print "<em>".$Kz->getText('InvalidAddress')."</em>";
	}
	else{
	
	
		$mailheader  ='MIME-Version: 1.0'."\n";
		$mailheader .='From: "'.$name.'"<'.$email.'>'."\n";
		$mailheader .='Reply-To: '.$email."\n";
		$mailheader .='Content-Type: text/plain; charset="utf-8"'."\n";
		$mailheader .='Content-Transfer-Encoding: 8bit'; 
		
		$compose = str_replace('\\\'','\'',$Kz->getText('MailIntro')." :\n".$message."\n\n".$name."\n".$email);
		if(mail($toemail, ($Kz->getText('MailTag').stripslashes($subject)), $compose, $mailheader)){
			print $Kz->getText('MailSuccess');
		}
		else{
			throw new Exception("Send Mail error");
		}
	}
}
else {
	echo "<em>".$Kz->getText('BadCrypto')."</em><br />\n";
}
?>