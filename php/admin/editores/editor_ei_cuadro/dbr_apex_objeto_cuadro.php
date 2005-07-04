<?
//Generacion: 4-07-2005 00:43:11
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_cuadro extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_cuadro'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla']='apex_objeto_cuadro';
		$definicion['clave'][0]='objeto_cuadro_proyecto';
		$definicion['clave'][1]='objeto_cuadro';
		$definicion['no_nulo'][0]='objeto_cuadro_proyecto';
		$definicion['no_nulo'][1]='objeto_cuadro';
		$definicion['columna'][0]='titulo';
		$definicion['columna'][1]='subtitulo';
		$definicion['columna'][2]='sql';
		$definicion['columna'][3]='columnas_clave';
		$definicion['columna'][4]='archivos_callbacks';
		$definicion['columna'][5]='ancho';
		$definicion['columna'][6]='ordenar';
		$definicion['columna'][7]='paginar';
		$definicion['columna'][8]='tamano_pagina';
		$definicion['columna'][9]='eof_invisible';
		$definicion['columna'][10]='eof_customizado';
		$definicion['columna'][11]='exportar';
		$definicion['columna'][12]='exportar_rtf';
		$definicion['columna'][13]='pdf_propiedades';
		$definicion['columna'][14]='pdf_respetar_paginacion';
		$definicion['columna'][15]='asociacion_columnas';
		$definicion['columna'][16]='ev_seleccion';
		$definicion['columna'][17]='ev_eliminar';
		$definicion['columna'][18]='dao_nucleo_proyecto';
		$definicion['columna'][19]='dao_nucleo';
		$definicion['columna'][20]='dao_metodo';
		$definicion['columna'][21]='dao_parametros';
		$definicion['columna'][22]='desplegable';
		$definicion['columna'][23]='desplegable_activo';
		$definicion['columna'][24]='scroll';
		$definicion['columna'][25]='scroll_alto';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_cuadro = '{$id['objeto']}'";
		$where[] = "objeto_cuadro_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>