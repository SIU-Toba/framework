<?
//Generacion: 22-08-2005 01:26:48
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_datos_rel_asoc extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_datos_rel_asoc'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_datos_rel_asoc';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='asoc_id';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_objeto_datos_rel_asoc_seq';
		$def['columna'][3]['nombre']='identificador';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='padre_proyecto';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='padre_objeto';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='padre_id';
		$def['columna'][6]['no_nulo']='1';
		$def['columna'][7]['nombre']='padre_clave';
		$def['columna'][8]['nombre']='hijo_proyecto';
		$def['columna'][8]['no_nulo']='1';
		$def['columna'][9]['nombre']='hijo_objeto';
		$def['columna'][9]['no_nulo']='1';
		$def['columna'][10]['nombre']='hijo_id';
		$def['columna'][10]['no_nulo']='1';
		$def['columna'][11]['nombre']='hijo_clave';
		$def['columna'][12]['nombre']='cascada';
		$def['columna'][13]['nombre']='orden';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "objeto = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>