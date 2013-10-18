<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: page.php
 * Description: Display caches and a field to clean pages
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

if ($Kz->canDo('section[@id=\':{base}\']'))
{
	$pattern = (isset($_REQUEST['Submit']))?($_REQUEST['Submit']):('');
	if($pattern != ''){
		$Kz->uncache("all");
	}
	echo '<form method="post" action="'.$Kz->getEnv("PAGEURL").'"><fieldset><legend>'.$Kz->getText('CacheManagment').'</legend><input type="submit" name="Submit" value="'.$Kz->getText('ButtonTitle').'" /></fieldset></form><br />';
	echo $Kz->getText('Files');;
	$opened = false;
	
	if ($handletheme = opendir($Kz->GetPath('docroot').'/var/cache')) {
		while (false !== ($dirtheme = readdir($handletheme))) {
			if($dirtheme == ".")  continue;
			if($dirtheme == "..")  continue;
			if ($handle = opendir($Kz->GetPath('docroot').'/var/cache/'.$dirtheme)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						if(!$opened){
							echo '<ul>';
							$opened = true;
						}
						echo '<li>'.$dirtheme.'/'.$file.'</li>';
					}
				}
				closedir($handle);
			}
		}
		closedir($handletheme);
	}
	if($opened) echo '</ul>';
}
?>