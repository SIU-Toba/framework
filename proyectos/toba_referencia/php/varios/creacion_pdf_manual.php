<?php

require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');

$pdf = new Cezpdf();
$pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
$pdf->ezText('Tabla', 14);

//-- Cuadro con datos
$opciones = array(
		'splitRows'=>0,
		'rowGap' => 1,
		'showHeadings' => true,	
		'titleFontSize' => 9,
		'fontSize' => 10,
		'shadeCol' => array(0.9,0.9,0.9),
		'outerLineThickness' => 0.7,
		'innerLineThickness' => 0.7,
       	'xOrientation' => 'center',
       	'width' => 500
);
$datos = array(
	array('col1' => 1, 'col2' => 2),
	array('col1' => 3, 'col2' => 4),
);
$pdf->ezTable($datos, array('col1'=>'Columna 1', 'col2' => 'Columna 2'), 'Titulo Tabla', $opciones); 

$pdf->ezText("\nCódigo fuente", 14);
$pdf->ezText("\n".file_get_contents(__FILE__), 10);

$tmp = $pdf->ezOutput(0);
header('Cache-Control: private');
header('Content-type: application/pdf');
header('Content-Length: '.strlen(ltrim($tmp)));
header('Content-Disposition: attachment; filename="Archivo.pdf"');
header('Pragma: no-cache');
header('Expires: 0');
 
echo ltrim($tmp);

?>