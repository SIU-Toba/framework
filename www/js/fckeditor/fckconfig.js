/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: fckconfig.js
 * 	Editor configuration settings.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-08-30 23:27:01
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

// Custom Configurations (leave blank to ignore)
FCKConfig.CustomConfigurationsPath = '' ;	// '/fckeditor.config.js' ;

// Enables the debug window
FCKConfig.Debug = false ;

// Set the path for the skin files to use.
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;

// Language settings
FCKConfig.AutoDetectLanguage = true ;
FCKConfig.DefaultLanguage    = "en" ;

// Enable XHTML support
FCKConfig.EnableXHTML		= true ;
FCKConfig.EnableSourceXHTML	= true ;

// Tells Gecko browsers to use SPAN instead of <B>, <I> and <U>.
FCKConfig.GeckoUseSPAN = true ;

// Force the editor to get the focus on startup (page load).
FCKConfig.StartupFocus = true ;

// Cut and Paste options
FCKConfig.ForcePasteAsPlainText	= false ;

// Link: Target Windows
FCKConfig.LinkShowTargets = true ;
FCKConfig.LinkTargets = '_blank;_parent;_self;_top' ;
FCKConfig.LinkDefaultTarget = '' ;

FCKConfig.ToolbarStartExpanded	= true ;
FCKConfig.ToolbarCanCollapse	= true ;

//##
//## Toolbar Buttons Sets
//##
FCKConfig.ToolbarSets = new Object() ;
FCKConfig.ToolbarSets["Default"] = [
	['Source','-','Save','NewPage','Preview'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print'/*,'SpellCheck'*/],
	['Undo','Redo',/*'-','Find','Replace',*/'-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList',],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink'/*,'Anchor'*/],
	['Image','Table','Rule','SpecialChar'/*,'UniversalKey'*/,'Smiley'],
	/*['Form','Checkbox','Radio','Input','Textarea','Select','Button','ImageButton','Hidden']*/
	/*['ShowTableBorders','ShowDetails','-','Zoom'],*/
	[/*'FontStyleAdv','-','FontStyle','-',*/'FontFormat','-','FontName','-','FontSize'],
	['TextColor','BGColor'],
	['About']
] ;
FCKConfig.ToolbarSets["Toba"] = [
	['Bold','Italic','Underline','-'],
	['Outdent','Indent','-','OrderedList','UnorderedList'],
	['-','FontName','-','FontSize'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['TextColor','BGColor']
] ;
FCKConfig.ToolbarSets["Basic"] = [
	['Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','-','About']
] ;



// Font Colors
FCKConfig.FontColors = '000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,808080,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,C0C0C0,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF,FFFFFF' ;

// Font Names
FCKConfig.FontNames = 'Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana' ;

// Link Browsing
FCKConfig.LinkBrowser = true ;
FCKConfig.LinkBrowserURL = FCKConfig.BasePath + "filemanager/browser/default/browser.html?Connector=connectors/aspx/connector.aspx" ;
FCKConfig.LinkBrowserWindowWidth	= screen.width * 0.7 ;	// 70%
FCKConfig.LinkBrowserWindowHeight	= screen.height * 0.7 ;	// 70%

// Link Upload
FCKConfig.LinkUpload = false ;
FCKConfig.LinkUploadURL = FCKConfig.BasePath + "filemanager/upload/aspx/upload.aspx" ;
FCKConfig.LinkUploadWindowWidth		= 300 ;
FCKConfig.LinkUploadWindowHeight	= 150 ;
FCKConfig.LinkUploadAllowedExtensions	= "*" ;		// * or empty for all
FCKConfig.LinkUploadDeniedExtensions	= ".exe .asp .php .aspx .js .cfm .dll" ;	// empty for no one

// Image Browsing
FCKConfig.ImageBrowser = true ;
FCKConfig.ImageBrowserURL = FCKConfig.BasePath + "filemanager/browser/default/browser.html?Type=Image&Connector=connectors/aspx/connector.aspx" ;
FCKConfig.ImageBrowserWindowWidth  = screen.width * 0.7 ;	// 70% ;
FCKConfig.ImageBrowserWindowHeight = screen.height * 0.7 ;	// 70% ;

// Smiley Dialog
FCKConfig.SmileyPath	= FCKConfig.BasePath + "images/smiley/msn/" ;
FCKConfig.SmileyImages	= ["regular_smile.gif","sad_smile.gif","wink_smile.gif","teeth_smile.gif","confused_smile.gif","tounge_smile.gif","embaressed_smile.gif","omg_smile.gif","whatchutalkingabout_smile.gif","angry_smile.gif","angel_smile.gif","shades_smile.gif","devil_smile.gif","cry_smile.gif","lightbulb.gif","thumbs_down.gif","thumbs_up.gif","heart.gif","broken_heart.gif","kiss.gif","envelope.gif"] ;
FCKConfig.SmileyColumns = 8 ;
FCKConfig.SmileyWindowWidth		= 320 ;
FCKConfig.SmileyWindowHeight	= 240 ;

//**
// Load the custom configurations file
if ( FCKConfig.CustomConfigurationsPath.length > 0 )
	FCKScriptLoader.AddScript( FCKConfig.CustomConfigurationsPath ) ;