<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$title}</title>
	<link rel="stylesheet" type="text/css" href="{$subdir}media/style.css">
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" height="48" width="100%">
  <tr>
	<td class="header-top-left"><img src="{$subdir}media/logo.png" border="0" alt="phpDocumentor {$phpdocver}" /></td>
  	<td class="header-top-center">
  		<div class="package">
      {section name=packagelist loop=$packageindex}
        	<a href="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</a>
        	{if ! $smarty.section.packagelist.last}
        		|
        	{/if}
      {/section}
      </div>
   	</td>	
    <td class="header-top-right">
    	<a href='{$subdir}../wiki/trac/toba/wiki/WikiStart.html' title="Navegar hacia la documentación WIKI">
    		<img border='0' style='vertical-align: middle' src="{$subdir}media/wiki-small.png" /></a>    
     	<img height=64 border='0' style='vertical-align: middle' src="{$subdir}media/php.png" />  
    	<a href='{$subdir}../api_js/index.html'  title="Navegar hacia la documentación JAVASCRIPT">
    		<img border='0' style='vertical-align: middle' src="{$subdir}media/javascript-small.png" /></a> 
    	</td>
  </tr>
  <tr><td colspan="3" class="header-line">
  <img src="{$subdir}media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
  <tr>
    <td colspan="3" class="header-menu">
  		  [ <a href="{$subdir}classtrees_{$package}.html" class="menu">árbol de herencia: {$package}</a> ]
		  [ <a href="{$subdir}elementindex_{$package}.html" class="menu">índice: {$package}</a> ]
		  [ <a href="{$subdir}elementindex.html" class="menu">índice general</a> ]
    </td>
  </tr>
  <tr><td colspan="3" class="header-line"><img src="{$subdir}media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr valign="top">
    <td width="195" class="menu">
		<div class="package-title">{$package}</div>
{if count($ric) >= 1}
  <div class="package">
	<div id="ric">
		{section name=ric loop=$ric}
			<p><a href="{$subdir}{$ric[ric].file}">{$ric[ric].name}</a></p>
		{/section}
	</div>
	</div>
{/if}

      <br />
{if $tutorials}
		<b>Tutorials/Manuals:</b><br />
  <div class="package">
		{if $tutorials.pkg}
			<strong>Package-level:</strong>
			{section name=ext loop=$tutorials.pkg}
				{$tutorials.pkg[ext]}
			{/section}
		{/if}
		{if $tutorials.cls}
			<strong>Class-level:</strong>
			{section name=ext loop=$tutorials.cls}
				{$tutorials.cls[ext]}
			{/section}
		{/if}
		{if $tutorials.proc}
			<strong>Procedural-level:</strong>
			{section name=ext loop=$tutorials.proc}
				{$tutorials.proc[ext]}
			{/section}
	</div>
		{/if}
{/if}
      {if !$noleftindex}{assign var="noleftindex" value=false}{/if}
      {if !$noleftindex}
      {if $compiledclassindex}
      {eval var=$compiledclassindex}
      {/if}
      {/if}
      {if $compiledinterfaceindex}
      <br />
      <b>Interfaces:</b><br />
      {eval var=$compiledinterfaceindex}
      {/if}
      
{if $hastodos}
 <!-- <div class="package">
	<div id="todolist">
			<p><a href="{$subdir}{$todolink}">Todo List</a></p>
	</div>
	</div> -->
{/if}
	<div id="topdiv">
			<p><a href="javascript:scroll(0,0)">[ Top ]</a></p>
	</div>
    </td>
    <td>
      <table cellpadding="10" cellspacing="0" width="100%" border="0"><tr><td valign="top">

{if !$hasel}{assign var="hasel" value=false}{/if}
{if $eltype == 'clase' && $is_interface}{assign var="eltype" value="interface"}{/if}
{if $hasel}
<h1>{$class_name}</h1>
{/if}