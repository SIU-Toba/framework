<?
//Generacion: 14-07-2005 17:04:53
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto';
		$def['columna'][0]['nombre']='proyecto';
		$def['columna'][0]['pk']='1';
		$def['columna'][1]['nombre']='objeto';
		$def['columna'][1]['pk']='1';
		$def['columna'][1]['secuencia']='apex_objeto_seq';
		$def['columna'][2]['nombre']='anterior';
		$def['columna'][3]['nombre']='reflexivo';
		$def['columna'][4]['nombre']='clase_proyecto';
		$def['columna'][5]['nombre']='clase';
		$def['columna'][6]['nombre']='subclase';
		$def['columna'][7]['nombre']='subclase_archivo';
		$def['columna'][8]['nombre']='objeto_categoria_proyecto';
		$def['columna'][9]['nombre']='objeto_categoria';
		$def['columna'][10]['nombre']='nombre';
		$def['columna'][10]['no_nulo']='1';
		$def['columna'][11]['nombre']='titulo';
		$def['columna'][12]['nombre']='colapsable';
		$def['columna'][13]['nombre']='descripcion';
		$def['columna'][14]['nombre']='fuente_datos_proyecto';
		$def['columna'][14]['no_nulo']='1';
		$def['columna'][15]['nombre']='fuente_datos';
		$def['columna'][15]['no_nulo']='1';
		$def['columna'][16]['nombre']='solicitud_registrar';
		$def['columna'][17]['nombre']='solicitud_obj_obs_tipo';
		$def['columna'][18]['nombre']='solicitud_obj_observacion';
/*
		$def['columna'][19]['nombre']='parametro_a';
		$def['columna'][20]['nombre']='parametro_b';
		$def['columna'][21]['nombre']='parametro_c';
		$def['columna'][22]['nombre']='parametro_d';
		$def['columna'][23]['nombre']='parametro_e';
		$def['columna'][24]['nombre']='parametro_f';
		$def['columna'][25]['nombre']='usuario';
		$def['columna'][26]['nombre']='creacion';
*/
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