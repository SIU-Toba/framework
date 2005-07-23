<?
//Generacion: 23-07-2005 05:04:17
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ci_pantalla extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ci_pantalla'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ci_pantalla';
		$def['columna'][0]['nombre']='objeto_ci_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_ci';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='pantalla';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_obj_ci_pantalla_seq';
		$def['columna'][3]['nombre']='identificador';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='orden';
		$def['columna'][5]['nombre']='etiqueta';
		$def['columna'][6]['nombre']='descripcion';
		$def['columna'][7]['nombre']='tip';
		$def['columna'][8]['nombre']='imagen_recurso_origen';
		$def['columna'][9]['nombre']='imagen';
		$def['columna'][10]['nombre']='objetos';
		$def['columna'][11]['nombre']='ev_procesar';
		$def['columna'][12]['nombre']='ev_cancelar';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ci_proyecto = '{$id['objeto_ci_proyecto']}'";
		$where[] = "objeto_ci = '{$id['objeto_ci']}'";
		$where[] = "pantalla = '{$id['pantalla']}'";
		$this->cargar_datos($where);
	}
}
?>