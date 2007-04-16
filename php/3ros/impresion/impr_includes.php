<?php
$path_origen = '';

require_once(toba_dir()."/php/3ros/ezpdf/class.ezpdf.php");
 
require_once($path_origen . 'impr_documento.php');
require_once($path_origen . 'impr_hoja.php');
require_once($path_origen . 'impr_encabezado.php');
require_once($path_origen . 'impr_cuerpo.php');
require_once($path_origen . 'impr_pie.php');
require_once($path_origen . 'impr_bloque.php');
require_once($path_origen . 'impr_etiqueta.php');
require_once($path_origen . 'impr_grafico.php');
require_once($path_origen . 'impr_funciones.php');
require_once($path_origen . 'impr_etiqueta_barcode.php');
require_once($path_origen . 'impr_grafico_imagen.php');
require_once($path_origen . 'impr_grafico_rectangulo.php');
require_once($path_origen . 'impr_grafico_rectangulo_fino.php');
require_once($path_origen . 'barcode/int25.php');

//require_once('adodb340/adodb.inc.php');
//require_once('adodb340/tohtml.inc.php');
?>