<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ut_formulario extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ut_formulario'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ut_formulario';
		$def['columna'][0]['nombre']='objeto_ut_formulario_proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_ut_formulario';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
/*
		$def['columna'][2]['nombre']='tabla';
		$def['columna'][3]['nombre']='titulo';
		$def['columna'][4]['nombre']='ev_agregar';
		$def['columna'][5]['nombre']='ev_agregar_etiq';
		$def['columna'][6]['nombre']='ev_mod_modificar';
		$def['columna'][7]['nombre']='ev_mod_modificar_etiq';
		$def['columna'][8]['nombre']='ev_mod_eliminar';
		$def['columna'][9]['nombre']='ev_mod_eliminar_etiq';
		$def['columna'][10]['nombre']='ev_mod_limpiar';
		$def['columna'][11]['nombre']='ev_mod_limpiar_etiq';
		$def['columna'][12]['nombre']='ev_mod_clave';
		$def['columna'][13]['nombre']='clase_proyecto';
		$def['columna'][14]['nombre']='clase';
		$def['columna'][15]['nombre']='auto_reset';
		$def['columna'][17]['nombre']='campo_bl';
*/
		$def['columna'][16]['nombre']='ancho';
		$def['columna'][18]['nombre']='scroll';
		$def['columna'][19]['nombre']='filas';
		$def['columna'][20]['nombre']='filas_agregar';
		$def['columna'][21]['nombre']='filas_undo';
		$def['columna'][22]['nombre']='filas_ordenar';
		$def['columna'][23]['nombre']='filas_numerar';
		$def['columna'][24]['nombre']='ev_seleccion';
		$def['columna'][25]['nombre']='alto';
		$def['columna'][26]['nombre']='analisis_cambios';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ut_formulario_proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_ut_formulario = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>