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
 * File Name: ar.js
 * 	Arabic language file.
 * 
 * Version:  2.0 Beta 2
 * Modified: 2004-05-27 14:04:07
 * 
 * File Authors:
 * 		Aziz Oraij (aziz@oraij.com)
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
"Dir"				: "rtl",

// Toolbar Items and Context Menu
"Cut"				: "قص",
"Copy"				: "نسخ",
"Paste"				: "لصق",
"PasteText"			: "لصق كنص بسيط",
"PasteWord"			: "لصق من وورد",
"Find"				: "بحث",
"SelectAll"			: "تحديد الكل",
"RemoveFormat"		: "إزالة التنسيقات",
"InsertLink"		: "إدراج/تحرير رابط",
"RemoveLink"		: "إزالة رابط",
"InsertImage"		: "إدراج/تحرير صورة",
"InsertTable"		: "إدراج/تحرير جدول",
"InsertLine"		: "إدراج خط فاصل",
"InsertSpecialChar"	: "إدراج  رمز..ِ",
"InsertSmiley"		: "إدراج ابتسامات",
"About"				: "حول FCKeditor",

"Bold"				: "غامق",
"Italic"			: "مائل",
"Underline"			: "تسطير",
"StrikeThrough"		: "يتوسطه خط",
"Subscript"			: "منخفض",
"Superscript"		: "مرتفع",
"LeftJustify"		: "محاذاة إلى اليسار",
"CenterJustify"		: "توسيط",
"RightJustify"		: "محاذاة إلى اليمين",
"BlockJustify"		: "ضبط",
"DecreaseIndent"	: "إنقاص المسافة البادئة",
"IncreaseIndent"	: "زيادة المسافة البادئة",
"Undo"				: "تراجع",
"Redo"				: "إعادة",
"NumberedList"		: "تعداد رقمي",
"BulletedList"		: "تعداد نقطي",

"ShowTableBorders"	: "معاينة حدود الجداول",
"ShowDetails"		: "معاينة التفاصيل",

"FontStyle"			: "نمط",
"FontFormat"		: "تنسيق",
"Font"				: "خط",
"FontSize"			: "حجم الخط",
"TextColor"			: "لون النص",
"BGColor"			: "لون الخلفية",
"Source"			: "شفرة المصدر",

// Context Menu

"EditLink"			: "تحرير رابط",
"InsertRow"			: "إدراج صف",
"DeleteRows"		: "حذف صفوف",
"InsertColumn"		: "إدراج عمود",
"DeleteColumns"		: "حذف أعمدة",
"InsertCell"		: "إدراج خلية",
"DeleteCells"		: "حذف خلايا",
"MergeCells"		: "دمج خلايا",
"SplitCell"			: "تقسيم خلية",
"CellProperties"	: "خصائص الخلية",
"TableProperties"	: "خصائص الجدول",
"ImageProperties"	: "خصائص الصورة",

// Alerts and Messages

"ProcessingXHTML"	: "تتم الآن معالجة  XHTML. انتظر قليلاً...",
"Done"				: "تم",
"PasteWordConfirm"	: "يبدو أن النص المراد لصقه منسوخ من برنامج وورد. هل تود تنظيفه قبل الشروع في عملية اللصق؟",
"NotCompatiblePaste": "This command is available for Internet Explorer version 5.5 or more. Do you want to paste without cleaning?",

// Dialogs
"DlgBtnOK"			: "موافق",
"DlgBtnCancel"		: "إلغاء الأمر",
"DlgBtnClose"		: "إغلاق",

// Image Dialog
"DlgImgTitleInsert"	: "إدراج صورة",
"DlgImgTitleEdit"	: "تحرير صورة",
"DlgImgBtnUpload"	: "أرسلها للخادم",
"DlgImgURL"			: "URL",
"DlgImgUpload"		: "رفع",
"DlgImgBtnBrowse"	: "تصفح صور الموقع",
"DlgImgAlt"			: "الوصف",
"DlgImgWidth"		: "العرض",
"DlgImgHeight"		: "الارتفاع",
"DlgImgLockRatio"	: "المحافظة على نسبة العرض للارتفاع",
"DlgBtnResetSize"	: "استعادة الحجم الأصلي",
"DlgImgBorder"		: "سمك الحدود",
"DlgImgHSpace"		: "تباعد أفقي",
"DlgImgVSpace"		: "تباعد عمودي",
"DlgImgAlign"		: "محاذاة",
"DlgImgAlignLeft"	: "يسار",
"DlgImgAlignAbsBottom"	: "أسفل النص",
"DlgImgAlignAbsMiddle"	: "وسط السطر",
"DlgImgAlignBaseline"	: "على السطر",
"DlgImgAlignBottom"	: "أسفل",
"DlgImgAlignMiddle"	: "وسط",
"DlgImgAlignRight"	: "يمين",
"DlgImgAlignTextTop": "أعلى النص",
"DlgImgAlignTop"	: "أعلى",
"DlgImgPreview"		: "معاينة",
"DlgImgMsgWrongExt"	: "عفواً، لا يسمح برفع الملفات غير المطابقة لأنواع الملفات التالية:\n\n" + FCKConfig.ImageUploadAllowedExtensions + "\n\nOperation canceled.",
"DlgImgAlertSelect"	: "فضلاً اختر صورة ليتم رفعها.",		// NEW


// Link Dialog
"DlgLnkWindowTitle"	: "ارتباط تشعبي",		// NEW
"DlgLnkURL"			: "URL",
"DlgLnkUpload"		: "رفع",
"DlgLnkTarget"		: "الهدف",
"DlgLnkTargetNotSet": "<Not set>",
"DlgLnkTargetBlank"	: "إطار جديد (_blank)",
"DlgLnkTargetParent": "الإطار الأصل (_parent)",
"DlgLnkTargetSelf"	: "نفس الإطار (_self)",
"DlgLnkTargetTop"	: "صفحة كاملة (_top)",
"DlgLnkTitle"		: "وصف الرابط",
"DlgLnkBtnUpload"	: "أرسل للموقع",
"DlgLnkBtnBrowse"	: "تصفح الموقع",
"DlgLnkMsgWrongExtA": "عفواً، لا يسمح برفع الملفات غير المطابقة لأنواع الملفات التالية:\n\n" + FCKConfig.LinkUploadAllowedExtensions + "\n\nOperation canceled.",
"DlgLnkMsgWrongExtD": "عفواً، لا يسمح برفع الملفات ذات أنواع الملفات التالية:\n\n" + FCKConfig.LinkUploadDeniedExtensions + "\n\nOperation canceled.",

// Color Dialog
"DlgColorTitle"		: "اختر لوناً",
"DlgColorBtnClear"	: "مسح",
"DlgColorHighlight"	: "تحديد",
"DlgColorSelected"	: "اختيار",

// Smiley Dialog
"DlgSmileyTitle"	: "إدراج ابتسامات ",

// Special Character Dialog
"DlgSpecialCharTitle"	: "إدراج رمز",

// Table Dialog
"DlgTableTitleInsert"	: "إدراج جدول",
"DlgTableTitleEdit"	: "تحرير جدول",
"DlgTableRows"		: "صفوف",
"DlgTableColumns"	: "أعمدة",
"DlgTableBorder"	: "سمك الحدود",
"DlgTableAlign"		: "المحاذاة",
"DlgTableAlignNotSet"	: "<Not set>",
"DlgTableAlignLeft"	: "يسار",
"DlgTableAlignCenter"	: "وسط",
"DlgTableAlignRight": "يمين",
"DlgTableWidth"		: "العرض",
"DlgTableWidthPx"	: "بكسل",
"DlgTableWidthPc"	: "بالمئة",
"DlgTableHeight"	: "الارتفاع",
"DlgTableCellSpace"	: "تباعد الخلايا",
"DlgTableCellPad"	: "المسافة البادئة",
"DlgTableCaption"	: "الوصف",

// Table Cell Dialog
"DlgCellTitle"		: "خصائص الخلية",
"DlgCellWidth"		: "العرض",
"DlgCellWidthPx"	: "بكسل",
"DlgCellWidthPc"	: "بالمئة",
"DlgCellHeight"		: "الارتفاع",
"DlgCellWordWrap"	: "التفاف النص",
"DlgCellWordWrapNotSet"	: "<Not set>",
"DlgCellWordWrapYes": "نعم",
"DlgCellWordWrapNo"	: "لا",
"DlgCellHorAlign"	: "المحاذاة الأفقية",
"DlgCellHorAlignNotSet"	: "<Not set>",
"DlgCellHorAlignLeft"	: "يسار",
"DlgCellHorAlignCenter"	: "وسط",
"DlgCellHorAlignRight"	: "يمين",
"DlgCellVerAlign"		: "المحاذاة العمودية",
"DlgCellVerAlignNotSet"	: "<Not set>",
"DlgCellVerAlignTop"	: "أعلى",
"DlgCellVerAlignMiddle"	: "وسط",
"DlgCellVerAlignBottom"	: "أسفل",
"DlgCellVerAlignBaseline"	: "على السطر",
"DlgCellRowSpan"	: "امتداد الصفوف",
"DlgCellCollSpan"	: "امتداد الأعمدة",
"DlgCellBackColor"	: "لون الخلفية",
"DlgCellBorderColor": "لون الحدود",
"DlgCellBtnSelect"	: "حدّد...",

// About Dialog
"DlgAboutVersion"	: "الإصدار",
"DlgAboutLicense"	: "مرخّص بحسب قانون  GNU LGPL",
"DlgAboutInfo"		: "لمزيد من المعلومات تفضل بزيارة"
}

