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
 * File Name: it.js
 * 	Italian language file.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-05-27 19:26:03
 * 
 * File Authors:
 * 		Simone Chiaretta
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
"Dir"				: "ltr",

// Toolbar Items and Context Menu
"Cut"				: "Taglia",
"Copy"				: "Copia",
"Paste"				: "Incolla",
"PasteText"			: "Incolla come testo semplice",
"PasteWord"			: "Incolla da Word",
"Find"				: "Cerca",
"SelectAll"			: "Seleziona tutto",
"RemoveFormat"		: "Rimuovi formattazione",
"InsertLink"		: "Inserisci/Modifica Link",
"RemoveLink"		: "Rimuovi Link",
"InsertImage"		: "Inserisci/Modifica immagine",
"InsertTable"		: "Inserisci/Modifica tabella",
"InsertLine"		: "Inserisci linea orizzontale",
"InsertSpecialChar"	: "Inserisci carattere speciale",
"InsertSmiley"		: "Inserisci emoticon",
"About"				: "Informazioni su FCKeditor",

"Bold"				: "Grassetto",
"Italic"			: "Corsivo",
"Underline"			: "Sottolinea",
"StrikeThrough"		: "Barrato",
"Subscript"			: "Pedice",
"Superscript"		: "Apice",
"LeftJustify"		: "Allinea a sinistra",
"CenterJustify"		: "Centra",
"RightJustify"		: "Allinea a destra",
"BlockJustify"		: "Giustifica",
"DecreaseIndent"	: "Aumenta rientro",
"IncreaseIndent"	: "Riduci rientro",
"Undo"				: "Annulla",
"Redo"				: "Ripeti",
"NumberedList"		: "Elenco numerato",
"BulletedList"		: "Elenco puntato",

"ShowTableBorders"	: "Mostra i bordi delle tabelle",
"ShowDetails"		: "Mostra dettagli",

"FontStyle"			: "Stile",
"FontFormat"		: "Formato",
"Font"				: "Font",
"FontSize"			: "Dimensione",
"TextColor"			: "Colore del testo",
"BGColor"			: "Colore dello sfondo",
"Source"			: "Sorgente",

// Context Menu

"EditLink"			: "Modifica link",
"InsertRow"			: "Aggiungi righa",
"DeleteRows"		: "Elimina righa",
"InsertColumn"		: "Aggiungi colonna",
"DeleteColumns"		: "Elimina colonna",
"InsertCell"		: "Aggiungi cella",
"DeleteCells"		: "Elimina cella",
"MergeCells"		: "Unisci celle",
"SplitCell"			: "Dividi cella",
"CellProperties"	: "Proprietà della cella",
"TableProperties"	: "Proprietà della tabella",
"ImageProperties"	: "Proprietà dell'immagine",

// Alerts and Messages

"ProcessingXHTML"	: "Elaborazione del XHTML. Attendere prego...",
"Done"				: "Completata",
"PasteWordConfirm"	: "Il testo da incollare sembra provenire da Word. Desidera pulirlo prima di incollare?",
"NotCompatiblePaste": "Questa funzione è disponibile soltanto sui browser Internet Explorer versione 5.5 in poi. Desidera incollare il testo senza pulirlo?",

// Dialogs
"DlgBtnOK"			: "OK",
"DlgBtnCancel"		: "Annulla",
"DlgBtnClose"		: "Chiudi",

// Image Dialog
"DlgImgTitleInsert"	: "Inserisci immagine",
"DlgImgTitleEdit"	: "Modifica immagine",
"DlgImgBtnUpload"	: "Invia al server",
"DlgImgURL"			: "Indirizzo (URL)",
"DlgImgUpload"		: "Upload",
"DlgImgBtnBrowse"	: "Cerca sul server",
"DlgImgAlt"			: "Testo alternativo",
"DlgImgWidth"		: "Larghezza",
"DlgImgHeight"		: "Altezza",
"DlgImgLockRatio"	: "Blocca proporzioni",
"DlgBtnResetSize"	: "Reimposta dimensioni",
"DlgImgBorder"		: "Bordo",
"DlgImgHSpace"		: "HSpace",
"DlgImgVSpace"		: "VSpace",
"DlgImgAlign"		: "Allineamento",
"DlgImgAlignLeft"	: "Sinistra",
"DlgImgAlignAbsBottom"	: "Abs in basso",
"DlgImgAlignAbsMiddle"	: "Abs in mezzo",
"DlgImgAlignBaseline"	: "Linea base",
"DlgImgAlignBottom"	: "In basso",
"DlgImgAlignMiddle"	: "In mezzo",
"DlgImgAlignRight"	: "Destra",
"DlgImgAlignTextTop": "Top testo",
"DlgImgAlignTop"	: "In alto",
"DlgImgPreview"		: "Anteprima",
"DlgImgMsgWrongExt"	: "Sono consentiti soltanto i seguenti tipi di file:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperazione annullata.",
"DlgImgAlertSelect"	: "Selezionare il file da fare upload.",

// Link Dialog
"DlgLnkWindowTitle"	: "Link",
"DlgLnkURL"			: "Indirizzo (URL)",
"DlgLnkUpload"		: "Upload",
"DlgLnkTarget"		: "Destinazione",
"DlgLnkTargetNotSet": "<Non impostata>",
"DlgLnkTargetBlank"	: "Nuova finestra (_blank)",
"DlgLnkTargetParent": "Finestra padre (_parent)",
"DlgLnkTargetSelf"	: "Stessa finestra (_self)",
"DlgLnkTargetTop"	: "Finestra superiore (_top)",
"DlgLnkTitle"		: "Titolo",
"DlgLnkBtnUpload"	: "Invia al server",
"DlgLnkBtnBrowse"	: "Cerca sul server",
"DlgLnkMsgWrongExtA": "Sono consentiti soltanto i seguenti tipi di file:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperazione annullata.",
"DlgLnkMsgWrongExtD": "Non sono consentiti i seguenti tipi di file:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperazione annullata.",

// Color Dialog
"DlgColorTitle"		: "Seleziona il colore",
"DlgColorBtnClear"	: "Pulisci",
"DlgColorHighlight"	: "Attivo",
"DlgColorSelected"	: "Selezionato",

// Smiley Dialog
"DlgSmileyTitle"	: "Inserisci emoticon",

// Special Character Dialog
"DlgSpecialCharTitle"	: "Inserisci carattere speciale",

// Table Dialog
"DlgTableTitleInsert"	: "Inserisci tabella",
"DlgTableTitleEdit"	: "Modifica tabella",
"DlgTableRows"		: "Righe",
"DlgTableColumns"	: "Colonne",
"DlgTableBorder"	: "Bordo",
"DlgTableAlign"		: "Allineamento",
"DlgTableAlignNotSet"	: "<Non impostato>",
"DlgTableAlignLeft"	: "Sinistra",
"DlgTableAlignCenter"	: "Centrato",
"DlgTableAlignRight": "Destra",
"DlgTableWidth"		: "Larghezza",
"DlgTableHeight"	: "Altezza",
"DlgTableCellSpace"	: "Spazio celle",
"DlgTableCellPad"	: "Margini celle",
"DlgTableCaption"	: "Etichetta",
"DlgTableWidthPx"	: "pixels",
"DlgTableWidthPc"	: "percento",

// Table Cell Dialog
"DlgCellTitle"		: "Proprietà della cella",
"DlgCellWidth"		: "Larghezza",
"DlgCellWidthPx"	: "pixels",
"DlgCellWidthPc"	: "percento",
"DlgCellHeight"		: "Altezza",
"DlgCellWordWrap"	: "Vai a capo",
"DlgCellWordWrapNotSet"	: "<Default>",
"DlgCellWordWrapYes": "Sì",
"DlgCellWordWrapNo"	: "No",
"DlgCellHorAlign"	: "Allineamento orizzontale",
"DlgCellHorAlignNotSet"	: "<Non impostato>",
"DlgCellHorAlignLeft"	: "Sinistra",
"DlgCellHorAlignCenter"	: "Centrato",
"DlgCellHorAlignRight"	: "Destra",
"DlgCellVerAlign"	: "Allineamento verticale",
"DlgCellVerAlignNotSet"	: "<Non impostato>",
"DlgCellVerAlignTop"	: "Sopra",
"DlgCellVerAlignMiddle"	: "In mezzo",
"DlgCellVerAlignBottom"	: "Sotto",
"DlgCellVerAlignBaseline"	: "Linea base",
"DlgCellRowSpan"	: "Row span",
"DlgCellCollSpan"	: "Coll span",
"DlgCellBackColor"	: "Colore di sfondo",
"DlgCellBorderColor": "Colore del bordo",
"DlgCellBtnSelect"	: "Seleziona...",

// About Dialog
"DlgAboutVersion"	: "versione",
"DlgAboutLicense"	: "Rilasciato sotto la licensa GNU Lesser General Public License",
"DlgAboutInfo"		: "Per maggiori informazioni visitare"
}

