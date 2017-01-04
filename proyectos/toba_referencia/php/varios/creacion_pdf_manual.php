<?php
$pdf = new Cezpdf();
$diff = array(193 => 'Aacute', 225 => 'aacute', 
              201 => 'Eacute', 233 => 'eacute', 
              205 => 'Iacute', 237 => 'iacute', 
              209 => 'Ntilde', 241 => 'ntilde', 
              211 => 'Oacute', 243 => 'oacute', 
              218 => 'Uacute', 250 => 'uacute',
              220 => 'Udieresis', 252 => 'udieresis');
$pdf->selectFont('Helvetica', array('encoding' => 'WinAnsiEncoding', 'differences'=> $diff));
$pdf->ezText('Tabla', 14);

//-- Cuadro con datos
$opciones = array(
    'splitRows' => 0,
    'rowGap' => 1,
    'showHeadings' => true,
    'titleFontSize' => 9,
    'fontSize' => 10,
    'shadeCol' => array(0.9, 0.9, 0.9),
    'outerLineThickness' => 0.7,
    'innerLineThickness' => 0.7,
    'xOrientation' => 'center',
    'width' => 500
);
$datos = array(
    array('col1' => 1, 'col2' => 2),
    array('col1' => 3, 'col2' => 4),
);
$pdf->ezTable($datos, array('col1' => 'Columna 1', 'col2' => 'Columna 2'), utf8_encode('Titulo Tabla'), $opciones);

$pdf->ezText(utf8_encode("\nCódigo fuente"), 14);
$pdf->ezText(utf8_encode("\n" . file_get_contents(__FILE__)), 10);
$tmp = $pdf->ezOutput();

header('Cache-Control: private');
header('Content-type: application/pdf');
header('Content-Length: ' . strlen(ltrim($tmp)));
header('Content-Disposition: attachment; filename="Archivo.pdf"');
header('Pragma: no-cache');
header('Expires: 0');
echo ltrim($tmp);
?>
