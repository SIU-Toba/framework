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
 * File Name: fck_dialog_common.js
 * 	Useful functions used by almost all dialog window pages.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-06-18 01:15:41
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

// Gets a element by its Id. Used for shorter coding.
function GetE( elementId )
{
	return document.getElementById( elementId )  ;
}

function ShowE( element, isVisible )
{
	if ( typeof( element ) == 'string' )
		element = GetE( element ) ;
	element.style.display = isVisible ? '' : 'none' ;
}

function SetAttribute( element, attName, attValue )
{
	if ( attValue == null || attValue.length == 0 )
		element.removeAttribute( attName, 0 ) ;			// 0 : Case Insensitive
	else
		element.setAttribute( attName, attValue, 0 ) ;	// 0 : Case Insensitive

}

function GetAttribute( element, attName, valueIfNull )
{
	var oAtt = element.attributes[attName] ;
	
	if ( oAtt == null || !oAtt.specified )
		return valueIfNull ;
		
	var oValue = element.getAttribute( attName, 2 ) ;
	
	return ( oValue == null ? valueIfNull : oValue ) ;
}