<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_plan_linea extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_plan_linea'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_plan_linea';
		$def['columna'][0]['nombre']='objeto_plan_proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_plan';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='linea';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_objeto_plan_linea_seq';
		$def['columna'][3]['nombre']='descripcion_corta';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='descripcion';
		$def['columna'][5]['nombre']='fecha';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='color';
		$def['columna'][7]['nombre']='ancho';
		$def['columna'][8]['nombre']='estilo';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_plan_proyecto = '{$id['objeto_plan_proyecto']}'";
		$where[] = "objeto_plan = '{$id['objeto_plan']}'";
		$where[] = "linea = '{$id['linea']}'";
		$this->cargar_datos($where);
	}
}
?>