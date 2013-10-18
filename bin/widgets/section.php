<?php   # coding: utf-8
$widgetsrc = $field->getElementsByTagName("widget_section")->item(0);
$source = $widgetsrc->getAttribute("src");
$source = str_replace(':{base}',$Kz->getEnv('BASE'),$source);
$sql = $Kz->db_query(
    "SELECT ".$Kz->getEnv('APPTABLE')."_sections.id, ".$Kz->getEnv('APPTABLE')."_sections_titles.title, ".$Kz->getEnv('APPTABLE')."_sections_titles.lang FROM ".$Kz->getEnv('APPTABLE')."_sections JOIN ".$Kz->getEnv('APPTABLE')."_sections_titles ON (".$Kz->getEnv('APPTABLE')."_sections_titles.sectionid = ".$Kz->getEnv('APPTABLE')."_sections.id)"
);
if (!$sql->execute()) throw new Exception($Kz->db_error($sql));

while($res = $sql->fetch()){
    $lid = $res[0];
    $lname = $res[1];
    $llng = strtolower($res[2]);
    $ptoption = null;
    foreach($widgetsrc->getElementsByTagName("option") as $option){
        if($option->getAttribute("id") == $lid){ //just add the new language
            $ptoption = $option;
        }
    }
    if($ptoption == null){ //no option for this id present (first language) create the option
        $ptoption = $XmlSrc->createElement("option");
        $ptoption->setAttribute("id",$lid);
        $widgetsrc->appendChild($ptoption);
    }
    $pttext = $XmlSrc->createElement("title");
    $pttext->setAttribute("xml:lang",$llng);
    $val = $XmlSrc->createTextNode($lname);
    $pttext->appendChild($val);
    $ptoption->appendChild($pttext);
}
?>
