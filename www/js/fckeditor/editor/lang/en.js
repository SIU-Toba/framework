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
 * File Name: en.js
 * 	English language file.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-05-31 23:07:54
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
"Dir"				: "ltr",

// Toolbar Items and Context Menu
"Cut"				: "Cut" ,
"Copy"				: "Copy" ,
"Paste"				: "Paste" ,
"PasteText"			: "Paste as plain text" ,
"PasteWord"			: "Paste from Word" ,
"Find"				: "Find" ,
"SelectAll"			: "Select All" ,
"RemoveFormat"		: "Remove Format" ,
"InsertLink"		: "Insert/Edit Link" ,
"RemoveLink"		: "Remove Link" ,
"InsertImage"		: "Insert/Edit Image" ,
"InsertTable"		: "Insert/Edit Table" ,
"InsertLine"		: "Insert Horizontal Line" ,
"InsertSpecialChar"	: "Insert Special Character" ,
"InsertSmiley"		: "Insert Smiley" ,
"About"				: "About FCKeditor" ,
"Bold"				: "Bold" ,
"Italic"			: "Italic" ,
"Underline"			: "Underline" ,
"StrikeThrough"		: "Strike Through" ,
"Subscript"			: "Subscript" ,
"Superscript"		: "Superscript" ,
"LeftJustify"		: "Left Justify" ,
"CenterJustify"		: "Center Justify" ,
"RightJustify"		: "Right Justify" ,
"BlockJustify"		: "Block Justify" ,
"DecreaseIndent"	: "Decrease Indent" ,
"IncreaseIndent"	: "Increase Indent" ,
"Undo"				: "Undo" ,
"Redo"				: "Redo" ,
"NumberedList"		: "Numbered List" ,
"BulletedList"		: "Bulleted List" ,
"ShowTableBorders"	: "Show Table Borders" ,
"ShowDetails"		: "Show Details" ,
"FontStyle"			: "Style" ,
"FontFormat"		: "Format" ,
"Font"				: "Font" ,
"FontSize"			: "Size" ,
"TextColor"			: "Text Color" ,
"BGColor"			: "Background Color" ,
"Source"			: "Source" ,

// Context Menu
"EditLink"			: "Edit Link" ,
"InsertRow"			: "Insert Row" ,
"DeleteRows"		: "Delete Rows" ,
"InsertColumn"		: "Insert Column" ,
"DeleteColumns"		: "Delete Columns" ,
"InsertCell"		: "Insert Cell" ,
"DeleteCells"		: "Delete Cells" ,
"MergeCells"		: "Merge Cells" ,
"SplitCell"			: "Split Cell" ,
"CellProperties"	: "Cell Properties" ,
"TableProperties"	: "Table Properties" ,
"ImageProperties"	: "Image Properties" ,

// Alerts and Messages
"ProcessingXHTML"	: "Processing XHTML. Please wait..." ,
"Done"				: "Done" ,
"PasteWordConfirm"	: "The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?" ,
"NotCompatiblePaste": "This command is available for Internet Explorer version 5.5 or more. Do you want to paste without cleaning?" ,

// Dialogs
"DlgBtnOK"			: "OK" ,
"DlgBtnCancel"		: "Cancel" ,
"DlgBtnClose"		: "Close" ,

// Image Dialog
"DlgImgTitleInsert"	: "Insert Image" ,
"DlgImgTitleEdit"	: "Edit Image" ,
"DlgImgBtnUpload"	: "Send it to the Server" ,
"DlgImgURL"			: "URL" ,
"DlgImgUpload"		: "Upload" ,
"DlgImgBtnBrowse"	: "Browse Server" ,
"DlgImgAlt"			: "Alternative Text" ,
"DlgImgWidth"		: "Width" ,
"DlgImgHeight"		: "Height" ,
"DlgImgLockRatio"	: "Lock Ratio" ,
"DlgBtnResetSize"	: "Reset Size" ,
"DlgImgBorder"		: "Border" ,
"DlgImgHSpace"		: "HSpace" ,
"DlgImgVSpace"		: "VSpace" ,
"DlgImgAlign"			: "Align" ,
"DlgImgAlignLeft"		: "Left" ,
"DlgImgAlignAbsBottom"	: "Abs Bottom" ,
"DlgImgAlignAbsMiddle"	: "Abs Middle" ,
"DlgImgAlignBaseline"	: "Baseline" ,
"DlgImgAlignBottom"	: "Bottom" ,
"DlgImgAlignMiddle"	: "Middle" ,
"DlgImgAlignRight"	: "Right" ,
"DlgImgAlignTextTop": "Text Top" ,
"DlgImgAlignTop"	: "Top" ,
"DlgImgPreview"		: "Preview" ,
"DlgImgMsgWrongExt"	: "Sorry, only the following file types uploads are allowed:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperation canceled." ,
"DlgImgAlertSelect"	: "Please select an image to upload." ,

// Link Dialog
"DlgLnkWindowTitle"	: "Link" ,
"DlgLnkURL"			: "URL" ,
"DlgLnkUpload"		: "Upload" ,
"DlgLnkTarget"		: "Target" ,
"DlgLnkTargetNotSet": "<Not set>" ,
"DlgLnkTargetBlank"	: "New Window (_blank)" ,
"DlgLnkTargetParent": "Parent Window (_parent)" ,
"DlgLnkTargetSelf"	: "Same Window (_self)" ,
"DlgLnkTargetTop"	: "Topmost Window (_top)" ,
"DlgLnkTitle"		: "Title" ,
"DlgLnkBtnUpload"	: "Send it to the Server" ,
"DlgLnkBtnBrowse"	: "Browse Server" ,
"DlgLnkMsgWrongExtA": "Sorry, only the following file types uploads are allowed:\n\n" + FCKConfig.LinkUploadAllowedExtensions + "\n\nOperation canceled." ,
"DlgLnkMsgWrongExtD": "Sorry, the following file types uploads are not allowed:\n\n" + FCKConfig.LinkUploadDeniedExtensions + "\n\nOperation canceled." ,

// Color Dialog
"DlgColorTitle"		: "Select Color" ,
"DlgColorBtnClear"	: "Clear" ,
"DlgColorHighlight"	: "Highlight" ,
"DlgColorSelected"	: "Selected" ,

// Smiley Dialog
"DlgSmileyTitle"	: "Insert a Smiley" ,

// Special Character Dialog
"DlgSpecialCharTitle"	: "Insert Special Character" ,

// Table Dialog
"DlgTableTitleInsert"	: "Insert Table" ,
"DlgTableTitleEdit"		: "Edit Table" ,
"DlgTableRows"			: "Rows" ,
"DlgTableColumns"		: "Columns" ,
"DlgTableBorder"		: "Border size" ,
"DlgTableAlign"			: "Alignment" ,
"DlgTableAlignNotSet"	: "<Not set>" ,
"DlgTableAlignLeft"		: "Left" ,
"DlgTableAlignCenter"	: "Center" ,
"DlgTableAlignRight"	: "Right" ,
"DlgTableWidth"			: "Width" ,
"DlgTableWidthPx"		: "pixels" ,
"DlgTableWidthPc"		: "percent" ,
"DlgTableHeight"		: "Height" ,
"DlgTableCellSpace"		: "Cell spacing" ,
"DlgTableCellPad"		: "Cell padding" ,
"DlgTableCaption"		: "Caption" ,

// Table Cell Dialog
"DlgCellTitle"			: "Cell Properties" ,
"DlgCellWidth"			: "Width" ,
"DlgCellWidthPx"		: "pixels" ,
"DlgCellWidthPc"		: "percent" ,
"DlgCellHeight"			: "Height" ,
"DlgCellWordWrap"		: "Word Wrap" ,
"DlgCellWordWrapNotSet"	: "<Not set>" ,
"DlgCellWordWrapYes"	: "Yes" ,
"DlgCellWordWrapNo"		: "No" ,
"DlgCellHorAlign"		: "Horizontal Alignment" ,
"DlgCellHorAlignNotSet"	: "<Not set>" ,
"DlgCellHorAlignLeft"	: "Left" ,
"DlgCellHorAlignCenter"	: "Center" ,
"DlgCellHorAlignRight"	: "Right" ,
"DlgCellVerAlign"		: "Vertical Alignment" ,
"DlgCellVerAlignNotSet"	: "<Not set>" ,
"DlgCellVerAlignTop"	: "Top" ,
"DlgCellVerAlignMiddle"	: "Middle" ,
"DlgCellVerAlignBottom"	: "Bottom" ,
"DlgCellVerAlignBaseline"	: "Baseline" ,
"DlgCellRowSpan"		: "Rows Span" ,
"DlgCellCollSpan"		: "Columns Span" ,
"DlgCellBackColor"		: "Background Color" ,
"DlgCellBorderColor"	: "Border Color" ,
"DlgCellBtnSelect"		: "Select..." ,

// About Dialog
"DlgAboutVersion"	: "version" ,
"DlgAboutLicense"	: "Licensed under the terms of the GNU Lesser General Public License" ,
"DlgAboutInfo"		: "For further information go to"
}

