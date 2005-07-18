<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_mt_me_etapa_dep extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_mt_me_etapa_dep'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_mt_me_etapa_dep';
		$def['columna'][0]['nombre']='objeto_mt_me_proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_mt_me';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='posicion';
		$def['columna'][2]['pk']='1';
		//$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='proyecto';
		$def['columna'][3]['pk']='1';
		//$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='objeto_consumidor';
		$def['columna'][4]['pk']='1';
		//$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='identificador';
		$def['columna'][5]['pk']='1';
		$def['columna'][5]['no_nulo']='1';
		$def['columna'][6]['nombre']='orden';
		$def['columna'][6]['no_nulo']='1';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_mt_me_proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_mt_me = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>