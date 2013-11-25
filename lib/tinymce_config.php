<?php
session_start();
$root = rtrim($_SERVER['DOCUMENT_ROOT'],'/'); // don't touch this configuration
include($root."/kazoe/bin/kconfig.php");
$Kz = new KData(isset($_SERVER['HTTP_REFERER'])?($_SERVER['HTTP_REFERER']):(""));
header('Content-Type: application/javascript'); 
?>
tinymce.init({
	selector: "textarea.ckeditor",
	menubar : false,
	language : 'fr_FR',
	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak",
		"wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
		"table contextmenu directionality paste textcolor responsivefilemanager youtube"
	],
	image_advtab: false,
	extended_valid_elements: "table[class=table|border:0|width=100%]",
	filemanager_title:"Choisir le fichier" ,
	external_filemanager_path:"/kazoe/lib/filemanager/",
	external_plugins: { "filemanager" : "../filemanager/plugin.min.js"},
	external_plugins: { "youtube" : "../youtube/plugin.min.js"},
<?php
	if(is_file($Kz->getPath('docroot').'/skin/'.$Kz->getEnv('SKIN')."/css/editor.css"))
	{
		echo "\tcontent_css : \"/skin/".$Kz->getEnv('SKIN')."/css/editor.css\",\n";
	}
?>
	toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tablecontrols | link unlink anchor image youtube | fullpage <?php if($Kz->getEnv('USER_ID') == 0) echo 'code'; ?>"
	
});
