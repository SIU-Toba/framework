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
 * File Name: pt-br.js
 * 	Portuguese language file.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-06-04 09:28:44
 * 
 * File Authors:
 * 		Alexandre Mendonça Lima (amlima@unitech.com.br)
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
"Dir"				: "ltr",

// Toolbar Items and Context Menu
"Cut"				: "Recortar" ,
"Copy"				: "Copiar" ,
"Paste"				: "Colar" ,
"PasteText"			: "Colar como Texto Puro" ,
"PasteWord"			: "Colar do Microsoft Word" ,
"Find"				: "Localizar" ,
"SelectAll"			: "Selecionar Tudo" ,
"RemoveFormat"		: "Remover Formatação" ,
"InsertLink"		: "Inserir/Editar Link" ,
"RemoveLink"		: "Remover Link" ,
"InsertImage"		: "Inserir/Editar Imagem" ,
"InsertTable"		: "Inserir/Editar Tabela" ,
"InsertLine"		: "Inserir Linha Horizontal" ,
"InsertSpecialChar"	: "Inserir Caracter Especial" ,
"InsertSmiley"		: "Inserir Carinha" ,
"About"				: "About FCKeditor" ,
"Bold"				: "Negrito" ,
"Italic"			: "Itálico" ,
"Underline"			: "Sublinhado" ,
"StrikeThrough"		: "Riscado" ,
"Subscript"			: "Subscrito" ,
"Superscript"		: "Superscrito" ,
"LeftJustify"		: "Alinhamento à Esquerda" ,
"CenterJustify"		: "Alinhamento ao Centro" ,
"RightJustify"		: "Alinhamento à Direita" ,
"BlockJustify"		: "Alinhamento Justificado" ,
"DecreaseIndent"	: "Diminuir Identação" ,
"IncreaseIndent"	: "Aumentar Identação" ,
"Undo"				: "Desfazer" ,
"Redo"				: "Refazer" ,
"NumberedList"		: "Lista Numerada" ,
"BulletedList"		: "Lista Marcada" ,
"ShowTableBorders"	: "Exibir Bordas da Tabela" ,
"ShowDetails"		: "Exibir Detalhes" ,
"FontStyle"			: "Estilo da Fonte" ,
"FontFormat"		: "Formatação da Fonte" ,
"Font"				: "Fonte" ,
"FontSize"			: "Tamanho da Fonte" ,
"TextColor"			: "Cor do Texto" ,
"BGColor"			: "Cor do Fundo do Texto" ,
"Source"			: "Código-Fonte" ,

// Context Menu
"EditLink"			: "Editar Link" ,
"InsertRow"			: "Inserir Linha" ,
"DeleteRows"		: "Apagar Linhas" ,
"InsertColumn"		: "Inserir Coluna" ,
"DeleteColumns"		: "Apagar Colunas" ,
"InsertCell"		: "Inserir células" ,
"DeleteCells"		: "Apagar células" ,
"MergeCells"		: "Mesclar células" ,
"SplitCell"			: "Dividir célula" ,
"CellProperties"	: "Propriedades da Célula" ,
"TableProperties"	: "Propriedades da Tabela" ,
"ImageProperties"	: "Propriedades da Imagem" ,

// Alerts and Messages
"ProcessingXHTML"	: "Processando XHTML. Por favor, aguarde..." ,
"Done"				: "Concluído" ,
"PasteWordConfirm"	: "O texto que você quer colar parece ser copiado do Microsoft Word. Deseja limpar formatação antes de colar?" ,
"NotCompatiblePaste": "Este comando está disponível para Internet Explorer 5.5 ou superior. Deseja colar sem formatação?" ,

// Dialogs
"DlgBtnOK"			: "OK" ,
"DlgBtnCancel"		: "Cancelar" ,
"DlgBtnClose"		: "Fechar" ,

// Image Dialog
"DlgImgTitleInsert"	: "Inserir Imagem" ,
"DlgImgTitleEdit"	: "Editar Imagem" ,
"DlgImgBtnUpload"	: "Enviar para o servidor" ,
"DlgImgURL"			: "URL" ,
"DlgImgUpload"		: "Enviar" ,
"DlgImgBtnBrowse"	: "Navegar no Servidor" ,
"DlgImgAlt"			: "Texto Alternativo" ,
"DlgImgWidth"		: "Largura" ,
"DlgImgHeight"		: "Altura" ,
"DlgImgLockRatio"	: "Travar Proporção" ,
"DlgBtnResetSize"	: "Tamanho Padrão" ,
"DlgImgBorder"		: "Borda" ,
"DlgImgHSpace"		: "Espaçamento Horizontal" ,
"DlgImgVSpace"		: "Espaçamento Vertical" ,
"DlgImgAlign"			: "Alinhamento" ,
"DlgImgAlignLeft"		: "Esquerda" ,
"DlgImgAlignAbsBottom"	: "Abaixo Absoluto" ,
"DlgImgAlignAbsMiddle"	: "Meio Absoluto" ,
"DlgImgAlignBaseline"	: "Linha de Base" ,
"DlgImgAlignBottom"	: "Abaixo" ,
"DlgImgAlignMiddle"	: "Meio" ,
"DlgImgAlignRight"	: "Direita" ,
"DlgImgAlignTextTop": "Topo do Texto" ,
"DlgImgAlignTop"	: "Topo" ,
"DlgImgPreview"		: "Prever Imagem" ,
"DlgImgMsgWrongExt"	: "Infelizmente, somente o envio dos seguintes tipos de arquivos são permitidos:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperação cancelada." ,
"DlgImgAlertSelect"	: "Por favor, selecione uma imagem para enviar." ,

// Link Dialog
"DlgLnkWindowTitle"	: "Link" ,
"DlgLnkURL"			: "URL" ,
"DlgLnkUpload"		: "Enviar" ,
"DlgLnkTarget"		: "Alvo" ,
"DlgLnkTargetNotSet": "<nenhum>" ,
"DlgLnkTargetBlank"	: "Nova Janela (_blank)" ,
"DlgLnkTargetParent": "Janela Pai (_parent)" ,
"DlgLnkTargetSelf"	: "Mesma Janela (_self)" ,
"DlgLnkTargetTop"	: "Janela superiora (_top)" ,
"DlgLnkTitle"		: "Título" ,
"DlgLnkBtnUpload"	: "Enviar para o Servidor" ,
"DlgLnkBtnBrowse"	: "Navegar no Servidor" ,
"DlgLnkMsgWrongExtA": "Infelizmente, somente o envio dos seguintes tipos de arquivos são permitidos:\n\n" + FCKConfig.LinkUploadAllowedExtensions + "\n\nOperação cancelada." ,
"DlgLnkMsgWrongExtD": "Infelizmente, somente o envio dos seguintes tipos de arquivos são permitidos:\n\n" + FCKConfig.LinkUploadDeniedExtensions + "\n\nOperação cancelada." ,

// Color Dialog
"DlgColorTitle"		: "Selecionar Cor" ,
"DlgColorBtnClear"	: "Limpar" ,
"DlgColorHighlight"	: "Destacar" ,
"DlgColorSelected"	: "Selecionado" ,

// Smiley Dialog
"DlgSmileyTitle"	: "Inserir uma Carinha" ,

// Special Character Dialog
"DlgSpecialCharTitle"	: "Inserir Caracter Especial" ,

// Table Dialog
"DlgTableTitleInsert"	: "Inserir Tabela" ,
"DlgTableTitleEdit"		: "Editar Tabela" ,
"DlgTableRows"			: "Linhas" ,
"DlgTableColumns"		: "Colunas" ,
"DlgTableBorder"		: "Tamanho da Borda" ,
"DlgTableAlign"			: "Alinhamento" ,
"DlgTableAlignNotSet"	: "<nenhum>" ,
"DlgTableAlignLeft"		: "Esquerda" ,
"DlgTableAlignCenter"	: "Centro" ,
"DlgTableAlignRight"	: "Direita" ,
"DlgTableWidth"			: "Largura" ,
"DlgTableWidthPx"		: "pixels" ,
"DlgTableWidthPc"		: "porcentagem" ,
"DlgTableHeight"		: "Altura" ,
"DlgTableCellSpace"		: "Espaçamento da Célula" ,
"DlgTableCellPad"		: "Padding da Célula" ,
"DlgTableCaption"		: "Cabeçalho" ,

// Table Cell Dialog
"DlgCellTitle"			: "Propriedades da Célula" ,
"DlgCellWidth"			: "Largura" ,
"DlgCellWidthPx"		: "pixels" ,
"DlgCellWidthPc"		: "porcentagem" ,
"DlgCellHeight"			: "Altura" ,
"DlgCellWordWrap"		: "Quebrar Texto" ,
"DlgCellWordWrapNotSet"	: "<nenhum>" ,
"DlgCellWordWrapYes"	: "Sim" ,
"DlgCellWordWrapNo"		: "Não" ,
"DlgCellHorAlign"		: "Alinhamento Horizontal" ,
"DlgCellHorAlignNotSet"	: "<nenhum>" ,
"DlgCellHorAlignLeft"	: "Esquerda" ,
"DlgCellHorAlignCenter"	: "Centro" ,
"DlgCellHorAlignRight"	: "Direita" ,
"DlgCellVerAlign"		: "Alinhamento Vertical" ,
"DlgCellVerAlignNotSet"	: "<nenhum>" ,
"DlgCellVerAlignTop"	: "Topo" ,
"DlgCellVerAlignMiddle"	: "Meio" ,
"DlgCellVerAlignBottom"	: "Abaixo" ,
"DlgCellVerAlignBaseline"	: "Linha de Base" ,
"DlgCellRowSpan"		: "Rows Span" ,
"DlgCellCollSpan"		: "Columns Span" ,
"DlgCellBackColor"		: "Cor do Fundo da Célula" ,
"DlgCellBorderColor"	: "Cor da Borda" ,
"DlgCellBtnSelect"		: "Selecionar..." ,

// About Dialog
"DlgAboutVersion"	: "versão" ,
"DlgAboutLicense"	: "Licenciado sob os termos da Licença Geral Pública GNU" ,
"DlgAboutInfo"		: "Para maiores informações, vá para "
}

