<?
//Generacion: 26-07-2005 17:50:39
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_db_registros_col extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_db_registros_columna'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_db_registros_col';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='col_id';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_objeto_dbr_columna_seq';
		$def['columna'][3]['nombre']='columna';
		//$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='tipo';
		$def['columna'][5]['nombre']='pk';
		$def['columna'][6]['nombre']='secuencia';
		$def['columna'][7]['nombre']='no_nulo';
		$def['columna'][8]['nombre']='no_nulo_db';
		$def['columna'][9]['nombre']='largo';
		$def['columna'][10]['nombre']='externa';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
		$this->set_no_duplicado( array("columna") );		
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "objeto = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>