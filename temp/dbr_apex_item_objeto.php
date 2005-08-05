<?
//Generacion: 5-08-2005 17:08:50
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_item_objeto extends db_registros_s
//db_registros especifico de la tabla 'apex_item_objeto'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_item_objeto';
		$def['columna'][0]['nombre']='item_id';
		$def['columna'][1]['nombre']='proyecto';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='item';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo']='1';
		$def['columna'][3]['nombre']='objeto';
		$def['columna'][3]['pk']='1';
		$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='orden';
		$def['columna'][4]['no_nulo']='1';
		$def['columna'][5]['nombre']='inicializar';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "item = '{$id['item']}'";
		$where[] = "objeto = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>