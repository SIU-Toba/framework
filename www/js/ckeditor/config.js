/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:

	config.defaultLanguage = 'es' ;
	config.language = 'es';
	config.removePlugins = 'elementspath';
	config.scayt_autoStartup = false;
	config.scayt_sLang = 'es_ES';
		
	config.toolbar_Toba = [
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','-'] },
		{ name: 'paragraph', items: ['Outdent','Indent', '-','NumberedList','BulletedList']},
		{ name: 'styles',  items : [ 'Font','FontSize' ] },
		{ name: 'correme', items:['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']},
		{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
		{ name: 'tools',       items : [ 'Maximize' ] }
	] ;

	config.toolbar_Layout = [
		{name: 'basicstyles', items: ['Table', '-', 'FontSize','-','Bold','Italic','-', 'TextColor','BGColor']},
		{name: 'correme', items: ['JustifyLeft','JustifyCenter','JustifyRight']},
		'/',
		{name: 'tools', items: ['Source', 'Templates', 'Maximize']}
	] ;
	
	config.sharedSpaces =
	{
		top : 'toolbar_Toba'
	}
};
