<?
//Generacion: 22-08-2005 01:26:48
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_datos_rel extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_datos_rel'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_datos_rel';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='clave';
		$def['columna'][3]['nombre']='ap';
		$def['columna'][4]['nombre']='ap_clase';
		$def['columna'][5]['nombre']='ap_archivo';
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