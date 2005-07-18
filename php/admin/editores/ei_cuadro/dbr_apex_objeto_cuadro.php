<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_cuadro extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_cuadro'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_cuadro';
		$def['columna'][0]['nombre']='objeto_cuadro_proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_cuadro';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='titulo';
		$def['columna'][3]['nombre']='subtitulo';
		$def['columna'][4]['nombre']='sql';
		$def['columna'][5]['nombre']='columnas_clave';
		$def['columna'][6]['nombre']='archivos_callbacks';
		$def['columna'][7]['nombre']='ancho';
		$def['columna'][8]['nombre']='ordenar';
		$def['columna'][9]['nombre']='paginar';
		$def['columna'][10]['nombre']='tamano_pagina';
		$def['columna'][11]['nombre']='eof_invisible';
		$def['columna'][12]['nombre']='eof_customizado';
		$def['columna'][13]['nombre']='exportar';
		$def['columna'][14]['nombre']='exportar_rtf';
		$def['columna'][15]['nombre']='pdf_propiedades';
		$def['columna'][16]['nombre']='pdf_respetar_paginacion';
		$def['columna'][17]['nombre']='asociacion_columnas';
		$def['columna'][18]['nombre']='ev_seleccion';
		$def['columna'][19]['nombre']='ev_eliminar';
		$def['columna'][20]['nombre']='dao_nucleo_proyecto';
		$def['columna'][21]['nombre']='dao_nucleo';
		$def['columna'][22]['nombre']='dao_metodo';
		$def['columna'][23]['nombre']='dao_parametros';
		$def['columna'][24]['nombre']='desplegable';
		$def['columna'][25]['nombre']='desplegable_activo';
		$def['columna'][26]['nombre']='scroll';
		$def['columna'][27]['nombre']='scroll_alto';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_cuadro_proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_cuadro = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>