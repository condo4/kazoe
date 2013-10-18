<?php   # coding: utf-8
/**
 * Project: KaZoe
 * File name: section_translation_addbutton.php
 * Description: Button for add new translation
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
echo "<form method='post' action='".$Kz->getEnv("PAGEURL")."'>\n<p><input type='hidden' name='query' value='addtranslation' />\n<input type='hidden' name='id' value='".$_REQUEST["id"]."' />\n<input type='submit' value='".$Kz->getText("AddTransButton")."' /></p>\n</form>\n";
?>