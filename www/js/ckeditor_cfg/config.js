/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.toolbar_Toba = [
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','-'] },
		{ name: 'paragraph', items: ['Outdent','Indent', '-','NumberedList','BulletedList']},
		{ name: 'styles',  items : [ 'Font','FontSize' ] },
		{ name: 'correme', items:['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']},
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'Links', items : [ 'Link' ] },
		{ name: 'tools', items : [ 'Maximize' ] }		
	] ;

	config.toolbar_Layout = [
		{name: 'basicstyles', items: ['Table', '-', 'FontSize','-','Bold','Italic','-', 'TextColor','BGColor']},
		{name: 'correme', items: ['JustifyLeft','JustifyCenter','JustifyRight']},
		'/',
		{name: 'tools', items: ['Source', 'Templates', 'Maximize']}
	] ;

	config.removeButtons = 'Subscript,Superscript,PasteText,PasteFromWord,Redo,Scayt,Unlink,Source,Strike,About';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';	
	config.defaultLanguage = 'es' ;
	config.language = 'es';
	config.removePlugins = 'elementspath';
	config.scayt_autoStartup = false;
	config.scayt_sLang = 'es_ES';
	config.extraPlugins= 'templates,font,justify,colorbutton';
	config.allowedContent = true;
	
	config.sharedSpaces =
	{
		top : 'toolbar_Toba'
	};
};
