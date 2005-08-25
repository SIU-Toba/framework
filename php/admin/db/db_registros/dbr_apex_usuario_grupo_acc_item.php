<?
//Generacion: 25-08-2005 16:37:45
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_usuario_grupo_acc_item extends db_registros_s
//db_registros especifico de la tabla 'apex_usuario_grupo_acc_item'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_usuario_grupo_acc_item';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
//		$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='usuario_grupo_acc';
		$def['columna'][1]['pk']='1';
//		$def['columna'][1]['no_nulo']='1';
		$def['columna'][3]['nombre']='item';
		$def['columna'][3]['pk']='1';
//		$def['columna'][3]['no_nulo']='1';
		
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "item = '{$id['item']}'";
		$this->cargar_datos($where);
	}
}
?>