<?php   # coding: utf-8

$widgetsrc = $field->getElementsByTagName("widget_language")->item(0);
$source = $widgetsrc->getAttribute("src");

$langs = $Kz->getEnv('LANGS');

foreach(array_keys($langs) as $lang){
   $lg = $langs[$lang];
   foreach(array_keys($lg) as $translation){
		$ptoption = null;
		foreach($widgetsrc->getElementsByTagName("option") as $option){
			if($option->getAttribute("id") == $lang){ //just add the new language
				$ptoption = $option;
			}
		}
		if($ptoption == null){ //no option for this id present (first language) create the option
			$ptoption = $XmlSrc->createElement("option");
			$ptoption->setAttribute("id",$lang);
			$widgetsrc->appendChild($ptoption);
		}
		$pttext = $XmlSrc->createElement("desc");
		$pttext->setAttribute("xml:lang",$translation);
		$val = $XmlSrc->createTextNode($lg[$translation]);
		$pttext->appendChild($val);
		$ptoption->appendChild($pttext);
	}
}
?>
