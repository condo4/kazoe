xinha_editors = null;
xinha_init    = null;
xinha_config  = null;
xinha_plugins = null;

// This contains the names of textareas we will make into Xinha editors
xinha_init = xinha_init ? xinha_init : function()
{
	xinha_editors = xinha_editors ? xinha_editors :
	[
		'rte', 'anotherOne'
	];

	xinha_plugins = xinha_plugins ? xinha_plugins :
	[
		'ContextMenu',
		'ExtendedFileManager'
	];

	// THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
	if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;

	xinha_config = xinha_config ? xinha_config() : new Xinha.Config();
   
	//this is the standard toolbar, feel free to remove buttons as you like
	xinha_config.toolbar =
	[
		["popupeditor"],
		["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],
		["separator","forecolor","hilitecolor","textindicator"],
		["separator","subscript","superscript"],
		["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
		["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
		["separator","inserthorizontalrule","createlink","insertimage","inserttable"],
		["linebreak","separator","undo","redo"],
		["separator","removeformat"],
	];
	
	xinha_config.pageStyleSheets = ["/skin/Orange/css/editor.css"];
	
	xinha_config.ExtendedFileManager.use_linker = true;
	
	if (xinha_config.ExtendedFileManager) {
		with (xinha_config.ExtendedFileManager)
		{
			<?php
			$page = $_SERVER['HTTP_REFERER'];
			$tabs = preg_split("/-/",str_replace(".html","",substr($page,strrpos($page,"/")+1)));
			$path = "/root/".$tabs[0]."/".str_replace('.','/',$tabs[2])."/__local_img__";
			$pathf = "/root/".$tabs[0]."/".str_replace('.','/',$tabs[2])."/__local_files__";
			
			// define backend configuration for the plugin
			$IMConfig = array();

			$IMConfig['allow_upload'] = true;
			$IMConfig['allow_new_dir'] = false;
			$IMConfig['images_enable_styling'] = false;

			$IMConfig['images_dir'] = $_SERVER['DOCUMENT_ROOT'].$path;
			$IMConfig['images_url'] = $path;
			$IMConfig['files_dir'] = $_SERVER['DOCUMENT_ROOT'].$pathf;
			$IMConfig['files_url'] = $pathf;

			$IMConfig['thumbnail_prefix'] = 't_';
			$IMConfig['thumbnail_dir'] = '';
			$IMConfig['resized_prefix'] = 'resized_';
			$IMConfig['resized_dir'] = '';
			$IMConfig['tmp_prefix'] = '_tmp';
			$IMConfig['max_filesize_kb_image'] = 2000;
			// maximum size for uploading files in 'insert image' mode (2000 kB here)

			$IMConfig['max_filesize_kb_link'] = 5000;
			// maximum size for uploading files in 'insert link' mode (5000 kB here)

			// Maximum upload folder size in Megabytes.
			// Use 0 to disable limit
			$IMConfig['max_foldersize_mb'] = 0;
			
			$IMConfig['allowed_image_extensions'] = array("jpg","gif","png");
			$IMConfig['allowed_link_extensions'] = array("jpg","gif","pdf","ip","txt","psd","png","html","swf","xml","xls");

			require_once $_SERVER["DOCUMENT_ROOT"].'/kazoe/lib/xinha/contrib/php-xinha.php';
			xinha_pass_to_php_backend($IMConfig);
			
			?>
		}
	}
	
	xinha_editors   = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);

	Xinha.startEditors(xinha_editors);
}

Xinha._addEvent(window,'load', xinha_init); // this executes the xinha_init function on page load 
                                            // and does not interfere with window.onload properties set by other scripts
