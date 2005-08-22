<?
//Generacion: 19-07-2005 05:58:17
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_eventos extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_eventos'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_eventos';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][0]['no_nulo_db']='1';
		$def['columna'][1]['nombre']='objeto';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['no_nulo_db']='1';
		$def['columna'][2]['nombre']='identificador';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['no_nulo_db']='1';
		$def['columna'][3]['nombre']='etiqueta';
		$def['columna'][3]['no_nulo_db']='1';
		$def['columna'][4]['nombre']='maneja_datos';
		$def['columna'][5]['nombre']='sobre_fila';
		$def['columna'][6]['nombre']='confirmacion';
		$def['columna'][7]['nombre']='estilo';
		$def['columna'][8]['nombre']='imagen_recurso_origen';
		$def['columna'][9]['nombre']='imagen';
		$def['columna'][10]['nombre']='en_botonera';
		$def['columna'][11]['nombre']='ayuda';
		$def['columna'][12]['nombre']='orden';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
		$this->set_no_duplicado( array("identificador") );
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "proyecto = '{$id['proyecto']}'";
		$where[] = "objeto = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}
}
?>