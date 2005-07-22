<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_cuadro_columna extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_cuadro_columna'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_cuadro_columna';
		$def['columna'][0]['nombre']='objeto_cuadro_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo_db']='1';
		$def['columna'][1]['nombre']='objeto_cuadro';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo_db']='1';
		$def['columna'][2]['nombre']='orden';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo_db']='1';
		$def['columna'][3]['nombre']='titulo';
		$def['columna'][3]['no_nulo_db']='1';
		$def['columna'][4]['nombre']='columna_estilo';
		$def['columna'][4]['no_nulo_db']='1';
		$def['columna'][5]['nombre']='columna_ancho';
		$def['columna'][6]['nombre']='ancho_html';
		$def['columna'][7]['nombre']='total';
		$def['columna'][8]['nombre']='valor_sql';
		$def['columna'][9]['nombre']='valor_sql_formato';
		$def['columna'][10]['nombre']='valor_fijo';
		$def['columna'][11]['nombre']='valor_proceso';
		$def['columna'][12]['nombre']='valor_proceso_esp';
		$def['columna'][13]['nombre']='valor_proceso_parametros';
		$def['columna'][14]['nombre']='vinculo_indice';
		$def['columna'][15]['nombre']='par_dimension_proyecto';
		$def['columna'][16]['nombre']='par_dimension';
		$def['columna'][17]['nombre']='par_tabla';
		$def['columna'][18]['nombre']='par_columna';
		$def['columna'][19]['nombre']='no_ordenar';
		$def['columna'][20]['nombre']='mostrar_xls';
		$def['columna'][21]['nombre']='mostrar_pdf';
		$def['columna'][22]['nombre']='pdf_propiedades';
		$def['columna'][23]['nombre']='desabilitado';
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