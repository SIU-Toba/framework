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
 * File Name: fck_link.js
 * 	Scripts related to the Link dialog window (see fck_link.html).
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-06-02 00:26:07
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

var oEditor = window.parent.InnerDialogLoaded() ;
var FCK		= oEditor.FCK ;

// Set the language direction.
window.document.dir = oEditor.FCKLang.Dir ;

// Set the Skin CSS.
document.write( '<link href="' + oEditor.FCKConfig.SkinPath + 'fck_dialog.css" type="text/css" rel="stylesheet">' ) ;

//#### Dialog Tabs

// Set the dialog tabs.
window.parent.AddTab( 'Info', 'Link Info' ) ;
window.parent.AddTab( 'Target', 'Target', true ) ;
// TODO : Enable File Upload (1/3).
//window.parent.AddTab( 'Upload', 'Upload', true ) ;
window.parent.AddTab( 'Advanced', 'Advanced' ) ;

// Function called when a dialog tag is selected.
function OnDialogTabChange( tabCode )
{
	ShowE('divInfo'		, ( tabCode == 'Info' ) ) ;
	ShowE('divTarget'	, ( tabCode == 'Target' ) ) ;
// TODO : Enable File Upload (2/3).
//	ShowE('divUpload'	, ( tabCode == 'Upload' ) ) ;
	ShowE('divAttribs'	, ( tabCode == 'Advanced' ) ) ;
}

//#### Regular Expressions library.
var oRegex = new Object() ;

oRegex.UriProtocol = new RegExp('') ; 
oRegex.UriProtocol.compile( '^(((http|https|ftp|news):\/\/)|mailto:)', 'gi' ) ;

oRegex.UrlOnChangeProtocol = new RegExp('') ; 
oRegex.UrlOnChangeProtocol.compile( '^(http|https|ftp|news)://(?=.)', 'gi' ) ;

oRegex.UrlOnChangeTestOther = new RegExp('') ; 
oRegex.UrlOnChangeTestOther.compile( '^(javascript:|#|/)', 'gi' ) ;

oRegex.ReserveTarget = new RegExp('') ;
oRegex.ReserveTarget.compile( '^_(blank|self|top|parent)$', 'i' ) ;

oRegex.PopupUri = new RegExp('') ; 
oRegex.PopupUri.compile( "^javascript:void\\(\\s*window.open\\(\\s*'([^']+)'\\s*,\\s*(?:'([^']*)'|null)\\s*,\\s*'([^']*)'\\s*\\)\\s*\\)\\s*$" ) ;

oRegex.PopupFeatures = new RegExp('') ;
oRegex.PopupFeatures.compile( '(?:^|,)([^=]+)=(\\d+|yes|no)', 'gi' ) ;

//#### Parser Functions

var oParser = new Object() ;

oParser.ParseEMailUrl = function( emailUrl )
{
	// Initializes the EMailInfo object.
	var oEMailInfo = new Object() ;
	oEMailInfo.Address	= '' ;
	oEMailInfo.Subject	= '' ;
	oEMailInfo.Body		= '' ;

	var oParts = emailUrl.match( /^([^\?]+)\??(.+)?/ ) ;
	if ( oParts )
	{
		// Set the e-mail address.
		oEMailInfo.Address = oParts[1] ;

		// Look for the optional e-mail parameters.
		if ( oParts[2] )
		{
			var oMatch = oParts[2].match( /(^|&)subject=([^&]+)/i ) ;
			if ( oMatch ) oEMailInfo.Subject = unescape( oMatch[2] ) ;

			oMatch = oParts[2].match( /(^|&)body=([^&]+)/i ) ;
			if ( oMatch ) oEMailInfo.Body = unescape( oMatch[2] ) ;
		}
	}

	return oEMailInfo ;
}

oParser.CreateEMailUri = function( address, subject, body )
{
	var sBaseUri = 'mailto:' + address ;

	var sParams = '' ;

	if ( subject.length > 0 )
		sParams = '?subject=' + escape( subject ) ;

	if ( body.length > 0 )
	{
		sParams += ( sParams.length == 0 ? '?' : '&' ) ;
		sParams += 'body=' + escape( body ) ;
	}

	return sBaseUri + sParams ;
}

//#### Initialization Code

// oLink: The actual selected link in the editor.
var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;
if ( oLink )
	FCK.Selection.MoveToNode( oLink ) ;

window.onload = function()
{
	// Translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage(document) ;

	// Fill the Anchor Names and Ids combos.
	LoadAnchorNamesAndIds() ;

	// Load the selected link information (if any).
	LoadSelection() ;
	
	// Update the dialog box.
	SetLinkType( GetE('cmbLinkType').value ) ;
	
	// Show/Hide the "Browse Server" button.
	GetE('divBrowseServer').style.display = oEditor.FCKConfig.LinkBrowser ? '' : 'none' ;
	
	// Show the initial dialog content.
	GetE('divInfo').style.display = '' ;

	// Activate the "OK" button.
	window.parent.SetOkButton( true ) ;
}

var bHasAnchors ;

function LoadAnchorNamesAndIds()
{
	var aAnchors	= oEditor.FCK.EditorDocument.anchors ;
	var aIds		= oEditor.FCKTools.GetAllChildrenIds( oEditor.FCK.EditorDocument.body ) ;

	bHasAnchors = ( aAnchors.lenght > 0 || aIds.length > 0 ) ;
	
	for ( var i = 0 ; i < aAnchors.length ; i++ )
	{
		var sName = aAnchors[i].name ;
		if ( sName && sName.length > 0 )
			oEditor.FCKTools.AddSelectOption( document, GetE('cmbAnchorName'), sName, sName ) ;
	}

	for ( var i = 0 ; i < aIds.length ; i++ )
	{
		oEditor.FCKTools.AddSelectOption( document, GetE('cmbAnchorId'), aIds[i], aIds[i] ) ;
	}
	
	ShowE( 'divSelAnchor'	, bHasAnchors ) ;
	ShowE( 'divNoAnchor'	, !bHasAnchors ) ;
}

function LoadSelection()
{
	if ( !oLink ) return ;

	var sType = 'url' ;

	// Get the actual Link href.
	var sHRef = oLink.getAttribute('href',2) + '' ;

	// Look for a popup javascript link.
	var oPopupMatch = oRegex.PopupUri.exec( sHRef ) ;
	if( oPopupMatch )
	{
		GetE('cmbTarget').value = 'popup' ;
		sHRef = oPopupMatch[1] ;
		FillPopupFields( oPopupMatch[2], oPopupMatch[3] ) ;
		SetTarget( 'popup' ) ;
	}

	// Search for the protocol.
	var sProtocol = oRegex.UriProtocol.exec( sHRef ) ;

	if ( sProtocol )
	{
		sProtocol = sProtocol[0].toLowerCase() ;
		GetE('cmbLinkProtocol').value = sProtocol ;
	
		// Remove the protocol and get the remainig URL.
		var sUrl = sHRef.replace( oRegex.UriProtocol, '' ) ;

		if ( sProtocol == 'mailto:' )	// It is an e-mail link.
		{
			sType = 'email' ;

			var oEMailInfo = oParser.ParseEMailUrl( sUrl ) ;
			GetE('txtEMailAddress').value	= oEMailInfo.Address ;
			GetE('txtEMailSubject').value	= oEMailInfo.Subject ;
			GetE('txtEMailBody').value		= oEMailInfo.Body ;
		}
		else				// It is a normal link.
		{
			sType = 'url' ;
			GetE('txtUrl').value = sUrl ;
		}
	}
	else if ( sHRef.substr(0,1) == '#' && sHRef.length > 2 )	// It is an anchor link.
	{
		sType = 'anchor' ;
		GetE('cmbAnchorName').value = GetE('cmbAnchorId').value = sHRef.substr(1) ;
	}
	else					// It is another type of link.
	{
		sType = 'url' ;
		GetE('cmbLinkProtocol').value = '' ;
		GetE('txtUrl').value = sHRef ;
	}

	if ( !oPopupMatch )
	{
		// Get the target.
		var sTarget = oLink.target ;
		
		if ( sTarget && sTarget.length > 0 )
		{
			if ( oRegex.ReserveTarget.test( sTarget ) )
			{
				sTarget = sTarget.toLowerCase() ;
				GetE('cmbTarget').value = sTarget ;
			}
			else
				GetE('cmbTarget').value = 'frame' ;
			GetE('txtTargetFrame').value = sTarget ;
		}
	}
	
	// Get Advances Attributes
	GetE('txtAttId').value			= oLink.id ;
	GetE('txtAttName').value		= oLink.name ;
	GetE('cmbAttLangDir').value		= oLink.dir ;
	GetE('txtAttLangCode').value	= oLink.lang ;
	GetE('txtAttAccessKey').value	= oLink.accessKey ;
	GetE('txtAttTabIndex').value	= oLink.tabIndex <= 0 ? '' : oLink.tabIndex ;
	GetE('txtAttTitle').value		= oLink.title ;
	GetE('txtAttClasses').value		= oLink.getAttribute('class',2) || '' ;
	GetE('txtAttContentType').value	= oLink.type ;
	GetE('txtAttCharSet').value		= oLink.charset ;
	
	if ( oEditor.FCKBrowserInfo.IsIE ) 
		GetE('txtAttStyle').value	= oLink.style.cssText ;
	else
		GetE('txtAttStyle').value	= oLink.getAttribute('style',2) ;
	
	// Update the Link type combo.
	GetE('cmbLinkType').value = sType ;
}

//#### Link type selection.
function SetLinkType( linkType )
{
	ShowE('divLinkTypeUrl'		, (linkType == 'url') ) ;
	ShowE('divLinkTypeAnchor'	, (linkType == 'anchor') ) ;
	ShowE('divLinkTypeEMail'	, (linkType == 'email') ) ;

	window.parent.SetTabVisibility( 'Target'	, (linkType == 'url') ) ;
// TODO : Enable File Upload (3/3).
//	window.parent.SetTabVisibility( 'Upload'	, (linkType == 'url') ) ;
	window.parent.SetTabVisibility( 'Advanced'	, (linkType != 'anchor' || bHasAnchors) ) ;
}

//#### Target type selection.
function SetTarget( targetType )
{
	GetE('tdTargetFrame').style.display	= ( targetType == 'popup' ? 'none' : '' ) ;
	GetE('tdPopupName').style.display	=
		GetE('tablePopupFeatures').style.display = ( targetType == 'popup' ? '' : 'none' ) ;

	switch ( targetType )
	{
		case "_blank" :
		case "_self" :
		case "_parent" :
		case "_top" :
			GetE('txtTargetFrame').value = targetType ;
			break ;
		case "" :
			GetE('txtTargetFrame').value = '' ;
			break ;
	}
}

//#### Called while the user types the URL.
function OnUrlChange()
{
	var sUrl = GetE('txtUrl').value ;
	var sProtocol = oRegex.UrlOnChangeProtocol.exec( sUrl ) ;
	
	if ( sProtocol )
	{
		sUrl = sUrl.substr( sProtocol[0].length ) ;
		GetE('txtUrl').value = sUrl ;
		GetE('cmbLinkProtocol').value = sProtocol[0].toLowerCase() ;
	}
	else if ( oRegex.UrlOnChangeTestOther.test( sUrl ) )
	{
		GetE('cmbLinkProtocol').value = '' ;
	}
}

//#### Called while the user types the target name.
function OnTargetNameChange()
{
	var sFrame = GetE('txtTargetFrame').value ;

	if ( sFrame.length == 0 )
		GetE('cmbTarget').value = '' ;
	else if ( oRegex.ReserveTarget.test( sFrame ) )
		GetE('cmbTarget').value = sFrame.toLowerCase() ;
	else
		GetE('cmbTarget').value = 'frame' ;
}

//#### Builds the javascript URI to open a popup to the specified URI.
function BuildPopupUri( uri )
{
	var oReg = new RegExp( "'", "g" ) ;
	var sWindowName = "'" + GetE('txtPopupName').value.replace(oReg, "\\'") + "'" ;
	
	var sFeatures = '' ;
	var aChkFeatures = document.getElementsByName('chkFeature') ;
	for ( var i = 0 ; i < aChkFeatures.length ; i++ )
	{
		if ( i > 0 ) sFeatures += ',' ;
		sFeatures += aChkFeatures[i].value + '=' + ( aChkFeatures[i].checked ? 'yes' : 'no' ) ;
	}
	
	if ( GetE('txtPopupWidth').value.length > 0 )	sFeatures += ',width=' + GetE('txtPopupWidth').value ;
	if ( GetE('txtPopupHeight').value.length > 0 )	sFeatures += ',height=' + GetE('txtPopupHeight').value ;
	if ( GetE('txtPopupLeft').value.length > 0 )	sFeatures += ',left=' + GetE('txtPopupLeft').value ;
	if ( GetE('txtPopupTop').value.length > 0 )		sFeatures += ',top=' + GetE('txtPopupTop').value ;
	
	return ( "javascript:void(window.open('" + uri + "'," + sWindowName + ",'" + sFeatures + "'))" ) ;
}

//#### Fills all Popup related fields.
function FillPopupFields( windowName, features )
{
	if ( windowName )
		GetE('txtPopupName').value = windowName ;

	var oFeatures = new Object() ;
	var oFeaturesMatch ;
	while( ( oFeaturesMatch = oRegex.PopupFeatures.exec( features ) ) != null )
	{
		var sValue = oFeaturesMatch[2] ;
		if ( sValue == ( 'yes' || '1' ) )
			oFeatures[ oFeaturesMatch[1] ] = true ;
		else if ( ! isNaN( sValue ) && sValue != 0 )
			oFeatures[ oFeaturesMatch[1] ] = sValue ;
	}
	
	// Update all features check boxes.
	var aChkFeatures = document.getElementsByName('chkFeature') ;
	for ( var i = 0 ; i < aChkFeatures.length ; i++ )
	{
		if ( oFeatures[ aChkFeatures[i].value ] )
			aChkFeatures[i].checked = true ;
	}
	
	// Update position and size text boxes.
	if ( oFeatures['width'] )	GetE('txtPopupWidth').value		= oFeatures['width'] ;
	if ( oFeatures['height'] )	GetE('txtPopupHeight').value	= oFeatures['height'] ;
	if ( oFeatures['left'] )	GetE('txtPopupLeft').value		= oFeatures['left'] ;
	if ( oFeatures['top'] )		GetE('txtPopupTop').value		= oFeatures['top'] ;
}

//#### The OK button was hit.
function Ok()
{
	var sUri ;

	switch ( GetE('cmbLinkType').value )
	{
		case 'url' :
			sUri = GetE('cmbLinkProtocol').value + GetE('txtUrl').value ;
			
			if( GetE('cmbTarget').value == 'popup' )
				sUri = BuildPopupUri( sUri ) ;
				
			break ;

		case 'email' :
			sUri = oParser.CreateEMailUri(
				GetE('txtEMailAddress').value,
				GetE('txtEMailSubject').value,
				GetE('txtEMailBody').value ) ;
			break ;

		case 'anchor' :
			var sAnchor = GetE('cmbAnchorName').value ;
			if ( sAnchor.length == 0 ) sAnchor = GetE('cmbAnchorId').value ;
			sUri = '#' + sAnchor ;
			break ;
	}

	if ( oLink )	// Modifying an existent link.
		oLink.href = sUri ;
	else			// Creating a new link.
		oLink = oEditor.FCK.CreateLink( sUri ) ;

	// Target
	if( GetE('cmbTarget').value != 'popup' )
		SetAttribute( oLink, 'target', GetE('txtTargetFrame').value ) ;
	else
		SetAttribute( oLink, 'target', null ) ;

	// Advances Attributes
	SetAttribute( oLink, 'id'		, GetE('txtAttId').value ) ;
	SetAttribute( oLink, 'name'		, GetE('txtAttName').value ) ;		// No IE. Set but doensn't update the outerHTML.
	SetAttribute( oLink, 'dir'		, GetE('cmbAttLangDir').value ) ;
	SetAttribute( oLink, 'lang'		, GetE('txtAttLangCode').value ) ;
	SetAttribute( oLink, 'accesskey', GetE('txtAttAccessKey').value ) ;
	SetAttribute( oLink, 'tabindex'	, ( GetE('txtAttTabIndex').value > 0 ? GetE('txtAttTabIndex').value : null ) ) ;
	SetAttribute( oLink, 'title'	, GetE('txtAttTitle').value ) ;
	SetAttribute( oLink, 'class'	, GetE('txtAttClasses').value ) ;
	SetAttribute( oLink, 'type'		, GetE('txtAttContentType').value ) ;
	SetAttribute( oLink, 'charset'	, GetE('txtAttCharSet').value ) ;
	
	if ( oEditor.FCKBrowserInfo.IsIE ) 
		oLink.style.cssText = GetE('txtAttStyle').value ;
	else
		SetAttribute( oLink, 'style', GetE('txtAttStyle').value ) ;

	return true ;
}

function BrowseServer()
{
	// Set the browser window feature.
	var iWidth	= oEditor.FCKConfig.LinkBrowserWindowWidth ;
	var iHeight	= oEditor.FCKConfig.LinkBrowserWindowHeight ;
	
	var iLeft = (screen.width  - iWidth) / 2 ;
	var iTop  = (screen.height - iHeight) / 2 ;

	var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
	sOptions += ",width=" + iWidth ; 
	sOptions += ",height=" + iHeight ;
	sOptions += ",left=" + iLeft ;
	sOptions += ",top=" + iTop ;

	// Open the browser window.
	var oWindow = window.open( oEditor.FCKConfig.LinkBrowserURL, "FCKBrowseWindow", sOptions ) ;
}

function SetUrl( url )
{
	document.getElementById('txtUrl').value = url ;
	OnUrlChange() ;
}