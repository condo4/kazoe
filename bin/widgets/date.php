<?php   # coding: utf-8

$widgetsrc = $field->getElementsByTagName("widget_date")->item(0);

$value = $field->getElementsByTagName("value");
if($value->item(0) != null)
    $default = $value->item(0)->nodeValue;
else $default = $widgetsrc->getAttribute("default");
$range = preg_split("/;/",$widgetsrc->getAttribute("range"));
$min = $range[0];
$max = $range[1];

$sel_year   = date('Y',strtotime($default));
$sel_month  = date('m',strtotime($default));
$sel_day    = date('d',strtotime($default));
$min_year   = date('Y',strtotime($min));
$min_month  = date('m',strtotime($min));
$min_day    = date('d',strtotime($min));
$max_year   = date('Y',strtotime($max));
$max_month  = date('m',strtotime($max));
$max_day    = date('d',strtotime($max));

if($min_year != $max_year){
    $min_month = "1";
    $min_day = "1";
    $max_month = "12";
    $max_day = "31";
}
elseif($min_month != $max_month){
    $min_day = "1";
    $max_day = "31";
}

$days = $XmlSrc->createElement("days");
$widgetsrc->appendChild($days);
for($day=(int)$min_day; $day <= (int)$max_day; $day++){
    $day_item = $XmlSrc->createElement("day");
    $days->appendChild($day_item);
    $day_item->setAttribute("name","".$day);
    $day_item->setAttribute("value","".$day);
    if($day == (int)$sel_day) $day_item->setAttribute("sel","true");
    else $day_item->setAttribute("sel","false");
}
$months = $XmlSrc->createElement("months");
$widgetsrc->appendChild($months);
for($month=(int)$min_month; $month <= (int)$max_month; $month++){
    $month_item = $XmlSrc->createElement("month");
    $months->appendChild($month_item);
    $month_item->setAttribute("name","".$month);
    $month_name = $Kz->getText('month_'.str_pad(strval($month),2,'0',STR_PAD_LEFT));
    $month_item->setAttribute("value","".$month_name);
    if($month == (int)$sel_month) $month_item->setAttribute("sel","true");
    else $month_item->setAttribute("sel","false");
}

$years = $XmlSrc->createElement("years");
$widgetsrc->appendChild($years);
for($year=(int)$min_year; $year <= (int)$max_year; $year++){
    $year_item = $XmlSrc->createElement("year");
    $years->appendChild($year_item);
    $year_item->setAttribute("name","".$year);
    $year_item->setAttribute("value","".$year);
    if($year == (int)$sel_year) $year_item->setAttribute("sel","true");
    else $year_item->setAttribute("sel","false");
}

?>
