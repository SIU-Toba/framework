<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.5.0, 2007-10-23
 */


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Reader_IReader */
require_once 'PHPExcel/Reader/IReader.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Style */
require_once 'PHPExcel/Style.php';

/** PHPExcel_Style_Borders */
require_once 'PHPExcel/Style/Borders.php';

/** PHPExcel_Style_Conditional */
require_once 'PHPExcel/Style/Conditional.php';

/** PHPExcel_Style_Protection */
require_once 'PHPExcel/Style/Protection.php';

/** PHPExcel_Worksheet_BaseDrawing */
require_once 'PHPExcel/Worksheet/BaseDrawing.php';

/** PHPExcel_Worksheet_Drawing */
require_once 'PHPExcel/Worksheet/Drawing.php';

/** PHPExcel_Shared_Drawing */
require_once 'PHPExcel/Shared/Drawing.php';


/**
 * PHPExcel_Reader_Excel2007
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2007 implements PHPExcel_Reader_IReader
{
	/**
	 * Read data only?
	 *
	 * @var boolean
	 */
	private $_readDataOnly = false;
	
	/**
	 * Read data only?
	 *
	 * @return boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}
	
	/**
	 * Set read data only
	 *
	 * @param boolean $pValue
	 */
	public function setReadDataOnly($pValue = false) {
		$this->_readDataOnly = $pValue;
	}
	
	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */	
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}
			
		// Initialisations
		$excel = new PHPExcel;
		$excel->removeSheetByIndex(0);
		$zip = new ZipArchive;
		$zip->open($pFilename);
		
		$rels = simplexml_load_string($zip->getFromName("_rels/.rels")); //~ http://schemas.openxmlformats.org/package/2006/relationships");
		foreach ($rels->Relationship as $rel) {
			switch ($rel["Type"]) {
				case "http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties":
					$xmlCore = simplexml_load_string($zip->getFromName("{$rel['Target']}"));
					$xmlCore->registerXPathNamespace("dc", "http://purl.org/dc/elements/1.1/");
					$xmlCore->registerXPathNamespace("dcterms", "http://purl.org/dc/terms/");
					$xmlCore->registerXPathNamespace("cp", "http://schemas.openxmlformats.org/package/2006/metadata/core-properties");
					$docProps = $excel->getProperties();
					$docProps->setCreator((string) self::array_item($xmlCore->xpath("dc:creator")));
					$docProps->setLastModifiedBy((string) self::array_item($xmlCore->xpath("cp:lastModifiedBy")));
					$docProps->setCreated(strtotime(self::array_item($xmlCore->xpath("dcterms:created")))); //! respect xsi:type
					$docProps->setModified(strtotime(self::array_item($xmlCore->xpath("dcterms:modified")))); //! respect xsi:type
					$docProps->setTitle((string) self::array_item($xmlCore->xpath("dc:title")));
					$docProps->setDescription((string) self::array_item($xmlCore->xpath("dc:description")));
					$docProps->setSubject((string) self::array_item($xmlCore->xpath("dc:subject")));
				break;
				
				case "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument":
					$dir = dirname($rel["Target"]);
					$relsWorkbook = simplexml_load_string($zip->getFromName("$dir/_rels/" . basename($rel["Target"]) . ".rels"));  //~ http://schemas.openxmlformats.org/package/2006/relationships");
					$relsWorkbook->registerXPathNamespace("rel", "http://schemas.openxmlformats.org/package/2006/relationships");
					
					$sharedStrings = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings']"));
					$xmlStrings = simplexml_load_string($zip->getFromName("$dir/$xpath[Target]"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					if (isset($xmlStrings) && isset($xmlStrings->si)) {
						foreach ($xmlStrings->si as $val) {
							$sharedStrings[] = (string) $val->t;
						}
					}
					
					$worksheets = array();
					foreach ($relsWorkbook->Relationship as $ele) {
						if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet") {
							$worksheets[(string) $ele["Id"]] = $ele["Target"];
						}
					}
					
					$styles = array();
					$xpath = self::array_item($relsWorkbook->xpath("rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles']"));
					$xmlStyles = simplexml_load_string($zip->getFromName("$dir/$xpath[Target]")); //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					$numFmts = $xmlStyles->numFmts[0];
					if ($numFmts) {
						$numFmts->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					}
					if (!$this->_readDataOnly) {
						foreach ($xmlStyles->cellXfs->xf as $xf) {
							$styles[] = (object) array(
								"numFmt" => ($numFmts && $xf["numFmtId"] ? self::array_item($numFmts->xpath("sml:numFmt[@numFmtId=$xf[numFmtId]]")) : 0),
								"font" => $xmlStyles->fonts->font[intval($xf["fontId"])],
								"fill" => $xmlStyles->fills->fill[intval($xf["fillId"])],
								"border" => $xmlStyles->borders->border[intval($xf["borderId"])],
								"alignment" => $xf->alignment,
								"protection" => $xf->protection
							);
						}
					}
					
					$dxfs = array();
					if (!$this->_readDataOnly) {
						foreach ($xmlStyles->dxfs->dxf as $dxf) {
							$style = new PHPExcel_Style;
							$this->_readStyle($style, $dxf);
							$dxfs[] = $style;
						}
					}

					$xmlWorkbook = simplexml_load_string($zip->getFromName("{$rel['Target']}"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");
					foreach ($xmlWorkbook->sheets->sheet as $eleSheet) {
						$docSheet = $excel->createSheet();
						$docSheet->setTitle((string) $eleSheet["name"]);
						$fileWorksheet = $worksheets[(string) self::array_item($eleSheet->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
						$xmlSheet = simplexml_load_string($zip->getFromName("$dir/$fileWorksheet"));  //~ http://schemas.openxmlformats.org/spreadsheetml/2006/main");

						if (isset($xmlSheet->cols) && !$this->_readDataOnly) {
							foreach ($xmlSheet->cols->col as $col) {
								for ($i = intval($col["min"]) - 1; $i < intval($col["max"]) && $i < 256; $i++) {
									if ($col["bestFit"]) {
										$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
									}
									if ($col["hidden"]) {
										$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setVisible(false);
									}
									$docSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(floatval($col["width"]));
								}
								
							}
						}
						
						if (isset($xmlSheet->printOptions) && !$this->_readDataOnly) {
							if ($xmlSheet->printOptions['gridLines'] && $xmlSheet->printOptions['gridLinesSet']) {
								$docSheet->setShowGridlines(true);
							}
						}

						foreach ($xmlSheet->sheetData->row as $row) {
							if ($row["ht"] && !$this->_readDataOnly) {
								$docSheet->getRowDimension(intval($row["r"]))->setRowHeight(floatval($row["ht"]));
							}
							if ($row["hidden"] && !$this->_readDataOnly) {
								$docSheet->getRowDimension(intval($row["r"]))->setVisible(false);
							}

							foreach ($row->c as $c) {
								$r = (string) $c["r"];

								switch ($c["t"]) {
									case "s": $value = $sharedStrings[intval($c->v)]; break;
									case "b": $value = (bool) $c->v; break;
									case "inlineStr":
										$value = new PHPExcel_RichText( $docSheet->getCell($r) );

										if (isset($c->is->t)) {
											$value->createText( (string) $c->is->t );
										} else {
											foreach ($c->is->r as $run) {
												$objText = $value->createTextRun( (string) $run->t );
							
												if (isset($run->rPr->rFont["val"])) {
													$objText->getFont()->setName($run->rPr->rFont["val"]);
												}
												
												if (isset($run->rPr->sz["val"])) {
													$objText->getFont()->setSize($run->rPr->sz["val"]);
												}
												
												if (isset($run->rPr->color)) {
													$objText->getFont()->setColor( new PHPExcel_Style_Color( $this->_readColor($run->rPr->color) ) );
												}
												
												if (isset($run->rPr->b["val"]) && ($run->rPr->b["val"] == 'true' || $run->rPr->b["val"] == '1')) {
													$objText->getFont()->setBold(true);
												}
												
												if (isset($run->rPr->i["val"]) && ($run->rPr->i["val"] == 'true' || $run->rPr->i["val"] == '1')) {
													$objText->getFont()->setItalic(true);
												}
												
												if (isset($run->rPr->u["val"]) && ($run->rPr->u["val"] == 'true' || $run->rPr->u["val"] == '1')) {
													$objText->getFont()->setUnderline(true);
												}
												
												if (isset($run->rPr->strike["val"])  && ($run->rPr->strike["val"] == 'true' || $run->rPr->strike["val"] == '1')) {
													$objText->getFont()->setStriketrough(true);
												}
											}
										}
										
										break;
									
									default:
										if (!isset($c->f)) {
											$value = (string) $c->v;
										} else {
											$value = "={$c->f}";
										}
										
										break;
								}
								
								if ($value) {
									$docSheet->setCellValue($r, $value);
								}
								if ($c["s"] && !$this->_readDataOnly) {
									if (isset($styles[intval($c["s"])])) {
										$this->_readStyle($docSheet->getStyle($r), $styles[intval($c["s"])]);
									}
								}
							}
						}

						$conditionals = array();
						if (!$this->_readDataOnly) {
							foreach ($xmlSheet->conditionalFormatting as $conditional) {
								foreach ($conditional->cfRule as $cfRule) {
									if (
										(
											(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_NONE ||
											(string)$cfRule["type"] == PHPExcel_Style_Conditional::CONDITION_CELLIS
										) && isset($dxfs[intval($cfRule["dxfId"])])
									) {
										$conditionals[(string) $conditional["sqref"]][intval($cfRule["priority"])] = $cfRule;
									}
								}
							}
								
							foreach ($conditionals as $ref => $cfRules) {
								ksort($cfRules);
								$conditionalStyles = array();
								foreach ($cfRules as $cfRule) {
									$objConditional = new PHPExcel_Style_Conditional();
									$objConditional->setConditionType((string) $cfRule["type"]);
									$objConditional->setOperatorType((string) $cfRule["operator"]);
									$objConditional->setCondition((string) $cfRule->formula);
									$objConditional->setStyle(clone $dxfs[intval($cfRule["dxfId"])]);
									$conditionalStyles[] = $objConditional;
								}
								
								// Extract all cell references in $ref
								$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($ref);
								foreach ($aReferences as $reference) {
									$docSheet->getStyle($reference)->setConditionalStyles($conditionalStyles);
								}
							}
						}

						$aKeys = array("sheet", "objects", "scenarios", "formatCells", "formatColumns", "formatRows", "insertColumns", "insertRows", "insertHyperlinks", "deleteColumns", "deleteRows", "selectLockedCells", "sort", "autoFilter", "pivotTables", "selectUnlockedCells");
						if (!$this->_readDataOnly) {
							foreach ($aKeys as $key) {
								$method = "set" . ucfirst($key);
								$docSheet->getProtection()->$method($xmlSheet->sheetProtection[$key] == "true");
							}
						}
						
						if (!$this->_readDataOnly) {
							$docSheet->getProtection()->setPassword((string) $xmlSheet->sheetProtection["password"], true);
							if ($xmlSheet->protectedRanges->protectedRange) {
								foreach ($xmlSheet->protectedRanges->protectedRange as $protectedRange) {
									$docSheet->protectCells((string) $protectedRange["sqref"], (string) $protectedRange["password"], true);
								}
							}
						}

						if ($xmlSheet->autoFilter && !$this->_readDataOnly) {
							$docSheet->setAutoFilter((string) $xmlSheet->autoFilter["ref"]);
						}

						if ($xmlSheet->mergeCells->mergeCell && !$this->_readDataOnly) {
							foreach ($xmlSheet->mergeCells->mergeCell as $mergeCell) {
								$docSheet->mergeCells((string) $mergeCell["ref"]);
							}
						}

						if (!$this->_readDataOnly) {
							$docPageMargins = $docSheet->getPageMargins();
							$docPageMargins->setLeft(floatval($xmlSheet->pageMargins["left"]));
							$docPageMargins->setRight(floatval($xmlSheet->pageMargins["right"]));
							$docPageMargins->setTop(floatval($xmlSheet->pageMargins["top"]));
							$docPageMargins->setBottom(floatval($xmlSheet->pageMargins["bottom"]));
							$docPageMargins->setHeader(floatval($xmlSheet->pageMargins["header"]));
							$docPageMargins->setFooter(floatval($xmlSheet->pageMargins["footer"]));
						}
						
						if (!$this->_readDataOnly) {
							$docPageSetup = $docSheet->getPageSetup();
							
							if (isset($xmlSheet->pageSetup["orientation"])) {
								$docPageSetup->setOrientation((string) $xmlSheet->pageSetup["orientation"]);
							}
							if (isset($xmlSheet->pageSetup["paperSize"])) {
								$docPageSetup->setPaperSize(intval($xmlSheet->pageSetup["paperSize"]));
							}
							if (isset($xmlSheet->pageSetup["scale"])) {
								$docPageSetup->setScale(intval($xmlSheet->pageSetup["scale"]));
							}
							if (isset($xmlSheet->pageSetup["fitToHeight"])) {
								$docPageSetup->setFitToHeight(intval($xmlSheet->pageSetup["fitToHeight"]));
							}
							if (isset($xmlSheet->pageSetup["fitToWidth"])) {
								$docPageSetup->setFitToWidth(intval($xmlSheet->pageSetup["fitToWidth"]));
							}
						}
						
						if (!$this->_readDataOnly) {
							$docHeaderFooter = $docSheet->getHeaderFooter();
							$docHeaderFooter->setDifferentOddEven($xmlSheet->headerFooter["differentOddEven"] == 'true');
							$docHeaderFooter->setDifferentFirst($xmlSheet->headerFooter["differentFirst"] == 'true');
							$docHeaderFooter->setScaleWithDocument($xmlSheet->headerFooter["scaleWithDoc"] == 'true');
							$docHeaderFooter->setAlignWithMargins($xmlSheet->headerFooter["alignWithMargins"] == 'true');
							$docHeaderFooter->setOddHeader((string) $xmlSheet->headerFooter->oddHeader);
							$docHeaderFooter->setOddFooter((string) $xmlSheet->headerFooter->oddFooter);
							$docHeaderFooter->setEvenHeader((string) $xmlSheet->headerFooter->evenHeader);
							$docHeaderFooter->setEvenFooter((string) $xmlSheet->headerFooter->evenFooter);
							$docHeaderFooter->setFirstHeader((string) $xmlSheet->headerFooter->firstHeader);
							$docHeaderFooter->setFirstFooter((string) $xmlSheet->headerFooter->firstFooter);
						}
						
						if ($xmlSheet->rowBreaks->brk && !$this->_readDataOnly) {
							foreach ($xmlSheet->rowBreaks->brk as $brk) {
								if ($brk["man"]) {
									$docSheet->setBreak("A$brk[id]", PHPExcel_Worksheet::BREAK_ROW);
								}
							}
						}
						if ($xmlSheet->colBreaks->brk && !$this->_readDataOnly) {
							foreach ($xmlSheet->colBreaks->brk as $brk) {
								if ($brk["man"]) {
									$docSheet->setBreak(PHPExcel_Cell::stringFromColumnIndex($brk["id"]) . "1", PHPExcel_Worksheet::BREAK_COLUMN);
								}
							}
						}
					
						if ($xmlSheet->dataValidations && !$this->_readDataOnly) {
							foreach ($xmlSheet->dataValidations->dataValidation as $dataValidation) {
							    // Uppercase coordinate
						    	$range = strtoupper($dataValidation["sqref"]);
						    	
								// Extract all cell references in $range
								$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($range);
								foreach ($aReferences as $reference) {								
									// Create validation
									$docValidation = $docSheet->getCell($reference)->getDataValidation();
									$docValidation->setType((string) $dataValidation["type"]);
									$docValidation->setErrorStyle((string) $dataValidation["errorStyle"]);
									$docValidation->setOperator((string) $dataValidation["operator"]);
									$docValidation->setAllowBlank($dataValidation["allowBlank"] != 0);
									$docValidation->setShowDropDown($dataValidation["showDropDown"] == 0);
									$docValidation->setShowInputMessage($dataValidation["showInputMessage"] != 0);
									$docValidation->setShowErrorMessage($dataValidation["showErrorMessage"] != 0);
									$docValidation->setErrorTitle((string) $dataValidation["errorTitle"]);
									$docValidation->setError((string) $dataValidation["error"]);
									$docValidation->setPromptTitle((string) $dataValidation["promptTitle"]);
									$docValidation->setPrompt((string) $dataValidation["prompt"]);
									$docValidation->setFormula1((string) $dataValidation->formula1);
									$docValidation->setFormula2((string) $dataValidation->formula2);
								}
							}
						}
						
						// Add hyperlinks
						$hyperlinks = array();
						if (!$this->_readDataOnly) {
							// Locate hyperlink relations
							if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
								$relsWorksheet = simplexml_load_string($zip->getFromName( dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
								foreach ($relsWorksheet->Relationship as $ele) {
									if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink") {
										$hyperlinks[(string)$ele["Id"]] = (string)$ele["Target"];
									}
								}
							}

							// Loop trough hyperlinks
							if ($xmlSheet->hyperlinks) {
								foreach ($xmlSheet->hyperlinks->hyperlink as $hyperlink) {
									// Link url
									$linkRel = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
									$docSheet->getCell( $hyperlink['ref'] )->getHyperlink()->setUrl( $hyperlinks[ (string)$linkRel['id'] ] );
										
									// Tooltip
									if (isset($hyperlink['tooltip'])) {
										$docSheet->getCell( $hyperlink['ref'] )->getHyperlink()->setTooltip( (string)$hyperlink['tooltip'] );
									}								
								}
							}
						}
						
// TODO: Make sure drawings and graph are loaded differently!
						if ($zip->locateName(dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels")) {
							$relsWorksheet = simplexml_load_string($zip->getFromName( dirname("$dir/$fileWorksheet") . "/_rels/" . basename($fileWorksheet) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
							$drawings = array();
							foreach ($relsWorksheet->Relationship as $ele) {
								if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing") {
									$drawings[(string) $ele["Id"]] = self::dir_add("$dir/$fileWorksheet", $ele["Target"]);
								}
							}
							if ($xmlSheet->drawing && !$this->_readDataOnly) {
								foreach ($xmlSheet->drawing as $drawing) {
									$fileDrawing = $drawings[(string) self::array_item($drawing->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "id")];
									$relsDrawing = simplexml_load_string($zip->getFromName( dirname($fileDrawing) . "/_rels/" . basename($fileDrawing) . ".rels") ); //~ http://schemas.openxmlformats.org/package/2006/relationships");
									$images = array();
									foreach ($relsDrawing->Relationship as $ele) {
										if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {
											$images[(string) $ele["Id"]] = self::dir_add($fileDrawing, $ele["Target"]);
										}
									}
									$xmlDrawing = simplexml_load_string($zip->getFromName($fileDrawing))->children("http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing");
									foreach ($xmlDrawing->oneCellAnchor as $oneCellAnchor) {
										$blip = $oneCellAnchor->pic->blipFill->children("http://schemas.openxmlformats.org/drawingml/2006/main")->blip;
										$xfrm = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->xfrm;
										$outerShdw = $oneCellAnchor->pic->spPr->children("http://schemas.openxmlformats.org/drawingml/2006/main")->effectLst->outerShdw;
										$objDrawing = new PHPExcel_Worksheet_Drawing;
										$objDrawing->setName((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "name"));
										$objDrawing->setDescription((string) self::array_item($oneCellAnchor->pic->nvPicPr->cNvPr->attributes(), "descr"));
										$objDrawing->setPath("zip://$pFilename#" . $images[(string) self::array_item($blip->attributes("http://schemas.openxmlformats.org/officeDocument/2006/relationships"), "embed")], false);								
										$objDrawing->setCoordinates(PHPExcel_Cell::stringFromColumnIndex($oneCellAnchor->from->col) . ($oneCellAnchor->from->row + 1));
										$objDrawing->setOffsetX(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->colOff));
										$objDrawing->setOffsetY(PHPExcel_Shared_Drawing::EMUToPixels($oneCellAnchor->from->rowOff));
										$objDrawing->setResizeProportional(false);
										$objDrawing->setWidth(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cx")));
										$objDrawing->setHeight(PHPExcel_Shared_Drawing::EMUToPixels(self::array_item($oneCellAnchor->ext->attributes(), "cy")));
										if ($xfrm) {
											$objDrawing->setRotation(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($xfrm->attributes(), "rot")));
										}
										if ($outerShdw) {
											$shadow = $objDrawing->getShadow();
											$shadow->setVisible(true);
											$shadow->setBlurRadius(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "blurRad")));
											$shadow->setDistance(PHPExcel_Shared_Drawing::EMUTopixels(self::array_item($outerShdw->attributes(), "dist")));
											$shadow->setDirection(PHPExcel_Shared_Drawing::angleToDegrees(self::array_item($outerShdw->attributes(), "dir")));
											$shadow->setAlignment((string) self::array_item($outerShdw->attributes(), "algn"));
											$shadow->getColor()->setRGB(self::array_item($outerShdw->srgbClr->attributes(), "val"));
											$shadow->setAlpha(self::array_item($outerShdw->srgbClr->alpha->attributes(), "val") / 1000);
										}
										$objDrawing->setWorksheet($docSheet);
									}
								}
							}
						}

					}

					if (!$this->_readDataOnly) {
						$excel->setActiveSheetIndex(intval($xmlWorkbook->bookView->workbookView["activeTab"]));
					}
				break;
			}
	
		}
		
		return $excel;
	}
	
	private function _readColor($color) {
		if (isset($color["rgb"])) {
			return $color["rgb"];
		} else if (isset($color["indexed"])) {
			return PHPExcel_Style_Color::indexedColor($color["indexed"])->getARGB();
		}
	}
	
	private function _readStyle($docStyle, $style) {
		// format code
		$docStyle->getNumberFormat()->setFormatCode((string) $style->numFmt["formatCode"]);
		
		// font
		$docStyle->getFont()->setName((string) $style->font->name["val"]);
		$docStyle->getFont()->setSize((string) $style->font->sz["val"]);
		$docStyle->getFont()->setBold($style->font->b["val"] == 'true');
		$docStyle->getFont()->setItalic($style->font->i["val"] == 'true');
		$docStyle->getFont()->setStriketrough($style->font->strike["val"] == 'true');
		$docStyle->getFont()->getColor()->setARGB($this->_readColor($style->font->color));
		$docStyle->getFont()->setUnderline((string) $style->font->u["val"]);
		
		// fill
		if ($style->fill->gradientFill) {
			$gradientFill = $style->fill->gradientFill[0];
			$docStyle->getFill()->setFillType((string) $gradientFill["type"]);
			$docStyle->getFill()->setRotation(floatval($gradientFill["degree"]));
			$gradientFill->registerXPathNamespace("sml", "http://schemas.openxmlformats.org/spreadsheetml/2006/main");
			$docStyle->getFill()->getStartColor()->setARGB($this->_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=0]"))->color) );
			$docStyle->getFill()->getEndColor()->setARGB($this->_readColor( self::array_item($gradientFill->xpath("sml:stop[@position=1]"))->color) );
		} elseif ($style->fill->patternFill) {
			$docStyle->getFill()->setFillType((string) $style->fill->patternFill["patternType"]);
			if ($style->fill->patternFill->fgColor) {
				$docStyle->getFill()->getStartColor()->setARGB($this->_readColor($style->fill->patternFill->fgColor));
			}
			if ($style->fill->patternFill->bgColor) {
				$docStyle->getFill()->getEndColor()->setARGB($this->_readColor($style->fill->patternFill->bgColor));
			}
		}
		
		// border
		if ($style->border["diagonalUp"] == 'true') {
			$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_UP);
		} elseif ($style->border["diagonalDown"] == 'true') {
			$docStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_DOWN);
		}
		$docStyle->getBorders()->setOutline($style->border["outline"] == 'true');
		$this->_readBorder($docStyle->getBorders()->getLeft(), $style->border->left);
		$this->_readBorder($docStyle->getBorders()->getRight(), $style->border->right);
		$this->_readBorder($docStyle->getBorders()->getTop(), $style->border->top);
		$this->_readBorder($docStyle->getBorders()->getBottom(), $style->border->bottom);
		$this->_readBorder($docStyle->getBorders()->getDiagonal(), $style->border->diagonal);
		$this->_readBorder($docStyle->getBorders()->getVertical(), $style->border->vertical);
		$this->_readBorder($docStyle->getBorders()->getHorizontal(), $style->border->horizontal);
		
		// alignment
		$docStyle->getAlignment()->setHorizontal((string) $style->alignment["horizontal"]);
		$docStyle->getAlignment()->setVertical((string) $style->alignment["vertical"]);
		
		$textRotation = 0;
		if ($style->alignment["textRotation"] <= 90) {
			$textRotation = $style->alignment["textRotation"];
		} else if ($style->alignment["textRotation"] > 90) {
			$textRotation = 90 - $style->alignment["textRotation"];
		}
				
		$docStyle->getAlignment()->setTextRotation(intval($textRotation));
		$docStyle->getAlignment()->setWrapText($style->alignment["wrapText"] == "true");
		
		// protection
		if (isset($style->protection)) {
			if (isset($style->protection['locked'])) {
				if ((string)$style->protection['locked'] == 'true') {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}

			if (isset($style->protection['hidden'])) {
				if ((string)$style->protection['hidden'] == 'true') {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_PROTECTED);
				} else {
					$docStyle->getProtection()->setHidden(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				}
			}
		}
	}
	
	private function _readBorder($docBorder, $eleBorder) {
		if (isset($eleBorder["style"])) {
			$docBorder->setBorderStyle((string) $eleBorder["style"]);
		}
		if (isset($eleBorder->color)) {
			$docBorder->getColor()->setARGB($this->_readColor($eleBorder->color));
		}
	}

	private static function array_item($array, $key = 0) {
		return (isset($array[$key]) ? $array[$key] : null);
	}
	
	private static function dir_add($base, $add) {
		return preg_replace('~[^/]+/\.\./~', '', dirname($base) . "/$add");
	}
	
}
